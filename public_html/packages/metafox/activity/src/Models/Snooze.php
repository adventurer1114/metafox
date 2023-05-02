<?php

namespace MetaFox\Activity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use MetaFox\Activity\Database\Factories\SnoozeFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Snooze.
 * @mixin Builder
 * @property int    $id
 * @property int    $user_id
 * @property string $user_type
 * @property int    $owner_id
 * @property string $owner_type
 * @property bool   $is_system
 * @property bool   $is_snoozed
 * @property int    $is_snooze_forever
 * @property string $snooze_until
 * @property string $created_at
 * @property string $updated_at
 * @method   $this  expired()
 * @method   $this  subscription()
 * @method   static SnoozeFactory factory()
 */
class Snooze extends Model implements Entity
{
    use HasEntity;
    use HasOwnerMorph;
    use HasUserMorph;
    use HasFactory;

    public const FRIEND = 'friend';
    public const PAGE   = 'page';
    public const GROUP  = 'group';

    public const ENTITY_TYPE = 'activity_snooze';

    protected $table = 'activity_snoozes';

    protected $fillable = ['user_id', 'user_type', 'owner_id', 'owner_type', 'is_system', 'is_snoozed', 'snooze_until', 'is_snooze_forever'];

    /**
     * Add scope to filter which snoozes are expired.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeExpired(Builder $query)
    {
        return $query->whereDate('snooze_until', '<=', Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * Add scope to filter which snoozes has subscription.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSubscription(Builder $query)
    {
        return $query->leftJoin('activity_subscriptions as a', function (JoinClause $join) {
            $join->on('s.user_id', '=', 'a.user_id');
            $join->on('s.owner_id', '=', 'a.owner_id');
        });
    }

    /**
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            self::FRIEND,
            self::PAGE,
            self::GROUP,
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return SnoozeFactory::new();
    }
}
