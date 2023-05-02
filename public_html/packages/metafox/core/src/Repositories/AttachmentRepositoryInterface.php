<?php

namespace MetaFox\Core\Repositories;

use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Models\Attachment;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Attachment.
 * @mixin BaseRepository
 * @method Attachment getModel()
 * @method Attachment find($id, $columns = ['*'])()
 */
interface AttachmentRepositoryInterface
{
    /**
     * <pre>.
     *
     * Attachments array must contain attachment id and "status"
     *
     * status = "remove" : remove attachment from item
     * status = "create" : insert new attachment to item
     * </pre>
     *
     * @param array<mixed>       $attachments
     * @param HasTotalAttachment $item
     *
     * @return bool
     */
    public function updateItemId(?array $attachments, HasTotalAttachment $item): bool;
}
