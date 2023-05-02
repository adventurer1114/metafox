<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Friend\Models\Friend as Model;
use MetaFox\Friend\Support\Browse\Scopes\Friend\SortScope;
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
            ->acceptPageParams(['q', 'sort', 'when', 'returnUrl'])
            ->setValue([
                'view' => Browse::VIEW_SEARCH,
                'q'    => '',
            ]);
    }

    protected function initialize(): void
    {
        $rules = ['truthy', 'filters'];
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);

        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->placeholder(__p('friend::phrase.search_friends'))
                ->className('mb2'),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->autoSubmit()
                ->forBottomSheetForm()
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->autoSubmit()
                ->forBottomSheetForm()
                ->options($this->getWhenOptions()),
        );
        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);
        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['sort', 'when', 'category_id']),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->showWhen($rules)
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->showWhen($rules)
                ->options($this->getWhenOptions()),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getSortOptions(): array
    {
        return [
            [
                'label' => __p('core::phrase.sort.recent'),
                'value' => Browse::SORT_RECENT,
            ], [
                'label' => __p('friend::phrase.by_full_name'),
                'value' => SortScope::SORT_FULL_NAME,
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function getWhenOptions(): array
    {
        return [
            [
                'label' => __p('core::phrase.when.all'),
                'value' => Browse::WHEN_ALL,
            ], [
                'label' => __p('core::phrase.when.this_month'),
                'value' => Browse::WHEN_THIS_MONTH,
            ], [
                'label' => __p('core::phrase.when.this_week'),
                'value' => Browse::WHEN_THIS_WEEK,
            ], [
                'label' => __p('core::phrase.when.today'),
                'value' => Browse::WHEN_TODAY,
            ],
        ];
    }
}
