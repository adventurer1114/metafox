<?php

namespace MetaFox\Localize\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Localize\Database\Factories\PhraseFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Phrase.
 *
 * @property        int           $id
 * @property        string        $name
 * @property        string        $key
 * @property        string        $group
 * @property        int           $is_modified
 * @property        string        $text
 * @property        string        $locale
 * @property        string        $namespace
 * @property        string        $package_id
 * @method   static PhraseFactory factory(...$parameters)
 */
class Phrase extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'phrase';

    protected $table = 'phrases';

    /** @var string[] */
    protected $fillable = [
        'id',
        'locale',
        'key',
        'namespace',
        'group',
        'text',
        'name',
        'modified',
        'package_id',
        'updated_at',
        'created_at',
    ];

    /**
     * @return PhraseFactory
     */
    protected static function newFactory(): PhraseFactory
    {
        return PhraseFactory::new();
    }
}

// end
