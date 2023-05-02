<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Database\Factories\CancelReasonFactory;
use function PHPUnit\Framework\isNull;

/**
 * Class CancelReason.
 *
 * @property int                 $id
 * @property string              $phrase_var
 * @property string              $title
 * @property int                 $is_active
 * @property int                 $ordering
 * @method   CancelReasonFactory factory(...$parameters)
 */
class CancelReason extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;

    public const ENTITY_TYPE = 'user_cancel_reason';

    protected $table = 'user_delete_reasons';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'phrase_var',
        'is_active',
        'ordering',
        'updated_at',
        'created_at',
    ];

    protected $appends = [
        'title',
    ];

    /**
     * @return CancelReasonFactory
     */
    protected static function newFactory()
    {
        return CancelReasonFactory::new();
    }

    public function getTitleAttribute(): string
    {
        if (null === $this->phrase_var) {
            return '';
        }

        return __p($this->phrase_var);
    }
}

// end
