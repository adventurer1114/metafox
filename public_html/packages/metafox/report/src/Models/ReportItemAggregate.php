<?php

namespace MetaFox\Report\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Report\Database\Factories\ReportItemAggregateFactory;
use MetaFox\Platform\Contracts\User;

/**
 * Class ReportItemAggregate.
 * @mixin Builder
 *
 * @property User|null $last_user
 * @property int       $total_reports
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @method static ReportItemAggregateFactory factory(...$parameters)
 */
class ReportItemAggregate extends Model implements Entity, HasAmounts
{
    use HasEntity;
    use HasFactory;
    use HasItemMorph;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'report_item_aggregate';

    protected $table = 'report_item_aggregate';

    /** @var string[] */
    protected $fillable = [
        'item_id',
        'item_type',
        'last_user_id',
        'last_user_type',
        'total_reports',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): ReportItemAggregateFactory
    {
        return ReportItemAggregateFactory::new();
    }

    public function last_user(): MorphTo
    {
        return $this->morphTo('last_user', 'last_user_type', 'last_user_id')->withTrashed();
    }
}

// end
