<?php

namespace MetaFox\Saved\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;

/**
 * Class SavedListData.
 *
 * @property int $id
 * @property int $list_id
 * @property int $saved_id
 */
class SavedListData extends Pivot
{
    use HasEntity;
    use HasItemMorph;

    public const ENTITY_TYPE = 'saved_list_data';

    protected $table = 'saved_list_data';

    public $incrementing = false;

    protected $fillable = [
        'list_id',
        'saved_id',
    ];
}
