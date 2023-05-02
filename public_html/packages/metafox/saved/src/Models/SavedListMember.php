<?php

namespace MetaFox\Saved\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Saved.
 *
 * @mixin Builder
 * @property int $id
 * @property int $list_id
 * @property int $user_id
 */
class SavedListMember extends Model implements Entity
{
    use HasEntity;

    public const ENTITY_TYPE = 'saved_list_member';

    protected $table = 'saved_list_members';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'list_id',
        'user_id',
    ];
}
