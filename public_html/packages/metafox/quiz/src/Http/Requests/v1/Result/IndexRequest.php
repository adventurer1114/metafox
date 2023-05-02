<?php

namespace MetaFox\Quiz\Http\Requests\v1\Result;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\PaginationLimitRule;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Quiz\Models\Quiz;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Quiz\Http\Controllers\Api\v1\ResultController::index;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class IndexRequest.
 */
class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'quiz_id' => ['required', 'numeric', 'min:1', sprintf('exists:%s,id', Quiz::class)],
            'page'    => ['sometimes', 'numeric', 'min:1'],
            'limit'   => ['sometimes', 'numeric', new PaginationLimitRule()],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['limit'])) {
            $data['limit'] = Pagination::DEFAULT_ITEM_PER_PAGE;
        }

        return $data;
    }
}
