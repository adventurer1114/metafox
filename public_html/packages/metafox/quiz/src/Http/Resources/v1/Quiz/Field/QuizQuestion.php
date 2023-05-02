<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz\Field;

use MetaFox\Form\AbstractField;
use MetaFox\Platform\Facades\Settings;

class QuizQuestion extends AbstractField
{
    public function initialize(): void
    {
        $context = user();

        $this->setAttributes([
            'component'      => 'QuizQuestion',
            'name'           => 'questions',
            'variant'        => 'outlined',
            'fullWidth'      => true,
            'minQuestions'   => (int) $context->getPermissionValue('quiz.min_question_quiz'),
            'maxQuestions'   => (int) $context->getPermissionValue('quiz.max_question_quiz'),
            'minAnswers'     => (int) $context->getPermissionValue('quiz.min_answer_question_quiz'),
            'maxAnswers'     => (int) $context->getPermissionValue('quiz.max_answer_question_quiz'),
            'defaultAnswers' => (int) $context->getPermissionValue('quiz.number_of_answers_per_default'),
            'returnKeyType'  => 'next',
        ]);
    }
}
