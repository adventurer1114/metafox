<?php

namespace MetaFox\Chat\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Chat\Models\Message;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Message.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface MessageRepositoryInterface
{
    public function viewMessages(array $attributes);

    public function viewMessage(User $context, int $id);

    /**
     * Create a room.
     *
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Message
     * @throws AuthorizationException
     * @see StoreBlockLayoutRequest
     */
    public function addMessage(User $context, array $attributes): Message;

    public function updateMessage(User $context, int $id, array $attributes): Message;

    public function getRoomLastMessage(int $userId, int $roomId): Message|null;

    public function reactMessage(User $context, int $id, array $param);

    public function normalizeReactions(array|null $reactions);
}
