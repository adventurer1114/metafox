<?php

namespace MetaFox\Event\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use MetaFox\Event\Contracts\EventContract;
use MetaFox\Event\Models\Event as Model;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Support\Facades\UserPrivacy;

class Event implements EventContract
{
    public function getPrivacyList(): array
    {
        return [
            [
                'privacy_type'    => Model::EVENT_OWNER,
                'privacy'         => MetaFoxPrivacy::ONLY_ME,
                'privacy_icon'    => 'ico-calendar-check',
                'privacy_tooltip' => [
                    'var_name' => 'event::phrase.privacy_tooltip',
                ],
            ],
            [
                'privacy_type'    => Model::EVENT_HOSTS,
                'privacy'         => MetaFoxPrivacy::CUSTOM,
                'privacy_icon'    => 'ico-calendar-check',
                'privacy_tooltip' => [
                    'var_name' => 'event::phrase.privacy_tooltip',
                ],
            ],
            [
                'privacy_type'    => Model::EVENT_MEMBERS,
                'privacy'         => MetaFoxPrivacy::FRIENDS,
                'privacy_icon'    => 'ico-calendar-check',
                'privacy_tooltip' => [
                    'var_name' => 'event::phrase.privacy_tooltip',
                ],
            ],
        ];
    }

    public function checkFeedReactingPermission(User $user, User $owner): ?bool
    {
        if (!$owner instanceof Model) {
            return null;
        }

        return UserPrivacy::hasAccess($user, $owner, 'feed.view_wall');
    }

    public function checkPermissionMassEmail(User $user, int $eventId): bool
    {
        $now             = Carbon::now();
        $latestMassEmail = resolve(EventRepositoryInterface::class)->getLatestMassEmailByUser($user, $eventId);

        if ($latestMassEmail == null) {
            return false;
        }

        return $latestMassEmail > $now;
    }

    public function createLocationWithName(string $locationName): ?array
    {
        $apiKey = env('MFOX_GOOGLE_MAP_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        $location        = null;
        $locationNameUrl = rawurlencode($locationName);

        try {
            $key      = env('MFOX_GOOGLE_MAP_API_KEY');
            $url      = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$locationNameUrl&key=$key";
            $response = Http::get($url);

            $results  = $response->object()->results ?? null;
            $location = empty($results) ? null : (array) $results[0]->geometry->location;
        } catch (Exception $e) {
        }

        if (null == $location) {
            return null;
        }

        return [
            'location_name'      => $locationName,
            'location_latitude'  => $location['lat'],
            'location_longitude' => $location['lng'],
        ];
    }
}
