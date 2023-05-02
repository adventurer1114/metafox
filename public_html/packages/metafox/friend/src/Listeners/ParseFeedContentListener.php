<?php

namespace MetaFox\Friend\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;

/**
 * Class ParseFeedContentListener.
 * @ignore
 * @codeCoverageIgnore
 */
class ParseFeedContentListener
{
    /**
     * @param Entity $item
     * @param string $content
     *
     * @return void
     */
    public function handle(Entity $item, string &$content): void
    {
        $userEntities = $this->getAllTaggedFriends($item);

        $content = preg_replace_callback('/\[user=(\d+)\](.+?)\[\/user\]/u', function ($groups) use ($userEntities) {
            [, $userId, $oldUserName] = $groups;

            if (!array_key_exists($userId, $userEntities)) {
                return "<b>$oldUserName</b>";
            }

            $user     = $userEntities[$userId];
            $userType = Arr::get($user, 'entity_type', 'user');
            $fullName = Arr::get($user, 'name');
            $userName = Arr::get($user, 'user_name');
            $url      = $userName ? url_utility()->makeApiUrl($userName) : '';

            if ($this->canTagged($userId)) {
                return "<a href='$url' target='_blank' id='$userId' type='$userType'>$fullName</a>";
            }

            return "<b>{$fullName}</b>";
        }, $content);
    }

    /**
     * @param Entity $item
     *
     * @return array<int, mixed>
     */
    private function getAllTaggedFriends(Entity $item): array
    {
        $userEntities = resolve(TagFriendRepositoryInterface::class)->getAllTaggedFriends($item);

        return $userEntities->keyBy('id')->toArray();
    }

    protected function canTagged(int $userId): bool
    {
        $canBeTaggedPrivacy = resolve(UserPrivacyRepositoryInterface::class)->getUserPrivacyByName($userId, 'user.can_i_be_tagged');

        if (null === $canBeTaggedPrivacy) {
            return true;
        }

        if ($canBeTaggedPrivacy->privacy == MetaFoxPrivacy::ONLY_ME) {
            return false;
        }

        return true;
    }
}
