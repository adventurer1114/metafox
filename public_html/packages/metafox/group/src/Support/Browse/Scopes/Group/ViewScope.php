<?php

namespace MetaFox\Group\Support\Browse\Scopes\Group;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Group\Models\Invite;
use MetaFox\Group\Support\InviteType;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

/**
 * Class ViewScope.
 */
class ViewScope extends BaseScope
{
    public const VIEW_DEFAULT = Browse::VIEW_ALL;
    public const VIEW_JOINED  = 'joined';
    public const VIEW_INVITED = 'invited';

    /**
     * @return array<int, string>
     */
    public static function getAllowView(): array
    {
        return [
            Browse::VIEW_ALL,
            Browse::VIEW_MY,
            Browse::VIEW_FRIEND,
            Browse::VIEW_PENDING,
            Browse::VIEW_FEATURE,
            Browse::VIEW_SPONSOR,
            self::VIEW_JOINED,
            self::VIEW_INVITED,
            Browse::VIEW_SEARCH,
            Browse::VIEW_MY_PENDING,
        ];
    }

    /**
     * @var string
     */
    private string $view = self::VIEW_DEFAULT;

    /**
     * @var User
     */
    private User $userContext;

    /**
     * @var bool
     */
    private bool $isProfile = false;

    /**
     * @var int
     */
    protected int $profileId = 0;

    /**
     * @return bool
     */
    public function isViewProfile(): bool
    {
        return $this->isProfile;
    }

    /**
     * @param bool $isProfile
     *
     * @return ViewScope
     */
    public function setIsViewProfile(bool $isProfile): self
    {
        $this->isProfile = $isProfile;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserContext(): User
    {
        return $this->userContext;
    }

    /**
     * @param User $userContext
     *
     * @return ViewScope
     */
    public function setUserContext(User $userContext): self
    {
        $this->userContext = $userContext;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileId(): int
    {
        return $this->profileId;
    }

    /**
     * @param int $profileId
     *
     * @return ViewScope
     */
    public function setProfileId(int $profileId): self
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return ViewScope
     */
    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws AuthenticationException
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->isViewProfile()) {
            return;
        }

        $view = $this->getView();

        $context   = $this->getUserContext();
        $contextId = $context->entityId();
        switch ($view) {
            case Browse::VIEW_MY:
                $builder->where('groups.is_approved', 1)
                    ->where('groups.user_id', '=', $contextId);
                break;
            case Browse::VIEW_FRIEND:
                $builder->join('friends AS f', function (JoinClause $join) use ($contextId) {
                    $join->on('f.user_id', '=', 'groups.user_id')
                        ->where('f.owner_id', '=', $contextId)
                        ->where('groups.is_approved', 1);
                });

                $builder->leftJoin('groups as g2', function (JoinClause $join) {
                    $join->on('g2.id', '=', 'groups.id')
                        ->where('g2.privacy_type', '=', PrivacyTypeHandler::SECRET);
                });

                $builder->leftJoin('group_members AS gm', function (JoinClause $join) use ($contextId) {
                    $join->on('gm.group_id', '=', 'g2.id')
                        ->where('gm.user_id', '=', $contextId);
                });

                $builder->where(function (Builder $q) {
                    $q->whereNull('g2.id')
                        ->orWhere([
                            ['g2.id', '!=', null],
                            ['gm.id', '!=', null],
                        ]);
                });
                break;
            case Browse::VIEW_PENDING:
                $builder->where('groups.is_approved', '!=', 1);
                break;

            case Browse::VIEW_MY_PENDING:
                $builder->where('groups.is_approved', '!=', 1);

                $builder->where('groups.user_id', $contextId);
                break;
            case self::VIEW_JOINED:
                $builder->join('group_members AS gm', function (JoinClause $join) use ($contextId) {
                    $join->on('gm.group_id', '=', 'groups.id')
                        ->where('gm.user_id', $contextId)
                        ->where('groups.is_approved', 1);
                });
                break;
            case self::VIEW_INVITED:
                $builder->whereHas('invites', function (Builder $query) use ($contextId) {
                    $query->where('owner_id', $contextId);
                    $query->where('status_id', Invite::STATUS_PENDING);
                    $query->where('invite_type', InviteType::INVITED_MEMBER);
                })->where('groups.is_approved', 1);
                break;
            case Browse::VIEW_SEARCH:
                if (!$context->hasPermissionTo('group.approve')) {
                    $builder->where(function (Builder $builder) use ($contextId) {
                        $builder->where('groups.is_approved', 1)
                            ->orWhere('groups.user_id', '=', $contextId);
                    });
                }

                break;
            default:
                $builder->where('groups.is_approved', 1);
        }
    }
}
