<?php

namespace MetaFox\Photo\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Photo\Jobs\DeleteAlbumJob;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\AlbumItem;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Photo\Support\Browse\Scopes\Album\PrivacyScope as AlbumPrivacyScope;
use MetaFox\Photo\Support\Browse\Scopes\Album\SortScope;
use MetaFox\Photo\Support\Browse\Scopes\Album\ViewScope;
use MetaFox\Photo\Support\Facades\Album as Facade;
use MetaFox\Platform\Contracts\HasAvatar;
use MetaFox\Platform\Contracts\HasCover;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Traits\UserMorphTrait;
use Throwable;

/**
 * Class AlbumRepository.
 * @property Album $model
 * @method   Album getModel()
 * @method   Album find($id, $columns = ['*'])
 *
 * @mixin UserMorphTrait;
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AlbumRepository extends AbstractRepository implements AlbumRepositoryInterface
{
    use HasFeatured;
    use HasSponsor;
    use HasApprove;
    use CollectTotalItemStatTrait;
    use UserMorphTrait;

    public function model(): string
    {
        return Album::class;
    }

    protected function groupRepository(): PhotoGroupRepositoryInterface
    {
        return resolve(PhotoGroupRepositoryInterface::class);
    }

    public function viewAlbums(User $context, User $owner, array $attributes = []): Paginator
    {
        policy_authorize(AlbumPolicy::class, 'viewAny', $context, $owner);

        $view = $attributes['view'] ?? Browse::VIEW_ALL;

        $limit = !empty($attributes['limit']) ? $attributes['limit'] : Pagination::DEFAULT_ITEM_PER_PAGE;

        switch ($view) {
            case 'feature':
                return $this->findFeature($limit);
            case 'sponsor':
                return $this->findSponsor($limit);
        }

        $query = $this->buildQueryViewAlbums($context, $owner, $attributes);

        return $query
            ->with(['albumInfo', 'coverPhoto', 'userEntity', 'ownerEntity'])
            ->simplePaginate($limit, ['photo_albums.*']);
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     */
    private function buildQueryViewAlbums(User $context, User $owner, array $attributes): Builder
    {
        $sort      = $attributes['sort'] ?? Browse::SORT_RECENT;
        $sortType  = $attributes['sort_type'] ?? Browse::SORT_TYPE_DESC;
        $when      = $attributes['when'] ?? Browse::WHEN_ALL;
        $view      = $attributes['view'] ?? Browse::VIEW_ALL;
        $search    = $attributes['q'] ?? null;
        $profileId = $attributes['user_id'] ?? 0;

        if (!$context->isGuest()) {
            if ($profileId == $context->entityId()) {
                $view = Browse::VIEW_MY;
            }
        }

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($context->entityId())
            ->setModerationPermissionName('photo_album.moderate');

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view);

        $query = $this->getModel()->newQuery();

        $this->applyDisplayPhotoSetting($query, $context, $owner, $attributes);

        if ($owner instanceof HasPrivacyMember) {
            if (!$owner->isMember($context)) {
                $query->where('photo_albums.total_item', '>', 0);
            }
        }

        $ownerId = null;

        if ($owner->entityId() != $context->entityId()) {
            $ownerId = $owner->entityId();
        }

        if ($profileId) {
            $ownerId = $profileId;
        }

        if (null !== $ownerId) {
            $privacyScope->setOwnerId($ownerId);

            $viewScope->setIsViewOwner(true);
        }

        if ($search != null) {
            $query = $query->addScope(new SearchScope($search, ['name']));
        }

        return $query
            ->addScope($privacyScope)
            ->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($viewScope);
    }

    public function viewAlbum(User $context, int $id): Album
    {
        $album = $this->with(['albumInfo', 'coverPhoto', 'userEntity', 'ownerEntity'])->find($id);

        policy_authorize(AlbumPolicy::class, 'view', $context, $album);

        $album->incrementAmount('total_view');

        return $album;
    }

    /**
     * @throws AuthorizationException
     * @throws PermissionDeniedException
     */
    public function createAlbum(User $context, User $owner, array $attributes): Album
    {
        policy_authorize(AlbumPolicy::class, 'create', $context, $owner);

        app('events')->dispatch('photo.album.pre_photo_album_create', [$context, $attributes], true);

        // Quota check for album
        $quotaCheckData = [
            'message' => __p('photo::phrase.album_quota_limit_reached'),
            'where'   => [
                'album_type' => 0,
            ],
        ];
        app('quota')->checkQuotaControlWhenCreateItem(
            $context,
            Album::ENTITY_TYPE,
            1,
            $quotaCheckData
        );

        // flood check for album
        app('flood')->checkFloodControlWhenCreateItem($context, Album::ENTITY_TYPE);

        $newItems    = Arr::get($attributes, 'items.new', []);
        $updateItems = Arr::get($attributes, 'items.update', []);
        unset($attributes['items']);

        // Check photo quota per items create + remove
        $this->checkPhotoQuota($context, $newItems);

        $attributes = array_merge($attributes, [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
            'module_id'  => Photo::ENTITY_TYPE,
            'album_type' => Arr::get($attributes, 'album_type', Album::NORMAL_ALBUM),
        ]);

        $album = $this->getModel()->fill($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $album->setPrivacyListAttribute($attributes['list']);
        }

        $album->save();

        //Handle add new album items
        if (!empty($newItems)) {
            $this->uploadAlbumItems($context, $album, $newItems, $attributes);
        }

        // Add below items as album
        if (!empty($updateItems)) {
            $this->syncItemsWithAlbum($context, $album, $updateItems);
        }

        $album->refresh();
        app('events')->dispatch('activity.feed.create_from_resource', [$album], true);

        return $album;
    }

    /**
     * @param  User                   $context
     * @param  int                    $id
     * @param  array<string, mixed>   $attributes
     * @return Album
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function updateAlbum(User $context, int $id, array $attributes): Album
    {
        $album = $this->find($id);

        policy_authorize(AlbumPolicy::class, 'update', $context, $album);

        app('events')->dispatch('photo.album.pre_photo_album_update', [$context, $attributes], true);

        $newItems    = $attributes['items']['new'] ?? [];
        $updateItems = $attributes['items']['update'] ?? [];
        $removeItems = $attributes['items']['remove'] ?? [];
        unset($attributes['items']);

        // Check photo quota
        $this->checkPhotoQuota($context, $newItems, $removeItems);

        $album->fill($attributes);

        if (isset($attributes['privacy']) && $attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $album->setPrivacyListAttribute($attributes['list']);
        }

        $album->save();

        // todo ? check require owner_id? on request class?
        if ($attributes['owner_id'] ?? 0) {
            $owner                    = UserEntity::getById($attributes['owner_id']);
            $attributes['owner_id']   = $owner->entityId();
            $attributes['owner_type'] = $owner->entityType();
        }

        $attributes = array_merge($attributes, [
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'module_id' => Photo::ENTITY_TYPE,
        ]);

        //Handle add new album items
        if (!empty($newItems)) {
            $this->uploadAlbumItems($context, $album, $newItems, $attributes);
        }

        // Add below items as album
        if (!empty($updateItems)) {
            $this->syncItemsWithAlbum($context, $album, $updateItems);
        }

        //Handle remove old album items
        if (!empty($removeItems)) {
            $this->removeAlbumItems($removeItems);
        }

        $album->refresh();

        return $album;
    }

    /**
     * @param  User                   $context
     * @param  int                    $id
     * @param  array<string, mixed>   $attributes
     * @return Album
     * @throws AuthorizationException
     */
    public function uploadMedias(User $context, int $id, array $attributes): array
    {
        $album = $this->find($id);

        policy_authorize(AlbumPolicy::class, 'uploadMedias', $context, $album);

        app('events')->dispatch('photo.album.pre_photo_album_upload_media', [$context, $attributes], true);

        $newItems    = $attributes['items']['new'] ?? [];
        $newItems    = $this->handleNewItems($context, $album, $newItems);
        $updateItems = $attributes['items']['update'] ?? [];
        $removeItems = $attributes['items']['remove'] ?? [];

        // Check photo quota
        $this->checkPhotoQuota($context, $newItems, $removeItems);

        $owner = UserEntity::getById($attributes['owner_id']);

        $attributes = array_merge($attributes, [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
            'module_id'  => Photo::ENTITY_TYPE,
        ]);

        $uploadedPhoto = 0;
        $uploadedVideo = 0;
        $updatedPhoto  = 0;
        $updatedVideo  = 0;

        //Handle add new album items
        if (!empty($newItems)) {
            [$uploadedPhoto, $uploadedVideo] = $this->uploadAlbumItems($context, $album, $newItems, $attributes);
        }

        // Add below items as album
        if (!empty($updateItems)) {
            [$updatedPhoto, $updatedVideo] = $this->syncItemsWithAlbum($context, $album, $updateItems);
        }

        //Handle remove old album items
        if (!empty($removeItems)) {
            $this->removeAlbumItems($removeItems);
        }

        $album->refresh();

        return [
            'album'          => $album,
            'uploaded_photo' => $uploadedPhoto,
            'uploaded_video' => $uploadedVideo,
            'updated_photo'  => $updatedPhoto,
            'updated_video'  => $updatedVideo,
        ];
    }

    protected function handleNewItems(User $context, Album $album, array $newItems): array
    {
        if (!count($newItems)) {
            return $newItems;
        }

        foreach ($newItems as $key => $newItem) {
            $can = app('events')->dispatch(
                'photo.album.can_upload_to_album',
                [$context, $album->owner, Arr::get($newItem, 'type')],
                true
            );

            if (!$can) {
                unset($newItems[$key]);
            }
        }

        return $newItems;
    }

    public function deleteAlbum(User $context, int $id): bool
    {
        /** @var Album $album */
        $album = $this->find($id);

        policy_authorize(AlbumPolicy::class, 'delete', $context, $album);

        DeleteAlbumJob::dispatch($context, $album);

        return true;
    }

    public function deleteAlbumAndPhotos(User $context, Album $album): bool
    {
        if (!$album->forceDelete()) {
            return false;
        }

        Photo::query()
            ->where('album_id', '=', $album->entityId())
            ->lazy()
            ->each(function (mixed $photo) {
                if (!$photo instanceof Photo) {
                    return true;
                }

                $photo->delete();
            });

        return true;
    }

    /**
     * @param int $limit
     *
     * @return Paginator
     * @todo implement cache
     */
    public function findFeature(int $limit = 4): Paginator
    {
        //Todo: check privacy ?
        return $this->getModel()->newModelInstance()
            ->with('albumInfo')
            ->where('total_item', '>', 0)
            ->where('is_featured', '=', Album::IS_FEATURED)
            ->where('is_approved', '=', Album::IS_APPROVED)
            ->orderByDesc(HasFeature::FEATURED_AT_COLUMN)
            ->simplePaginate($limit);
    }

    /**
     * @param int $limit
     *
     * @return Paginator
     * @todo implement cache
     */
    public function findSponsor(int $limit = 4): Paginator
    {
        //Todo: check privacy ?
        return $this->getModel()->newModelInstance()
            ->with('albumInfo')
            ->where('total_item', '>', 0)
            ->where('is_sponsor', '=', Album::IS_SPONSOR)
            ->where('is_approved', '=', Album::IS_APPROVED)
            ->simplePaginate($limit);
    }

    public function updateAlbumCover(Album $album, int $photoId = 0): void
    {
        if ($photoId == 0) {
            /** @var Photo $photo */
            $photo = $album->photos()->first();

            if (null != $photo) {
                $photoId = $photo->entityId();
            }
        }

        /*
         * Not need to trigger updated observer
         */
        $album->updateQuietly(['cover_photo_id' => $photoId]);
    }

    /**
     * @param  Builder $query
     * @param  User    $owner
     * @param  array   $attributes
     * @return void
     */
    private function applyDisplayPhotoSetting(Builder $query, User $context, User $owner, array $attributes): void
    {
        $hasPrivacyMember = $owner instanceof HasPrivacyMember;

        match ($hasPrivacyMember) {
            true  => $this->queryAlbumForPrivacyMember($query, $owner),
            false => $this->queryAlbumForUserProfile(
                $query,
                $context,
                Arr::get($attributes, 'view', Browse::VIEW_ALL),
                Arr::get($attributes, 'user_id', 0)
            ),
        };
    }

    protected function queryAlbumForPrivacyMember(Builder $query, ?User $owner = null): void
    {
        $specialTypes = [];

        $entityType = $owner instanceof User ? $owner->entityType() : 'photo';

        if (!Settings::get("{$entityType}.display_profile_photo_within_gallery", false)) {
            $specialTypes[] = Album::PROFILE_ALBUM;
        }

        if (!Settings::get("{$entityType}.display_cover_photo_within_gallery", false)) {
            $specialTypes[] = Album::COVER_ALBUM;
        }

        // Some apps do not have this setting
        if (!Settings::get("{$entityType}.display_timeline_photo_within_gallery", true)) {
            $specialTypes[] = Album::TIMELINE_ALBUM;
        }

        if (count($specialTypes)) {
            $query->whereNotIn('photo_albums.album_type', $specialTypes);
        }
    }

    protected function queryAlbumForUserProfile(Builder $query, User $context, string $view, int $profileId): void
    {
        if ($profileId) {
            return;
        }

        if ($view == Browse::VIEW_MY) {
            return;
        }

        $this->queryAlbumForPrivacyMember($query);

        $ownerTypes = [];

        if (!Settings::get('photo.display_photo_album_created_in_page', false)) {
            $ownerTypes[] = 'page';
        }

        if (!Settings::get('photo.display_photo_album_created_in_group', false)) {
            $ownerTypes[] = 'group';
        }

        if (count($ownerTypes)) {
            $query->whereNotIn('photo_albums.owner_type', $ownerTypes);
        }
    }

    /**
     * @param  User                          $context
     * @param  User                          $owner
     * @return array<int,             mixed>
     * @throws AuthorizationException
     */
    public function getAlbumsForForm(User $context, User $owner): array
    {
        policy_authorize(AlbumPolicy::class, 'viewAny', $context, $owner);

        $albums = $this->getModel()->newModelInstance()
            ->where('album_type', Album::NORMAL_ALBUM)
            ->where('owner_id', $owner->entityId())
            ->where('owner_type', $owner->entityType())
            ->where('is_approved', '=', 1)
            ->get()
            ->collect();

        $albumData = [];

        foreach ($albums as $album) {
            /* @var Album $album */
            $albumData[] = [
                'label' => __p($album->name),
                'value' => $album->entityId(),
            ];
        }

        return $albumData;
    }

    public function viewAlbumItems(User $context, int $id, array $attributes = []): Paginator
    {
        $album = $this->getModel()->query()->findOrFail($id);

        policy_authorize(AlbumPolicy::class, 'view', $context, $album);

        $limit = !empty($attributes['limit']) ? $attributes['limit'] : Pagination::DEFAULT_ITEM_PER_PAGE;

        $query = $this->buildQueryAlbumItems($context, $id, $attributes);

        return $query
            ->simplePaginate($limit);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function buildQueryAlbumItems(User $context, int $id, array $attributes): Builder
    {
        $sort = $attributes['sort'] ?? Browse::SORT_RECENT;

        $sortType = $attributes['sort_type'] ?? Browse::SORT_TYPE_DESC;

        $query = AlbumItem::query();

        if (!$context->hasPermissionTo('photo_album.moderate')) {
            // Scopes.
            $privacyScope = new AlbumPrivacyScope();

            $privacyScope->setUserId($context->entityId());

            $query->addScope($privacyScope);
        }

        $sortScope = new SortScope();

        $sortScope->setSort($sort)->setSortType($sortType);

        $query->where('photo_album_item.album_id', '=', $id);

        return $query
            ->addScope($sortScope);
    }

    /**
     * @param  User                             $context
     * @param  Album                            $album
     * @param  array<int, array<string, mixed>> $newItems
     * @param  array<string, mixed>             $params
     * @return void
     */
    protected function uploadAlbumItems(User $context, Album $album, array $newItems, array $params = []): array
    {
        /**
         * Default photo set only pending if photos are pending
         * So is_approved is depend on user group setting.
         *
         * ===12-01-2023==
         * Photo Group is always created as pending, after its items are created, is_approved will
         * be updated accordingly.
         */
        $default = [
            'album_id'    => $album->entityId(),
            'privacy'     => $album->privacy,
            'list'        => $album->getPrivacyListAttribute(),
            'is_approved' => 0,
        ];

        $owner = $album->owner;

        if (null === $owner) {
            return [0, 0];
        }

        $params = array_merge($default, $params);

        $params['content'] = $params['text'] ?? null;

        $group = new PhotoGroup();

        $group->fill($params);

        if ($group->privacy == MetaFoxPrivacy::CUSTOM) {
            $group->setPrivacyListAttribute($params['list']);
        }

        $group->save();

        $group->refresh();

        /*
         * In case owner has pending mode, is_approved is depend on pending mode status
         */
        if ($owner->hasPendingMode()) {
            $params['is_approved'] = (int) $group->isApproved();
        }

        //Override listener models.creating from Core
        if ($context->userId() != $album->userId() && policy_check(AlbumPolicy::class, 'update', $context, $album)) {
            $group->update(['privacy' => $album->privacy]);
        }

        $params['group_id'] = $group->entityId();

        $uploadedPhoto = 0;
        $uploadedVideo = 0;
        $isApproved    = 0;

        foreach ($newItems as $item) {
            $tempFile = upload()->getFile($item['id']);

            $itemResult = app('events')->dispatch('photo.media_upload', [
                $context,
                $album->owner,
                $tempFile->item_type,
                $tempFile,
                $params,
            ], true);

            if ($item['type'] == 'photo') {
                $uploadedPhoto++;
            }

            if ($item['type'] == 'video') {
                $uploadedVideo++;
            }

            if ($itemResult?->isApproved()) {
                $isApproved = 1;
            }
        }

        if ($isApproved) {
            $this->groupRepository()->updateApprovedStatus($group->entityId());
        }

        return [$uploadedPhoto, $uploadedVideo];
    }

    /**
     * @param  User              $context
     * @param  Album             $album
     * @param  array<int, mixed> $updateItems
     * @return void
     */
    protected function syncItemsWithAlbum(User $context, Album $album, array $updateItems): array
    {
        $updatedPhoto = 0;
        $updatedVideo = 0;

        foreach ($updateItems as $item) {
            $data = [
                'id'         => $item['id'] ?? 0,
                'type'       => $item['type'] ?? null,
                'album_id'   => $item['album_id'] ?? $album->entityId(),
                'album_type' => $item['album_type'] ?? $album->album_type,
                'privacy'    => $album->privacy,
            ];

            if (MetaFoxPrivacy::CUSTOM == $data['privacy']) {
                Arr::set($data, 'list', $album->privacy_list);
            }

            app('events')->dispatch('photo.media_add_to_album', [$context, $data], true);

            if ($item['type'] == 'photo') {
                $updatedPhoto++;
            }

            if ($item['type'] == 'video') {
                $updatedVideo++;
            }
        }

        return [$updatedPhoto, $updatedVideo];
    }

    /**
     * @param  array<int, mixed> $removeItems
     * @return void
     */
    protected function removeAlbumItems(array $removeItems): void
    {
        foreach ($removeItems as $item) {
            app('events')->dispatch('photo.media_remove', [$item['id'], $item['type']], true);
        }
    }

    public function getDefaultUserAlbums(int $ownerId, array $types = []): Collection
    {
        if (!count($types)) {
            $types = Facade::getDefaultTypes();
        }

        return $this->getModel()->newQuery()
            ->whereIn('album_type', $types)
            ->where('owner_id', $ownerId)
            ->get();
    }

    public function isDefaultUserAlbum(int $id, int $ownerId = 0): bool
    {
        $where = [
            'id' => $id,
        ];

        if ($ownerId) {
            Arr::set($where, 'owner_id', $ownerId);
        }

        $exists = $this->getModel()->newQuery()
            ->whereIn('album_type', Facade::getDefaultTypes())
            ->where($where)
            ->count(['id']);

        return $exists == 1;
    }

    public function getAlbumById(int $id): ?Album
    {
        return $this->getModel()->newModelQuery()
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * @param  User              $context
     * @param  array<int, mixed> $newItems
     * @param  array<int, mixed> $removedItems
     * @return void
     */
    private function checkPhotoQuota(User $context, array $newItems = [], array $removedItems = []): void
    {
        if (empty($newItems) && empty($removedItems)) {
            return;
        }

        $totalNew = collect($newItems)->groupBy('type')->map(function ($item) {
            return count($item);
        })->get('photo', 0);

        $totalRemove = collect($removedItems)->groupBy('type')->map(function ($item) {
            return count($item);
        })->get('photo', 0);

        app('quota')->checkQuotaControlWhenCreateItem($context, Photo::ENTITY_TYPE, $totalNew - $totalRemove);
    }

    public function feature(User $context, int $id, int $feature): bool
    {
        $model = $this->find($id);

        if (!$model->items()->count() && !$model->is_featured) {
            abort(401, __p('photo::phrase.cannot_feature_empty_album'));
        }

        gate_authorize($context, 'feature', $model, $model, $feature);

        return $model->update(['is_featured' => $feature]);
    }

    /**
     * @param  Album $album
     * @param  Photo $photo
     * @return void
     */
    public function removeAvatarFromAlbum(Album $album, Photo $photo): void
    {
        if (Album::PROFILE_ALBUM != $album->album_type) {
            return;
        }

        $owner = $photo->owner;
        if ($owner instanceof HasUserProfile) {
            $owner = $owner->profile;
        }

        if (!$owner instanceof HasAvatar) {
            return;
        }

        if ($owner->getAvatarId() != $photo->entityId()) {
            return;
        }

        if ($owner instanceof UserProfile) {
            $photo->owner->update([
                'updated_at' => Carbon::now(), //to update user => trigger toUserResource
                'profile'    => $owner->getAvatarDataEmpty(),
            ]);
        }

        if (!$owner instanceof UserProfile) {
            $owner->update($owner->getAvatarDataEmpty());
        }
    }

    /**
     * @param  Album $album
     * @param  Photo $photo
     * @return void
     */
    public function removeCoverFromAlbum(Album $album, Photo $photo): void
    {
        if (Album::COVER_ALBUM != $album->album_type) {
            return;
        }

        $owner = $photo->owner;
        if ($owner instanceof HasUserProfile) {
            $owner = $owner->profile;
        }

        if (!$owner instanceof HasCover) {
            return;
        }

        if ($owner->getCoverId() == $photo->entityId()) {
            $owner->update($owner->getCoverDataEmpty());
        }
    }
}
