<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Photo\Http\Requests\v1\Album\CreateFormRequest;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Support\Facades\Album as FacadesAlbum;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditAlbumForm.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 *
 * @driverType form-mobile
 * @driverName photo.album.update
 */
class UpdateAlbumMobileForm extends StoreAlbumMobileForm
{
    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(CreateFormRequest $request, AlbumRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $this->resource = $repository->find($id);
        policy_authorize(AlbumPolicy::class, 'update', $context, $this->resource);
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();
        $albumInfo = $this->resource->albumInfo;
        $privacy = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $description = '';
        if ($albumInfo) {
            $description = $albumInfo->description;
        }

        $name = $this->resource->name;
        if (FacadesAlbum::isDefaultAlbum($this->resource->album_type)) {
            $name = FacadesAlbum::getDefaultAlbumTitle($this->resource);
        }

        $this->title(__p('photo::phrase.edit_photo_album'))
            ->action(url_utility()->makeApiUrl("photo-album/{$this->resource->entityId()}"))
            ->asPut()
            ->setValue([
                'name'          => $name,
                'owner_id'      => $this->resource->owner_id,
                'text'          => $description,
                'privacy'       => $privacy,
                'canSetPrivacy' => $context->hasPermissionTo('photo_album.set_privacy'),
            ]);
    }
}
