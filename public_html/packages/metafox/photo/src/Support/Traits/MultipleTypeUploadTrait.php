<?php

namespace MetaFox\Photo\Support\Traits;

use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Support\Facades\UserEntity;

trait MultipleTypeUploadTrait
{
    public function canUploadVideos(): bool
    {
        if (!Settings::get('photo.photo_allow_uploading_video_to_photo_album', true)) {
            return false;
        }

        if (!Settings::get('photo.allow_uploading_with_video', true)) {
            return false;
        }

        $context = user();

        $owner = $context;

        if ($this->ownerId) {
            $owner = UserEntity::getById($this->ownerId)->detail;
        }

        $can = app('events')->dispatch('photo.upload_with_photo', [$context, $owner, 'video'], true);

        if (null === $can) {
            return false;
        }

        return (bool) $can;
    }

    /**
     * @return string[]
     */
    public function getAcceptableTypes(): array
    {
        $accepts = ['photo'];
        if ($this->canUploadVideos()) {
            $accepts[] = 'video';
        }

        return $accepts;
    }

    /**
     * @param  array<string> $allowedTypes
     * @return string
     */
    public function getAcceptableMimeTypes(array $allowedTypes): string
    {
        $accept = collect($allowedTypes)
            ->map(function (string $type) {
                return file_type()->getMimeTypeFromType($type);
            })->values()
            ->toArray();

        if (count($accept)) {
            return implode(',', $accept);
        }

        return MetaFoxConstant::EMPTY_STRING;
    }
}
