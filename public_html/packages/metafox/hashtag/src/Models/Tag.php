<?php

namespace MetaFox\Hashtag\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Hashtag\Database\Factories\TagFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Tag.
 * @mixin Builder
 *
 * @property int    $id
 * @property string $text
 * @property string $tag_url
 * @property string $tag_hyperlink
 * @property int    $total_item
 * @method   static TagFactory factory(...$parameters)
 */
class Tag extends Model implements Entity, HasAmounts
{
    use HasFactory;
    use HasEntity;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'tag';

    protected $table = 'hashtag_tags';

    public $timestamps = false;

    protected $fillable = [
        'text',
        'tag_url',
        'total_item',
    ];

    /**
     * @return TagFactory
     */
    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    /**
     * @return string
     */
    public function getTagHyperlinkAttribute(): string
    {
        $hashtag = '#' . $this->text;

        return parse_output()->buildHashtagLink($hashtag, $this->tag_url);
    }
}
