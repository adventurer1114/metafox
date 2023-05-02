<?php

namespace MetaFox\Core\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use MetaFox\Core\Http\Requests\FileApi\UploadFileMultipleRequest;
use MetaFox\Core\Http\Requests\FileApi\UploadFileRequest;
use MetaFox\Core\Http\Requests\v1\Attachment\AttachmentRequest;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItem;
use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Storage\Http\Resources\v1\StorageFile\StorageFileCollection;
use MetaFox\Storage\Http\Resources\v1\StorageFile\StorageFileItem;

/**
 * Class FileController.
 * @group file
 * @authenticated
 * @ignore
 * @codeCoverageIgnore
 */
class FileController extends ApiController
{
    /**
     * Upload single.
     *
     * @param UploadFileRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group upload
     * @authenticated
     */
    public function upload(UploadFileRequest $request): JsonResponse
    {
        $params = $request->validated();

        // Get files from request.
        $file = $params['file'];

        $base64 = Arr::get($params, 'base64');

        if (!$file instanceof UploadedFile) {
            return $this->error('No file upload');
        }

        $fileType = $params['file_type'];

        if (!file_type()->verifyMime($file, $fileType)) {
            return $this->error(
                __p('validation.cannot_play_back_the_file_the_format_is_not_supported'),
                422
            );
        }

        $storageId = Arr::get($params, 'storage_id');

        if (!$storageId) {
            $storageId = 'photo';
        }

        // Upload files.
        $storageFile = upload()
            ->setStorage($storageId)
            ->setPath($params['item_type'])
            ->setThumbSizes($params['thumbnail_sizes'])
            ->setItemType($params['item_type'])
            ->setUser(user())
            ->setBase64($base64)
            ->storeFile($file);

        return $this->success(new StorageFileItem($storageFile));
    }

    /**
     * Upload multiple file.
     *
     * @param UploadFileMultipleRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group upload
     * @authenticated
     */
    public function uploadMultiple(UploadFileMultipleRequest $request): JsonResponse
    {
        $params = $request->validated();

        // Get files from request.
        $files = $params['file'];

        if (empty($files) || !is_array($files)) {
            return $this->error('No file upload');
        }
        // Get item types.
        $itemType = $params['item_type'];

        // Get upload type: public, s3.
        $uploadType = $params['upload_type'] ?? null;

        // Upload files.
        $storageFiles = upload()
            ->setStorage($uploadType)
            ->setThumbSizes($params['thumbnail_sizes'])
            ->setItemType($itemType)
            ->setUser(user())
            ->storeFiles($files);

        return $this->success(new StorageFileCollection($storageFiles));
    }

    /**
     * Upload attachment.
     *
     * @param AttachmentRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group upload
     */
    public function uploadAttachment(AttachmentRequest $request): JsonResponse
    {
        $params = $request->validated();

        // Get files from request.
        $file = $params['file'];

        // Get item types.
        $itemType = $params['item_type'];

        // Get upload type: public, s3.
        $uploadType = $params['upload_type'] ?? null;

        // Upload files.
        $attachment = upload()
            ->setStorage($uploadType)
            ->setThumbSizes(ResizeImage::SIZE)
            ->setItemType($itemType)
            ->setUser(user())
            ->storeAttachment($file);

        return $this->success(new AttachmentItem($attachment));
    }
}
