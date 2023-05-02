<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\User\Database\Factories\UserRelationFactory;

/**
 * Class UserRelation.
 *
 * @property int                 $id
 * @method   UserRelationFactory factory(...$parameters)
 */
class UserRelation extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_relation';

    protected $table = 'user_relation';

    /** @var string[] */
    protected $fillable = [
        'phrase_var',
        'confirm',
        'updated_at',
        'created_at',
    ];

    /**
     * @return UserRelationFactory
     */
    protected static function newFactory()
    {
        return UserRelationFactory::new();
    }
}

// end
