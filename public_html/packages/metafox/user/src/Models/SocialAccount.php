<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\User\Database\Factories\SocialAccountFactory;

/**
 * Class SocialAccount.
 *
 * @mixin Builder
 * @property int                  $id
 * @property string               $provider_user_id
 * @property string               $provider
 * @property int                  $user_id
 * @property string               $created_at
 * @property string               $updated_at
 * @property User                 $user
 * @method   SocialAccountFactory factory()
 */
class SocialAccount extends Model
{
    use HasEntity;
    use HasFactory;

    public const FACEBOOK = 'facebook';

    public const ENTITY_TYPE = 'social_account';

    protected $table = 'social_accounts';

    /** @var string[] */
    protected $fillable = [];

    protected static function newFactory(): SocialAccountFactory
    {
        return SocialAccountFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}

// end
