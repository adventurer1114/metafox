<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Database\Factories\UserVerifyFactory;

/**
 * Class UserVerify.
 *
 * @property int     $id
 * @property string  $user_type
 * @property string  $user_id
 * @property ?string $expires_at
 * @property ?string $action
 * @property string  $hash_code
 * @property ?string $email
 *
 * @method static UserVerifyFactory factory(...$parameters)
 */
class UserVerify extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;

    public const ENTITY_TYPE = 'user_verify';

    protected $table = 'user_verify';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'email',
        'hash_code',
        'action',
        'expires_at',
        'user_id',
        'user_type',

    ];

    /**
     * @return UserVerifyFactory
     */
    protected static function newFactory()
    {
        return UserVerifyFactory::new();
    }
}

// end
