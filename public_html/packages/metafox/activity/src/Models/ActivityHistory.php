<?php

namespace MetaFox\Activity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class ActivityHistory.
 *
 * @property int    $id
 * @property mixed  $phrase
 * @property mixed  $extra
 * @property string $content
 * @property int    $feed_id
 * @property mixed  $created_at
 */
class ActivityHistory extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;

    public const ENTITY_TYPE = 'activity_history';

    protected $table = 'activity_histories';

    /** @var string[] */
    protected $fillable = [
        'feed_id',
        'user_id',
        'user_type',
        'content',
        'phrase',
        'extra',
        'created_at',
    ];

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class, 'feed_id', 'id');
    }
}

// end
