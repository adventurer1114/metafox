<?php
namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Quiz\Models\Answer;
use MetaFox\Quiz\Models\Question;
use MetaFox\Quiz\Models\Quiz as Model;
use MetaFox\Quiz\Policies\QuizPolicy;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;
use MetaFox\Quiz\Http\Requests\v1\Quiz\CreateFormRequest;

/**
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditQuizMobileForm extends CreateQuizMobileForm
{
    public function boot(CreateFormRequest $request, QuizRepositoryInterface $repository, ?int $id = null): void
    {
        $context        = user();
        $this->resource = $repository->find($id);
        policy_authorize(QuizPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        $questions = $this->resource->questions->map(function (Question $question) {
            return [
                'question' => $question->question,
                'id'       => $question->entityId(),
                'ordering' => $question->ordering,
                'answers'  => $question->answers->map(function (Answer $answer) {
                    return [
                        'id'         => $answer->entityId(),
                        'ordering'   => $answer->ordering,
                        'is_correct' => $answer->is_correct,
                        'answer'     => $answer->answer,
                    ];
                }),
            ];
        });

        $quizText = $this->resource->quizText;
        $privacy = $this->resource->privacy;
        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $this
            ->title(__p('quiz::phrase.edit_quiz'))
            ->action(url_utility()->makeApiUrl('/quiz/' . $this->resource->entityId()))
            ->asPut()
            ->setValue([
                'title'       => $this->resource->title ?? '',
                'text'        => $quizText != null ? parse_output()->parse($quizText->text_parsed) : '',
                'questions'   => $questions,
                'privacy'     => $privacy,
                'owner_id'    => $this->resource->owner_id,
            ]);
    }
}
