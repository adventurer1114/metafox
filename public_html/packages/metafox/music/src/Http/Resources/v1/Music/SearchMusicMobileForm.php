<?php

namespace MetaFox\Music\Http\Resources\v1\Music;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Music\Support\Facades\Music;
use MetaFox\Platform\Support\Browse\Browse;

class SearchMusicMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/music/search')
            ->acceptPageParams(['q', 'sort', 'when', 'genre_id', 'returnUrl'])
            ->setValue([
                'view' => Browse::VIEW_SEARCH,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);

        $basic->addFields(
            Builder::text('q')
                ->placeholder(__p('music::phrase.search_items'))
                ->forBottomSheetForm('SFSearchBox')
                ->className('mb2'),
            Builder::button('filters')
                ->forBottomSheetForm(),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->forBottomSheetForm()
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->forBottomSheetForm()
                ->options($this->getWhenOptions()),
            Builder::autocomplete('genre_id')
                ->label(__p('music::phrase.genres'))
                ->forBottomSheetForm()
                ->useOptionContext()
                ->searchEndpoint('/music-genre'),
        );

        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);

        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['sort', 'when', 'category_id', 'genre_id']),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->forBottomSheetForm()
                ->showWhen(['truthy', 'filters'])
                ->options($this->getSortOptions())
                ->variant('standard-inlined'),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->forBottomSheetForm()
                ->showWhen(['truthy', 'filters'])
                ->options($this->getWhenOptions())
                ->variant('standard-inlined'),
            Builder::autocomplete('genre_id')
                ->label(__p('music::phrase.genres'))
                ->forBottomSheetForm()
                ->useOptionContext()
                ->showWhen(['truthy', 'filters'])
                ->searchEndpoint('/music-genre')
                ->variant('standard-inlined'),
            Builder::submit()
                ->showWhen(['truthy', 'filters'])
                ->label(__p('core::phrase.filter')),
        );
    }

    protected function getSortOptions(): array
    {
        return [
            ['label' => __p('core::phrase.sort.recent'), 'value' => Browse::SORT_RECENT],
            ['label' => __p('core::phrase.sort.most_viewed'), 'value' => Browse::SORT_MOST_VIEWED],
            ['label' => __p('core::phrase.sort.most_liked'), 'value' => Browse::SORT_MOST_LIKED],
            ['label' => __p('core::phrase.sort.most_discussed'), 'value' => Browse::SORT_MOST_DISCUSSED],
        ];
    }

    protected function getWhenOptions(): array
    {
        return [
            ['label' => __p('core::phrase.when.all'), 'value' => Browse::WHEN_ALL],
            ['label' => __p('core::phrase.when.this_month'), 'value' => Browse::WHEN_THIS_MONTH],
            ['label' => __p('core::phrase.when.this_week'), 'value' => Browse::WHEN_THIS_WEEK],
            ['label' => __p('core::phrase.when.today'), 'value' => Browse::WHEN_TODAY],
        ];
    }

    protected function getEntityTypes(): array
    {
        return Music::getEntityTypeOptions();
    }
}
