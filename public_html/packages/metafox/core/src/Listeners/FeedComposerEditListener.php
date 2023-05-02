<?php

namespace MetaFox\Core\Listeners;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use MetaFox\Core\Models\Link;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;

class FeedComposerEditListener
{
    /**
     * @param User                 $user
     * @param User                 $owner
     * @param mixed                $link
     * @param array<string, mixed> $params
     *
     * @return bool|array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(User $user, User $owner, mixed $link, array $params): ?array
    {
        if ($link?->entityType() != Link::ENTITY_TYPE) {
            return null;
        }

        if (!$link instanceof Link) {
            throw new ModelNotFoundException();
        }

        $location = [];

        if (Arr::has($params, 'location_latitude')) {
            $location = [
                'location_latitude'  => $params['location_latitude'],
                'location_longitude' => $params['location_longitude'],
                'location_name'      => $params['location_name'],
            ];
        }

        $link->fill(array_merge([
            'privacy'      => $params['privacy'],
            'feed_content' => Arr::get($params, 'content', ''),
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

        return [
            'success' => true,
        ];
    }
}
