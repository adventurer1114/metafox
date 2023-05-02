<?php

namespace MetaFox\Profile\Http\Requests\v1\Field\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CaseInsensitiveUnique;
use MetaFox\Profile\Http\Resources\v1\Field\Admin\CreateFieldForm;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Profile\Http\Controllers\Api\v1\FieldAdminController::store
 * stub: /packages/requests/api_action_request.stub
 */

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
            'section_id' => ['int', 'sometimes', 'nullable'],
            'field_name' => [
                'string', 'required', 'regex:/' . MetaFoxConstant::RESOURCE_IDENTIFIER_REGEX . '/',
                new CaseInsensitiveUnique('user_custom_fields', 'field_name'),
                'max:' . CreateFieldForm::MAX_NAME_LENGTH,
            ],
            'type_id'         => ['string', 'required'],
            'edit_type'       => ['string', 'required'],
            'view_type'       => ['string', 'required'],
            'var_type'        => ['string', 'required'],
            'is_active'       => ['int', 'sometimes'],
            'is_required'     => ['int', 'sometimes'],
            'is_register'     => ['int', 'sometimes'],
            'is_search'       => ['int', 'sometimes'],
            'is_feed'         => ['int', 'sometimes'],
            'label'           => ['string', 'required'],
            'description'     => ['string', 'sometimes'],
            'has_label'       => ['int', 'sometimes'],
            'has_description' => ['int', 'sometimes'],
            'extra'           => ['array', 'sometimes', 'nullable'],
        ];
    }
}
