<?php

namespace MetaFox\Photo\Models;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class AlbumInfo.
 * @property ?string $description
 */
class AlbumInfo extends Model
{
    use HasEntity;

    public const ENTITY_TYPE = 'photo_album_info';

    protected $table = 'photo_album_info';

    protected $fillable = ['id', 'description'];

    public $incrementing = false;

    public $timestamps = false;
}
