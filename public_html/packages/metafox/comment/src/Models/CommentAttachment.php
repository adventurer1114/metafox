<?php

namespace MetaFox\Comment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Comment\Database\Factories\CommentAttachmentFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class CommentAttachment.
 * @property        int                      $id
 * @property        int                      $comment_id
 * @property        int                      $item_id
 * @property        string                   $item_type
 * @property        string                   $params
 * @method   static CommentAttachmentFactory factory(...$parameters)
 */
class CommentAttachment extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public $timestamps = false;

    public const ENTITY_TYPE = 'comment_attachments';

    public const TYPE_FILE    = 'storage_file';
    public const TYPE_STICKER = 'sticker';
    public const TYPE_PREVIEW = 'preview';
    public const TYPE_LINK    = 'link';

    protected $table = 'comment_attachments';

    /** @var string[] */
    protected $fillable = [
        'comment_id',
        'item_id',
        'item_type',
        'params',
    ];

    /**
     * @return CommentAttachmentFactory
     */
    protected static function newFactory(): CommentAttachmentFactory
    {
        return CommentAttachmentFactory::new();
    }
}

// end
