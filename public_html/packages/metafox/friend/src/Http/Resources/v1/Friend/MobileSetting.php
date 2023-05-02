<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use MetaFox\Friend\Support\Browse\Scopes\Friend\SortScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\WhenScope;
use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * Friend Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->apiUrl('friend')
            ->apiParams([
                'q'    => ':q',
                'sort' => ':sort',
                'when' => ':when',
            ])
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes', 'sort', SortScope::getAllowSort(),
                ],
                'when' => ['includes', 'when', WhenScope::getAllowWhen()],
            ])
            ->placeholder(__p('friend::phrase.search_friends'));

        $this->add('viewAll')
            ->apiUrl('friend')
            ->pageUrl('friend')
            ->apiRules([
                'q'       => ['truthy', 'q'],
                'list_id' => ['truthy', 'list_id'],
                'sort'    => [
                    'includes', 'sort', SortScope::getAllowSort(),
                ],
                'when' => ['includes', 'when', WhenScope::getAllowWhen()],
            ]);

        $this->add('suggestItem')
            ->apiUrl('friend/suggestion');

        $this->add('getTagSuggestion')
            ->apiUrl('friend/tag-suggestion')
            ->asGet()
            ->apiParams(['q' => ':q', 'item_id' => ':item_id', 'item_type' => ':item_type']);

        $this->add('getForMention')
            ->apiUrl('friend/mention')
            ->asGet()
            ->apiParams([
                'q'        => ':q',
                'owner_id' => ':owner_id',
            ]);

        $this->add('getForMentionFriends')
            ->apiUrl('friend/mention')
            ->asGet()
            ->apiParams([
                'q'    => ':q',
                'view' => 'friend',
            ]);

        $this->add('viewFriendsByList')
            ->apiUrl('friend')
            ->apiParams(['list_id' => ':list_id']);

        $this->add('shareOnFriendProfile');
    }
}
