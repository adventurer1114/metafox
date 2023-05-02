<?php

namespace MetaFox\Like\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MetaFox\Like\Database\Factories\ReactionFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Reaction.
 *
 * @property string      $title
 * @property string      $icon_path
 * @property string      $color
 * @property string      $server_id
 * @property string|null $icon
 * @property string|null $icon_mobile
 * @property int         $ordering
 * @property int         $is_active
 * @property int         $is_default
 * @property string      $created_at
 * @property string      $updated_at
 * @method   static      ReactionFactory factory(...$parameters)
 *
 * @mixin Builder
 */
class Reaction extends Model implements Entity
{
    use HasFactory;
    use HasEntity;

    protected $table = 'like_reactions';

    public const ENTITY_TYPE = 'preaction';

    public const IS_ACTIVE  = 1;
    public const IS_DEFAULT = 1;

    protected $fillable = [
        'title',
        'is_active',
        'icon_path',
        'color',
        'server_id',
        'ordering',
        'is_default',
    ];

    protected static function newFactory(): ReactionFactory
    {
        return ReactionFactory::new();
    }

    public function getTitleAttribute(string $value): string
    {
        return __p($value);
    }

    public function getIconAttribute(): string
    {
        return app('storage')->disk('asset')->url($this->icon_path);
    }

    public function getIconMobileAttribute(): ?string
    {
        $iconPath = $this->icon;

        return Str::replace('.svg', '.png', $iconPath);
    }
}
