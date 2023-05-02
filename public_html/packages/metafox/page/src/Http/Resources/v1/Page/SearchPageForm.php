<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Support\Browse\Browse;

class SearchPageForm extends AbstractForm
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
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('page::phrase.search_pages'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['category_id', 'q', 'view']),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->margin('normal')
                ->options([
                    ['label' => __p('core::phrase.sort.recent'), 'value' => 'recent'],
                    ['label' => __p('core::phrase.sort.most_liked'), 'value' => 'most_member'],
                ]),
            Builder::choice('when')
                ->label(__p('core::phrase.when_label'))
                ->margin('normal')
                ->options([
                    [
                        'label' => __p('core::phrase.when.all'),
                        'value' => 'all',
                    ],
                    [
                        'label' => __p('core::phrase.when.this_month'),
                        'value' => 'this_month',
                    ],
                    [
                        'label' => __p('core::phrase.when.this_week'),
                        'value' => 'this_week',
                    ],
                    [
                        'label' => __p('core::phrase.when.today'),
                        'value' => 'today',
                    ],
                ]),
            Builder::filterCategory('category_id')
                ->label(__p('core::phrase.categories'))
                ->apiUrl('/page/category')
        );
    }
}
