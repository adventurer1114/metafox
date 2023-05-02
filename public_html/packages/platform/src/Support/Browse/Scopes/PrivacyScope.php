<?php

namespace MetaFox\Platform\Support\Browse\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Schema;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\DbTableHelper;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * Class PrivacyScope.
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class PrivacyScope extends BaseScope
{
    /**
     * @var int
     */
    protected int $userId;

    /** @var int|null */
    protected ?int $ownerId = null;

    /**
     * @var string|null
     */
    protected ?string $privacyColumn = null;

    /**
     * @var string|null
     */
    protected ?string $moderationPermissionName = null;

    /**
     * @var array|null
     */
    protected ?array $moderationUserRoles = null;
    protected bool $hasUserBlock          = true;

    public function setPrivacyColumn(string $column): void
    {
        $this->privacyColumn = $column;
    }

    public function getPrivacyColumn(): string
    {
        if (null === $this->privacyColumn) {
            $this->privacyColumn = DbTableHelper::PRIVACY_COLUMN;
        }

        return $this->privacyColumn;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * @param  string $name
     * @return $this
     */
    public function setModerationPermissionName(string $name): self
    {
        $this->moderationPermissionName = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModerationPermissionName(): ?string
    {
        return $this->moderationPermissionName;
    }

    /**
     * @param  string $name
     * @return $this
     */
    public function setModerationUserRoles(array $roles): self
    {
        $this->moderationUserRoles = $roles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModerationUserRoles(): ?array
    {
        return $this->moderationUserRoles;
    }

    public function setHasUserBlock(bool $hasUserBlock): self
    {
        $this->hasUserBlock = $hasUserBlock;

        return $this;
    }

    public function getHasUserBlock(): bool
    {
        return $this->hasUserBlock;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $this->addPrivacyScope($builder, $model);

        $this->addPrivacyMemberScope($builder, $model);

        $this->addBlockedScope($builder, $model);

        $ownerId = $this->getOwnerId();

        $resourceOwnerColumn = $model->getTable() . '.owner_id';

        if (null !== $ownerId) {
            $builder->where($resourceOwnerColumn, $ownerId);
        }
    }

    protected function isFriendOfFriendScope(): bool
    {
        $userId = $this->getUserId();

        $ownerId = $this->getOwnerId();

        if (!app_active('metafox/friend')) {
            return false;
        }

        if ($userId === MetaFoxConstant::GUEST_USER_ID) {
            return false;
        }

        if (null === $ownerId) {
            return true;
        }

        if ($userId == $ownerId) {
            return false;
        }

        $context = UserEntity::getById($userId)->detail;

        $owner = UserEntity::getById($ownerId)->detail;

        return (bool) app('events')->dispatch('friend.is_friend_of_friend', [$context->id, $owner->id], true);
    }

    protected function addPrivacyScope(Builder $builder, Model $model): void
    {
        $streamTable = null;

        // Support models which not integrated privacy to core_privacy_streams but define privacy_stream in its
        if (method_exists($model, 'privacyStreams')) {
            $streamTable = $model->privacyStreams()->getRelated()->getTable();
        }

        if (null === $streamTable) {
            abort(400, __p('validation.this_model_not_support_stream_resource'));
        }

        if ($this->hasResourceModeration()) {
            return;
        }

        $isFriendOfFriend = $this->isFriendOfFriendScope();

        $ownerId = $this->getOwnerId();

        $table = $model->getTable();

        $primaryKey = sprintf('%s.%s', $table, $model->getKeyName());

        $streamTable = $model->privacyStreams()->getRelated()->getTable();

        $streamTableAs = sprintf('%s AS stream', $streamTable);

        $streamTableAs2 = sprintf('%s AS stream2', $streamTable);

        $builder
            ->join($streamTableAs, function (JoinClause $join) use ($primaryKey, $isFriendOfFriend) {
                $join->on($primaryKey, '=', 'stream.item_id');

                if (!$isFriendOfFriend) {
                    $join->where('stream.privacy_id', '!=', MetaFoxPrivacy::NETWORK_FRIEND_OF_FRIENDS_ID);
                }
            })
            ->leftJoin($streamTableAs2, function (JoinClause $join) use ($isFriendOfFriend) {
                $join->on('stream2.item_id', '=', 'stream.item_id');

                $join->on('stream2.id', '<', 'stream.id');

                if (!$isFriendOfFriend) {
                    $join->where('stream2.privacy_id', '!=', MetaFoxPrivacy::NETWORK_FRIEND_OF_FRIENDS_ID);
                }
            })->whereNull('stream2.id');

        if (null !== $ownerId) {
            return;
        }

        if ($isFriendOfFriend) {
            $this->addFriendOfFriendPrivacy($builder, $table);
        }
    }

    protected function hasResourceModeration(): bool
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return false;
        }

        $ownerId = $this->getOwnerId();

        $user = UserEntity::getById($userId)->detail;

        if (null !== $ownerId) {
            $owner = UserEntity::getById($ownerId)->detail;

            if (method_exists($owner, 'hasResourceModeration')) {
                if ($owner->hasResourceModeration($user)) {
                    return true;
                }
            }
        }

        $moderatePermissionName = $this->getModerationPermissionName();

        if (null !== $moderatePermissionName) {
            return $user->hasPermissionTo($moderatePermissionName);
        }

        $moderateUserRoles = $this->getModerationUserRoles();

        if (is_array($moderateUserRoles)) {
            return $user->hasRole($moderateUserRoles);
        }

        return false;
    }

    protected function addBlockedScope(Builder $builder, Model $model): void
    {
        $resourceUserColumn = $model->getTable() . '.user_id';

        $resourceOwnerColumn = $model->getTable() . '.owner_id';

        if ($this->getHasUserBlock()) {
            // Resources post by blocked users.
            $builder->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($resourceUserColumn) {
                $join->on('blocked_owner.owner_id', '=', $resourceUserColumn)
                    ->where('blocked_owner.user_id', '=', $this->getUserId());
            })->whereNull('blocked_owner.owner_id');

            // Resources post by users blocked you.
            $builder->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($resourceUserColumn) {
                $join->on('blocked_user.user_id', '=', $resourceUserColumn)
                    ->where('blocked_user.owner_id', '=', $this->getUserId());
            })->whereNull('blocked_user.user_id');
        }

        if (Schema::hasColumn($model->getTable(), 'owner_id')) {
            // Resources post on users blocked you.
            $builder->leftJoin(
                'user_blocked as blocked_on_user',
                function (JoinClause $join) use ($resourceOwnerColumn) {
                    $join->on('blocked_on_user.user_id', '=', $resourceOwnerColumn)
                        ->where('blocked_on_user.owner_id', '=', $this->getUserId());
                }
            )->whereNull('blocked_on_user.user_id');
        }
    }

    protected function hasPrivacyMemberScope(): bool
    {
        return true;
    }

    protected function addPrivacyMemberScope(Builder $builder, Model $model): void
    {
        if ($this->hasResourceModeration()) {
            return;
        }

        if (!$this->hasPrivacyMemberScope()) {
            return;
        }

        $builder->join('core_privacy_members AS member', function (JoinClause $join) {
            $join->on('stream.privacy_id', '=', 'member.privacy_id')
                ->where('member.user_id', '=', $this->getUserId());
        });
    }

    protected function addFriendOfFriendPrivacy(Builder $builder, string $modelTable): void
    {
        if (!$this->hasPrivacyMemberScope()) {
            return;
        }

        $builder->leftJoin('friends as friend', function (JoinClause $joinClause) use ($modelTable) {
            $privacyColumn = $this->getPrivacyColumn();

            $joinClause->on('friend.user_id', '=', $modelTable . '.user_id')
                ->where($modelTable . '.' . $privacyColumn, '=', MetaFoxPrivacy::FRIENDS_OF_FRIENDS);
        })
            ->leftJoin('friends as friend2', function (JoinClause $joinClause) {
                $joinClause->on('friend2.owner_id', '=', 'friend.owner_id')
                    ->where('friend2.user_id', '=', $this->getUserId());
            })
            ->where(function (Builder $builder) {
                $builder->whereNull('friend.id')
                    ->orWhere('friend.owner_id', '=', $this->getUserId())
                    ->orWhereNotNull('friend2.id');
            });
    }
}
