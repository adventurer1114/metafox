<?php

namespace MetaFox\Saved\Http\Requests\v1\Saved;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'item_id'          => ['required', 'numeric'],
            'item_type'        => ['required', 'string'],
            'saved_list_ids'   => ['sometimes', 'array'],
            'saved_list_ids.*' => ['numeric', 'exists:saved_lists,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['saved_list_ids'])) {
            $data['savedLists'] = $data['saved_list_ids'];
            unset($data['saved_list_ids']);
        }

        return $data;
    }
}
