<?php

namespace MetaFox\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PrivacyRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // if privacy is int then check allows.
        if (!is_array($value)) {
            $validator = Validator::make(['list' => $value], [
                'list' => [new PrivacyValidator()],
            ]);

            return $validator->passes();
        }

        $validator = Validator::make(['list' => $value], [
            'list' => [new PrivacyListValidator()],
        ]);

        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.invalid_privacy');
    }
}
