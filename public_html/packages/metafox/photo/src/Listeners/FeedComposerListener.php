<?php

namespace MetaFox\Photo\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Platform\Contracts\HasTimelineAlbum;
use MetaFox\Platform\Contracts\Media;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;

class FeedComposerListener
{
    private PhotoRepositoryInterface $repository;
    private PhotoGroupRepositoryInterface $photoGroupRepository;

    /**
     * @param PhotoRepositoryInterface      $repository
     * @param PhotoGroupRepositoryInterface $photoGroupRepository
     */
    public function __construct(
        PhotoRepositoryInterface $repository,
        PhotoGroupRepositoryInterface $photoGroupRepository
    ) {
        $this->repository           = $repository;
        $this->photoGroupRepository = $photoGroupRepository;
    }

    /**
     * Photo set feed content won't apply to every single photo. DO NOT assign content to per photo.
     *
     * @param  User                 $user
     * @param  User                 $owner
     * @param  string               $postType
     * @param  array<string, mixed> $params
     * @return array|null
     */
    public function handle(User $user, User $owner, string $postType, array $params): ?array
    {
        if ($postType != PhotoGroup::FEED_POST_TYPE) {
            return null;
        }

        if (false === app('events')->dispatch(
            'activity.has_feature',
            [PhotoGroup::ENTITY_TYPE, 'can_create_feed'],
            true
        )) {
            return [
                'error_message' => __('validation.no_permission'),
            ];
        }

        $files = Arr::get($params, 'photo_files.new');

        if (!is_array($files) || !count($files)) {
            return [
                'error_message' => __('validation.invalid'),
            ];
        }

        Arr::set($params, 'files', $files);

        unset($params['photo_files']);

        $feedId = $this->handleComposer($user, $owner, $params);

        return match ($feedId) {
            0       => ['error_message' => __('validation.no_permission')],
            -1      => ['is_processing' => true, 'message' => __p('activity::phrase.post_in_process_message')],
            default => ['id' => $feedId]
        };
    }

    protected function handleComposer(User $user, User $owner, array $params): int
    {
        if ($owner instanceof HasTimelineAlbum) {
            $params['album_id'] = $this->repository->getAlbum($user, $owner, Album::TIMELINE_ALBUM)->entityId();
        }

        $groupParams = array_merge($params, [
            'content'     => Arr::get($params, 'content', ''),
            'user_id'     => $user->entityId(),
            'user_type'   => $user->entityType(),
            'owner_id'    => $owner->entityId(),
            'owner_type'  => $owner->entityType(),
            'is_approved' => 0,
        ]);

        $group = new PhotoGroup();

        $group->fill($groupParams);

        if ($group->privacy == MetaFoxPrivacy::CUSTOM) {
            $group->setPrivacyListAttribute($params['list']);
        }

        $group->save();

        $group->refresh();

        $params['group_id'] = $group->entityId();

        $group->loadMissing('activity_feed');

        $content = Arr::get($params, 'content');

        unset($params['content']);

        $uploaded = $this->uploadMedias($user, $owner, $params, $content);

        if (!count($uploaded)) {
            return 0;
        }

        // Update photo group status after all of its items are
        $this->photoGroupRepository->updateApprovedStatus($group->entityId());

        // Create feed after all items are created
        app('events')->dispatch('activity.feed.create_from_resource', [$group], true);

        $group->refresh();
        if (!$group->activity_feed) {
            return -1;
        }

        app('events')->dispatch(
            'activity.notify.approved_new_post_in_owner',
            [$group->activity_feed, $group->activity_feed->owner],
            true
        );

        return $group->activity_feed->entityId();
    }

    /**
     * @param  User        $user
     * @param  User        $owner
     * @param  array       $params
     * @param  string|null $groupContentgroup
     * @return array
     */
    protected function uploadMedias(User $user, User $owner, array $params, ?string $groupContent): array
    {
        $medias = [];

        $files = Arr::get($params, 'files', []);

        if (!$this->canUploadMedia($files)) {
            return $medias;
        }

        if (MetaFoxConstant::EMPTY_STRING == $groupContent) {
            $groupContent = null;
        }

        if (null !== $groupContent) {
            $files = $this->photoGroupRepository->forceContentForGlobalSearch($files, $groupContent);
        }

        foreach ($files as $file) {
            $tempFile = upload()->getFile($file['id']);

            $update = $params;

            if (Arr::has($file, 'text')) {
                $text = Arr::get($file, 'text', '');
                Arr::set($update, 'text', $text);
                Arr::set($update, 'content', $text);
            }

            if (Arr::has($file, 'searchable_text')) {
                Arr::set($update, 'searchable_text', Arr::get($file, 'searchable_text'));
            }

            if (Arr::has($file, 'tagged_friends')) {
                Arr::set($update, 'tagged_friends', Arr::get($file, 'tagged_friends'));
            }

            /** @var Media|null $content */
            $content = app('events')->dispatch(
                'photo.media_upload',
                [$user, $owner, $tempFile->item_type, $tempFile, $update],
                true
            );

            if (!$content instanceof Media) {
                return [];
            }

            $medias[] = $content;
        }

        if (count($medias) == 0) {
            return [];
        }

        return $medias;
    }

    protected function canUploadMedia(array $files): bool
    {
        if (Settings::get('photo.allow_uploading_with_video', true)) {
            return true;
        }

        $types = array_unique(Arr::pluck($files, 'type'));

        if (in_array('photo', $types) && in_array('video', $types)) {
            return false;
        }

        return true;
    }
}
