<?php

namespace MetaFox\ActivityPoint\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use MetaFox\ActivityPoint\Database\Factories\PointPackageFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class PointPackage.
 * @mixin Builder
 *
 * @property int        $id
 * @property string     $title
 * @property string     $image_path
 * @property string     $server_id
 * @property int        $amount
 * @property array      $price
 * @property bool       $is_active
 * @property int        $total_purchase
 * @property int        $image_file_id
 * @property string     $created_at
 * @property string     $updated_at
 * @property Collection $purchases
 *
 * @method static PointPackageFactory factory(...$parameters)
 */
class PointPackage extends Model implements
    Entity,
    HasAmounts,
    HasThumbnail
{
    use HasEntity;
    use HasFactory;
    use HasAmountsTrait;
    use HasThumbnailTrait;

    public const ENTITY_TYPE = 'activitypoint_package';

    public const MAXIMUM_PACKAGE_TITLE = 50;

    protected $table = 'apt_packages';

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'array',
    ];

    /** @var string[] */
    protected $fillable = [
        'title',
        'image_file_id',
        'amount',
        'price',
        'is_active',
        'total_purchase',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): PointPackageFactory
    {
        return PointPackageFactory::new();
    }

    public function getThumbnail(): ?string
    {
        return $this->image_file_id;
    }

    public function incrementTotalPurchase(int $amount = 1): int
    {
        return $this->incrementAmount('total_purchase', $amount);
    }

    public function getAdminEditUrlAttribute()
    {
        return sprintf('/admincp/activitypoint/package/edit/' . $this->id);
    }

    public function getAdminBrowseUrlAttribute()
    {
        return sprintf('/admincp/activitypoint/package/browse');
    }
}

// end
