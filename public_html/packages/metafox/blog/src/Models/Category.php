<?php

namespace MetaFox\Blog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Blog\Database\Factories\CategoryFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasSubCategory;
use MetaFox\Platform\Contracts\HasTotalItem;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Category.
 * @mixin Builder
 * @property        int             $id
 * @property        string          $name
 * @property        string          $name_url
 * @property        bool            $is_active
 * @property        bool            $is_default
 * @property        int             $ordering
 * @property        int             $parent_id
 * @property        int             $total_item
 * @property        Category        $subCategories
 * @property        Category        $parentCategory
 * @property        string          $created_at
 * @property        string          $updated_at
 * @property        int             $level
 * @method   static CategoryFactory factory()
 */
class Category extends Model implements
    Entity,
    HasTotalItem,
    HasSubCategory
{
    use HasEntity;
    use HasFactory;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'blog_category';

    protected $table = 'blog_categories';

    public const IS_ACTIVE = 1;

    protected $fillable = [
        'name',
        'is_active',
        'ordering',
        'parent_id',
        'name_url',
        'total_item',
        'level',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(
            Blog::class,
            'blog_category_data',
            'category_id',
            'item_id'
        )
            ->using(CategoryData::class);
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo($this, 'parent_id', 'id');
    }

    public function toUrl(): ?string
    {
        return config('app.url') . "/blog/search?category_id={$this->id}";
    }

    public function toLink(): ?string
    {
        return config('app.url') . "/blog/search?category_id={$this->id}";
    }

    public function getIsDefaultAttribute(): bool
    {
        $categoryDefault = Settings::get('blog.default_category');

        return $this->entityId() == $categoryDefault;
    }

    public function toSubCategoriesLink(): string
    {
        return sprintf('/admincp/blog/category/%s/category/browse?parent_id=%s', $this->id, $this->id);
    }

    public function toSubCategoriesUrl(): string
    {
        return sprintf('/admincp/blog/category/%s/category/browse?parent_id=%s', $this->id, $this->id);
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }

    public function getAdminBrowseUrlAttribute()
    {
        if (!$this->parent_id) {
            return '/admincp/blog/category/browse';
        }

        return sprintf('/admincp/blog/category/%s/category/browse?parent_id=%s', $this->parent_id, $this->parent_id);
    }
}
