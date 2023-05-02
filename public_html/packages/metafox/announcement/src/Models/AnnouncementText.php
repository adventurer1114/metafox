<?php

namespace MetaFox\Announcement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\Platform\Contracts\ResourceText;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class AnnouncementText.
 * @mixin Builder
 * @property int    $id
 * @property string $text
 * @property string $text_parsed
 */
class AnnouncementText extends Model implements ResourceText
{
    use HasEntity;

    public const ENTITY_TYPE = 'announcement_text';

    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'announcement_text';

    protected $fillable = [
        'text',
        'text_parsed',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Announcement::class, 'id', 'id');
    }
}

// end
