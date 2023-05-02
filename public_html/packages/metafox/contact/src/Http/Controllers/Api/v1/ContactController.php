<?php

namespace MetaFox\Contact\Http\Controllers\Api\v1;

use Exception;
use Illuminate\Http\JsonResponse;
use MetaFox\Contact\Http\Requests\v1\Contact\StoreRequest;
use MetaFox\Contact\Support\Facades\Contact;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

/**
 * Class ContactController.
 * @codeCoverageIgnore
 * @ignore
 */
class ContactController extends ApiController
{
    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();
        try {
            Contact::send($params);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success([], [], __p('contact::phrase.contact_message_successfully_sent'));
    }
}
