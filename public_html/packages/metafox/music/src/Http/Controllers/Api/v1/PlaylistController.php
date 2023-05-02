<?php

namespace MetaFox\Music\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use MetaFox\Music\Http\Requests\v1\Playlist\AddSongRequest;
use MetaFox\Music\Http\Requests\v1\Playlist\IndexRequest;
use MetaFox\Music\Http\Requests\v1\Playlist\ItemsRequest;
use MetaFox\Music\Http\Requests\v1\Playlist\StoreRequest;
use MetaFox\Music\Http\Requests\v1\Playlist\UpdateRequest;
use MetaFox\Music\Http\Resources\v1\Playlist\PlaylistDetail;
use MetaFox\Music\Http\Resources\v1\Playlist\PlaylistItemCollection;
use MetaFox\Music\Http\Resources\v1\Song\SongPlaylistItemCollection;
use MetaFox\Music\Models\PlaylistData;
use MetaFox\Music\Policies\PlaylistPolicy;
use MetaFox\Music\Repositories\PlaylistDataRepositoryInterface;
use MetaFox\Music\Repositories\PlaylistRepositoryInterface;
use MetaFox\Music\Repositories\SongRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class PlaylistController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PlaylistController extends ApiController
{
    public PlaylistRepositoryInterface $repository;

    public function __construct(PlaylistRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $context = user();
        $owner   = $context;

        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (!policy_check(PlaylistPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                throw new AuthorizationException();
            }

            if (!UserPrivacy::hasAccess($context, $owner, 'music.profile_menu')) {
                return $this->success([]);
            }
        }

        if (Arr::get($params, 'genre_id')) {
            return $this->success([]);
        }

        $data = $this->repository->viewPlaylists($context, $owner, $params);

        return $this->success(new PlaylistItemCollection($data));
    }

    /**
     * @param  StoreRequest            $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $context = $owner = user();
        $params  = $request->validated();

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $playlist = $this->repository->createPlaylist($context, $owner, $params);

        $message = __p(
            'core::phrase.resource_create_success',
            ['resource_name' => __p('music::phrase.playlist')]
        );

        return $this->success([
            'id'            => $playlist->entityId(),
            'module_name'   => $playlist->moduleName(),
            'resource_name' => $playlist->entityType(),
        ], [], $message);
    }

    /**
     * @param  AddSongRequest          $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function addSong(AddSongRequest $request): JsonResponse
    {
        $context         = user();
        $params          = $request->validated();
        $playlistIds     = Arr::get($params, 'playlist_ids', []);
        $songId          = Arr::get($params, 'item_id');
        $song            = resolve(SongRepositoryInterface::class)->find($songId);
        $oldPlaylistIds  = resolve(PlaylistDataRepositoryInterface::class)->getPlaylistIdsBySong($context, $songId);
        $removePlaylists = array_diff($oldPlaylistIds, $playlistIds);

        $song->playlists()->detach($removePlaylists);

        foreach ($playlistIds as $playlistId) {
            $playlistData = resolve(PlaylistDataRepositoryInterface::class)
                ->findPlaylistData($playlistId, $songId);

            if ($playlistData instanceof PlaylistData) {
                continue;
            }

            $this->repository->addSong(user(), $playlistId, $songId);
        }

        return $this->success([], [], __p('music::phrase.successfully_add_to_playlist'));
    }

    /**
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $context  = user();
        $playlist = $this->repository->viewPlaylist($context, $id);

        if (null == $playlist) {
            return $this->error(
                __p('core::phrase.the_entity_name_you_are_looking_for_can_not_be_found', ['entity_name' => 'playlist']),
                403
            );
        }

        return $this->success(new PlaylistDetail($playlist), [], '');
    }

    /**
     * Update a resource.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        $data    = $this->repository->updatePlaylist($context, $id, $params);

        return $this->success(new PlaylistDetail($data), [], __p('core::phrase.resource_update_success', ['resource_name' => __p('music::phrase.playlist')]));
    }

    /**
     * Delete a resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $context = user();

        $this->repository->deletePlaylist($context, $id);

        return $this->success([
            'id' => $id,
        ], [], __p('music::phrase.playlist_deleted_successfully'));
    }

    /**
     * @param SponsorRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function sponsor(SponsorRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsor(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;

        $message = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message = __p($message, ['resource_name' => __p('music::phrase.music_playlist')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * @param FeatureRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];

        $this->repository->feature(user(), $id, $feature);

        $message = __p('music::phrase.playlist_featured_successfully');
        if (!$feature) {
            $message = __p('music::phrase.playlist_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ItemsRequest            $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function items(ItemsRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $context  = user();
        $playlist = $this->repository->find($id);

        policy_authorize(PlaylistPolicy::class, 'view', $context, $playlist);

        $data = $this->repository->viewPlaylistItems($context, $id, $params);

        return $this->success(new SongPlaylistItemCollection($data));
    }

    /**
     * Sponsor music in feed.
     *
     * @param SponsorInFeedRequest $request
     * @param int                  $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function sponsorInFeed(SponsorInFeedRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsorInFeed(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;

        $message = $isSponsor ? 'core::phrase.resource_sponsored_in_feed_successfully' : 'core::phrase.resource_unsponsored_in_feed_successfully';
        $message = __p($message, ['resource_name' => __p('music::phrase.music_playlist')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }
}
