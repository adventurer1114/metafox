<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Support\Facades\Group;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;

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
        $groupIds = Group::getMentions($content);

        if (count($groupIds)) {
            $groups = Group::getGroupsForMention($groupIds);

            $content = preg_replace_callback('/\[group=(\d+)\](.+?)\[\/group\]/u', function ($group) use ($groups) {
                [, $groupId, $oldGroupName] = $group;

                $filtered = $groups->get($groupId);

                if (!$filtered instanceof Content) {
                    return "<b>$oldGroupName</b>";
                }

                $userType = $filtered->entityType();
                $link     = $filtered->toLink();

                return "<a href='{$link}' target='_blank' id='{$groupId}' type='$userType'>{$filtered->toTitle()}</a>";
            }, $content);
        }
    }
}
