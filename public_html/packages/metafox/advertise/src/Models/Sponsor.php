<?php

namespace MetaFox\Advertise\Models;

use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Advertise\Database\Factories\SponsorFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Sponsor.
 *
 * @property        int            $id
 * @method   static SponsorFactory factory(...$parameters)
 */
class Sponsor extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'advertise_sponsor';

    protected $table = 'advertise_sponsors';

    /** @var string[] */
    protected $fillable = [
        'item_id',
        'item_type',
        'user_id',
        'user_type',
        'title',
        'status',
        'start_date',
        'end_date',
        'total_impression',
        'total_click',
    ];

    /**
     * @return SponsorFactory
     */
    protected static function newFactory()
    {
        return SponsorFactory::new();
    }
}

// end
