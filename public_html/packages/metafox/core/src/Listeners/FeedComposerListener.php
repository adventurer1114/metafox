<?php

namespace MetaFox\Core\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Core\Models\Link;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;

class FeedComposerListener
{
    /**
     * @param  User             $user
     * @param  User             $owner
     * @param  string           $postType
     * @param  array            $params
     * @return array|int[]|null
     */
    public function handle(User $user, User $owner, string $postType, array $params): ?array
    {
        if ($postType != Link::FEED_POST_TYPE) {
            return null;
        }

        if (false === app('events')->dispatch('activity.has_feature', [Link::ENTITY_TYPE, 'can_create_feed'], true)) {
            return [
                'error_message' => __('validation.no_permission'),
            ];
        }

        $content = Arr::get($params, 'content', '');

        unset($params['content']);

        $location = [];

        if (Arr::has($params, 'location_latitude')) {
            $location = [
                'location_latitude'  => $params['location_latitude'],
                'location_longitude' => $params['location_longitude'],
                'location_name'      => $params['location_name'],
            ];
        }

        $link = new Link();

        $link->fill(array_merge([
            'user_id'      => $user->entityId(),
            'user_type'    => $user->entityType(),
            'owner_id'     => $owner->entityId(),
            'owner_type'   => $owner->entityType(),
            'privacy'      => $params['privacy'],
            'feed_content' => $content,
            'title'        => $params['link_title'],
            'link'         => $params['link_url'],
            'host'         => $params['link_url'] ? parse_url($params['link_url'], PHP_URL_HOST) : null,
            'image'        => $params['link_image'],
            'description'  => $params['link_description'],
            'has_embed'    => 0,
        ], $location));

        if ($link->privacy == MetaFoxPrivacy::CUSTOM) {
            $link->setPrivacyListAttribute($params['list']);
        }

        $link->save();

        $link->load('activity_feed');

        return [
            'id' => $link->activity_feed ? $link->activity_feed->entityId() : 0,
        ];
    }
}
