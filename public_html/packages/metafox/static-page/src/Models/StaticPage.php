<?php

namespace MetaFox\StaticPage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\StaticPage\Database\Factories\StaticPageFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class StaticPage.
 *
 * @property int    $id
 * @property string $slug
 * @property string $title
 * @property string $text
 * @method   static StaticPageFactory factory(...$parameters)
 */
class StaticPage extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'static_page';

    protected $table = 'static_pages';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'module_id',
        'is_active',
        'is_phrase',
        'parse_php',
        'has_bookmark',
        'is_phrase',
        'full_size',
        'title',
        'text',
        'slug',
        'disallow_access',
        'total_view',
        'total_comment',
        'total_share',
        'total_tag',
        'total_attachment',
    ];

    /**
     * @return StaticPageFactory
     */
    protected static function newFactory()
    {
        return StaticPageFactory::new();
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl("{$this->slug}");
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl("{$this->slug}");
    }

    public function getDescriptionAttribute()
    {
        return substr($this->text,0,1000);
    }
}

// end
