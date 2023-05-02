<?php

namespace MetaFox\SEO\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\SEO\Database\Factories\MetaFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Meta.
 * @property        string      $name
 * @property        string      $module_id
 * @property        string      $package_id
 * @property        string      $resolution
 * @property        ?string     $secondary_menu
 * @property        ?string     $menu
 * @property        ?string     $breadcrumbs
 * @property        ?string     $chunk
 * @property        string      $title
 * @property        ?string     $phrase_title
 * @property        ?string     $phrase_heading
 * @property        ?string     $phrase_keywords
 * @property        ?string     $phrase_description
 * @property        string      $description
 * @property        string      $keywords
 * @property        string      $heading
 * @property        ?string     $item_type
 * @property        int         $id
 * @property        int         $custom_sharing_route
 * @method   static MetaFactory factory(...$parameters)
 */
class Meta extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'core_seo_meta';

    protected $table = 'core_seo_meta';

    /** @var string[] */
    protected $fillable = [
        'name',
        'module_id',
        'package_id',
        'phrase_heading',
        'phrase_title',
        'phrase_keywords',
        'phrase_description',
        'menu',
        'url',
        'item_type',
        'custom_sharing_route',
        'page_type',
        'secondary_menu',
        'resolution',
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if (!$model->resolution) {
                $model->resolution = str_starts_with($model->name, 'admin.') ? 'admin' : 'web';
            }
            if (!$model->module_id) {
                $model->module_id = PackageManager::getAlias($model->package_id);
            }
            if ('admin' == $model->resolution) {
                $model->phrase_keywords    = null;
                $model->phrase_description = null;
                $model->phrase_heading     = null;
            }
        });
    }

    /**
     * @return MetaFactory
     */
    protected static function newFactory()
    {
        return MetaFactory::new();
    }

    public function getTitleAttribute(): ?string
    {
        return $this->phrase_title ? app('phrases')->translationOf($this->phrase_title) : null;
    }

    public function getHeadingAttribute(): ?string
    {
        return $this->phrase_heading ? app('phrases')->translationOf($this->phrase_heading) : null;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->phrase_description ? app('phrases')->translationOf($this->phrase_description) : null;
    }

    public function getKeywordsAttribute(): ?string
    {
        return $this->phrase_keywords ? app('phrases')->translationOf($this->phrase_keywords) : null;
    }

    public function getMetaKeywords()
    {
        return $this->phrase_keywords ? __p($this->phrase_keywords) : null;
    }

    public function getMetaDescription()
    {
        return $this->phrase_description ? __p($this->phrase_description) : null;
    }
}

// end
