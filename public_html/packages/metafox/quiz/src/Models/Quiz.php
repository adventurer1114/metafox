<?php

namespace MetaFox\Quiz\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Traits\HasTotalAttachmentTrait;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasHashTag;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
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
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Quiz\Database\Factories\QuizFactory;
use MetaFox\Quiz\Notifications\QuizApproveNotifications;
use MetaFox\Quiz\Policies\QuizPolicy;
use MetaFox\Hashtag\Models\Tag;

/**
 * Class Quiz.
 * @mixin Builder
 * @property        int           $id
 * @property        string        $title
 * @property        string        $description
 * @property        int           $view_id
 * @property        int           $privacy
 * @property        int           $is_featured
 * @property        int           $is_sponsor
 * @property        int           $is_approved
 * @property        int           $sponsor_in_feed
 * @property        int           $total_view
 * @property        int           $total_like
 * @property        int           $total_comment
 * @property        int           $total_share
 * @property        int           $total_attachment
 * @property        int           $total_play
 * @property        int           $questions_count
 * @property        string        $image_file_id
 * @property        string        $created_at
 * @property        string        $updated_at
 * @property        Collection    $questions
 * @property        Collection    $results
 * @property        QuizText|null $quizText
 * @method   static QuizFactory   factory()
 */
class Quiz extends Model implements
    Content,
    ActivityFeedSource,
    AppendPrivacyList,
    HasPrivacy,
    HasApprove,
    HasFeature,
    HasSponsor,
    HasResourceStream,
    HasTotalView,
    HasTotalLike,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasTotalAttachment,
    HasThumbnail,
    HasSavedItem,
    HasGlobalSearch,
    HasHashTag
{
    use HasContent;
    use HasOwnerMorph;
    use HasUserMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasFactory;
    use HasThumbnailTrait;
    use HasTotalAttachmentTrait;

    /** @var array<string,mixed> */
    protected array $nestedAttributes = [
        'quizText' => ['text', 'text_parsed'],
    ];

    public const ENTITY_TYPE = 'quiz';

    protected $table = 'quizzes';

    /**
     * @var string[]
     */
    protected $appends = ['image'];

    protected $fillable = [
        'view_id',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'privacy',
        'title',
        'image_file_id',
        'total_comment',
        'total_reply',
        'total_like',
        'total_share',
        'total_view',
        'total_attachment',
        'image_file_id',
        'total_play',
        'is_featured',
        'is_sponsor',
        'is_approved',
        'sponsor_in_feed',
        'updated_at',
        'created_at',
    ];

    protected static function newFactory(): QuizFactory
    {
        return QuizFactory::new();
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->isApproved()) {
            return null;
        }

        if (null === $this->user) {
            return null;
        }

        if (!policy_check(QuizPolicy::class, 'view', $this->user, $this)) {
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
        ]);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'quiz_id', 'id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class, 'quiz_id', 'id');
    }

    public function quizText(): HasOne
    {
        return $this->hasOne(QuizText::class, 'id', 'id');
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(PrivacyStream::class, 'item_id', 'id');
    }

    public function toSavedItem(): array
    {
        return [
            'title'          => $this->title,
            'image'          => $this->images,
            'item_type_name' => __p("quiz::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $this->getThumbnail() ? 1 : 0,
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    /**
     * toSearchable.
     *
     * @return array<mixed>
     */
    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $text = $this->quizText;

        return [
            'title' => $this->title,
            'text'  => $text ? $text->text_parsed : '',
        ];
    }

    public function toTitle(): string
    {
        return $this->title;
    }

    /**
     * toApprovedNotification.
     *
     * @return array<mixed>
     */
    public function toApprovedNotification(): array
    {
        return [$this->user, new QuizApproveNotifications($this)];
    }

    public function tagData(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'quiz_tag_data',
            'item_id',
            'tag_id'
        )->using(QuizTagData::class);
    }
}
