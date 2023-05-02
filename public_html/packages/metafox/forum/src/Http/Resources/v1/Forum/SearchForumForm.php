<?php

namespace MetaFox\Forum\Http\Resources\v1\Forum;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Forum\Models\Forum as Model;
use MetaFox\Forum\Policies\ForumPolicy;
use MetaFox\Forum\Support\Browse\Scopes\ThreadSortScope;
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
class SearchForumForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->action('/forum/search')
            ->acceptPageParams(['q', 'sort', 'when', 'forum_id', 'item_type', 'returnUrl', 'view'])
            ->setValue([
                'item_type' => ForumSupport::SEARCH_BY_THREAD,
                'view'      => Browse::VIEW_SEARCH,
                'sort'      => ThreadSortScope::SORT_LATEST_DISCUSSED,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $canViewForum = policy_check(ForumPolicy::class, 'viewAny', user());

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('forum::web.search_discussions'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['view', 'forum_id', 'q']),
            Builder::choice('item_type')
                ->label(__p('forum::phrase.browse_by'))
                ->margin('normal')
                ->sizeLarge()
                ->options([
                    ['label' => __p('forum::phrase.show_threads'), 'value' => ForumSupport::SEARCH_BY_THREAD], ['label' => __p('forum::phrase.show_posts'), 'value' => ForumSupport::SEARCH_BY_POST],
                ]),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_by'))
                ->marginNormal()
                ->sizeLarge()
                ->relatedFieldName('item_type')
                ->optionRelatedMapping(
                    [
                        ForumSupport::SEARCH_BY_THREAD => $this->getSortThreadOptions(),
                        ForumSupport::SEARCH_BY_POST   => $this->getSortPostOptions(),
                    ]
                )
                ->options($this->getSortThreadOptions()),
            Builder::choice('when')
                ->label(__p('forum::phrase.when'))
                ->marginNormal()
                ->sizeLarge()
                ->options([
                    ['label' => __p('core::phrase.when.all'), 'value' => Browse::WHEN_ALL], ['label' => __p('core::phrase.when.this_month'), 'value' => Browse::WHEN_THIS_MONTH], ['label' => __p('core::phrase.when.this_week'), 'value' => Browse::WHEN_THIS_WEEK], ['label' => __p('core::phrase.when.today'), 'value' => Browse::WHEN_TODAY],
                ]),
            $canViewForum ? Builder::filterCategory('forum_id')
                ->label(__p('forum::phrase.forums'))
                ->apiUrl('/forum')
                ->marginNormal()
                ->sizeLarge() : null,
        );
    }

    protected function getSortThreadOptions()
    {
        return [
            [
                'label' => __p('forum::phrase.latest_discussed'), 'value' => ThreadSortScope::SORT_LATEST_DISCUSSED,
            ],
            [
                'label' => __p('forum::phrase.recent_post'), 'value' => ThreadSortScope::SORT_RECENT_POST,
            ],
            [
                'label' => __p('core::phrase.sort.most_liked'), 'value' => Browse::SORT_MOST_LIKED,
            ],
            [
                'label' => __p('core::phrase.sort.most_discussed'), 'value' => Browse::SORT_MOST_DISCUSSED,
            ],
        ];
    }

    protected function getSortPostOptions()
    {
        return [
            [
                'label' => __p('core::phrase.sort.recent'), 'value' => Browse::SORT_RECENT,
            ],
            [
                'label' => __p('core::phrase.sort.most_liked'), 'value' => Browse::SORT_MOST_LIKED,
            ],
        ];
    }
}
