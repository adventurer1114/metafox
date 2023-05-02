<?php

namespace MetaFox\Chat\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Chat\Http\Requests\v1\Message\IndexRequest;
use MetaFox\Chat\Http\Requests\v1\Message\ReactRequest;
use MetaFox\Chat\Http\Requests\v1\Message\StoreRequest;
use MetaFox\Chat\Http\Requests\v1\Message\UpdateRequest;
use MetaFox\Chat\Http\Resources\v1\Message\MessageDetail;
use MetaFox\Chat\Http\Resources\v1\Message\MessageItemCollection;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

class MessageController extends ApiController
{
    /**
     * @var MessageRepositoryInterface
     */
    private MessageRepositoryInterface $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $messages = $this->messageRepository->viewMessages($params);

        return $this->success(new MessageItemCollection($messages));
    }

    public function show(int $id)
    {
        $context = user();

        $message = $this->messageRepository->viewMessage($context, $id);

        return $this->success(new MessageDetail($message));
    }

    public function store(StoreRequest $request)
    {
        $context = user();

        $params = $request->validated();
        $message = $this->messageRepository->addMessage($context, $params);

        return $this->success(new MessageDetail($message), [], '');
    }

    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $context = user();

        $params = $request->validated();
        $message = $this->messageRepository->updateMessage($context, $id, $params);

        return $this->success(new MessageDetail($message), [], '');
    }

    public function removeMessage(int $id)
    {
        $context = user();

        $message = $this->messageRepository->updateMessage($context, $id, ['type' => 'delete']);

        return $this->success(new MessageDetail($message), [], '');
    }

    public function reactMessage(ReactRequest $request, int $id)
    {
        $context = user();
        $params = $request->validated();

        $message = $this->messageRepository->reactMessage($context, $id, $params);

        return $this->success(new MessageDetail($message), [], '');
    }
}
