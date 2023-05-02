<?php

namespace MetaFox\Music\Http\Resources\v1\Album;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Music\Http\Requests\v1\Album\CreateFormRequest;
use MetaFox\Music\Http\Resources\v1\Song\SongItem;
use MetaFox\Music\Models\Album as Model;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Policies\AlbumPolicy;
use MetaFox\Music\Repositories\AlbumRepositoryInterface;
use MetaFox\Music\Repositories\SongRepositoryInterface;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateAlbumForm.
 * @property Model $resource
 */
class UpdateAlbumForm extends StoreAlbumForm
{
    /**
     * @param  CreateFormRequest       $request
     * @param  SongRepositoryInterface $repository
     * @param  int|null                $id
     * @return void
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function boot(CreateFormRequest $request, AlbumRepositoryInterface $repository, ?int $id = null): void
    {
        $context        = user();
        $this->resource = $repository->find($id);
        policy_authorize(AlbumPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $albumText = $this->resource->albumText;
        $privacy   = $this->resource->privacy;

        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $genres = $this->resource->genres
            ->pluck('id')
            ->toArray();

        $songs = $this->resource->songs->map(function ($song) {
            if ($song instanceof Song) {
                return new SongItem($song);
            }

            return null;
        });

        $this->title(__p('music::phrase.edit_album'))
            ->action(url_utility()->makeApiUrl('/music/album/' . $this->resource->entityId()))
            ->setBackProps(__p('core::web.music'))
            ->asPut()
            ->setValue([
                'name'         => $this->resource->name,
                'text'         => $albumText != null ? parse_output()->parse($albumText->text_parsed) : '',
                'genres'       => $genres,
                'songs'        => $songs,
                'privacy'      => $privacy,
                'owner_id'     => $this->resource->owner_id,
                'year'         => $this->resource->year,
                'useThumbnail' => true,
                'attachments'  => $this->resource->attachmentsForForm(),
                'module_id'    => $this->resource->entityType(),
            ]);
    }
}
