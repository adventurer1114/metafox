<?php

namespace MetaFox\Group\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Group\Database\Factories\ExampleRuleFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class ExampleRule.
 *
 * @property        int                $id
 * @property        string             $title
 * @property        string             $description
 * @property        int                $ordering
 * @property        int                $is_active
 * @method   static ExampleRuleFactory factory(...$parameters)
 */
class ExampleRule extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'group_rule_example';
    public const IS_ACTIVE   = 1;

    protected $table   = 'group_rule_examples';
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    /**
     * @return ExampleRuleFactory
     */
    protected static function newFactory(): ExampleRuleFactory
    {
        return ExampleRuleFactory::new();
    }
}

// end
