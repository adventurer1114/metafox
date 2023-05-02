<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Video\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Support\FileSystem\UploadFile;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Photo\Policies\PhotoPolicy;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\TempFileModel;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\Platform\Support\Repository\HasSponsorInFeed;
use MetaFox\Storage\Models\StorageFile;
use MetaFox\User\Traits\UserMorphTrait;
use MetaFox\Video\Jobs\VideoProcessingJob;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Notifications\VideoDoneProcessingNotification;
use MetaFox\Video\Policies\CategoryPolicy;
use MetaFox\Video\Policies\VideoPolicy;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;
use MetaFox\Video\Repositories\VideoRepositoryInterface;
use MetaFox\Video\Support\Browse\Scopes\Video\SortScope;
use MetaFox\Video\Support\Browse\Scopes\Video\ViewScope;
use MetaFox\Video\Support\CacheManager;

/**
 * Class VideoRepository.
 *
 * @method   Model getModel()
 * @method   Model find($id, $columns = ['*'])
 * @method   Model newModelInstance()
 * @property Model $model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class VideoRepository extends AbstractRepository implements VideoRepositoryInterface
{
    use HasApprove;
    use HasFeatured;
    use HasSponsor;
    use HasSponsorInFeed;
    use CollectTotalItemStatTrait;
    use UserMorphTrait;

    public function model(): string
    {
        return Model::class;
    }

    /**
     * @inheritdoc
     */
    public function viewVideos(ContractUser $context, ContractUser $owner, array $attributes): Paginator
    {
        policy_authorize(VideoPolicy::class, 'viewAny', $context, $owner);
        $limit     = $attributes['limit'];
        $profileId = $attributes['user_id'];
        $view      = $attributes['view'];

        if ($view == Browse::VIEW_FEATURE) {
            return $this->findFeature(6);
        }

        if ($view == Browse::VIEW_SPONSOR) {
            return $this->findSponsor();
        }

        if ($context->entityId() && $profileId == $context->entityId() && $view != Browse::VIEW_PENDING) {
            $attributes['view'] = $view = Browse::VIEW_MY;
        }

        if (Browse::VIEW_PENDING == $view) {
            if (Arr::get($attributes, 'user_id') == 0 || Arr::get($attributes, 'user_id') != $context->entityId()) {
                if ($context->isGuest() || !$context->hasPermissionTo('video.approve')) {
                    throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
                }
            }
        }

        $categoryId = Arr::get($attributes, 'category_id', 0);

        if ($categoryId > 0) {
            $category = resolve(CategoryRepositoryInterface::class)->find($categoryId);

            policy_authorize(CategoryPolicy::class, 'viewActive', $context, $category);
        }
        $query     = $this->buildQueryViewVideos($context, $owner, $attributes);
        $relations = ['videoText', 'user', 'userEntity', 'activeCategories'];

        /** @var \Illuminate\Pagination\Paginator $videoData */
        $videoData = $query
            ->with($relations)
            ->simplePaginate($limit, ['videos.*']);

        $attributes['current_page'] = $videoData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes)) {
            return $videoData;
        }

        $userId = $context->entityId();

        $cacheKey  = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);
        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_ITEM;

        return $this->transformPaginatorWithSponsor($videoData, $cacheKey, $cacheTime, 'id', $relations);
    }

    public function viewVideo(ContractUser $context, int $id): Model
    {
        $video = $this
            ->with(['videoText', 'user', 'userEntity', 'categories', 'activeCategories'])
            ->find($id);

        policy_authorize(VideoPolicy::class, 'view', $context, $video);

        $video->incrementTotalView();

        return $video->refresh();
    }

    /**
     * @inheritdoc
     */
    public function createVideo(ContractUser $context, ContractUser $owner, array $attributes): Model
    {
        policy_authorize(VideoPolicy::class, 'create', $context, $owner);

        app('events')->dispatch('video.pre_video_create', [$context, $attributes], true);

        if (Arr::has($attributes, 'text')) {
            $text = Arr::get($attributes, 'text');

            if (null === $text) {
                $text = MetaFoxConstant::EMPTY_STRING;
            }

            $attributes = array_merge($attributes, [
                'text' => $text,
            ]);
        }

        if (isset($attributes['content'])) {
            $attributes['content'] = $this->cleanContent($attributes['content']);
        }

        if (Arr::has($attributes, 'video_url')) {
            $thumbnail                   = $this->createThumbnailFromLink($context, Arr::get($attributes, 'thumbnail'));
            $attributes['image_file_id'] = $thumbnail instanceof StorageFile ? $thumbnail->entityId() : null;
        }

        $videoTempFile = null;
        $tempFile      = Arr::get($attributes, 'temp_file', 0);
        $jobExtra      = [];

        if ($tempFile > 0) {
            $attributes['in_process']    = Model::VIDEO_IN_PROCESS;
            $attributes['image_file_id'] = null;

            $videoTempFile = upload()->getFile($tempFile);
        }

        $thumbTempFile = Arr::get($attributes, 'thumb_temp_file', 0);
        if ($thumbTempFile > 0) {
            $thumbnailTemp                   = upload()->getFile($thumbTempFile);
            $attributes['thumbnail_file_id'] = $thumbnailTemp->entityId();

            // Delete temp file after done
            upload()->rollUp($thumbTempFile);
        }

        $attributes['title'] = $this->cleanTitle($attributes['title']);

        $attributes = array_merge($attributes, [
            'user_id'     => $context->entityId(),
            'user_type'   => $context->entityType(),
            'owner_id'    => $owner->entityId(),
            'owner_type'  => $owner->entityType(),
            'is_approved' => (int) $context->hasPermissionTo('video.auto_approved'),
        ]);

        if ($owner->hasPendingMode()) {
            $attributes['is_approved'] = 1;
        }

        /** @var Model $model */
        $model = $this->getModel()->newModelInstance();
        $model->fill($attributes);

        Arr::set($jobExtra, 'privacy', Arr::get($attributes, 'privacy'));
        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $model->setPrivacyListAttribute($attributes['list']);
            Arr::set($jobExtra, 'privacy_list', Arr::get($attributes, 'list'));
        }

        $model->save();

        if (null !== $videoTempFile) {
            VideoProcessingJob::dispatch($videoTempFile, $model->entityId(), $jobExtra);
        }

        $model->refresh();

        return $model;
    }

    /**
     * @param  ContractUser           $context
     * @param  int                    $id
     * @param  array<string, mixed>   $attributes
     * @return Model
     * @throws AuthorizationException
     */
    public function updateVideo(ContractUser $context, int $id, array $attributes): Model
    {
        $removeThumbnail = Arr::get($attributes, 'remove_thumbnail', 0);
        $thumbTempFile   = Arr::get($attributes, 'thumb_temp_file', 0);

        $video = $this
            ->with(['group'])
            ->find($id);

        policy_authorize(VideoPolicy::class, 'update', $context, $video);

        if (isset($attributes['title'])) {
            $attributes['title'] = $this->cleanTitle($attributes['title']);
        }

        $attributes = $this->handleContent($attributes, 'text');

        $groupAttributes = null;

        if (null !== $video->group) {
            $groupAttributes = $attributes;

            $this->prepareDataForGroupUpdate($video->group, $video, $attributes, $groupAttributes);
        }

        if ($removeThumbnail) {
            $oldFile = $video->thumbnail_file_id;
            app('storage')->deleteFile($oldFile, null);
            $attributes['thumbnail_file_id'] = null;
        }

        if ($thumbTempFile > 0) {
            $tempFile = upload()->getFile($thumbTempFile);

            $attributes['thumbnail_file_id'] = $tempFile->id;

            // Delete temp file after done
            upload()->rollUp($thumbTempFile);
        }

        $video->fill($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $video->setPrivacyListAttribute($attributes['list']);
        }

        $video->save();

        $video->refresh();

        //Update photo group
        if (is_array($groupAttributes)) {
            app('events')->dispatch('photo.update_photo_group', [$context, $video->group, $groupAttributes], true);
        }

        $this->updateExtra($video, $attributes);

        return $video;
    }

    /**
     * @inerhitDoc
     * @throws AuthorizationException
     */
    public function deleteVideo(ContractUser $context, int $id): bool
    {
        $resource = $this
            ->with(['group'])
            ->find($id);

        policy_authorize(VideoPolicy::class, 'delete', $context, $resource);

        if (!$resource->delete()) {
            return false;
        }

        if ($resource->group instanceof Content) {
            app('events')->dispatch('photo.group.update_search_for_first_media', [
                $resource->group,
                $resource->group->content,
                $resource->group->total_item > 1 ? $resource->group->total_item - 1 : 0,
            ], true);
        }

        return true;
    }

    /**
     * @param ContractUser         $context
     * @param ContractUser         $owner
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     */
    private function buildQueryViewVideos(ContractUser $context, ContractUser $owner, array $attributes): Builder
    {
        $sort       = $attributes['sort'];
        $sortType   = $attributes['sort_type'];
        $when       = $attributes['when'];
        $view       = $attributes['view'];
        $search     = $attributes['q'];
        $categoryId = $attributes['category_id'];
        $profileId  = $attributes['user_id'];

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($context->entityId())
            ->setModerationPermissionName('video.moderate');

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view)->setProfileId($profileId);

        $query = $this->getModel()->newQuery();

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['title']));
        }

        if ($owner->entityId() != $context->entityId()) {
            $privacyScope->setOwnerId($owner->entityId());
            $viewScope->setIsViewOwner(true);

            if (!policy_check(VideoPolicy::class, 'approve', $context, resolve(Model::class))) {
                $query->where('videos.is_approved', '=', 1);
            }
        }

        if ($categoryId > 0) {
            if (!is_array($categoryId)) {
                $categoryId = [$categoryId];
            }

            $categoryScope = new CategoryScope();
            $categoryScope->setCategories($categoryId);
            $query = $query->addScope($categoryScope);
        }

        $query = $this->applyDisplayVideoSetting($query, $owner, $view);

        return $query
            ->addScope($privacyScope)
            ->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($viewScope);
    }

    public function findFeature(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', HasFeature::IS_FEATURED)
            ->where('is_approved', '=', 1)
            ->orderByDesc(HasFeature::FEATURED_AT_COLUMN)
            ->simplePaginate($limit);
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', \MetaFox\Platform\Contracts\HasSponsor::IS_SPONSOR)
            ->where('is_approved', '=', 1)
            ->simplePaginate($limit);
    }

    /**
     * @param  Builder                 $query
     * @param  ContractUser            $owner
     * @param  string                  $view
     * @return Builder
     * @throws AuthenticationException
     */
    private function applyDisplayVideoSetting(Builder $query, ContractUser $owner, string $view): Builder
    {
        $context = user();

        if ($owner instanceof HasPrivacyMember) {
            return $query;
        }

        $condition    = [];
        $checkViewAll = in_array($view, [Browse::VIEW_ALL, Browse::VIEW_ALL_DEFAULT, Browse::VIEW_LATEST]);

        if ($checkViewAll) {
            $condition[] = ['videos.owner_type', $context->entityType()];
        }

        $query->where($condition);

        return $query;
    }

    /**
     * @param  string $assetId
     * @return bool
     */
    public function deleteVideoByAssetId(string $assetId): bool
    {
        $video = $this->getModel()->newQuery()
            ->where('asset_id', $assetId)
            ->first();

        if (!$video instanceof Model) {
            return true;
        }

        return (bool) $this->delete($video->entityId());
    }

    /**
     * @param  int                  $videoId
     * @param  array<string, mixed> $attributes
     * @return bool
     * @throws Exception
     */
    public function doneProcessVideo(int $videoId, array $attributes): bool
    {
        $video = $this->with(['user', 'owner'])->find($videoId);

        if (!$video instanceof Model) {
            return false;
        }

        // If video is done processing, no longer need this action
        if ($video->in_process == 0) {
            return true;
        }

        $video->fill($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $video->setPrivacyListAttribute($attributes['privacy_list']);
        }

        $video->save();

        app('events')->dispatch('activity.feed.create_from_resource', [$video], true);

        if (Arr::has($attributes, 'searchable_text')) {
            $this->updateGlobalSearch($video, Arr::get($attributes, 'searchable_text'));
        }

        if ($video->group_id > 0) {
            return $this->doneProcessingVideosInGroup($video->group_id);
        }

        // Notify creator that their video is ready
        Notification::send($video->user, new VideoDoneProcessingNotification($video));

        return true;
    }

    /**
     * @param  ContractUser           $context
     * @param  ContractUser           $owner
     * @param  TempFileModel          $tempFile
     * @param  array<string, mixed>   $params
     * @return Model
     * @throws AuthorizationException
     */
    public function tempFileToVideo(
        ContractUser $context,
        ContractUser $owner,
        TempFileModel $tempFile,
        array $params = []
    ): Model {
        policy_authorize(VideoPolicy::class, 'create', $context, $owner);

        $isApproved = $context->hasPermissionTo('video.auto_approved');

        if ($owner->hasPendingMode()) {
            $isApproved = !$owner->isPendingMode();
        }

        $content = null;

        $extra = [];

        if (Arr::has($params, 'text')) {
            $text = Arr::get($params, 'text');

            if (is_string($content) && MetaFoxConstant::EMPTY_STRING != $text) {
                $content = $text;
            }

            unset($params['text']);
        }

        $params = array_merge($params, [
            'user_id'     => $context->entityId(),
            'user_type'   => $context->entityType(),
            'owner_id'    => $owner->entityId(),
            'owner_type'  => $owner->entityType(),
            'title'       => __p(Model::VIDEO_DEFAULT_TITLE_PHRASE),
            'text'        => '',
            'is_approved' => (int) $isApproved,
            'in_process'  => Model::VIDEO_IN_PROCESS,
            'image_path'  => null,
            'content'     => $content,
        ]);

        if (!array_key_exists('categories', $params)) {
            $params['categories'] = [Settings::get('video.default_category')];
        }

        /** @var Model $model */
        $model = $this->getModel()->newModelInstance();

        $model->fill($params);

        Arr::set($extra, 'privacy', Arr::get($params, 'privacy'));
        if (MetaFoxPrivacy::CUSTOM == $params['privacy']) {
            $model->setPrivacyListAttribute($params['list']);
            Arr::set($extra, 'privacy_list', Arr::get($params, 'list'));
        }

        $model->save();

        $model->refresh();

        if (Arr::has($params, 'searchable_text')) {
            Arr::set($extra, 'searchable_text', Arr::get($params, 'searchable_text'));
        }

        VideoProcessingJob::dispatch($tempFile, $model->entityId(), $extra);

        return $model;
    }

    /**
     * @param  ContractUser           $context
     * @param  ContractUser           $owner
     * @param  Model                  $video
     * @param  TempFileModel          $tempFile
     * @param  array<string, mixed>   $params
     * @return Model
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function tempFileToExistVideo(
        ContractUser $context,
        ContractUser $owner,
        Model $video,
        TempFileModel $tempFile,
        array $params = []
    ): Model {
        policy_authorize(PhotoPolicy::class, 'update', $context, $video);
        $jobExtra = [];
        $params   = array_merge($params, [
            'title'       => $tempFile->file_name,
            'text'        => '',
            'is_approved' => (int) $context->hasPermissionTo('video.auto_approved'),
            'image_path'  => Settings::get('video.video_in_processing_image'),
            'in_process'  => Model::VIDEO_IN_PROCESS,
            'group_id'    => 0,
        ]);

        if ($owner->hasPendingMode()) {
            $params['is_approved'] = 1;
        }

        $video->fill($params);

        Arr::set($jobExtra, 'privacy', Arr::get($params, 'privacy'));
        if ($params['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $video->setPrivacyListAttribute($params['list']);
            Arr::set($jobExtra, 'privacy_list', Arr::get($params, 'list'));
        }

        $video->save();
        $video->refresh();

        VideoProcessingJob::dispatch($tempFile, $video->entityId(), $jobExtra);

        return $video;
    }

    public function getVideosByGroupId(int $groupId): ?Collection
    {
        return $this->getModel()->newModelInstance()
            ->where([
                'group_id' => $groupId,
            ])
            ->get();
    }

    /**
     * @param  int  $groupId
     * @return bool
     */
    public function doneProcessingVideosInGroup(int $groupId): bool
    {
        $inProcessVideos = $this->getModel()
            ->newModelQuery()
            ->where('group_id', '=', $groupId)
            ->where('in_process', '=', 1)
            ->count();

        if ($inProcessVideos > 0) {
            return false;
        }

        app('events')->dispatch('photo.done_processing_photo_group_items', [$groupId]);

        return true;
    }

    protected function updateGlobalSearch(Model $video, ?string $text): ?bool
    {
        if (!$video instanceof HasGlobalSearch) {
            return false;
        }

        $searchable = $video->toSearchable();

        if (null === $searchable) {
            return false;
        }

        $searchable = array_merge($searchable, [
            'text' => $text,
        ]);

        return app('events')->dispatch(
            'search.update_search_text',
            [$video->entityType(), $video->entityId(), $searchable],
            true
        );
    }

    public function updatePatchVideo(int $id, array $attributes): bool
    {
        $video = $this
            ->with(['group'])
            ->find($id);

        $attributes = $this->handleContent($attributes, 'text');

        if (Arr::has($attributes, 'text')) {
            Arr::set($attributes, 'content', Arr::get($attributes, 'text'));
            unset($attributes['text']);
        }

        if (null !== $video->group) {
            $this->prepareDataForGroupUpdate($video->group, $video, $attributes);
        }

        $video->fill($attributes);

        $video->save();

        $this->updateExtra($video, $attributes);

        return true;
    }

    protected function handleContent(array $attributes, string $field = 'content'): array
    {
        if (Arr::has($attributes, $field)) {
            $content = Arr::get($attributes, $field);

            if (null === $content) {
                Arr::set($attributes, $field, MetaFoxConstant::EMPTY_STRING);
            }
        }

        return $attributes;
    }

    protected function prepareDataForGroupUpdate(
        Content $group,
        Model $video,
        array &$attributes,
        ?array &$groupAttributes = null
    ): void {
        if ($group->total_item != 1) {
            return;
        }

        if (null !== $video->content) {
            return;
        }

        if (Arr::has($attributes, 'searchable_text')) {
            return;
        }

        //Update from Feed and we need to re-index searchable text for video to be sync with feed when searching global
        if (null === $groupAttributes) {
            Arr::set($attributes, 'searchable_text', $group->content);

            return;
        }

        $text = Arr::get($attributes, 'text');

        unset($attributes['text']);

        Arr::set($groupAttributes, 'content', $text);

        Arr::set($attributes, 'searchable_text', $text);
    }

    protected function updateExtra(Model $video, array $attributes)
    {
        // In case must not update content/text of photo but need to searching this photo like feed
        if (Arr::has($attributes, 'searchable_text')) {
            if (null === $video->getFeedContent()) {
                $this->updateGlobalSearch($video, Arr::get($attributes, 'searchable_text'));
            }
        }
    }

    protected function createThumbnailFromLink(ContractUser $user, ?string $url): ?StorageFile
    {
        $response = Http::get($url);
        if (!$response->ok()) {
            return null;
        }
        $tempFile = sprintf('%s.%s', tempnam(sys_get_temp_dir(), 'metafox'), File::extension($url) ?? 'jpg');
        file_put_contents($tempFile, $response->body());

        $uploadedFile = UploadFile::pathToUploadedFile($tempFile);

        if (!$uploadedFile instanceof UploadedFile) {
            return null;
        }

        return upload()
            ->setStorage('photo')
            ->setPath('video')
            ->setThumbSizes(['500'])
            ->setItemType('photo')
            ->setUser($user)
            ->storeFile($uploadedFile);
    }
}
