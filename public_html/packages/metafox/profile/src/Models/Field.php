<?php

namespace MetaFox\Profile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MetaFox\Form\Builder;
use MetaFox\Form\FormField;
use MetaFox\Form\Mobile\Builder as MobileBuilder;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Profile\Database\Factories\FieldFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * class Field.
 *
 * @property int      $id
 * @property string   $section_id
 * @property string   $field_name
 * @property string   $module_id
 * @property string   $product_id
 * @property ?int     $role_id
 * @property int      $privacy
 * @property string   $type_id
 * @property string   $edit_type
 * @property string   $view_type
 * @property string   $var_type
 * @property bool     $is_active
 * @property bool     $is_required
 * @property bool     $is_feed
 * @property int      $ordering
 * @property bool     $is_register
 * @property bool     $is_search
 * @property bool     $has_description
 * @property bool     $has_label
 * @property string   $label
 * @property string   $editingLabel
 * @property string   $editingDescription
 * @property ?Section $section
 * @property string   $description
 * @property string   $name
 * @property ?array   $extra
 * @method   static   FieldFactory factory(...$parameters)
 */
class Field extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_custom_field';

    protected $table = 'user_custom_fields';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'section_id',
        'field_name',
        'is_section',
        'type_id',
        'edit_type',
        'view_type',
        'var_type',
        'privacy',
        'ordering',
        'is_active',
        'is_required',
        'is_feed',
        'is_register',
        'is_search',
        'has_label',
        'label',
        'description',
        'has_description',
        'extra',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra'     => 'array',
    ];

    /**
     * @return FieldFactory
     */
    protected static function newFactory()
    {
        return FieldFactory::new();
    }

    public function getLabelAttribute(): ?string
    {
        if (!$this->has_label) {
            return null;
        }

        return __p('profile::phrase.' . $this->field_name . '_label');
    }

    public function getEditingLabelAttribute()
    {
        return __p('profile::phrase.' . $this->field_name . '_label');
    }

    public function getNameAttribute()
    {
        return $this->field_name;
    }

    public function setLabelAttribute($value)
    {
        $key = 'profile::phrase.' . $this->field_name . '_label';

        resolve(PhraseRepositoryInterface::class)
            ->updatePhrases([$key => $value]);
    }

    public function setDescriptionAttribute($value)
    {
        $key = 'profile::phrase.' . $this->field_name . '_description';

        resolve(PhraseRepositoryInterface::class)
            ->updatePhrases([$key => $value ? $value : '']);
    }

    public function getDescriptionAttribute(): ?string
    {
        if (!$this->has_description) {
            return null;
        }

        return __p('profile::phrase.' . $this->field_name . '_description');
    }

    public function getEditingDescriptionAttribute(): ?string
    {
        if (!$this->has_description) {
            return null;
        }

        return __p('profile::phrase.' . $this->field_name . '_description');
    }

    private function getCreator(?string $resolution = null): ?string
    {
        return match ($resolution) {
            MetaFoxConstant::RESOLUTION_MOBILE => MobileBuilder::getCreator($this->edit_type),
            default                            => Builder::getCreator($this->edit_type),
        };
    }

    public function toEditField(?string $resolution = null): ?FormField
    {
        $creator = $this->getCreator($resolution);

        if (!$creator) {
            return null;
        }

        $field = new $creator([
            'name'        => $this->field_name,
            'label'       => $this->editingLabel,
            'description' => $this->editingDescription,
            'required'    => $this->is_required,
        ]);

        if (method_exists($field, 'options')) {
            // put option to fields.

            $field->options([
                ['label' => '1', 'value' => '1'],
            ]);
        }

        return $field;
    }

    public function section(): ?HasOne
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    public function toRule()
    {
        if ($this->is_required) {
            return [$this->var_type, 'required'];
        }

        return [$this->var_type, 'sometimes', 'nullable'];
    }
}

// end
