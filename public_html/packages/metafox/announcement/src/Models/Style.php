<?php

namespace MetaFox\Announcement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Announcement\Database\Factories\StyleFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Style.
 *
 * @mixin Builder
 *
 * @property int    $id
 * @property string $name
 * @property string $icon_image
 * @property string $icon_font
 * @method   static StyleFactory factory()
 */
class Style extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'announcement_style';

    protected $table = 'announcement_styles';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'name',
        'icon_image',
        'icon_font',
    ];

    /**
     * @return StyleFactory
     */
    protected static function newFactory(): StyleFactory
    {
        return StyleFactory::new();
    }
}

// end
