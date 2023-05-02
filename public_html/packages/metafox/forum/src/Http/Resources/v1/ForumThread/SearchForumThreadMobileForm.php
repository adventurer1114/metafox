<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Forum\Models\Forum as Model;
use MetaFox\Forum\Policies\ForumPolicy;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchForumForm.
 * @property ?Model $resource
 */
class SearchForumThreadMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->action('/forum/search')
            ->acceptPageParams(['q', 'sort', 'when', 'forum_id', 'item_type', 'returnUrl', 'view'])
            ->setValue([
                'item_type' => ForumSupport::SEARCH_BY_THREAD,
                'view'      => Browse::VIEW_SEARCH,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);

        $canViewForum = policy_check(ForumPolicy::class, 'viewAny', user());

        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->placeholder(__p('forum::web.search_discussions'))
                ->className('mb2'),
            Builder::button('filters')
                ->forBottomSheetForm(),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->options($this->getSortThreadOptions()),
            Builder::choice('when')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.when_label'))
                ->options($this->getWhenOptions()),
            $canViewForum ? Builder::autocomplete('forum_id')
                ->forBottomSheetForm()
                ->useOptionContext()
                ->label(__p('forum::web.communities'))
                ->searchEndpoint('/forum/option') : null,
        );

        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);

        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['sort', 'when', 'forum_id']),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->showWhen(['truthy', 'filters'])
                ->variant('standard-inlined')
                ->options($this->getSortThreadOptions()),
            Builder::choice('when')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.when_label'))
                ->variant('standard-inlined')
                ->showWhen(['truthy', 'filters'])
                ->options($this->getWhenOptions()),
            $canViewForum ? Builder::autocomplete('forum_id')
                ->forBottomSheetForm()
                ->useOptionContext()
                ->label(__p('forum::web.communities'))
                ->variant('standard-inlined')
                ->showWhen(['truthy', 'filters'])
                ->searchEndpoint('/forum/option') : null,
            Builder::submit()
                ->showWhen(['truthy', 'filters'])
                ->label(__p('core::phrase.show_results')),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getSortThreadOptions(): array
    {
        return [
            ['label' => __p('core::phrase.sort.recent'), 'value' => Browse::SORT_RECENT],
            ['label' => __p('core::phrase.sort.most_liked'), 'value' => Browse::SORT_MOST_LIKED],
            ['label' => __p('core::phrase.sort.most_discussed'), 'value' => Browse::SORT_MOST_DISCUSSED],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function getWhenOptions(): array
    {
        return [
            ['label' => __p('core::phrase.when.all'), 'value' => Browse::WHEN_ALL],
            ['label' => __p('core::phrase.when.this_month'), 'value' => Browse::WHEN_THIS_MONTH],
            ['label' => __p('core::phrase.when.this_week'), 'value' => Browse::WHEN_THIS_WEEK],
            ['label' => __p('core::phrase.when.today'), 'value' => Browse::WHEN_TODAY],
        ];
    }
}
