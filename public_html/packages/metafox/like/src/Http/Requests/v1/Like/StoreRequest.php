<?php

namespace MetaFox\Like\Http\Requests\v1\Like;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Like\Models\Reaction;

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
    public function rules()
    {
        return [
            'item_id'     => ['required', 'numeric'],
            'item_type'   => ['required', 'string'],
            'reaction_id' => ['sometimes', 'numeric', 'exists:like_reactions,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['reaction_id'])) {
            /** @var Reaction $reaction */
            $reaction = Reaction::query()->orderBy('id')->firstOrFail();
            $data['reaction_id'] = $reaction->entityId();
        }

        return $data;
    }
}
