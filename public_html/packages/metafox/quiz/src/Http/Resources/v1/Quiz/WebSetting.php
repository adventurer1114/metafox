<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use MetaFox\Platform\Resource\WebSetting as ResourceSetting;
use MetaFox\Platform\Support\Browse\Browse;

/**
 *--------------------------------------------------------------------------
 * Quiz Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('quiz');

        $this->add('searchItem')
            ->placeholder(__p('quiz::phrase.search_quizzes'))
            ->pageUrl('quiz/search');

        $this->add('viewAll')
            ->apiUrl('quiz')
            ->apiRules(
                [
                    'q'    => ['truthy', 'q'],
                    'sort' => ['includes', 'sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed']],
                    'when' => ['includes', 'when', ['this-month', 'this-week', 'today']],
                    'view' => [
                        'includes', 'view', [
                            Browse::VIEW_MY,
                            Browse::VIEW_FRIEND,
                            Browse::VIEW_PENDING,
                            Browse::VIEW_FEATURE,
                            Browse::VIEW_SPONSOR,
                            Browse::VIEW_SEARCH,
                            Browse::VIEW_MY_PENDING,
                        ],
                    ],
                ]
            );

        $this->add('viewItem')
            ->pageUrl('quiz/:id')
            ->apiUrl('quiz/:id');

        $this->add('deleteItem')
            ->apiUrl('quiz/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('quiz::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('quiz/add')
            ->apiUrl('quiz/form');

        $this->add('editItem')
            ->pageUrl('quiz/edit/:id')
            ->apiUrl('quiz/form/:id');

        $this->add('editFeedItem')
            ->pageUrl('quiz/edit/:id')
            ->apiUrl('quiz/form/:id');

        $this->add('sponsorItem')
            ->apiUrl('quiz/sponsor/:id');

        $this->add('sponsorItemInFeed')
            ->apiUrl('quiz/sponsor-in-feed/:id');

        $this->add('featureItem')
            ->apiUrl('quiz/feature/:id');

        $this->add('approveItem')
            ->apiUrl('quiz/approve/:id')
            ->asPatch();

        $this->add('viewQuizSummary')
            ->apiUrl('quiz-question/view-plays')
            ->apiParams([
                'question_id' => ':id',
            ])
            ->asGet();

        $this->add('viewQuizResultIndividual')
            ->apiUrl('quiz-result/view-individual-play')
            ->apiParams([
                'quiz_id' => ':quiz_id',
                'user_id' => ':user_id',
            ])
            ->asGet();
    }
}
