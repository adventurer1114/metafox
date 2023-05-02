<?php

namespace MetaFox\Storage\Http\Requests\v1\Disk\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Storage\Http\Controllers\Api\v1\DiskAdminController::awsS3
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateSftpDiskRequest.
 */
class UpdateSftpDiskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'host'            => 'required|string',
            'port'            => 'sometimes|int|nullable',
            'timeout'         => 'sometimes|int|nullable',
            'maxTries'         => 'sometimes|int|nullable',
            'username'        => 'required|string',
            'password'        => 'sometimes|string|nullable',
            'root'            => 'sometimes|string|nullable',
            'hostFingerprint' => 'sometimes|string|nullable',
            'throw'           => 'sometimes|boolean|nullable',
            'passive'         => 'sometimes|boolean|nullable',
            'useAgent'        => 'sometimes|boolean|nullable',
            'driver'          => 'required|string',
            'visibility'      => 'sometimes|string|nullable',
        ];
    }
}
