<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use MetaFox\Platform\Resource\MobileSetting as ResourceSetting;
use MetaFox\Platform\Support\Browse\Browse;

/**
 *--------------------------------------------------------------------------
 * Quiz Mobile Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class QuizMobileSetting
 * Inject this class into property $resources.
 * @link \MetaFox\Quiz\Http\Resources\v1\MobileAppSetting::$resources;
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class MobileSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->placeholder(__p('quiz::phrase.search_quizzes'))
            ->apiUrl('quiz')
            ->apiParams([
                'q'    => ':q',
                'sort' => ':sort',
                'when' => ':when',
                'view' => Browse::VIEW_SEARCH,
            ]);

        $this->add('viewAll')
            ->apiUrl('quiz')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => ['includes', 'sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed']],
                'when' => ['includes', 'when', ['this-month', 'this-week', 'today']],
                'view' => ['includes', 'view', ['my', 'friend', 'pending', 'feature', 'sponsor', 'my_pending']],
            ]);

        $this->add('viewMyQuizzes')
            ->apiUrl('quiz')
            ->apiParams(['view' => 'my']);

        $this->add('viewFriendQuizzes')
            ->apiUrl('quiz')
            ->apiParams(['view' => 'friend']);

        $this->add('viewPendingQuizzes')
            ->apiUrl('quiz')
            ->apiParams(['view' => 'pending']);

        $this->add('viewMyPendingQuizzes')
            ->apiUrl('quiz')
            ->apiParams([
                'view' => 'my_pending',
            ]);

        $this->add('viewItem')
            ->pageUrl('quiz/:id')
            ->apiUrl('quiz/:id');

        $this->add('deleteItem')
            ->apiUrl('quiz/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p(
                        'quiz::phrase.delete_confirm'
                    ),
                ]
            );

        $this->add('addItem')
            ->pageUrl('quiz/add')
            ->apiUrl('core/mobile/form/quiz.quiz.store')
            ->apiParams(['owner_id' => ':id']);

        $this->add('editItem')
            ->pageUrl('quiz/edit/:id')
            ->apiUrl('core/mobile/form/quiz.quiz.edit/:id');

        $this->add('sponsorItem')
            ->apiUrl('quiz/sponsor/:id');

        $this->add('sponsorItemInFeed')
            ->apiUrl('quiz/sponsor-in-feed/:id');

        $this->add('featureItem')
            ->apiUrl('quiz/feature/:id');

        $this->add('approveItem')
            ->apiUrl('quiz/approve/:id')
            ->asPatch();

        $this->add('searchGlobalQuiz')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'quiz',
                'q'                           => ':q',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);

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
