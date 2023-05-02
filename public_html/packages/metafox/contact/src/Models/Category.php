<?php

namespace MetaFox\Contact\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Models/Category.stub.
 */

/**
 * Class Category.
 *
 * @mixin Builder
 * @property int      $id
 * @property string   $name
 * @property string   $name_url
 * @property bool     $is_active
 * @property int      $ordering
 * @property int      $parent_id
 * @property int      $total_item
 * @property Category $subCategories
 * @property Category $parentCategory
 * @property string   $created_at
 * @property string   $updated_at
 */
class Category extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'contact_category';

    protected $table = 'contact_categories';

    public const IS_ACTIVE = 1;

    protected $fillable = [
        'name',
        'is_active',
        'ordering',
        'parent_id',
        'name_url',
    ];

    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo($this, 'parent_id', 'id');
    }

    public function toUrl(): ?string
    {
        return config('app.url') . "/contact/search?category_id={$this->id}";
    }

    public function toLink(): ?string
    {
        return config('app.url') . "/contact/search?category_id={$this->id}";
    }

    public function toSubCategoriesLink(): string
    {
        return sprintf('/admincp/contact/category/%s/category/browse?parent_id=%s', $this->id, $this->id);
    }

    public function toSubCategoriesUrl(): string
    {
        return sprintf('/admincp/contact/category/%s/category/browse?parent_id=%s', $this->id, $this->id);
    }

    public function getAdminBrowseUrlAttribute()
    {
        if (!$this->parent_id) {
            return '/admincp/contact/category/browse';
        }

        return sprintf('/admincp/contact/category/%s/category/browse?parent_id=%s', $this->parent_id, $this->parent_id);
    }
}
