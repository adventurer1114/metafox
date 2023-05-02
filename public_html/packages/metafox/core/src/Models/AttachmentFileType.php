<?php

namespace MetaFox\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Core\Database\Factories\AttachmentFileTypeFactory;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;

/**
 * Class AttachmentFileType.
 *
 * @property int    $id
 * @property string $extension
 * @property string $mime_type
 * @property int    $is_active
 * @method   static AttachmentFileTypeFactory factory(...$parameters)
 */
class AttachmentFileType extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'core_attachment_file_type';

    protected $table = 'core_attachment_file_types';

    public $timestamps = false;

    /** @var string[] */
    protected $fillable = [
        'extension',
        'mime_type',
        'is_active',
    ];

    /**
     * @return AttachmentFileTypeFactory
     */
    protected static function newFactory(): AttachmentFileTypeFactory
    {
        return AttachmentFileTypeFactory::new();
    }
}

// end
