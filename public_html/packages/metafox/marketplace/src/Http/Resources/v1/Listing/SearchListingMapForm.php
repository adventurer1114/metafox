<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * @preload 1
 */
class SearchListingMapForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/marketplace/search-map')
            ->acceptPageParams([
                'q', 'when', 'sort', 'limit', 'returnUrl', 'bounds_west',
                'bounds_east', 'bounds_south', 'bounds_north', 'zoom',
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('marketplace::phrase.search_marketplace'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label('Reset')
                ->align('right')
                ->excludeFields(['category_id', 'q', 'view']),
            Builder::choice('sort_type')
                ->label(__p('core::phrase.sort_label'))
                ->marginNormal()
                ->options([
                    [
                        'label' => __p('core::phrase.new'),
                        'value' => Browse::SORT_TYPE_DESC,
                    ], [
                        'label' => __p('core::phrase.sort.latest'),
                        'value' => Browse::SORT_TYPE_ASC,
                    ],
                ]),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->marginNormal()
                ->options($this->getWhenOptions()),
            Builder::choice('limit')
                ->label(__p('core::phrase.view'))
                ->marginNormal()
                ->options($this->getLimitOptions()),
        );
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

    protected function getLimitOptions(): array
    {
        return [
            [
                'label' => __p('marketplace::phrase.nearest.view_5_nearest_listings'),
                'value' => MetaFoxConstant::VIEW_5_NEAREST,
            ], [
                'label' => __p('marketplace::phrase.nearest.view_10_nearest_listings'),
                'value' => MetaFoxConstant::VIEW_10_NEAREST,
            ], [
                'label' => __p('marketplace::phrase.nearest.view_15_nearest_listings'),
                'value' => MetaFoxConstant::VIEW_15_NEAREST,
            ], [
                'label' => __p('marketplace::phrase.nearest.view_20_nearest_listings'),
                'value' => MetaFoxConstant::VIEW_20_NEAREST,
            ],
        ];
    }
}
