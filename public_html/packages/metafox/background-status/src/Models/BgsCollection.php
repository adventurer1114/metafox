<?php

namespace MetaFox\BackgroundStatus\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\BackgroundStatus\Database\Factories\BgsCollectionFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;

/**
 * Class BgsCollection.
 * @property int           $id
 * @property string        $title
 * @property int           $main_background_id
 * @property int           $is_active
 * @property int           $is_default
 * @property int           $is_deleted
 * @property int           $total_background
 * @property int           $view_only
 * @property string        $created_at
 * @property Collection    $backgrounds
 * @property BgsBackground $mainBackground
 *
 * @method static BgsCollectionFactory factory(...$parameters)
 * @mixin Builder
 */
class BgsCollection extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasAmountsTrait;
    use HasNestedAttributes;

    public const ENTITY_TYPE = 'pstatusbg_collection';

    public const IS_DELETED   = 1;
    public const IS_DEFAULT   = 1;
    public const IS_ACTIVE    = 1;
    public const IS_VIEW_ONLY = 1;

    protected $table = 'bgs_collections';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'title',
        'main_background_id',
        'is_active',
        'is_default',
        'is_deleted',
        'total_background',
        'view_only',
    ];

    /**
     * @var array<string>|array<string, mixed>
     */
    public array $nestedAttributes = [
        'backgrounds',
    ];

    /**
     * @return BgsCollectionFactory
     */
    protected static function newFactory(): BgsCollectionFactory
    {
        return BgsCollectionFactory::new();
    }

    public function backgrounds(): HasMany
    {
        return $this->hasMany(BgsBackground::class, 'collection_id', 'id');
    }

    public function mainBackground(): BelongsTo
    {
        return $this->belongsTo(BgsBackground::class, 'main_background_id', 'id');
    }

    public function getAdminBrowseUrlAttribute()
    {
        return '/admincp/bgs/collection/browse';
    }

    public function getAdminEditUrlAttribute()
    {
        return "/admincp/bgs/collection/edit/{$this->id}";
    }
}
