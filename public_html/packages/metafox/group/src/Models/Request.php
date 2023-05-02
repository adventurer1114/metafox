<?php

namespace MetaFox\Group\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use MetaFox\Group\Database\Factories\RequestFactory;
use MetaFox\Group\Notifications\PendingRequestNotification;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\IsNotifyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Request.
 * @property        int            $id
 * @property        int            $status_id
 * @property        int            $group_id
 * @property        Group          $group
 * @property        Collection     $answers
 * @method   static RequestFactory factory(...$parameters)
 */
class Request extends Model implements
    Entity,
    IsNotifyInterface
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;

    public const ENTITY_TYPE = 'group_request';

    public const STATUS_PENDING  = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_DENIED   = 2;

    protected $table = 'group_requests';

    protected $fillable = [
        'status_id',
        'group_id',
        'user_id',
        'user_type',
    ];

    protected static function newFactory(): RequestFactory
    {
        return RequestFactory::new();
    }

    public function user(): MorphTo
    {
        return $this->morphTo('user', 'user_type', 'user_id')->withTrashed();
    }

    public function group(): HasOne
    {
        return $this->hasOne(Group::class, 'id', 'group_id')->withTrashed();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answers::class, 'request_id', 'id');
    }

    public function toNotification(): ?array
    {
        $group = $this->group;

        $users = [];

        if ($group instanceof Group) {
            $groupOwner = $group->user;
            if ($groupOwner instanceof User) {
                $users = [$groupOwner];
            }
        }

        return [$users, new PendingRequestNotification($this)];
    }
}
