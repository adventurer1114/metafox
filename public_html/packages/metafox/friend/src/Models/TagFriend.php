<?php

namespace MetaFox\Friend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Friend\Database\Factories\TagFriendFactory;
use MetaFox\Friend\Notifications\FriendTag;
use MetaFox\Platform\Contracts\ActionEntity;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\Contracts\IsNotifyInterface;
use MetaFox\Platform\Contracts\TagFriendModel;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class TagFriend.
 *
 * @property        int              $id
 * @property        float            $px
 * @property        float            $py
 * @property        int              $is_mention
 * @property        string|null      $content
 * @method   static TagFriendFactory factory(...$parameters)
 */
class TagFriend extends Model implements Entity, TagFriendModel, IsNotifyInterface, HasUrl
{
    use HasEntity;
    use HasFactory;
    use HasUserMorph;
    use HasOwnerMorph;
    use HasItemMorph;

    public const ENTITY_TYPE = 'tag_friend';

    protected $table = 'friend_tag_friends';

    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'px'         => 'float',
        'py'         => 'float',
        'is_mention' => 'boolean',
    ];

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'item_id',
        'item_type',
        'px',
        'py',
        'is_mention',
        'content',
    ];

    /**
     * @return TagFriendFactory
     */
    protected static function newFactory(): TagFriendFactory
    {
        return TagFriendFactory::new();
    }

    /**
     * @return ?array<int, mixed>
     */
    public function toNotification(): ?array
    {
        $notification = new FriendTag($this);

        // In case delete models via job
        if (app()->runningInConsole()) {
            return [[], $notification];
        }

        $item = $this->item;
        if (!$item instanceof Content) {
            return null;
        }

        $privacyItem = $item->privacyItem();

        if (!$privacyItem instanceof Content) {
            return null;
        }

        if (!PrivacyPolicy::checkPermission($this->owner, $privacyItem)) {
            return null;
        }
        $pass = app('events')->dispatch('like.owner.notification', [$this->owner, $item], true);
        if ($pass === false) {
            return null;
        }

        $userItem = $item->user;

        $context = user();

        if ($context->userId() == $item->userId() && $context->userId() == $this->ownerId()) {
            return null;
        }

        $owner = $this->owner;

        if ($owner instanceof HasPrivacyMember) {
            $notifiables = app('events')->dispatch('friend.mention.notifiables', [$context, $owner], true);

            if (!is_array($notifiables) || !count($notifiables)) {
                return null;
            }

            return [$notifiables, $notification];
        }

        if ($this->userId() == $this->ownerId()) {
            return [$userItem, $notification];
        }

        /* Don't send notifications to users tagged on their timelines posts
         *  Except comment still send notifications to users tagged
         */
        if ($owner->entityId() == $item->ownerId() && !$item instanceof ActionEntity) {
            return null;
        }

        if ($context->userId() == $userItem->entityId()) {
            return [$owner, $notification];
        }

        if ($userItem->entityId() == $this->ownerId()) {
            return [$userItem, $notification];
        }

        return [[$userItem, $owner], $notification];
    }

    public function toLink(): ?string
    {
        $user = $this->user;

        if (!$user instanceof User) {
            return null;
        }

        return $user->toLink();
    }

    public function toUrl(): ?string
    {
        $user = $this->user;

        if (!$user instanceof User) {
            return null;
        }

        return $user->toUrl();
    }

    public function toRouter(): ?string
    {
        $user = $this->user;

        if (!$user instanceof User) {
            return null;
        }

        return $user->toRouter();
    }
}

// end
