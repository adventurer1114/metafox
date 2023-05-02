<?php

namespace MetaFox\Profile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Profile\Database\Factories\ValueFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * class Value.
 *
 * @property int    $id
 * @method   static ValueFactory factory(...$parameters)
 */
class Value extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_custom_value';

    protected $table = 'user_custom_value';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'field_id',
        'field_value_text',
        'ordering',
        'privacy',
    ];

    /**
     * @return ValueFactory
     */
    protected static function newFactory()
    {
        return ValueFactory::new();
    }
}

// end
