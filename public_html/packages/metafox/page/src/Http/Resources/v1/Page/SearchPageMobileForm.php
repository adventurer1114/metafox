<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Platform\Support\Browse\Browse;

class SearchPageMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/page/search')
            ->acceptPageParams(['q', 'sort', 'from', 'related_comment_friend_only', 'category_id', 'returnUrl', 'when'])
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
                ->placeholder(__p('page::phrase.search_pages'))
                ->className('mb2'),
            Builder::button('filters')
                ->forBottomSheetForm(),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.when_label'))
                ->options($this->getWhenOptions()),
            Builder::autocomplete('category_id')
                ->forBottomSheetForm()
                ->useOptionContext()
                ->label(__p('core::phrase.categories'))
                ->searchEndpoint('/page/category')
                ->searchParams(['level' => 0]),
        );

        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);
        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['sort', 'when', 'category_id']),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->label(__p('core::phrase.sort_label'))
                ->showWhen(['truthy', 'filters'])
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->autoSubmit()
                ->forBottomSheetForm()
                ->variant('standard-inlined')
                ->label(__p('core::phrase.when_label'))
                ->showWhen(['truthy', 'filters'])
                ->options($this->getWhenOptions()),
            Builder::autocomplete('category_id')
                ->forBottomSheetForm()
                ->useOptionContext()
                ->variant('standard-inlined')
                ->label(__p('core::phrase.categories'))
                ->showWhen(['truthy', 'filters'])
                ->searchEndpoint('/page/category')
                ->searchParams(['level' => 0]),
            Builder::submit()
                ->showWhen(['truthy', 'filters'])
                ->label(__p('core::phrase.show_results')),
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function getSortOptions(): array
    {
        return [
            ['label' => __p('core::phrase.sort.latest'), 'value' => 'recent'],
            ['label' => __p('core::phrase.sort.most_liked'), 'value' => 'most_member'],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function getWhenOptions(): array
    {
        return [
            [
                'label' => __p('core::phrase.when.all'),
                'value' => 'all',
            ], [
                'label' => __p('core::phrase.when.this_month'),
                'value' => 'this_month',
            ], [
                'label' => __p('core::phrase.when.this_week'),
                'value' => 'this_week',
            ], [
                'label' => __p('core::phrase.when.today'),
                'value' => 'today',
            ],
        ];
    }
}
