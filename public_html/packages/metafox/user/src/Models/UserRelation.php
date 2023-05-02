<?php

namespace MetaFox\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\User\Database\Factories\UserRelationFactory;

/**
 * Class UserRelation.
 *
 * @property int                 $id
 * @property string              $phrase_var
 * @property int                 $confirm
 * @property mixed               $is_active
 * @property mixed               $is_custom
 * @property mixed               $image_file_id
 * @property mixed               $avatar
 * @property string              $relation_name
 * @method   UserRelationFactory factory(...$parameters)
 */
class UserRelation extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'user_relation';

    protected $table = 'user_relation';

    /** @var string[] */
    protected $fillable = [
        'phrase_var',
        'confirm',
        'image_file_id',
        'is_active',
        'is_custom',
        'relation_name',
        'updated_at',
        'created_at',
    ];

    /**
     * @return UserRelationFactory
     */
    protected static function newFactory()
    {
        return UserRelationFactory::new();
    }

    public function getAvatarAttribute(): ?string
    {
        if ($this->image_file_id != null) {
            return app('storage')->getUrl($this->image_file_id);
        }

        if ($this->relation_name == null) {
            return null;
        }

        $fileId = app('asset')->findByName($this->relation_name)?->file_id;

        if ($fileId !== null) {
            return app('storage')->getUrl($fileId);
        }

        return null;
    }

    public function getAdminEditUrlAttribute()
    {
        return "/admincp/user/relation/edit/{$this->id}";
    }

    public function getAdminBrowseUrlAttribute()
    {
        return sprintf('/admincp/user/relation/browse');
    }
}

// end
