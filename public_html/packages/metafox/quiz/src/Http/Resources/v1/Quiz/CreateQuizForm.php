<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Quiz\Http\Resources\v1\Quiz\Field\QuizQuestion;
use MetaFox\Quiz\Models\Quiz as Model;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateQuizForm extends AbstractForm
{
    protected bool $isEdit = false;

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $context = user();
        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'quiz.item_privacy');
        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        $minQuestions   = (int) $context->getPermissionValue('quiz.min_question_quiz');
        $defaultAnswers = (int) $context->getPermissionValue('quiz.number_of_answers_per_default');

        $questionsDefault = [];
        //factory data
        for ($i = 1; $i <= $minQuestions; $i++) {
            $answers = [];
            for ($j = 1; $j <= $defaultAnswers; $j++) {
                $isCorrect = 0;
                if ($j == 1) {
                    $isCorrect = 1;
                }

                $answers[] = ['answer' => '', 'is_correct' => $isCorrect, 'ordering' => $j];
            }

            $questionsDefault[] = [
                'question' => '',
                'ordering' => $i,
                'answers'  => $answers,
            ];
        }

        $this->title(__p('quiz::phrase.new_quiz'))
            ->action(url_utility()->makeApiUrl('/quiz'))
            ->setBackProps(__p('core::web.quiz'))
            ->asPost()->setValue([
                'questions'   => $questionsDefault,
                'title'       => $this->resource->title ?? '',
                'text'        => $this->resource->quizText->text ?? '',
                'attachments' => [],
                'privacy'     => $privacy,
                'owner_id'    => $this->resource->owner_id,
            ]);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function initialize(): void
    {
        $context        = user();
        $basic          = $this->addBasic();
        $minQuestion    = (int) $context->getPermissionValue('quiz.min_question_quiz');
        $maxQuestion    = (int) $context->getPermissionValue('quiz.max_question_quiz');
        $titleMaxLength = Settings::get('quiz.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $titleMinLength = Settings::get('quiz.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $basic->addFields(
            Builder::text('title')
                ->required(true)
                ->returnKeyType('next')
                ->maxLength($titleMaxLength)
                ->margin('normal')->label(__p('core::phrase.title'))
                ->placeholder(__p('quiz::phrase.fill_in_a_title_for_your_quiz'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $titleMaxLength]))
                ->yup(
                    Yup::string()
                        ->required()
                        ->maxLength($titleMaxLength)
                        ->minLength($titleMinLength)
                ),
            $this->buildBannerField(),
            Builder::richTextEditor('text')
                ->required(true)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description'))
                ->placeholder(__p('quiz::phrase.quiz_text_description'))
                ->yup(Yup::string()->required()),
            Builder::attachment()
                ->placeholder(__p('core::phrase.attach_files'))
                ->itemType('quiz'),
            new QuizQuestion([
                'name'       => 'questions',
                'minLength'  => Settings::get('quiz.min_length_quiz_question', 4),
                'maxLength'  => Settings::get('quiz.max_length_quiz_question', 100),
                'validation' => [
                    'type'     => 'array',
                    'required' => true,
                    'min'      => $minQuestion,
                    'max'      => $maxQuestion,
                    'of'       => [
                        'type'       => 'object',
                        'uniqueBy'   => 'question',
                        'properties' => [
                            'question' => [
                                'type'      => 'string',
                                'label'     => 'Question',
                                'required'  => true,
                                'minLength' => Settings::get('quiz.min_length_quiz_question', 4),
                                'maxLength' => Settings::get('quiz.max_length_quiz_question', 100),
                                'errors'    => [
                                    'required' => __p('quiz::validation.question_is_required'),
                                ],
                            ],
                            'answers' => [
                                'type'      => 'array',
                                'maxLength' => 255,
                                'required'  => true,
                                'min'       => (int) $context->getPermissionValue('quiz.min_answer_question_quiz'),
                                'max'       => (int) $context->getPermissionValue('quiz.max_answer_question_quiz'),
                                'of'        => [
                                    'type'       => 'object',
                                    'uniqueBy'   => 'answer',
                                    'properties' => [
                                        'answer' => [
                                            'type'      => 'string',
                                            'required'  => true,
                                            'maxLength' => 255,
                                            'errors'    => [
                                                'required'  => __p('quiz::validation.answer_is_required'),
                                                'maxLength' => __p(
                                                    'validation.field_must_be_at_most_max_length_characters',
                                                    [
                                                        'field'     => 'Title',
                                                        'maxLength' => 255,
                                                    ]
                                                ),
                                            ],
                                        ],
                                    ],
                                    'errors' => [
                                        'uniqueBy' => __p('quiz::validation.the_answers_list_must_be_unique'),
                                    ],
                                ],
                                'errors' => [
                                    'required' => __p('quiz::validation.answer_is_required'),
                                    'min'      => __p('validation.min.array', [
                                        'attribute' => 'answers',
                                        'min'       => (int) $context->getPermissionValue('quiz.min_answer_question_quiz'),
                                    ]),
                                    'max' => __p('validation.max.array', [
                                        'attribute' => 'answers',
                                        'max'       => (int) $context->getPermissionValue('quiz.max_answer_question_quiz'),
                                    ]),
                                ],
                            ],
                        ],
                        'errors' => [
                            'uniqueBy' => __p('quiz::validation.the_answers_list_must_be_unique'),
                        ],
                    ],
                    'errors' => [
                        'max' => __p('quiz::phrase.max_question_per_quiz_number', ['number' => $maxQuestion]),
                        'min' => __p('quiz::phrase.min_question_per_quiz_number', ['number' => $minQuestion]),
                    ],
                ],
            ]),
            Builder::privacy()
                ->description(__p('quiz::phrase.control_who_can_see_this_quiz'))
                ->minWidth(275)
                ->fullWidth(false)
                ->showWhen([
                    'or',
                    [
                        'falsy',
                        'owner_id',
                    ], [
                        'eq',
                        'owner_id',
                        $context->entityId(),
                    ],
                ]),
            Builder::hidden('owner_id')
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p($this->isEdit ? 'core::phrase.save_changes' : 'core::phrase.create'))
                    ->flexWidth(true),
                Builder::cancelButton()
            );
    }

    protected function buildBannerField()
    {
        $context = user();
        if ($context->hasPermissionTo('quiz.upload_photo_form')) {
            return Builder::singlePhoto()->label(__p('core::phrase.banner'))
                ->itemType('quiz')
                ->required($context->hasPermissionTo('quiz.require_upload_photo'))
                ->previewUrl($this->resource->image)
                ->thumbnailSizes($this->resource->getSizes())
                ->yup(
                    Yup::object()
                        ->nullable()
                        ->addProperty('id', [
                            'type'     => 'number',
                            'required' => $context->hasPermissionTo('quiz.require_upload_photo'),
                            'errors'   => [
                                'required' => __p('quiz::phrase.banner_is_required'),
                            ],
                        ])
                );
        }
    }
}
