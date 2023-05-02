<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Database\Factories\CancelFeedbackFactory;

/**
 * Class CancelFeedback.
 *
 * @property int                   $id
 * @method   CancelFeedbackFactory factory(...$parameters)
 */
class CancelFeedback extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;

    public const ENTITY_TYPE = 'user_cancel_feedback';

    protected $table = 'user_delete_feedback';

    /** @var string[] */
    protected $fillable = [
        'email',
        'name',
        'user_group_id',
        'feedback_text',
        'reason_id',
        'reasons_given',
        'user_id',
        'user_type',
        'created_at',
        'updated_at',
    ];

    /**
     * @return CancelFeedbackFactory
     */
    protected static function newFactory()
    {
        return CancelFeedbackFactory::new();
    }
}

// end
