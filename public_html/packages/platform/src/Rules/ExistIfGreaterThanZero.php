<?php

namespace MetaFox\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Class ExistIfGreaterThanZero
 * @package MetaFox\Platform\Rules
 */
class ExistIfGreaterThanZero implements Rule
{
    private string $ruleExist;
    private string $attribute;

    public function __construct(string $ruleExist)
    {
        $this->ruleExist = $ruleExist;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function passes($attribute, $value): bool
    {
        if (!preg_match('/^exists:/', $this->ruleExist)) {
            abort(400, __('validation.the_rule_must_be_exist_rule'));
        }

        if ($value == 0 || $value == null) {
            return true;
        }

        $validator = Validator::make([$attribute => $value], [
            $attribute => [$this->ruleExist],
        ]);

        $this->attribute = $attribute;

        return !$validator->fails();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __p('validation.exists', ['attribute' => $this->attribute]);
    }
}
