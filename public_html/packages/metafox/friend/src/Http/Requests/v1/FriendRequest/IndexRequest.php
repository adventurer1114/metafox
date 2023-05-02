<?php

namespace MetaFox\Friend\Http\Requests\v1\FriendRequest;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Friend\Support\Browse\Scopes\FriendRequest\ViewScope;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\PaginationLimitRule;

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
            'view'  => ['sometimes', 'string', new AllowInRule(ViewScope::getAllowView())],
            'page'  => ['sometimes', 'numeric', 'min:1'],
            'limit' => ['sometimes', 'numeric', new PaginationLimitRule()],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (empty($data['view'])) {
            $data['view'] = ViewScope::VIEW_SEND;
        }

        if (!isset($data['limit'])) {
            $limitSetting = Settings::get('friend.friend_request_total', 10);

            if ($limitSetting == 0) {
                $limitSetting = 10;
            }

            $data['limit'] = $limitSetting;
        }

        return $data;
    }
}
