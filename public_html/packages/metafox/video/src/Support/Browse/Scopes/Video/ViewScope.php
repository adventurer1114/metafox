<?php

namespace MetaFox\Video\Support\Browse\Scopes\Video;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;
use MetaFox\Video\Models\Video;

class ViewScope extends BaseScope
{
    public const VIEW_DEFAULT = Browse::VIEW_ALL;

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
    protected User $user;

    /**
     * @var bool
     */
    protected bool $isViewOwner = false;

    /**
     * @var int
     */
    protected int $profileId = 0;

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
     * @return bool
     */
    public function isViewOwner(): bool
    {
        return $this->isViewOwner;
    }

    /**
     * @param bool $isViewOwner
     *
     * @return ViewScope
     */
    public function setIsViewOwner(bool $isViewOwner): self
    {
        $this->isViewOwner = $isViewOwner;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserContext(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return ViewScope
     */
    public function setUserContext(User $user): self
    {
        $this->user = $user;

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
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->isViewOwner()) {
            return;
        }

        $table       = $model->getTable();
        $view        = $this->getView();
        $userContext = $this->getUserContext();

        switch ($view) {
            case Browse::VIEW_MY:
                $builder->where($this->alias($table, 'is_approved'), 1)
                    ->where('videos.user_id', '=', $userContext->entityId());
                break;
            case Browse::VIEW_FRIEND:
                $builder->join('friends AS f', function (JoinClause $join) use ($userContext) {
                    $join->on('f.user_id', '=', 'videos.owner_id')
                        ->where([
                            ['f.owner_id', '=', $userContext->entityId()],
                            ['videos.in_process', '=', Video::VIDEO_READY],
                            ['videos.is_approved', '=', 1],
                        ]);
                });
                break;
            case Browse::VIEW_PENDING:
                $builder->where($this->alias($table, 'is_approved'), '=', 0);
                if ($this->getProfileId() == 0) {
                    $builder->whereColumn('videos.user_id', '=', 'videos.owner_id');
                }
                break;
            case Browse::VIEW_MY_PENDING:
                $builder->where($this->alias($table, 'is_approved'), '=', 0)
                    ->where($this->alias($table, 'user_id'), $userContext->entityId())
                    ->where($this->alias($table, 'owner_type'), $userContext->entityType());
                break;
            default:
                $builder->where([
                    ['videos.in_process', '=', Video::VIDEO_READY],
                ]);

                switch ($view) {
                    case Browse::VIEW_SEARCH:
                        $builder->where(function (Builder $builder) use ($userContext, $table) {
                            $builder->where($this->alias($table, 'is_approved'), 1)
                                ->orWhere($this->alias($table, 'user_id'), '=', $userContext->entityId());
                        });
                        break;
                    default:
                        $builder->where($this->alias($table, 'is_approved'), '=', 1);
                        break;
                }
        }
    }
}
