<?php

namespace MetaFox\Notification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Notification\Database\Factories\NotificationChannelFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class NotificationChannel.
 *
 * @property int    $id
 * @method   static NotificationChannelFactory factory(...$parameters)
 */
class NotificationChannel extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'notification_channel';

    /**
     * @var string
     */
    protected $table = 'notification_channels';

    /**
     * @var bool
     */
    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'name',
        'title',
    ];

    /**
     * @return NotificationChannelFactory
     */
    protected static function newFactory(): NotificationChannelFactory
    {
        return NotificationChannelFactory::new();
    }
}

// end
