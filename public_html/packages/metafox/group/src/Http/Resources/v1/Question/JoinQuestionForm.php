<?php

namespace MetaFox\Group\Http\Resources\v1\Question;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\DividerField;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Models\Question;
use MetaFox\Group\Models\Rule;
use MetaFox\Group\Repositories\QuestionRepositoryInterface;
use MetaFox\Group\Repositories\RuleRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/create_form.stub.
 */

/**
 * Class JoinQuestionForm.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JoinQuestionForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__('group::phrase.answer_group_question'))
            ->asPost()
            ->action(url_utility()->makeApiUrl('group-question/answer'))
            ->successAction('updateMembershipQuestion/SUCCESS');
    }

    /**
     * @return QuestionRepositoryInterface
     */
    private function questionRepository(): QuestionRepositoryInterface
    {
        return resolve(QuestionRepositoryInterface::class);
    }

    /**
     * @return RuleRepositoryInterface
     */
    private function ruleRepository(): RuleRepositoryInterface
    {
        return resolve(RuleRepositoryInterface::class);
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    protected function initialize(): void
    {
        $questions = $this->questionRepository()->getQuestionsForForm($this->resource->entityId());

        $rules = $this->ruleRepository()->getRulesForForm($this->resource->entityId());

        $basic = $this->addBasic();

        $basic->addField(
            Builder::hidden('group_id')->setValue($this->resource->id)
        );

        $questionFields = $this->transformQuestions($questions);

        if (!empty($questionFields)) {
            $basic->addFields(...$questionFields);
        }

        $hasRules = $rules->isNotEmpty();

        if ($hasRules) {
            if (!empty($questionFields)) {
                $basic->addField(new DividerField());
            }

            $basic->addField(
                Builder::typography('type')
                    ->plainText('Group Rules')
                    ->variant('h5')
                    ->color('text.secondary')
            );

            foreach ($rules as $rule) {
                /** @var Rule $rule */
                $name = "rule_{$rule->entityId()}";
                $basic->addField(
                    Builder::description($name)
                        ->label($rule->title)
                        ->description($rule->description)
                        ->color('text.secondary'),
                );
            }

            $basic->addField(
                Builder::checkbox('is_confirmed')
                    ->label(__p('group::phrase.you_have_read_all_group_rules_and_agree_to_join_our_group'))
                    ->fullWidth(true)
                    ->color('text.secondary')
                    ->variant('body2')
            );
        }

        $submit = Builder::submit()
            ->label(__p('core::phrase.submit'));

        if ($hasRules) {
            if ($this->resource->is_rule_confirmation) {
                $submit->enableWhen(['and', ['eq', 'is_confirmed', 1]]);
            }
        }

        $this->addFooter()
            ->addFields($submit);
    }

    /**
     * @param Collection $questions
     *
     * @return array<int, mixed>
     */
    private function transformQuestions(Collection $questions): array
    {
        $fields = [];

        foreach ($questions as $key => $question) {
            /** @var Question $question */
            $field = $this->getField($question);
            if (null != $field) {
                $field->setAttribute('order', $key + 1)
                    ->setAttribute('hasFormOrder', true);
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * @param Question $question
     *
     * @return AbstractField|null
     */
    private function getField(Question $question): ?AbstractField
    {
        $name  = 'question' . MetaFoxConstant::NESTED_ARRAY_SEPARATOR . "question_{$question->entityId()}";

        $label = $question->question;

        $field = null;

        $hasRequired = (bool) $this->resource->is_answer_membership_question;

        switch ($question->type_id) {
            case Question::TYPE_TEXT:

                $yup = match ($hasRequired) {
                    true  => Yup::string()->required(),
                    false => Yup::string()->nullable()
                };

                $field = Builder::text($name)
                    ->label($label)
                    ->fullWidth(true)
                    ->hasFormLabel(true)
                    ->setValue('')
                    ->multipleLine()
                    ->styleGroup('question')
                    ->rows(3)
                    ->placeholder(__p('group::phrase.short_answer'))
                    ->yup($yup);
                break;

            case Question::TYPE_SELECT:
                $options = $this->getQuestionOptions($question);

                $yup = match ($hasRequired) {
                    true  => Yup::string()->required(),
                    false => Yup::string()->nullable()
                };

                $field   = Builder::radioGroup($name)
                    ->label($label)
                    ->fullWidth(true)
                    ->setValue((string) Arr::get(reset($options), 'value', ''))
                    ->options($options)
                    ->styleGroup('question')
                    ->yup($yup);
                break;

            case Question::TYPE_MULTI_SELECT:
                $yup = match ($hasRequired) {
                    true  => Yup::array()->required()->min(1),
                    false => Yup::array()->nullable(),
                };

                $field = Builder::checkboxGroup($name)
                    ->label($label)
                    ->fullWidth(true)
                    ->options($this->getQuestionOptions($question))
                    ->setValue([])
                    ->styleGroup('question')
                    ->yup($yup);
                break;
        }

        return $field;
    }

    /**
     * @param Question $question
     *
     * @return array<int, mixed>
     */
    private function getQuestionOptions(Question $question): array
    {
        $options = [];

        foreach ($question->questionFields as $field) {
            $options[] = [
                'label' => $field['title'],
                'value' => $field['id'],
            ];
        }

        return $options;
    }
}
