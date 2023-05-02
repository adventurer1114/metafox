<?php

namespace MetaFox\Activity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MetaFox\Activity\Database\Factories\TypeFactory;

/**
 * Class Type.
 *
 * @mixin Builder
 * @property int    $id
 * @property string $type
 * @property string $module_id
 * @property string $entity_type
 * @property string $title
 * @property string $description
 * @property ?array $value_actual
 * @property array  $value_default
 * @property bool   $is_active
 * @property bool   $is_system
 * @property array  $params
 *
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class Type extends Model
{
    use HasFactory;

    public const CAN_COMMENT              = 1;
    public const CAN_LIKE                 = 2;
    public const CAN_SHARE                = 4;
    public const CAN_EDIT                 = 8;
    public const CAN_CREATE_FEED          = 16;
    public const CAN_PUT_STREAM           = 32;
    public const ACTION_ON_FEED           = 64;
    public const CHANGE_PRIVACY_FROM_FEED = 128;
    public const CAN_REDIRECT_TO_DETAIL   = 256;
    public const PREVENT_EDIT_FEED_ITEM   = 512;

    public const CAN_COMMENT_TYPE                  = 'can_comment';
    public const CAN_LIKE_TYPE                     = 'can_like';
    public const CAN_SHARE_TYPE                    = 'can_share';
    public const CAN_EDIT_TYPE                     = 'can_edit';
    public const CAN_CREATE_FEED_TYPE              = 'can_create_feed';
    public const CAN_PUT_STREAM_TYPE               = 'can_put_stream';
    public const ACTION_ON_FEED_TYPE               = 'action_on_feed';
    public const CAN_CHANGE_PRIVACY_FROM_FEED_TYPE = 'can_change_privacy_from_feed';
    public const CAN_REDIRECT_TO_DETAIL_TYPE       = 'can_redirect_to_detail';
    public const PREVENT_EDIT_FEED_ITEM_TYPE       = 'prevent_from_edit_feed_item';

    protected $table = 'activity_types';

    /**
     * @return array|int[]
     */
    public function getAbilities(): array
    {
        return [
            self::CAN_COMMENT_TYPE                  => self::CAN_COMMENT,
            self::CAN_LIKE_TYPE                     => self::CAN_LIKE,
            self::CAN_SHARE_TYPE                    => self::CAN_SHARE,
            self::CAN_EDIT_TYPE                     => self::CAN_EDIT,
            self::CAN_CREATE_FEED_TYPE              => self::CAN_CREATE_FEED,
            self::CAN_PUT_STREAM_TYPE               => self::CAN_PUT_STREAM,
            self::ACTION_ON_FEED_TYPE               => self::ACTION_ON_FEED,
            self::CAN_CHANGE_PRIVACY_FROM_FEED_TYPE => self::CHANGE_PRIVACY_FROM_FEED,
            self::CAN_REDIRECT_TO_DETAIL_TYPE       => self::CAN_REDIRECT_TO_DETAIL,
            self::PREVENT_EDIT_FEED_ITEM_TYPE       => self::PREVENT_EDIT_FEED_ITEM,
        ];
    }

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'module_id',
        'entity_type',
        'title',
        'description',
        'is_active',
        'system_value',
        'is_system',
        'value_actual',
        'value_default',
        'params',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_active'     => 'boolean',
        'is_system'     => 'boolean',
        'params'        => 'array',
        'value_default' => 'array',
        'value_actual'  => 'array',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $appends = [];

    protected static function newFactory(): TypeFactory
    {
        return TypeFactory::new();
    }

    /**
     * @return array<string, mixed>
     */
    public function describe(): array
    {
        $data = $this->toArray();

        $mergedData = $this->value_actual ?? $this->value_default;

        if (is_array($mergedData)) {
            $data = array_merge($data, $mergedData);
        }

        return Arr::except($data, ['value_actual', 'value_default']);
    }
}
