<?php

namespace MetaFox\Activity\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;

class MigrateFeedContent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $typeIdList = ['activity_post'];

        foreach ($typeIdList as $key => $typeId) {
            $feeds = resolve(FeedRepositoryInterface::class)->getMissingContentFeed($typeId);

            if ($feeds->isEmpty()) {
                if ($key == count($typeIdList) - 1) {
                    return;
                }

                continue;
            }

            $feedMaps = [];
            $itemIds  = [];
            $itemMaps = [];

            foreach ($feeds as $feed) {
                if (!$feed instanceof Feed) {
                    continue;
                }

                $feedMaps[$feed->entityId()] = $feed;
                $itemIds[]                   = $feed->itemId();
            }

            $modelClass = Relation::getMorphedModel($typeId);
            $items      = $modelClass::query()
                ->whereIn('id', $itemIds)
                ->get(['id', 'content'])
                ->toArray();

            array_map(function ($item) use (&$itemMaps) {
                $itemMaps[$item['id']] = $item['content'] ?? '';
            }, $items);

            $feedData = [];

            foreach ($feedMaps as $feedValue) {
                $itemId = $feedValue->itemId();

                if (!isset($itemMaps[$itemId])) {
                    continue;
                }

                $feedData[] = array_merge($feedValue->toArray(), [
                    'content' => $itemMaps[$itemId] ?? '',
                ]);
            }

            Feed::query()->upsert($feedData, ['id']);
        }

        self::dispatch();
    }
}
