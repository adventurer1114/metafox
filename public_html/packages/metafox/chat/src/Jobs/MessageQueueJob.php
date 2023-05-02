<?php

namespace MetaFox\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Chat\Broadcasting\UserMessage;
use MetaFox\Chat\Models\Message;

class MessageQueueJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Message $message;
    protected int $userId;
    protected string $broadcastType;

    public function __construct(Message $message, int $userId, string $broadcastType = Message::MESSAGE_CREATE)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->broadcastType = $broadcastType;
    }

    public function handle()
    {
        broadcast(new UserMessage($this->message, $this->userId, $this->broadcastType));
    }
}
