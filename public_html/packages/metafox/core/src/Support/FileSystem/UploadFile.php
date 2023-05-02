<?php

namespace MetaFox\Core\Support\FileSystem;

use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use MetaFox\Core\Models\Attachment;
use MetaFox\Platform\Contracts\ResizeImageInterface as ResizeImage;
use MetaFox\Platform\Contracts\UploadFile as UploadFileContract;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxFileType;
use MetaFox\Storage\Models\StorageFile;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 * Class UploadFile.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class UploadFile implements UploadFileContract
{
    /**
     * @var array|string[]
     */
    private array $options = [
        'visibility' => 'public',
    ];

    /**
     * @var string
     */
    private string $storageId = 'photo';

    /**
     * @var string
     */
    private string $path = 'files';

    /**
     * @var string|null
     */
    private ?string $itemType = null;

    /**
     * @var ResizeImage
     */
    private ResizeImage $resizeImage;

    /**
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @var string|null
     */
    protected ?string $base64 = null;

    public function __construct(ResizeImage $resizeImage)
    {
        $this->resizeImage = $resizeImage;
    }

    public function getResizeImage(): ResizeImage
    {
        return $this->resizeImage;
    }

    /**
     * @param  string|null $itemType
     * @return UploadFile
     */
    public function setItemType(?string $itemType): static
    {
        $this->itemType = $itemType;

        return $this;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param int[] $sizes
     *
     * @return self
     */
    public function setThumbSizes(array $sizes): self
    {
        $this->resizeImage->setSizes($sizes);

        return $this;
    }

    public function getStorage(): string
    {
        return $this->storageId;
    }

    /**
     * Where to storage file etc.
     *
     * @param  mixed $storageId Example: "photo", "video", "temporary", "attachment"
     * @return $this
     */
    public function setStorage(mixed $storageId): self
    {
        if (is_string($storageId)) {
            $this->storageId = $storageId;
        }

        return $this;
    }

    /**
     * @param  string $path
     * @return static
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Generate file name.
     *
     * @param  UploadedFile $file
     * @return string
     */
    private function getFileName(UploadedFile $file): string
    {
        return sprintf('%s.%s', Uuid::uuid4(), $file->getClientOriginalExtension());
    }

    public function isImage(UploadedFile $file): bool
    {
        if ($file->getMimeType() == MetaFoxFileType::MINE_TYPE_GIF) {
            return false;
        }

        return !Validator::make(['file' => $file], [
            'file' => ['image'],
        ])->fails();
    }

    public function storeFiles(array $files): array
    {
        return array_map(function (UploadedFile $file) {
            return $this->storeFile($file);
        }, $files);
    }

    public function storeAttachments(array $files): array
    {
        return array_map(function (UploadedFile $file) {
            return $this->storeAttachment($file);
        }, $files);
    }

    public function storeAttachment(UploadedFile $file): Attachment
    {
        $context = $this->user;
        $file    = $this->storeFile($file);

        $attachment = new Attachment([
            'item_type' => $this->itemType,
            'user_id'   => $context?->entityId(),
            'user_type' => $context?->entityType(),
            'file_id'   => $file->id,
        ]);

        $attachment->save();

        return $attachment;
    }

    public function getFile(int $id): StorageFile
    {
        /** @var StorageFile $tempFile */
        $tempFile = StorageFile::query()->findOrFail($id);

        return $tempFile;
    }

    public function getFileId(?int $tempFileId, bool $rollUp = false): ?int
    {
        $fileId = null;
        if ($tempFileId) {
            $fileId = $this->getFile($tempFileId)?->id;
        }

        if ($rollUp && $tempFileId) {
            $this->rollUp($tempFileId);
        }

        return $fileId;
    }

    public function rollUp(int $id): bool
    {
        /** @var StorageFile $tempFile */
        $tempFile = StorageFile::query()->findOrFail($id);

        $tempFile->rollUp();

        return true;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function pathToUploadedFile(
        string $realpath,
        string $newFileName = null,
        bool $isTestMode = true
    ): bool|UploadedFile {
        if (!File::exists($realpath)) {
            throw new InvalidArgumentException('File not found ' . $realpath);
        }

        $originalName = $newFileName ?? File::basename($realpath);
        $mimeType     = File::mimeType($realpath);

        if ($mimeType === false) {
            $mimeType = 'application/x-empty';
        }

        return new UploadedFile($realpath, $originalName, $mimeType, null, $isTestMode);
    }

    public function convertBase64ToUploadedFile(string $imageBase64): UploadedFile
    {
        $imageParts   = explode(';base64,', $imageBase64);
        $imageTypeAux = explode('/', $imageParts[0]);
        $imageType    = $imageTypeAux[1];
        $fileData     = base64_decode($imageParts[1]);

        $time        = Carbon::now()->timestamp;
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'metafox') . "$time.{$imageType}";

        file_put_contents($tmpFilePath, $fileData);
        $tmpFile = new SymfonyFile($tmpFilePath);

        return new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );
    }

    public function convertImageToBase64(string $realpath): string
    {
        $extension = mb_pathinfo($realpath, PATHINFO_EXTENSION);
        $data      = file_get_contents($realpath);

        return 'data:image/' . $extension . ';base64,' . base64_encode($data);
    }

    public function asUploadedFile(string $realpath, $originalName = null, bool $testMode = false): UploadedFile
    {
        if (!File::exists($realpath)) {
            throw new InvalidArgumentException('File not found ' . $realpath);
        }

        $mimeType = File::mimeType($realpath);

        if ($mimeType === false) {
            $mimeType = 'application/x-empty';
        }

        return new UploadedFile($realpath, $originalName ?? File::basename($realpath), $mimeType, null, $testMode);
    }

    /**
     * @param  UploadedFile $uploadedFile
     * @return StorageFile
     */
    public function storeFile(UploadedFile $uploadedFile): StorageFile
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->storageId);

        // create prefix path
        $fileName = app('storage.path')->fileName($uploadedFile->getClientOriginalExtension());

        $localPath = $uploadedFile->getPathname();

        if (!$localPath) {
            throw new InvalidArgumentException('File not found');
        }

        $isImage = upload()->isImage($uploadedFile);

        if ($isImage) {
            $base64Path = $this->handleBase64($uploadedFile);

            if (null !== $base64Path) {
                $localPath = $base64Path;
            }

            // process handling result.
            return $this->getResizeImage()
                ->setImage($localPath)
                ->setOptions($this->options)
                ->setOriginalName($uploadedFile->getClientOriginalName())
                ->setServerId($this->storageId)
                ->setUser($this->user)
                ->setItemType($this->itemType)
                ->setPath($this->path)
                ->createFile();
        }

        $path = $disk->putFileAs($this->path, $uploadedFile, $fileName, $this->options);

        if ($path === false) {
            throw new InvalidArgumentException('Could not handle upload file', compact('prefixPath', 'fileName'));
        }

        $temp = new StorageFile([
            'file_name'     => mb_pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            'dir_name'      => mb_pathinfo($path, PATHINFO_DIRNAME),
            'path'          => $path,
            'url'           => $disk->url($path),
            'file_size'     => $uploadedFile->getSize(),
            'type'          => $uploadedFile->getType(),
            'mime_type'     => $uploadedFile->getMimeType(),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'storage_id'    => $this->getStorage(),
            'item_type'     => $this->itemType,
            'user_id'       => $this->user?->entityId(),
            'user_type'     => $this->user?->entityType(),
        ]);

        $temp->save();

        return $temp;
    }

    public function setBase64(?string $base64): static
    {
        $this->base64 = $base64;

        return $this;
    }

    public function getBase64(): ?string
    {
        return $this->base64;
    }

    protected function handleBase64(UploadedFile $uploadedFile): ?string
    {
        $base64 = $this->getBase64();

        if (null === $base64) {
            return null;
        }

        $localPath = $uploadedFile->getPathname();

        if (!file_exists($localPath)) {
            return null;
        }

        $localDisk = Storage::disk('local');

        $tempPath = md5($uploadedFile->getClientOriginalExtension() . time()) . '.' . $uploadedFile->extension();

        [, $base64] = explode(';', $base64);

        [, $base64] = explode(',', $base64);

        $base64 = base64_decode($base64);

        if (!$localDisk->put($tempPath, $base64)) {
            return null;
        }

        @register_shutdown_function(function () use ($tempPath, $localDisk) {
            $localDisk->delete($tempPath);
        });

        return $localDisk->path($tempPath);
    }
}
