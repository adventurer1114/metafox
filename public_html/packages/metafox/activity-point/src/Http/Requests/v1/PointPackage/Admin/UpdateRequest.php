<?php

namespace MetaFox\ActivityPoint\Http\Requests\v1\PointPackage\Admin;

use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\ActivityPoint\Http\Controllers\Api\v1\PointPackageAdminController::update
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends StoreRequest
{
    protected function applyFileRule(array $rules): array
    {
        return array_merge($rules, [
            'file'           => ['sometimes', 'array'],
            'file.temp_file' => [
                'required_if:file.status,update', 'numeric', new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'file.status' => ['required_with:file', 'string', new AllowInRule(['update', 'remove'])],
        ]);
    }

    /**
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function handleFileData(array $data): array
    {
        $data['temp_file'] = Arr::get($data, 'file.temp_file', 0);
        $removeImage       = Arr::get($data, 'file.status', null);

        if (is_string($removeImage)) {
            $data['remove_image'] = true;
        }

        return $data;
    }
}
