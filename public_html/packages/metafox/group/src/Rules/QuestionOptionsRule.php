<?php

namespace MetaFox\Group\Rules;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Arr;
use MetaFox\Group\Models\Question;

/**
 * Class AnswerQuestion.
 * @ignore
 */
class QuestionOptionsRule implements RuleContract
{
    private int $typeId;

    public function __construct(int $type = Question::TYPE_TEXT)
    {
        $this->typeId = $type;
    }

    /**
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if ($this->typeId == Question::TYPE_TEXT) {
            return true;
        }

        $options    = collect($value)->groupBy('status')->toArray();
        $newItem    = Arr::get($options, 'new', []);
        $updateItem = Arr::get($options, 'update', []);
        $totalItem  = count($newItem) + count($updateItem);

        return $totalItem >= Question::MIN_OPTION;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return __p('group::phrase.question_requires_at_least_two_option');
    }
}
