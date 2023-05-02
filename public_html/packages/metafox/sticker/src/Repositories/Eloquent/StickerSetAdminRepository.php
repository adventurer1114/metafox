<?php

namespace MetaFox\Sticker\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File as FileSupport;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Support\FileSystem\UploadFile;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxFileType;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Policies\StickerSetPolicy;
use MetaFox\Sticker\Repositories\StickerRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerSetAdminRepositoryInterface;
use MetaFox\Sticker\Support\Browse\Scopes\NotDeleteScope;
use ZipArchive;

/**
 * Class StickerSetRepository.
 * @method StickerSet find($id, $columns = ['*'])
 * @method StickerSet getModel()
 * @ignore
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StickerSetAdminRepository extends AbstractRepository implements StickerSetAdminRepositoryInterface
{
    public function model(): string
    {
        return StickerSet::class;
    }

    protected function stickerRepository(): StickerRepositoryInterface
    {
        return resolve(StickerRepositoryInterface::class);
    }

    public function viewStickerSets(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        $notDeleteScope = new NotDeleteScope();

        return $this->getModel()->newQuery()
            ->addScope($notDeleteScope)
            ->simplePaginate($attributes['limit']);
    }

    public function createStickerSet(User $context, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'create', $context);

        if (isset($attributes['sticker_temp_file'])) {
            foreach ($attributes['sticker_temp_file'] as $tempFileId) {
                $tempFile = upload()->getFile($tempFileId);
                $sticker  = [
                    'image_file_id' => $tempFile->id,
                ];

                if (isset($attributes['view_only'])) {
                    $sticker = array_merge($sticker, ['view_only' => $attributes['view_only']]);
                }

                $attributes['stickers'][] = $sticker;

                upload()->rollUp($tempFileId);
            }
        }

        /** @var StickerSet $stickerSet */
        $stickerSet = parent::create($attributes);

        return $stickerSet->refresh();
    }

    public function updateStickerSet(User $context, int $id, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);

        $stickerSet = $this->find($id);

        $this->checkCanUpdate($stickerSet);

        $file = Arr::get($attributes, 'file', []);
        unset($attributes['file']);

        $this->stickerRepository()->uploadStickers($context, $stickerSet, $file);
        $stickerSet->update($attributes);

        return $stickerSet->refresh();
    }

    public function viewStickerSet(User $context, int $id): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);
        $stickerSet = $this->find($id);

        //        $this->checkIsDeleted($stickerSet);

        $stickerSet->load([
            'stickers' => function (HasMany $query) {
                $notDeleteScope = new NotDeleteScope();

                return $query->addScope($notDeleteScope);
            },
        ]);

        return $stickerSet;
    }

    public function deleteStickerSet(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'delete', $context);

        $stickerSet = $this->find($id);
        $this->checkCanUpdate($stickerSet, 'delete');

        return $stickerSet->update(['is_deleted' => StickerSet::IS_DELETED]);
    }

    public function toggleActive(User $context, int $id, int $isActive): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        return $stickerSet->update(['is_active' => $isActive]);
    }

    /**
     * @param int $stickerId
     *
     * @return Sticker|null
     */
    public function getSticker(int $stickerId): ?Sticker
    {
        /** @var Sticker $sticker */
        $sticker = Sticker::query()->find($stickerId);

        return $sticker;
    }

    public function installStickerSet(User $context, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'create', $context);

        if (array_key_exists('file', $attributes)) {
            $attributes['stickers'] = [];
            if ($attributes['file'] instanceof UploadedFile) {
                $file     = $attributes['file'];
                $mineType = $file->getMimeType();

                $attributes['stickers'] = match ($mineType) {
                    MetaFoxFileType::MINE_TYPE_ZIP => $this->uploadStickerByZip($file),
                    MetaFoxFileType::MINE_TYPE_GIF => $this->uploadStickerByGif($file),
                    default                        => []
                };
            }
        }

        $stickerSet = parent::create($attributes);
        $stickerSet->refresh();

        return $stickerSet;
    }

    /**
     * @param  UploadedFile                     $file
     * @return array<int, array<string, mixed>>
     */
    public function uploadStickerByZip(UploadedFile $file): array
    {
        $filePath = $file->getRealPath();
        if ($filePath === false) {
            return [];
        }

        $newOrdering = 0;
        $stickers    = [];
        $tmpFolder   = FileSupport::dirname($filePath);
        $extractTo   = $tmpFolder . DIRECTORY_SEPARATOR . md5($file->getFilename());
        $archive     = new ZipArchive();
        if ($archive->open($filePath) === true) {
            $archive->extractTo($extractTo);
            $archive->close();
        }

        foreach (FileSupport::allFiles($extractTo) as $temp) {
            $uploadedTemp = UploadFile::pathToUploadedFile($temp->getRealPath() ?: MetaFoxConstant::EMPTY_STRING);
            if ($uploadedTemp === false) {
                continue;
            }
            $storageFile = upload()
                ->setPath('sticker')
                ->setStorage('asset')
                ->storeFile($uploadedTemp);

            $stickers[] = [
                'image_path'    => $storageFile->path,
                'server_id'     => $storageFile->storage_id,
                'ordering'      => $newOrdering++,
                'image_file_id' => $storageFile->id,
                'view_only'     => false,
            ];
        }

        return $stickers;
    }

    protected function uploadStickerByGif(UploadedFile $file): array
    {
        $filePath = $file->getRealPath();
        if ($filePath === false) {
            return [];
        }
        $uploadedTemp = UploadFile::pathToUploadedFile($filePath ?: MetaFoxConstant::EMPTY_STRING);

        $storageFile = upload()
            ->setPath('sticker')
            ->setStorage('asset')
            ->storeFile($uploadedTemp);

        $stickers[] = [
            'image_path'    => $storageFile->path,
            'server_id'     => $storageFile->storage_id,
            'image_file_id' => $storageFile->id,
            'ordering'      => 0,
            'view_only'     => false,
        ];

        return $stickers;
    }

    /**
     * @param StickerSet $stickerSet
     *
     * @throws ValidationException
     */
    private function checkIsDeleted(StickerSet $stickerSet): void
    {
        if ($stickerSet->is_deleted) {
            throw ValidationException::withMessages([
                __p('sticker::validation.sticker_set_already_deleted'),
            ]);
        }
    }

    /**
     * @param StickerSet $stickerSet
     * @param string     $action
     *
     * @throws ValidationException
     */
    private function checkCanUpdate(StickerSet $stickerSet, string $action = 'update'): void
    {
        $this->checkIsDeleted($stickerSet);

        if ($stickerSet->view_only) {
            throw ValidationException::withMessages([
                __p('sticker::validation.cant_action_default_sticker_set', ['action' => $action]),
            ]);
        }

        if ($action == 'delete') {
            if ($stickerSet->is_default) {
                throw ValidationException::withMessages([
                    __p('sticker::validation.cant_action_default_sticker_set', ['action' => $action]),
                ]);
            }
        }
    }
}
