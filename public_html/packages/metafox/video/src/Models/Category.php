<?php

namespace MetaFox\Video\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasSubCategory;
use MetaFox\Platform\Contracts\HasTotalItem;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Video\Database\Factories\CategoryFactory;

/**
 * Class Category.
 * @mixin Builder
 * @property        int             $id
 * @property        int             $parent_id
 * @property        string          $name
 * @property        string          $name_url
 * @property        bool            $is_active
 * @property        bool            $is_default
 * @property        int             $ordering
 * @property        int             $total_item
 * @property        int             $level
 * @property        string          $created_at
 * @property        string          $updated_at
 * @property        Category        $subCategories
 * @property        Category        $parentCategory
 * @method   static CategoryFactory factory()
 */
class Category extends Model implements Entity, HasTotalItem, HasSubCategory
{
    use HasEntity;
    use HasFactory;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'video_category';
    public const IS_ACTIVE   = 1;

    protected $table = 'video_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'name_url',
        'parent_id',
        'is_active',
        'ordering',
        'total_item',
        'level',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * @return HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            'video_category_data',
            'category_id',
            'item_id'
        )
            ->using(CategoryData::class);
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl("video/search?category_id={$this->id}");
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl("video/search?category_id={$this->id}");
    }

    public function getIsDefaultAttribute(): bool
    {
        $categoryDefault = Settings::get('video.default_category');

        return $this->entityId() == $categoryDefault;
    }

    public function toSubCategoriesLink(): string
    {
        return url_utility()->makeApiUrl(sprintf('admincp/video/category/%s/category/browse?parent_id=%s', $this->entityId(), $this->entityId()));
    }

    public function toSubCategoriesUrl(): string
    {
        return url_utility()->makeApiFullUrl(sprintf(
            'admincp/video/category/%s/category/browse?parent_id=%s',
            $this->entityId(),
            $this->entityId()
        ));
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function getAdminBrowseUrlAttribute(): string
    {
        if (!$this->parent_id) {
            return '/admincp/video/category/browse';
        }

        return sprintf('/admincp/video/category/%s/category/browse?parent_id=%s', $this->parent_id, $this->parent_id);
    }
}
