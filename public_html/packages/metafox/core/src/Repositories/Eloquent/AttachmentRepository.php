<?php

namespace MetaFox\Core\Repositories\Eloquent;

use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Models\Attachment;
use MetaFox\Core\Repositories\AttachmentRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * @method Attachment getModel()
 * @method Attachment find($id, $columns = ['*'])()
 */
class AttachmentRepository extends AbstractRepository implements AttachmentRepositoryInterface
{
    public function model(): string
    {
        return Attachment::class;
    }

    public function updateItemId(?array $attachments, HasTotalAttachment $item): bool
    {
        if (empty($attachments)) {
            return true;
        }

        $attachments = collect($attachments)->groupBy('status');

        // Getting new attachments by 'create' status
        $newAttachments = $attachments->get('create');
        $newIds         = $newAttachments ? $newAttachments->pluck('id')->toArray() : [];

        // Getting removed attachments by 'remove' status
        $removedAttachments = $attachments->get('remove');
        $removeIds          = $removedAttachments ? $removedAttachments->pluck('id')->toArray() : [];

        if (!empty($newIds)) {
            foreach ($newIds as $newId) {
                $this->getModel()->newQuery()
                    ->where('id', $newId)
                    ->update(['item_id' => $item->entityId()]);
            }
        }

        if (!empty($removeIds)) {
            $this->deleteAttachments($removeIds);
        }

        if (!empty($newIds) || !empty($removeIds)) {
            $this->updateTotalAttachment($item);
        }

        return true;
    }

    /**
     * @param HasTotalAttachment $item
     */
    private function updateTotalAttachment(HasTotalAttachment $item): void
    {
        $totalAttachment = $this->getModel()->newQuery()
            ->where('item_id', $item->entityId())
            ->where('item_type', $item->entityType())
            ->count('id');

        $item->update(['total_attachment' => $totalAttachment]);
    }

    /**
     * @param int[] $ids
     *
     * @return bool
     */
    private function deleteAttachments(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }

        // boot to attachment to delete file later.

        $this->getModel()->newQuery()
            ->whereIn('id', $ids)
            ->delete();

        return true;
    }
}
