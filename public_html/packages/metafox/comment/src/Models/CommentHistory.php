<?php

namespace MetaFox\Comment\Models;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph as HasItemMorphModel;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class CommentHistory.
 *
 * @property int         $id
 * @property string      $content
 * @property string      $created_at
 * @property string      $updated_at
 * @property string      $params
 * @property string|null $image_path
 * @property string      $server_id
 * @property string      $phrase
 * @property string      $text_parsed
 */
class CommentHistory extends Model implements
    HasItemMorph,
    Entity
{
    use HasEntity;
    use HasUserMorph;
    use HasItemMorphModel;

    public const ENTITY_TYPE = 'comment_histories';

    protected $table = 'comment_histories';

    public const PHRASE_COLUMNS_ADDED   = 'comment_add_photo';
    public const PHRASE_COLUMNS_DELETED = 'comment_delete_photo';
    public const PHRASE_COLUMNS_UPDATED = 'comment_update_photo';
    /**
     * @var array<string, mixed>
     */
    protected $casts = [
        'tagged_user_ids' => 'array',
    ];
    /** @var string[] */
    protected $fillable = [
        'comment_id',
        'user_id',
        'user_type',
        'item_id',
        'item_type',
        'content',
        'params',
        'phrase',
        'tagged_user_ids',
        'created_at',
    ];

    public function getTextParsedAttribute(): string
    {
        return $this->content;
    }
}

// end
