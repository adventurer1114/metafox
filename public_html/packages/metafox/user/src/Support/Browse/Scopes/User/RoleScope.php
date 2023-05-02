<?php

namespace MetaFox\User\Support\Browse\Scopes\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

class RoleScope extends BaseScope
{
    /**
     * @return array<string>
     */
    public static function getAllowRoles(): array
    {
        return collect(resolve(RoleRepositoryInterface::class)
            ->getRoleOptions())
            ->pluck('value')
            ->toArray();
    }

    /**
     * @var array
     */
    private array $roles;

    /**
     * @param ?array $roles
     * @return RoleScope
     */
    public function setRoles(?array $roles = null): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param ?string $role
     * @return RoleScope
     */
    public function setRole(?string $role = null): self
    {
        $this->roles = [$role];

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $roles = $this->getRoles();

        if (empty($roles)) {
            return;
        }

        $builder->whereHas('roles', function (Builder $q) use ($roles) {
            $q->whereIn('role_id', $roles);
        });
    }
}
