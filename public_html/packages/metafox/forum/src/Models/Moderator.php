<?php

namespace MetaFox\Forum\Models;

use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Moderator.
 *
 * @property int $id
 */
class Moderator extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'forum_moderator';

    protected $table = 'forum_moderator';

    /** @var string[] */
    protected $fillable = [
        'forum_id',
        'user_id',
        'user_type',
    ];

    public $timestamps = false;
}

// end
