<?php

namespace MetaFox\Announcement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use MetaFox\Announcement\Database\Factories\AnnouncementFactory;
use MetaFox\Authorization\Models\Role;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserAsOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalComment;

/**
 * Class Announcement.
 *
 * @mixin Builder
 *
 * @property        int                 $id
 * @property        int                 $is_active
 * @property        int                 $can_be_closed
 * @property        int                 $show_in_dashboard
 * @property        string              $start_date
 * @property        string              $country_iso
 * @property        int                 $gender
 * @property        int                 $age_from
 * @property        int                 $age_to
 * @property        string              $gmt_offset
 * @property        string              $subject_var
 * @property        string              $intro_var
 * @property        string              $created_at
 * @property        string              $updated_at
 * @property        AnnouncementText    $announcementText
 * @property        string              $admin_edit_url
 * @property        string              $admin_browse_url
 * @property        Style               $style
 * @property        Collection          $views
 * @property        int                 $total_view
 * @property        Collection          $roles
 * @method   static AnnouncementFactory factory()
 */
class Announcement extends Model implements
    Content,
    HasTotalView,
    HasTotalLike,
    HasTotalComment,
    HasPrivacy,
    HasTotalCommentWithReply
{
    use HasContent;
    use HasUserMorph;
    use HasUserAsOwnerMorph;
    use HasNestedAttributes;
    use HasFactory;

    public const ENTITY_TYPE = 'announcement';
    public const IS_ACTIVE   = 1;

    protected $table = 'announcements';

    /**
     * @var array<string>|array<string, mixed>
     */
    public array $nestedAttributes = [
        'announcementText' => ['text', 'text_parsed'],
        'roles',
    ];

    /** @var string[] */
    protected $fillable = [
        'is_active',
        'can_be_closed',
        'show_in_dashboard',
        'start_date',
        'country_iso',
        'gender',
        'age_from',
        'age_to',
        'user_id',
        'user_type',
        'gmt_offset',
        'style_id',
        'subject_var',
        'intro_var',
        'total_view',
    ];

    /**
     * @return HasOne
     */
    public function announcementText(): HasOne
    {
        return $this->hasOne(AnnouncementText::class, 'id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id', 'id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class, 'announcement_id', 'id');
    }

    /**
     * @return AnnouncementFactory
     */
    protected static function newFactory(): AnnouncementFactory
    {
        return AnnouncementFactory::new();
    }

    public function toTitle(): string
    {
        return $this->subject_var;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'announcement_role_data',
            'announcement_id',
            'role_id'
        )->using(AnnouncementData::class);
    }

    public function getTitleAttribute()
    {
        return $this->subject_var;
    }

    public function getAdminEditUrlAttribute()
    {
        return sprintf('/admincp/announcement/announcement/edit/%s', $this->id);
    }

    public function getAdminBrowseUrlAttribute()
    {
        return sprintf('/admincp/announcement/announcement/browse');
    }
}
