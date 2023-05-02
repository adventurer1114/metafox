<?php

namespace MetaFox\Profile\Http\Requests\v1\Field\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CaseInsensitiveUnique;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Profile\Http\Controllers\Api\v1\FieldAdminController::update
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = (int) $this->route('field');

        return [
            'section_id' => ['int', 'sometimes', 'nullable'],
            'field_name' => [
                'string', 'required', 'regex:/' . MetaFoxConstant::RESOURCE_IDENTIFIER_REGEX . '/',
                new CaseInsensitiveUnique('user_custom_fields', 'field_name', $id),
            ],
            'type_id'         => ['string', 'required'],
            'edit_type'       => ['string', 'required'],
            'label'           => ['string', 'required'],
            'view_type'       => ['string', 'required'],
            'var_type'        => ['string', 'required'],
            'is_active'       => ['integer', 'sometimes'],
            'is_required'     => ['integer', 'sometimes'],
            'is_register'     => ['integer', 'sometimes'],
            'is_search'       => ['integer', 'sometimes'],
            'is_feed'         => ['integer', 'sometimes'],
            'has_label'       => ['int', 'sometimes'],
            'description'     => ['string', 'sometimes', 'nullable'],
            'has_description' => ['int', 'sometimes'],
            'extra'           => ['array', 'sometimes', 'nullable'],
        ];
    }
}
