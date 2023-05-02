<?php

namespace MetaFox\Authorization\Repositories;

use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use MetaFox\Authorization\Models\UserDevice as Model;

/**
 * Interface DeviceRepositoryInterface.
 *
 * @method Model getModel()
 * @method Model find($id, $columns = ['*'])
 */
interface DeviceRepositoryInterface
{
    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Model
     */
    public function updateOrCreateDevice(User $context, array $attributes = []): Model;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Model
     */
    public function createDevice(User $context, array $attributes = []): Model;

    /**
     * @param  User           $context
     * @param  string         $deviceId
     * @return ?array<string>
     */
    public function deleteDeviceById(User $context, string $deviceId): ?array;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Collection
     */
    public function getDevices(User $context, array $attributes = []): Collection;

    /**
     * @param  User          $context
     * @param  string|null   $platform
     * @return array<string>
     */
    public function getUserActiveTokens(User $context, ?string $platform = null): array;
}
