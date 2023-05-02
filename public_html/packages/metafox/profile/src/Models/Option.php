<?php

namespace MetaFox\Profile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Profile\Database\Factories\OptionFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * class Option.
 *
 * @property int    $id
 * @property int    $field_id
 * @property string $option_label
 *
 * @method static OptionFactory factory(...$parameters)
 */
class Option extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_custom_option';

    protected $table = 'user_custom_options';

    /** @var string[] */
    protected $fillable = [
        'field_id',
        'option_label',
    ];

    /**
     * @return OptionFactory
     */
    protected static function newFactory()
    {
        return OptionFactory::new();
    }
}

// end
