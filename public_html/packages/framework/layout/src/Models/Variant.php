<?php

namespace MetaFox\Layout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\App\Models\Package;
use MetaFox\Layout\Database\Factories\VariantFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * class Variant.
 *
 * @property        int            $id
 * @method   static VariantFactory factory(...$parameters)
 * @property        string         $theme_id
 * @property        string         $variant_id
 * @property        string         $title
 * @property        string         $thumb_id
 * @property        string         $dark_thumb_id
 * @property        string         $module_id
 * @property        string         $package_id
 * @property        ?Package       $package
 * @property        string         $created_at
 * @property        string         $updated_at
 * @property        int            $is_active
 * @property        int            $is_system
 * @property        ?Theme         $theme
 */
class Variant extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'layout_variant';

    protected $table = 'layout_variants';

    /** @var string[] */
    protected $fillable = [
        'theme_id',
        'variant_id',
        'title',
        'thumb_id',
        'dark_thumb_id',
        'module_id',
        'is_active',
        'is_system',
        'package_id',
        'created_at',
        'updated_at',
    ];

    /** @var string[] */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * @return VariantFactory
     */
    protected static function newFactory()
    {
        return VariantFactory::new();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function theme()
    {
        return $this->hasOne(Theme::class, 'theme_id', 'theme_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function package()
    {
        return $this->hasOne(Package::class, 'package_id', 'name');
    }

    public function updateThemeVariant(string $themeId)
    {
        /** @var Theme $theme */
        $theme                = Theme::query()->where('theme_id', '=', $themeId)->firstOrFail();
        $theme->total_variant = self::query()->where('theme_id', '=', $themeId)->count();
        $theme->saveQuietly();
    }

    protected static function booted()
    {
        static::created(function (self $variant) {
            $variant->updateThemeVariant($variant->theme_id);
        });

        static::deleted(function (self $variant) {
            $variant->updateThemeVariant($variant->theme_id);
        });
    }
}

// end
