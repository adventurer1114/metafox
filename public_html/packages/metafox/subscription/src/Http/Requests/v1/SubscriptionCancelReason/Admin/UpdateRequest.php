<?php

namespace MetaFox\Subscription\Http\Requests\v1\SubscriptionCancelReason\Admin;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionCancelReasonAdminController::update
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends StoreRequest
{
    public function validated($key = null, $default = null): array
    {
        return $this->validator->validated();
    }
}
