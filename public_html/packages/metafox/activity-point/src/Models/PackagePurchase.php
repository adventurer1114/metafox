<?php

namespace MetaFox\ActivityPoint\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\ActivityPoint\Database\Factories\PackagePurchaseFactory;
use MetaFox\Localize\Models\Currency;
use MetaFox\Payment\Contracts\IsBillable;
use MetaFox\Payment\Traits\BillableTrait;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class PackagePurchase.
 * @mixin Builder
 *
 * @property int          $id
 * @property PointPackage $package
 * @property int          $status
 * @property float        $price
 * @property Currency     $currency
 * @property int          $gateway_id
 * @property int          $points
 * @property string       $created_at
 * @property string       $updated_at
 *
 * @method static PackagePurchaseFactory factory(...$parameters)
 */
class PackagePurchase extends Model implements Entity, IsBillable
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;
    use BillableTrait;

    public const ENTITY_TYPE = 'activitypoint_package_purchase';

    public const STATUS_INIT = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_FAILED = 3;

    protected $table = 'apt_package_purchases';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'package_id',
        'status',
        'price',
        'currency_id',
        'gateway_id',
        'points',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): PackagePurchaseFactory
    {
        return PackagePurchaseFactory::new();
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(PointPackage::class, 'package_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'code');
    }

    public function toTitle(): string
    {
        return $this->package->title;
    }

    public function getTotal(): float
    {
        return $this->price;
    }

    public function getCurrency(): string
    {
        return $this->currency->code;
    }
}

// end
