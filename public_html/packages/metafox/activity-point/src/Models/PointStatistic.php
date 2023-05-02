<?php

namespace MetaFox\ActivityPoint\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\ActivityPoint\Database\Factories\PointStatisticFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\User\Models\UserEntity;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class PointStatistic.
 *
 * @mixin Builder
 *
 * @property int         $id
 * @property ?UserEntity $userEntity
 * @property int         $current_points
 * @property int         $total_earned
 * @property int         $total_bought
 * @property int         $total_sent
 * @property int         $total_spent
 * @property int         $total_received
 * @property int         $total_retrieved
 *
 * @method static PointStatisticFactory factory($count = null, $state = [])
 */
class PointStatistic extends Model implements Entity, HasAmounts
{
    use HasEntity;
    use HasAmountsTrait;
    use HasFactory;

    public const ENTITY_TYPE = 'activitypoint_statistic';

    public $incrementing = false;

    protected $table = 'apt_statistics';

    /**
     * @var string[]
     */
    protected $with = ['userEntity'];

    /**
     * @var string[]
     */
    protected $casts = [
        'current_points'  => 'integer',
        'total_earned'    => 'integer',
        'total_bought'    => 'integer',
        'total_sent'      => 'integer',
        'total_spent'     => 'integer',
        'total_received'  => 'integer',
        'total_retrieved' => 'integer',
    ];

    /** @var string[] */
    protected $fillable = [
        'id',
        'current_points',
        'total_earned',
        'total_bought',
        'total_sent',
        'total_spent',
        'total_received',
        'total_retrieved',
    ];

    /**
     * @return BelongsTo
     */
    public function userEntity(): BelongsTo
    {
        return $this->belongsTo(UserEntity::class, 'id', 'id')->withTrashed();
    }

    public function updateTotalEarned(int $amount): int
    {
        return $this->incrementAmount('total_earned', $amount);
    }

    public function updateTotalBought(int $amount): int
    {
        return $this->incrementAmount('total_bought', $amount);
    }

    public function updateTotalSent(int $amount): int
    {
        return $this->incrementAmount('total_sent', $amount);
    }

    public function updateTotalSpent(int $amount): int
    {
        return $this->incrementAmount('total_spent', $amount);
    }

    public function updateTotalReceived(int $amount): int
    {
        return $this->incrementAmount('total_received', $amount);
    }

    public function updateTotalRetrieved(int $amount): int
    {
        return $this->incrementAmount('total_retrieved', $amount);
    }

    protected static function newFactory(): PointStatisticFactory
    {
        return PointStatisticFactory::new();
    }
}

// end
