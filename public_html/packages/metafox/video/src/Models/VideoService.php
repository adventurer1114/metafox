<?php

namespace MetaFox\Video\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Video\Database\Factories\VideoServiceFactory;

/**
 * Class VideoService.
 * @mixin Builder
 *
 * @property int                       $id
 * @property string                    $driver
 * @property string                    $name
 * @property int                       $is_default
 * @property int                       $is_active
 * @property string                    $service_class
 * @property array<string, mixed>|null $extra
 * @method   static                    VideoServiceFactory factory(...$parameters)
 */
class VideoService extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'video_service';

    protected $table = 'video_services';

    /** @var string[] */
    protected $fillable = [
        'is_default',
        'is_active',
        'driver',
        'name',
        'service_class',
        'extra',
        'created_at',
        'updated_at',
    ];

    /** @var array<string, mixed> */
    protected $casts = [
        'extra' => 'array',
    ];

    /**
     * @return VideoServiceFactory
     */
    protected static function newFactory(): VideoServiceFactory
    {
        return VideoServiceFactory::new();
    }
}

// end
