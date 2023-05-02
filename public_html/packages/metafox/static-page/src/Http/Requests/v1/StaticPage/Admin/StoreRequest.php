<?php

namespace MetaFox\StaticPage\Http\Requests\v1\StaticPage\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\UniqueSlug;
use MetaFox\StaticPage\Models\StaticPage;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\StaticPage\Http\Controllers\Api\v1\StaticPageAdminController::store
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
            'slug' => [
                'string',
                'required',
                new UniqueSlug(StaticPage::ENTITY_TYPE),
            ],
            'title' => ['string', 'required'],
            'text'  => ['string', 'required'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data    = parent::validated($key, $default);
        $context = user();

        $data['user_id']         = $context->entityId();
        $data['user_type']       = $context->entityType();
        $data['owner_id']        = $context->entityId();
        $data['owner_type']      = $context->entityType();
        $data['disallow_access'] = '';

        return $data;
    }
}
