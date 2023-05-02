<?php

namespace MetaFox\User\Support\Browse\Scopes\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

class StatusScope extends BaseScope
{
    public const STATUS_DEFAULT = Browse::VIEW_ALL;

    /**
     * @return array<string>
     */
    public static function getAllowStatus(): array
    {
        return Arr::pluck(self::getStatusOptions(), 'value');
    }

    /**
     * @return array<string>
     */
    public static function getStatusOptions(): array
    {
        return [
            [
                'label' => __p('user::phrase.all_members'),
                'value' => Browse::VIEW_ALL,
            ],
            [
                'label' => __p('user::phrase.featured_members'),
                'value' => MetaFoxConstant::STATUS_FEATURED,
            ],
            [
                'label' => __p('user::phrase.online'),
                'value' => MetaFoxConstant::STATUS_ONLINE,
            ],
            [
                'label' => __p('user::phrase.pending_verification_members'),
                'value' => MetaFoxConstant::STATUS_PENDING_VERIFICATION,
            ],
            [
                'label' => __p('core::phrase.pending_approval'),
                'value' => MetaFoxConstant::STATUS_PENDING_APPROVAL,
            ],
            [
                'label' => __p('user::phrase.not_approved'),
                'value' => MetaFoxConstant::STATUS_NOT_APPROVED,
            ],
        ];
    }

    /**
     * @var string
     */
    private string $status = Browse::VIEW_ALL;

    /**
     * @param  string      $status
     * @return StatusScope
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $status = $this->getStatus();
        $table  = $model->getTable();

        switch ($status) {
            case MetaFoxConstant::STATUS_FEATURED:
                $builder->where($this->alias($table, 'is_featured'), 1);
                break;
            case MetaFoxConstant::STATUS_PENDING_VERIFICATION:
                $builder->whereNull($this->alias($table, 'email_verified_at'));
                break;
            case MetaFoxConstant::STATUS_APPROVED:
                $builder->where($this->alias($table, 'approve_status'), MetaFoxConstant::STATUS_APPROVED);
                break;
            case MetaFoxConstant::STATUS_ONLINE:
                $now = Carbon::now()->subMinutes(5);

                $builder->join('user_activities', function (JoinClause $join) use ($now, $table) {
                    $join->on('user_activities.id', '=', $this->alias($table, 'id'));
                    $join->where('user_activities.last_activity', '>=', $now);
                });
                break;
            case MetaFoxConstant::STATUS_PENDING_APPROVAL:
                $builder->where($this->alias($table, 'approve_status'), MetaFoxConstant::STATUS_PENDING_APPROVAL);
                break;
            case MetaFoxConstant::STATUS_NOT_APPROVED:
                $builder->where($this->alias($table, 'approve_status'), MetaFoxConstant::STATUS_NOT_APPROVED);
                break;
            default:
                break;
        }
    }
}
