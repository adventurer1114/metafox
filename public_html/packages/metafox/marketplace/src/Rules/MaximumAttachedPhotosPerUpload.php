<?php

namespace MetaFox\Marketplace\Rules;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Facades\Validator;
use MetaFox\Platform\MetaFoxConstant;

class MaximumAttachedPhotosPerUpload implements RuleContract
{
    public function __construct(protected int $maxFiles = 0)
    {
    }

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value): bool
    {
        // This rule is applied when value is an array, other case will not applicable
        if (!is_array($value)) {
            return true;
        }

        if ($this->maxFiles == 0) {
            return true;
        }

        $totalItems = count($value);

        $collect = collect($value)
            ->groupBy('status')
            ->map(function ($item) {
                return count($item);
            });

        $createCount = (int) $collect->get(MetaFoxConstant::FILE_CREATE_STATUS);

        $updateCount = (int) $collect->get(MetaFoxConstant::FILE_UPDATE_STATUS);

        $count       =  $createCount + $updateCount;

        $count = $count ?? $totalItems;

        // Only when user sent a number of files then we should validate
        if ($count === 0) {
            return true;
        }

        $validator = Validator::make(['count' => $count], [
            'count' => ['integer', 'max:' . $this->maxFiles],
        ]);

        return $validator->passes();
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return __p('marketplace::phrase.maximum_per_upload_limit_reached', ['limit' => $this->maxFiles]);
    }
}
