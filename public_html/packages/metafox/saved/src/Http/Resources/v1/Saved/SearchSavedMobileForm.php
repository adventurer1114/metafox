<?php

namespace MetaFox\Saved\Http\Resources\v1\Saved;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm;
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
 * Class SearchSavedMobileForm.
 * @property ?Model $resource
 */
class SearchSavedMobileForm extends MobileForm
{
    protected function prepare(): void
    {
        $this->action('/saved/search')
            ->setValue(['type' => 'all', 'when' => 'all', 'open' => 'all'])
            ->acceptPageParams(['q', 'open', 'type', 'sort', 'when']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);
        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->placeholder(__p('saved::phrase.search_saved_items'))
                ->className('mb2'),
            Builder::choice('type')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.select_type'))
                ->options(SavedType::getFilterOptions()),
            Builder::choice('open')
                ->forBottomSheetForm()
                ->autoSubmit()
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
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->sizeLarge()
                ->options([
                    ['label' => __p('core::phrase.sort.recent'), 'value' => Browse::SORT_RECENT],
                    ['label' => __p('core::phrase.sort.previous'), 'value' => Browse::SORT_LATEST],
                ]),
            Builder::choice('when')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.when_label'))
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
