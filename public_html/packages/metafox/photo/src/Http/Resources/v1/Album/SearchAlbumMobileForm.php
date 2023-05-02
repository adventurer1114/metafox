<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Photo\Http\Resources\v1\Album;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * @driverName photo_album.search
 * @preload    1
 */
class SearchAlbumMobileForm extends AbstractForm
{
    public function prepare(): void
    {
        $this->action('/photo/albums/search')
            ->acceptPageParams(['q', 'sort', 'when']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);

        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->placeholder(__p('photo::phrase.search_albums'))
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
        );

        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);
        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['sort', 'when']),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->label(__p('core::phrase.sort_label'))
                ->showWhen(['truthy', 'filters'])
                ->options($this->getSortOptions()),
            Builder::choice('when')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->label(__p('core::phrase.when_label'))
                ->showWhen(['truthy', 'filters'])
                ->options($this->getWhenOptions()),
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
            [
                'label' => __p('core::phrase.sort.recent'),
                'value' => Browse::SORT_RECENT,
            ],
            [
                'label' => __p('core::phrase.sort.most_viewed'),
                'value' => Browse::SORT_MOST_VIEWED,
            ],
            [
                'label' => __p('core::phrase.sort.most_liked'),
                'value' => Browse::SORT_MOST_LIKED,
            ],
            [
                'label' => __p('core::phrase.sort.most_discussed'),
                'value' => Browse::SORT_MOST_DISCUSSED,
            ],
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
                'value' => Browse::WHEN_ALL,
            ],
            [
                'label' => __p('core::phrase.when.this_month'),
                'value' => Browse::WHEN_THIS_MONTH,
            ],
            [
                'label' => __p('core::phrase.when.this_week'),
                'value' => Browse::WHEN_THIS_WEEK,
            ],
            [
                'label' => __p('core::phrase.when.today'),
                'value' => Browse::WHEN_TODAY,
            ],
        ];
    }
}
