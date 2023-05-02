<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\PrivacyFieldMobileTrait;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Quiz\Http\Requests\v1\Quiz\CreateFormRequest;
use MetaFox\Quiz\Models\Quiz as Model;
use MetaFox\Quiz\Policies\QuizPolicy;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\Yup\Yup;

/**
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateQuizMobileForm extends AbstractForm
{
    use PrivacyFieldMobileTrait;

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function boot(CreateFormRequest $request, QuizRepositoryInterface $repository, ?int $id = null): void
    {
        $context = user();
        $params  = $request->validated();

        if ($params['owner_id'] != 0) {
            $userEntity = UserEntity::getById($params['owner_id']);
            $this->setOwner($userEntity->detail);
        }

        policy_authorize(QuizPolicy::class, 'create', $context, $this->owner);
        $this->resource = new Model($params);
    }

    protected function prepare(): void
    {
        $context = user();
        $privacy = UserPrivacy::getItemPrivacySetting($context->entityId(), 'quiz.item_privacy');
        if ($privacy === false) {
            $privacy = MetaFoxPrivacy::EVERYONE;
        }

        $minQuestions     = (int) $context->getPermissionValue('quiz.min_question_quiz');
        $defaultAnswers   = (int) $context->getPermissionValue('quiz.number_of_answers_per_default');
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
            ->asPost()->setValue([
                'questions' => $questionsDefault,
                'title'     => $this->resource->title ?? '',
                'text'      => $this->resource->quizText->text ?? '',
                'privacy'   => $privacy,
                'owner_id'  => $this->resource->owner_id,
            ]);
    }

    protected function initialize(): void
    {
        $context           = user();
        $basic             = $this->addBasic();
        $minQuestion       = (int) $context->getPermissionValue('quiz.min_question_quiz');
        $maxQuestion       = (int) $context->getPermissionValue('quiz.max_question_quiz');
        $titleMaxLength    = Settings::get('quiz.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
        $titleMinLength    = Settings::get('quiz.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $minAnswer         = (int) $context->getPermissionValue('quiz.min_answer_question_quiz');
        $maxAnswer         = (int) $context->getPermissionValue('quiz.max_answer_question_quiz');
        $maxLengthQuestion = Settings::get('quiz.max_length_quiz_question', 100);
        $minLengthQuestion = Settings::get('quiz.min_length_quiz_question', 4);

        $basic->addFields(
            Builder::text('title')->required(true)
                ->returnKeyType('next')
                ->label(__p('core::phrase.title'))
                ->maxLength($titleMaxLength)
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $titleMaxLength]))
                ->placeholder(__p('quiz::phrase.fill_in_a_title_for_your_quiz'))
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
            Builder::quizQuestion('questions')
                ->label(__p('quiz::phrase.question'))
                ->minLength($minLengthQuestion)
                ->maxLength($maxLengthQuestion)
                ->yup(
                    Yup::array()
                        ->required(__p('quiz::validation.question_is_a_required_field'))
                        ->min($minQuestion)
                        ->max($maxQuestion)
                        ->uniqueBy('question', __p('quiz::validation.the_question_list_must_be_unique'))
                        ->of(
                            Yup::object()
                                ->addProperty(
                                    'question',
                                    Yup::string()
                                        ->required(__p('quiz::validation.question_is_a_required_field'))
                                        ->maxLength(
                                            $maxLengthQuestion,
                                            __p(
                                                'quiz::validation.question_max_length',
                                                ['number' => $maxLengthQuestion]
                                            )
                                        )
                                        ->minLength(
                                            $minLengthQuestion,
                                            __p(
                                                'quiz::validation.question_min_length',
                                                ['number' => $minLengthQuestion]
                                            )
                                        )
                                )
                                ->addProperty(
                                    'answers',
                                    Yup::array()
                                        ->required()
                                        ->min($minAnswer, __p(
                                            'validation.min.array',
                                            ['attribute' => 'answers', 'min' => $minAnswer]
                                        ))
                                        ->max($maxAnswer, __p(
                                            'validation.max.array',
                                            ['attribute' => 'answers', 'max' => $maxAnswer]
                                        ))
                                        ->uniqueBy('answer', __p('quiz::validation.the_answers_list_must_be_unique'))
                                        ->of(
                                            Yup::object()
                                                ->addProperty(
                                                    'answer',
                                                    Yup::string()
                                                        ->required(__p('quiz::validation.answer_is_a_required_field'))
                                                        ->maxLength(255)
                                                )
                                        )
                                )
                        )
                ),
            $this->buildPrivacyField()
                ->description(__p('quiz::phrase.control_who_can_see_this_quiz'))
                ->minWidth(275)
                ->fullWidth(false),
            Builder::hidden('owner_id')
        );
    }

    protected function buildBannerField()
    {
        $context = user();
        if ($context->hasPermissionTo('quiz.upload_photo_form')) {
            return Builder::singlePhoto('file')
                ->itemType('quiz')
                ->label(__p('Banner'))
                ->previewUrl($this->resource->image)
                ->thumbnailSizes($this->resource->getSizes())
                ->required($context->hasPermissionTo('quiz.require_upload_photo'));
        }
    }
}
