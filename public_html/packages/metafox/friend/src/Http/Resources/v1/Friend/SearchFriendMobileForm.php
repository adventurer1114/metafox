<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use MetaFox\Friend\Models\Friend as Model;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @driverName friend.search
 * @driverType form
 * @preload    1
 */
class SearchFriendMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/friend/search')
            ->acceptPageParams(['q', 'sort', 'when', 'category_id', 'returnUrl'])
            ->setValue([
                'view' => Browse::VIEW_SEARCH,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);

        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->placeholder(__p('friend::phrase.search_friends'))
                ->className('mb2'),
        );
    }
}
