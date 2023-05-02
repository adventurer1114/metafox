<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * @preload 1
 */
class SearchGroupForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/group/search')
            ->acceptPageParams(['q', 'when', 'sort', 'category_id', 'type_id', 'returnUrl'])
            ->setValue([
                'view' => Browse::VIEW_SEARCH,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('group::phrase.search_groups'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['category_id', 'q', 'view']),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->margin('normal')
                ->options([
                    ['label' => __p('core::phrase.sort.latest'), 'value' => 'latest'], ['label' => __p('core::phrase.sort.most_joined'), 'value' => 'most_member'],
                ]),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->margin('normal')
                ->options([
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
                ]),
            Builder::filterCategory('category_id')
                ->label(__p('core::phrase.categories'))
                ->apiUrl('/group/category'),
        );
    }
}
