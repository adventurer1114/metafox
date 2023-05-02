<?php

namespace MetaFox\ActivityPoint\Http\Requests\v1\PointPackage\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Rules\ValidPackageThumbnailRule;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\ActivityPoint\Http\Controllers\Api\v1\PointPackageAdminController::store
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
    public function rules(Request $request): array
    {
        $priceRules = app('currency')->rules('price', ['required', 'integer']);

        $rules = [
            'title'     => ['required', 'string'],
            'amount'    => ['required', 'numeric', 'digits_between:1,7'],
            'is_active' => ['sometimes', 'numeric', 'nullable', new AllowInRule([0, 1])],
        ];

        $rules = array_merge($priceRules, $rules);

        return $this->applyFileRule($rules);
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $price = Arr::get($data, 'price', []);

        if (!is_array($price)) {
            $price = [];
        }

        $data['price'] = $price;

        $data = $this->handleFileData($data);

        $data['is_active'] = Arr::get($data, 'is_active', 1);

        return $data;
    }

    /**
     * @param  array<string, mixed> $rules
     * @return array<string, mixed>
     */
    protected function applyFileRule(array $rules): array
    {
        return array_merge($rules, [
            'file' => ['sometimes', resolve(ValidPackageThumbnailRule::class)],
        ]);
    }

    /**
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function handleFileData(array $data): array
    {
        $data['temp_file'] = Arr::get($data, 'file.temp_file', 0);

        return $data;
    }
}
