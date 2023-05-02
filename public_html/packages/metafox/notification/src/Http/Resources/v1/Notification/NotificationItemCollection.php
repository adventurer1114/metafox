<?php

namespace MetaFox\Notification\Http\Resources\v1\Notification;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationItemCollection extends ResourceCollection
{
    public $collects = NotificationItem::class;

    /**
     * toArray.
     *
     * @param  mixed        $request
     * @return array<mixed>
     */
    public function toArray($request): array
    {
        return $this->collection->map(function ($item) use ($request) {
            try {
                return $item->toArray($request);
            } catch (Throwable $e) {
                // silent
                Log::error($e);
            }
        })->filter()->toArray();
    }
}
