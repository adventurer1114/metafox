<?php

namespace MetaFox\Activity\Traits;

use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

trait HasTaggedFriendTrait
{
    public function isEnableTagFriends(): bool
    {
        return app_active('metafox/friend') && Settings::get('activity.feed.enable_tag_friends', false) === true;
    }

    /**
     * @param array<string, mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function applyTaggedFriendsRules(array $rules): array
    {
        if ($this->isEnableTagFriends()) {
            $rules['tagged_friends'] = ['sometimes', 'array'];
            $rules['tagged_friends.*'] = ['numeric', 'exists:user_entities,id'];

            $rules['tagged_in_photo'] = ['sometimes', 'array'];
            $rules['tagged_in_photo.*'] = ['array'];
            $rules['tagged_in_photo.*.friend_id'] = ['numeric', 'exists:user_entities,id'];
            $rules['tagged_in_photo.*.px'] = ['numeric'];
            $rules['tagged_in_photo.*.py'] = ['numeric'];
        }

        return $rules;
    }

    /**
     * @param array<string, mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function applyTaggedFriendsRulesForEdit(array $rules): array
    {
        if ($this->isEnableTagFriends()) {
            $rules['tagged_friends'] = ['sometimes', 'array'];
            $rules['tagged_friends.*'] = ['nullable', new ExistIfGreaterThanZero('exists:user_entities,id')];

            $rules['tagged_in_photo'] = ['sometimes', 'array'];
            $rules['tagged_in_photo.*'] = ['array'];
            $rules['tagged_in_photo.*.friend_id'] = ['numeric', 'exists:user_entities,id'];
            $rules['tagged_in_photo.*.px'] = ['numeric'];
            $rules['tagged_in_photo.*.py'] = ['numeric'];
        }

        return $rules;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string|int, mixed>
     *                           [
     *                           1 => 1,
     *                           2 => [
     *                           'friend_id' => 2,
     *                           'px' => 100,
     *                           'py' => 200,
     *                           ],
     *                           3 => [
     *                           'friend_id' => 2,
     *                           'is_mention' => 1,
     *                           'content' => 'user test ahihi',
     *                           ],
     *                           ]
     */
    public function handleTaggedFriend(array $data): array
    {
        $result = [];

        if (array_key_exists('tagged_friends', $data)) {
            foreach ($data['tagged_friends'] as $tagUserId) {
                $result[$tagUserId] = [
                    'friend_id' => $tagUserId,
                ];
            }
        }

        if (array_key_exists('content', $data)) {
            $mentions = app('events')->dispatch('user.get_mentions', [$data['content']], true);
            if (!empty($mentions)) {
                foreach ($mentions as $mentionId) {
                    $result[$mentionId] = [
                        'friend_id'  => $mentionId,
                        'is_mention' => 1,
                        'content'    => $data['content'], // Support for notification.
                    ];
                }
            }
        }

        // Tagged in photo is high priority, override normal tag + mentions.
        if (array_key_exists('tagged_in_photo', $data)) {
            foreach ($data['tagged_in_photo'] as $tagData) {
                $result[$tagData['friend_id']] = $tagData;
            }
        }

        return $result;
    }
}
