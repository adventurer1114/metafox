<?php

namespace MetaFox\Page\Listeners;

use MetaFox\Page\Models\Page;
use MetaFox\Platform\Contracts\User;

class GetPrivacyDetailOnOwnerListener
{
    public function handle(?User $context, User $owner): ?array
    {
        if (!$owner instanceof Page) {
            return null;
        }

        $privacyDetail = app('events')->dispatch(
            'activity.get_privacy_detail',
            [$context, $owner, $owner->privacy, true],
            true
        );

        $privacyDetail['label'] = $owner->name;

        return $privacyDetail;
    }
}
