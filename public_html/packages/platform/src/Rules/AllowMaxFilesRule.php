<?php

namespace MetaFox\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use MetaFox\Platform\MetaFoxConstant;

/**
 * Class ResourceNameRule.
 */
class AllowMaxFilesRule implements Rule
{
    private int $maxNumber;

    public function __construct(int $maxNumber = MetaFoxConstant::MAX_NUMBER_OF_FILES)
    {
        $this->maxNumber = $maxNumber;
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
        if (!is_array($value)) {
            return false;
        }

        $array       = collect($value)->groupBy('status')->toArray();
        $total       = count($array);
        $totalRemove = array_key_exists('remove', $array) ? count($array['remove']) : 0;

        return ($total - $totalRemove) <= $this->maxNumber;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __p('validation.max.array', [
            'attribute' => 'attachments',
            'max'       => $this->maxNumber,
        ]);
    }
}
