<?php

namespace MetaFox\Platform\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

/**
 * Class ResourceTextRule.
 */
class ResourceTextRule implements Rule
{
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
        try {
            // Replace all the non-breaking space bar character first
            /** @var string $value */
            $value = str_replace('&nbsp;', '', $value);

            // Strip all html tags inside
            $value = strip_tags($value);

            // IF the remain content contains any word => it shall pass the validation
            if (preg_match('@\S+@', $value)) {
                return true;
            }
        } catch (Exception $e) {
            // Silent error and log it
            Log::error($e);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __p('validation.invalid_content');
    }
}
