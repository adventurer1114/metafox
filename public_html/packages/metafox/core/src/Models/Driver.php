<?php

namespace MetaFox\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Core\Database\Factories\DriverFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Driver.
 *
 * @property int     $id
 * @property string  $type
 * @property string  $name
 * @property string  $driver
 * @property ?string $version
 * @property bool    $is_active
 * @property bool    $resolution
 * @property bool    $is_preload
 * @property ?string $alias
 * @property string  $title
 * @property string  $description
 * @property string  $url
 * @property string  $package_id
 * @property string  $module_id
 *
 * @method static DriverFactory factory(...$parameters)
 */
class Driver extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'core-driver';

    protected $table = 'core_drivers';

    /** @var string[] */
    protected $fillable = [
        'type',
        'name',
        'version',
        'driver',
        'alias',
        'resolution',
        'is_active',
        'is_preload',
        'title',
        'description',
        'category',
        'url',
        'module_id',
        'package_id',
    ];

    protected $append = [
        'is_active',
        'is_preload',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_preload' => 'boolean',
    ];

    /**
     * @return DriverFactory
     */
    protected static function newFactory()
    {
        return DriverFactory::new();
    }

    protected static function booted()
    {
        static::saving(function (self $item) {
            if (!$item->version) {
                $item->version = '*';
            }
            if (!$item->alias) {
                $item->alias = null;
            }
            if (!$item->url) {
                $item->url = null;
            }
            if (!$item->driver) {
                $item->driver = null;
            }
        });
    }
}

// end
