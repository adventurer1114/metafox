<?php

namespace MetaFox\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * Class ExistIfGreaterThanZero.
 */
class ExistIfGreaterThanZero implements Rule
{
    private string $ruleExist;
    private string $attribute;
    private ?string $message;

    public function __construct(string $ruleExist, ?string $message = null)
    {
        $this->ruleExist = $ruleExist;
        $this->message   = $message;
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

        // fix security: sql injections
        $value = intval($value, 10);

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
        if (null !== $this->message) {
            return $this->message;
        }

        return __p('validation.exists', ['attribute' => $this->attribute]);
    }
}
