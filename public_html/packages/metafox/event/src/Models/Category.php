<?php

namespace MetaFox\Event\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Event\Database\Factories\CategoryFactory;
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
 * @property        int             $ordering
 * @property        int             $parent_id
 * @property        int             $total_item
 * @property        bool            $is_default
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
    use HasFactory;
    use HasEntity;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'event_category';

    protected $table = 'event_categories';

    public const IS_ACTIVE = 1;

    protected $fillable = [
        'name',
        'is_active',
        'ordering',
        'parent_id',
        'name_url',
        'level',
    ];

    /**
     * @param  array<mixed>    $parameters
     * @return CategoryFactory
     */
    public static function newFactory(array $parameters = []): CategoryFactory
    {
        return CategoryFactory::new($parameters);
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(
            Event::class,
            'event_category_data',
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
        return url_utility()->makeApiFullUrl("event/search?category_id={$this->id}");
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl("event/search?category_id={$this->id}");
    }

    public function getIsDefaultAttribute(): bool
    {
        $categoryDefault = Settings::get('event.default_category');

        return $this->entityId() == $categoryDefault;
    }

    public function toSubCategoriesLink(): string
    {
        return url_utility()->makeApiUrl(sprintf('admincp/event/category/%s/category/browse?parent_id=%s', $this->entityId(), $this->entityId()));
    }

    public function toSubCategoriesUrl(): string
    {
        return url_utility()->makeApiFullUrl(sprintf(
            'admincp/event/category/%s/category/browse?parent_id=%s',
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
            return '/admincp/event/category/browse';
        }

        return sprintf('/admincp/event/category/%s/category/browse?parent_id=%s', $this->parent_id, $this->parent_id);
    }
}
