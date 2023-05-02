<?php

namespace MetaFox\Profile\Models;

use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Profile\Database\Factories\StructureFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * class Structure.
 *
 * @property int    $id
 * @property string $location
 * @method   static StructureFactory factory(...$parameters)
 */
class Structure extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_custom_structure';

    protected $table = 'user_custom_structure';

    /** @var string[] */
    protected $fillable = [
        'location',
    ];

    /**
     * @return StructureFactory
     */
    protected static function newFactory()
    {
        return StructureFactory::new();
    }
}

// end
