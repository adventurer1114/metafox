<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Models\Group;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\User;

class GetPrivacyDetailOnOwnerListener
{
    public function handle(?User $context, User $owner): ?array
    {
        if (!$owner instanceof Group) {
            return null;
        }

        $regName = __p(PrivacyTypeHandler::PRIVACY_PHRASE[$owner->privacy_type]);

        $privacyDetail = app('events')->dispatch(
            'activity.get_privacy_detail',
            [$context, $owner, $owner->privacy_item, true],
            true
        );

        $privacyDetail['label'] = $owner->name;

        if ($owner->isPublicPrivacy()) {
            $privacyDetail['label']   = $regName;
            $privacyDetail['tooltip'] = '';
        }

        return $privacyDetail;
    }
}
