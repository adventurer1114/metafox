<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Quiz\Models\Answer;
use MetaFox\Quiz\Models\Question;
use MetaFox\Quiz\Models\Quiz as Model;

/**
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EditQuizForm extends CreateQuizForm
{
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
        $privacy  = $this->resource->privacy;
        if ($privacy == MetaFoxPrivacy::CUSTOM) {
            $lists = PrivacyPolicy::getPrivacyItem($this->resource);

            $listIds = [];
            if (!empty($lists)) {
                $listIds = array_column($lists, 'item_id');
            }

            $privacy = $listIds;
        }

        $this->isEdit = true;

        $file    = null;
        $imageId = $this->resource->image_file_id;

        if ($imageId) {
            $file = [
                'id'        => $imageId,
                'temp_file' => $imageId,
                'status'    => 'update',
            ];
        }

        $this
            ->title(__p('quiz::phrase.edit_quiz'))
            ->action(url_utility()->makeApiUrl('/quiz/' . $this->resource->entityId()))
            ->setBackProps(__p('core::web.quiz'))
            ->asPut()
            ->setValue([
                'title'       => $this->resource->title ?? '',
                'text'        => $quizText != null ? parse_output()->parse($quizText->text_parsed) : '',
                'questions'   => $questions,
                'attachment'  => [], //Todo: need to implement this
                'privacy'     => $privacy,
                'owner_id'    => $this->resource->owner_id,
                'attachments' => $this->resource->attachmentsForForm(),
                'file'        => $file,
            ]);
    }
}
