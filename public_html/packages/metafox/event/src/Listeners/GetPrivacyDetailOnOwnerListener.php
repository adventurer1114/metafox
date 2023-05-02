<?php

namespace MetaFox\Event\Listeners;

use MetaFox\Event\Models\Event;
use MetaFox\Platform\Contracts\User;

class GetPrivacyDetailOnOwnerListener
{
    public function handle(User $context, User $owner): ?array
    {
        if (!$owner instanceof Event) {
            return null;
        }

        $privacyDetail = app('events')->dispatch(
            'activity.get_privacy_detail',
            [$context, $owner, $owner->privacy],
            true
        );

        $privacyDetail['label'] = $privacyDetail['tooltip'];

        return $privacyDetail;
    }
}
