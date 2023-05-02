<?php

namespace MetaFox\Importer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Comment\Models\Comment;
use MetaFox\Like\Models\Like;
use MetaFox\Like\Models\LikeAgg;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;

class MigrateChunkingLikeComment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected array $photoGroupIds = [])
    {
    }

    public function handle(): void
    {
        if (!count($this->photoGroupIds)) {
            return;
        }

        $photoGroups = PhotoGroup::query()
            ->whereIn('id', $this->photoGroupIds)
            ->get();

        if (!$photoGroups->count()) {
            return;
        }

        foreach ($photoGroups as $photoGroup) {
            if (!$photoGroup instanceof PhotoGroup) {
                continue;
            }

            $photoGroupItem = $photoGroup->items()->first() ?? null;
            if (!$photoGroupItem instanceof PhotoGroupItem) {
                continue;
            }

            $photo = $photoGroupItem->detail ?? null;
            if (!$photo instanceof Photo) {
                continue;
            }

            Comment::query()
                ->where([
                    'item_id'   => $photo->entityId(),
                    'item_type' => $photo->entityType(),
                ])
                ->update([
                    'item_id'   => $photoGroup->entityId(),
                    'item_type' => $photoGroup->entityType(),
                ]);

            LikeAgg::query()
                ->where([
                    'item_id'   => $photo->entityId(),
                    'item_type' => $photo->entityType(),
                ])
                ->update([
                    'item_id'   => $photoGroup->entityId(),
                    'item_type' => $photoGroup->entityType(),
                ]);

            Like::query()
                ->where([
                    'item_id'   => $photo->entityId(),
                    'item_type' => $photo->entityType(),
                ])
                ->update([
                    'item_id'   => $photoGroup->entityId(),
                    'item_type' => $photoGroup->entityType(),
                ]);
        }
    }
}
