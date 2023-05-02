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
 * @link \MetaFox\StaticPage\Http\Controllers\Api\v1\StaticPageAdminController::update
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
        $id = $this->route('page');

        return [
            'slug' => [
                'string',
                'required',
                new UniqueSlug(StaticPage::ENTITY_TYPE, $id),
            ],
            'title' => ['string', 'required'],
            'text'  => ['string', 'required'],
        ];
    }
}
