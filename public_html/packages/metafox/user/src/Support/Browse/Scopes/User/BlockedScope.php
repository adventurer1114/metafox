<?php

namespace MetaFox\User\Support\Browse\Scopes\User;

use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

class BlockedScope extends BaseScope
{
    private int $contextId;

    private ?string $table = null;

    private ?string $primaryKey = null;

    /**
     * Get the value of contextId.
     */
    public function getContextId(): int
    {
        return $this->contextId;
    }

    /**
     * Set the value of contextId.
     *
     * @return self
     */
    public function setContextId(int $contextId)
    {
        $this->contextId = $contextId;

        return $this;
    }

    /**
     * Get the value of table.
     */
    public function getTable(): ?string
    {
        if (null === $this->table) {
            return 'users';
        }

        return $this->table;
    }

    /**
     * Set the value of table.
     *
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the value of primary key.
     */
    public function getPrimaryKey(): ?string
    {
        if (null === $this->primaryKey) {
            return 'id';
        }

        return $this->primaryKey;
    }

    /**
     * Set the value of primary key.
     *
     * @return self
     */
    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model): void
    {
        $this->buildQuery($builder);
    }

    public function applyQueryBuilder(QueryBuilder $builder): void
    {
        $this->buildQuery($builder);
    }

    protected function buildQuery(BuilderContract $builder): void
    {
        $contextId = $this->getContextId();

        if (!$contextId) {
            return;
        }

        $table = $this->getTable();

        $primaryKey = $this->getPrimaryKey();

        $builder->leftJoin(
            'user_blocked as blocked_owner',
            function (JoinClause $join) use ($contextId, $table, $primaryKey) {
                $join->on($table . '.' . $primaryKey, '=', 'blocked_owner.owner_id');
                $join->where('blocked_owner.user_id', '=', $contextId);
            }
        )->whereNull('blocked_owner.id');

        $builder->leftJoin(
            'user_blocked as blocked_user',
            function (JoinClause $join) use ($contextId, $table, $primaryKey) {
                $join->on($table . '.' . $primaryKey, '=', 'blocked_user.user_id');
                $join->where('blocked_user.owner_id', '=', $contextId);
            }
        )->whereNull('blocked_user.id');
    }
}
