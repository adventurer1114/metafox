<?php

namespace MetaFox\ActivityPoint\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Database\Factories\PointTransactionFactory;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class PointTransaction.
 *
 * @mixin Builder
 *
 * @property int           $id
 * @property int           $module_id
 * @property string        $package_id
 * @property int           $type
 * @property string        $action
 * @property int           $points
 * @property bool          $is_hidden
 * @property array         $action_params
 * @property ?PointSetting $pointSetting
 * @property string        $created_at
 * @property string        $updated_at
 *
 * @method static PointTransactionFactory factory(...$parameters)
 */
class PointTransaction extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;
    use HasOwnerMorph;

    public const ENTITY_TYPE = 'activitypoint_transaction';

    protected $table = 'apt_transactions';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'module_id',
        'package_id',
        'type',
        'action',
        'points',
        'is_hidden',
        'is_admincp',
        'action_params',
        'point_setting_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_hidden'     => 'boolean',
        'action_params' => 'array',
    ];

    protected static function newFactory(): PointTransactionFactory
    {
        return PointTransactionFactory::new();
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(PointSetting::class, 'point_setting_id', 'id');
    }

    protected function isSubtracted(): Attribute
    {
        return Attribute::make(
            get: fn () => ActivityPoint::isSubtracted($this->type),
        );
    }

    protected function isAdded(): Attribute
    {
        return Attribute::make(
            get: fn () => ActivityPoint::isAdded($this->type),
        );
    }

    public function getActionAttribute(string $action): string
    {
        $actionParams = $this->action_params ?? [];

        return __p($action, $actionParams);
    }
}

// end
