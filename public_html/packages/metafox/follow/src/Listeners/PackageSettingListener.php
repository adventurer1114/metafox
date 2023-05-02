<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Follow\Listeners;

use MetaFox\Follow\Models\Follow;
use MetaFox\Follow\Policies\FollowPolicy;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Listeners/PackageSettingListener.stub.
 */

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getPolicies(): array
    {
        return [
            Follow::class => FollowPolicy::class,
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [];
    }

    public function getEvents(): array
    {
        return [
            'follow.is_follow' => [
                IsFollowListener::class,
            ],
            'follow.can_follow' => [
                CanFollowListener::class,
            ],
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'user.blocked' => [
                UserBlockedListener::class,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'follow.add_follow' => [
                'phrase' => 'follow::phrase.user_privacy.who_can_follow_me',
            ],
            'follow.view_following' => [
                'phrase' => 'follow::phrase.user_privacy.who_can_view_your_following',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'user' => [
                'follow.add_follow' => [
                    'default' => MetaFoxPrivacy::EVERYONE,
                    'list'    => [
                        MetaFoxPrivacy::EVERYONE,
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
                'follow.view_following' => [
                    'default' => MetaFoxPrivacy::MEMBERS,
                    'list'    => [
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS_OF_FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
            ],
        ];
    }
}
