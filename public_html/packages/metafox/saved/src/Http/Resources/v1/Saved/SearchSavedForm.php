<?php

namespace MetaFox\Saved\Http\Resources\v1\Saved;

use MetaFox\Form\Builder;
use MetaFox\Form\Html\BuiltinSearchForm;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Saved\Models\Saved as Model;
use MetaFox\Saved\Support\Facade\SavedType;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchSavedForm.
 * @property ?Model $resource
 */
class SearchSavedForm extends BuiltinSearchForm
{
    protected function prepare(): void
    {
        $this->action('/saved/search')
            ->setValue(['type' => 'all', 'when' => 'all', 'open' => 'all'])
            ->acceptPageParams(['q', 'open', 'type', 'sort', 'when']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('saved::phrase.search_saved_items'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['view', 'q']),
            Builder::choice('type')
                ->label(__p('core::phrase.select_type'))
                ->options(SavedType::getFilterOptions()),
            Builder::choice('open')
                ->label(__p('saved::phrase.open'))
                ->options([
                    [
                        'label' => __p('core::phrase.when.all'),
                        'value' => 'all',
                    ],
                    [
                        'label' => __p('saved::phrase.opened'),
                        'value' => 'opened',
                    ],
                    [
                        'label' => __p('saved::phrase.unopened'),
                        'value' => 'unopened',
                    ],
                ]),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->marginNormal()
                ->sizeLarge()
                ->options([
                    ['label' => __p('core::phrase.sort.recent'), 'value' => Browse::SORT_RECENT],
                    ['label' => __p('core::phrase.sort.previous'), 'value' => Browse::SORT_LATEST],
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
        );
    }
}
