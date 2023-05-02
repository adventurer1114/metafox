<?php

namespace MetaFox\Photo\Rules;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Facades\Validator;

class UploadedAlbumItems implements RuleContract
{
    /**
     * @inheritDoc
     */
    public function passes($attribute, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $items = collect($value)->groupBy('status')->toArray();
        $rules = [
            'new'             => ['array', 'nullable'],
            'new.*.id'        => ['required', 'numeric', 'exists:storage_files,id'],
            'new.*.type'      => ['required', 'string'],
            'new.*.status'    => ['required', 'string'],
            'update'          => ['array', 'nullable'],
            'update.*.id'     => ['required', 'numeric'],
            'update.*.type'   => ['required', 'string'],
            'update.*.status' => ['required', 'string'],
            'remove'          => ['array', 'nullable'],
            'remove.*.id'     => ['required', 'numeric'],
            'remove.*.type'   => ['required', 'string'],
            'remove.*.status' => ['required', 'string'],
        ];

        $validator = Validator::make($items, $rules);

        return $validator->passes();
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return __p('photo::validation.album_items_are_invalid');
    }
}
