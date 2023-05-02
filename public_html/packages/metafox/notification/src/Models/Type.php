<?php

namespace MetaFox\Notification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use MetaFox\Notification\Database\Factories\TypeFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class Type.
 * @property int        $id
 * @property string     $type
 * @property string     $title
 * @property string     $module_id
 * @property string     $handler
 * @property bool       $can_edit
 * @property bool       $is_request
 * @property bool       $is_active
 * @property bool       $is_system
 * @property string[]   $channels
 * @property int        $ordering
 * @property bool       $database
 * @property bool       $mail
 * @property Collection $typeChannels
 *
 * @method static TypeFactory factory(...$parameters)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class Type extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    /** @var string */
    public const ENTITY_TYPE = 'notification_type';

    public const IS_ACTIVE = 1;

    /** @var string */
    protected $table = 'notification_types';

    /** @var string[] */
    protected $fillable = [
        'type',
        'handler',
        'title',
        'module_id',
        'can_edit',
        'is_request',
        'is_active',
        'channels',
        'is_system',
        'ordering',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'can_edit'   => 'boolean',
        'is_request' => 'boolean',
        'is_active'  => 'boolean',
        'is_system'  => 'boolean',
        'channels'   => 'array',
    ];

    public $timestamps = false;

    protected static function newFactory(): TypeFactory
    {
        return TypeFactory::new();
    }

    public function typeChannels(): HasMany
    {
        return $this->hasMany(TypeChannel::class, 'type_id', 'id');
    }
}

// end
