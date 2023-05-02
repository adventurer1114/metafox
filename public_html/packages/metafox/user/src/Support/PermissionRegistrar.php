<?php

namespace MetaFox\User\Support;

use Illuminate\Cache\CacheManager;
use MetaFox\Authorization\Models\Permission;
use MetaFox\Platform\Contracts\User;

class PermissionRegistrar extends \Spatie\Permission\PermissionRegistrar implements \MetaFox\User\Contracts\PermissionRegistrar
{
    /** @var array<string, mixed> */
    protected $permissions;

    /**
     * [
     *      1 => [
     *           'feed.view' => true,
     *          'feed.create' => true,
     *      ]
     * ].
     * @var array<int, array<string, bool>>
     */
    protected array $permissionViaRole = [];

    public function getPermissionViaRole(User $user, string $permission): ?bool
    {
        if (!array_key_exists($user->entityId(), $this->permissionViaRole)) {
            return null;
        }

        if (!array_key_exists($permission, $this->permissionViaRole[$user->entityId()])) {
            return null;
        }

        return $this->permissionViaRole[$user->entityId()][$permission];
    }

    public function setPermissionViaRole(User $user, string $permission, bool $value): void
    {
        $this->permissionViaRole[$user->entityId()][$permission] = $value;
    }

    public function __construct(CacheManager $cacheManager)
    {
        parent::__construct($cacheManager);

        $this->init();
    }

    private function init()
    {
        if ($this->permissions === null) {
            $this->permissions = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
                $results = [];

                $permissions = $this->getPermissionClass()
                    ->with('roles')
                    ->where('guard_name', '=', 'api')
                    ->get()
                    ->keyBy('name');

                foreach ($permissions as $name => $data) {
                    $results[$name] = $data;
                }

                return $results;
            });
        }
    }

    /**
     * Get the permission based on name.
     *
     * @param string $name
     *
     * @return ?Permission
     */
    public function getPermission(string $name): ?Permission
    {
        if (!array_key_exists($name, $this->permissions)) {
            return null;
        }

        return $this->permissions[$name];
    }
}
