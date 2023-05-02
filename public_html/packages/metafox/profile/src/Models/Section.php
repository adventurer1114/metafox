<?php

namespace MetaFox\Profile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Profile\Database\Factories\SectionFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Section.
 *
 * @property        int            $id
 * @property        string         $name
 * @property        string         $label
 * @property        string         $description
 * @property        int            $ordering
 * @property        bool           $is_active
 * @property        Collection     $fields
 * @method   static SectionFactory factory(...$parameters)
 */
class Section extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_custom_section';

    protected $table = 'user_custom_sections';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'name',
        'label',
        'description',
        'is_active',
        'ordering',
        'extra',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra'     => 'array',
    ];

    public function getLabelAttribute(): ?string
    {
        return __p('profile::phrase.' . $this->name . '_label');
    }

    public function setLabelAttribute($value)
    {
        $key = 'profile::phrase.' . $this->name . '_label';

        resolve(PhraseRepositoryInterface::class)
            ->updatePhrases([$key => $value ? $value : '']);
    }

    public function setDescriptionAttribute($value)
    {
        $key = 'profile::phrase.' . $this->name . '_description';

        resolve(PhraseRepositoryInterface::class)
            ->updatePhrases([$key => $value ? $value : '']);
    }

    public function getDescription(): ?string
    {
        return __p('profile::phrase.' . $this->name . '_description');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'section_id', 'id');
    }

    /**
     * @return SectionFactory
     */
    protected static function newFactory()
    {
        return SectionFactory::new();
    }
}

// end
