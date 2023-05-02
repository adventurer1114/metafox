<?php

namespace MetaFox\Page\Listeners;

use MetaFox\Page\Support\Facade\Page;
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
        $pageIds = Page::getMentions($content);

        if (!count($pageIds)) {
            return;
        }

        $pages = Page::getPagesForMention($pageIds);

        $content = preg_replace_callback('/\[page=(\d+)\](.+?)\[\/page\]/u', function ($page) use ($pages) {
            [, $pageId, $oldPageName] = $page;

            $filtered = $pages->get($pageId);

            if (!$filtered instanceof Content) {
                return "<b>$oldPageName</b>";
            }

            $userType = $filtered->entityType();
            $link     = $filtered->toLink();

            return "<a href='{$link}' target='_blank' id='{$pageId}' type='$userType'>{$filtered->toTitle()}</a>";
        }, $content);
    }
}
