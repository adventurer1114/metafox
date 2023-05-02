<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Traits\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Models\UserEntity;

/**
 * Trait HasOwnerMorph.
 *
 * @mixin HasRelationships
 * @property string          $user_type
 * @property int             $user_id
 * @property User|null       $user
 * @property UserEntity|null $userEntity
 */
trait HasUserMorph
{
    public function userType(): string
    {
        return $this->user_type;
    }

    public function userId(): int
    {
        return $this->user_id;
    }

    /**
     * @return MorphTo
     */
    public function user()
    {
        return $this->morphTo('user', 'user_type', 'user_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function userEntity()
    {
        return $this->belongsTo(UserEntity::class, 'user_id', 'id')->withTrashed();
    }

    /**
     * check if $user is the user (creator) of entity.
     *
     * @param  User $user
     * @return bool
     */
    public function isUser(User $user): bool
    {
        return $user->entityId() == $this->userId();
    }
}
