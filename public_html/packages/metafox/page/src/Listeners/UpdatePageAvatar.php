<?php

namespace MetaFox\Page\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UpdatePageAvatar
{
    /**
     * @param  User                          $context
     * @param  User                          $owner
     * @param  UploadedFile|null             $image
     * @param  string                        $imageCrop
     * @return array<string,          mixed>
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function handle(?User $context, User $owner, ?UploadedFile $image, string $imageCrop): array
    {
        if (!$owner instanceof Page) {
            return [];
        }

        return resolve(PageRepositoryInterface::class)
            ->updateAvatar($context, $owner->entityId(), $image, $imageCrop);
    }
}
