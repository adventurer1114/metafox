<?php

namespace MetaFox\Poll\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Traits\HasTotalAttachmentTrait;
use MetaFox\Platform\Contracts\ActivityFeedForm;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasSponsorInFeed;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasTaggedFriendTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Poll\Database\Factories\PollFactory;
use MetaFox\Poll\Notifications\PendingPollNotification;
use MetaFox\Poll\Notifications\PollApproveNotification;
use MetaFox\Poll\Policies\PollPolicy;
use MetaFox\Poll\Support\Facade\Poll as PollFacade;
use MetaFox\Platform\Facades\Settings;

/**
 * Class Poll.
 *
 * @property        int           $id
 * @property        string        $question
 * @property        string        $description
 * @property        string | null $caption
 * @property        int           $view_id
 * @property        int           $privacy
 * @property        bool          $is_featured
 * @property        bool          $is_sponsor
 * @property        bool          $sponsor_in_feed
 * @property        int           $total_view
 * @property        int           $total_like
 * @property        int           $total_comment
 * @property        int           $total_share
 * @property        int           $total_attachment
 * @property        int           $total_vote
 * @property        string        $image_file_id
 * @property        int           $randomize
 * @property        bool          $public_vote
 * @property        bool          $is_multiple
 * @property        bool          $is_closed
 * @property        Carbon        $closed_at
 * @property        string        $created_at
 * @property        string        $updated_at
 * @property        Collection    $answers
 * @property        int           $answers_count
 * @property        Collection    $results
 * @property        PollText|null $pollText
 * @property        Design        $design
 * @method   static PollFactory   factory(...$parameters)
 */
class Poll extends Model implements
    Content,
    ActivityFeedSource,
    ActivityFeedForm,
    AppendPrivacyList,
    HasPrivacy,
    HasResourceStream,
    HasApprove,
    HasFeature,
    HasSponsor,
    HasSponsorInFeed,
    HasTotalLike,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasTotalView,
    HasTotalAttachment,
    HasThumbnail,
    HasSavedItem,
    HasGlobalSearch,
    HasTaggedFriend,
    HasLocationCheckin
{
    use HasContent;
    use HasUserMorph;
    use HasOwnerMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasFactory;
    use HasThumbnailTrait;
    use HasTotalAttachmentTrait;
    use HasTaggedFriendTrait;

    public const ENTITY_TYPE = 'poll';

    public const FEED_POST_TYPE = self::ENTITY_TYPE;

    protected $table = 'polls';

    /**
     * @var string[]
     */
    protected $appends = ['image', 'is_closed'];

    /**
     * @var string[]
     */
    protected $dates = ['closed_at'];

    /**
     * @var array<string>|array<string, mixed>
     */
    protected array $nestedAttributes = [
        'design'   => ['percentage', 'background', 'border'],
        'pollText' => ['text', 'text_parsed'],
    ];

    protected $fillable = [
        'view_id',
        'privacy',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'question',
        'caption',
        'image_file_id',
        'randomize',
        'public_vote',
        'is_multiple',
        'closed_at',
        'is_approved',
        'is_featured',
        'is_sponsor',
        'sponsor_in_feed',
        'total_like',
        'total_comment',
        'total_reply',
        'total_share',
        'total_view',
        'total_attachment',
        'total_vote',
        'updated_at',
        'created_at',
        'location_name',
        'location_latitude',
        'location_longitude',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_sponsor'      => 'boolean',
        'sponsor_in_feed' => 'boolean',
        'is_featured'     => 'boolean',
        'public_vote'     => 'boolean',
        'is_multiple'     => 'boolean',
        'is_closed'       => 'boolean',
    ];

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->isApproved()) {
            return null;
        }

        if ($this->view_id === PollFacade::getIntegrationViewId()) {
            return null;
        }

        if (null === $this->user) {
            return null;
        }

        if (!policy_check(PollPolicy::class, 'view', $this->user, $this)) {
            return null;
        }

        return new FeedAction([
            'user_id'    => $this->userId(),
            'user_type'  => $this->userType(),
            'owner_id'   => $this->ownerId(),
            'owner_type' => $this->ownerType(),
            'item_id'    => $this->entityId(),
            'item_type'  => $this->entityType(),
            'type_id'    => $this->entityType(),
            'privacy'    => $this->privacy,
            'content'    => $this->getFeedContent(),
        ]);
    }

    public function getIsClosedAttribute(): bool
    {
        return !empty($this->closed_at) && $this->closed_at->lessThan(now());
    }

    protected static function newFactory(): PollFactory
    {
        return PollFactory::new();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'poll_id', 'id');
    }

    public function design(): HasOne
    {
        return $this->hasOne(Design::class, 'id', 'id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'poll_id', 'id');
    }

    public function pollText(): HasOne
    {
        return $this->hasOne(PollText::class, 'id', 'id');
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(PrivacyStream::class, 'item_id', 'id');
    }

    public function getFeedContent(): ?string
    {
        return $this->caption;
    }

    public function toSavedItem(): array
    {
        return [
            'title'          => $this->question,
            'image'          => $this->images,
            'item_type_name' => __p("poll::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $this->getThumbnail() ? 1 : 0,
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        if ($this->view_id === PollFacade::getIntegrationViewId()) {
            return null;
        }

        $text = $this->pollText;

        return [
            'title' => $this->question,
            'text'  => $text ? $text->text_parsed : '',
        ];
    }

    public function toTitle(): string
    {
        return $this->question ?? '';
    }

    /**
     * @return array<int, mixed>
     */
    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function toApprovedNotification(): array
    {
        return [$this->user, new PollApproveNotification($this)];
    }
}
