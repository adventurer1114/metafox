<?php

namespace MetaFox\Storage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Storage\Database\Factories\AssetFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Asset.
 *
 * @property int    $id
 * @property string $name
 * @property string $module_id
 * @property string $package_id
 * @property int    $file_id
 * @property string $local_path
 * @property string $url
 * @method   static AssetFactory factory(...$parameters)
 */
class Asset extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'storage_asset';

    protected $table = 'storage_assets';

    /** @var string[] */
    protected $fillable = [
        'name',
        'module_id',
        'package_id',
        'file_id',
        'local_path',
    ];

    /**
     * @return AssetFactory
     */
    protected static function newFactory()
    {
        return AssetFactory::new();
    }

    public function getUrlAttribute(): ?string
    {
        return app('storage')->getUrl($this->file_id);
    }
}

// end
