<?php

namespace MetaFox\Photo\Listeners;

use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Platform\Contracts\Entity;

class ProxyItemListener
{
    public function handle(Entity $entity): ?array
    {
        if (!$entity instanceof PhotoGroup) {
            return null;
        }

        $returnThis = false;

        if ($entity->total_item > 1) {
            $returnThis = true;
        }

        $items = $entity->items()->get();

        if ($items->count() > 1) {
            $returnThis = true;
        }

        if ($items->count() == 0) {
            $returnThis = true;
        }

        $item = $items->first();

        if (null === $item->detail) {
            $returnThis = true;
        }

        if ($returnThis) {
            return [
                'alternative_item_type' => $entity->entityType(),
                'alternative_item_id'   => $entity->entityId(),
            ];
        }

        return [
            'alternative_item_type' => $item->detail->entityType(),
            'alternative_item_id'   => $item->detail->entityId(),
        ];
    }
}
