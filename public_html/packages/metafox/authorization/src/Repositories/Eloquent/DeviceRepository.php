<?php

namespace MetaFox\Authorization\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Authorization\Repositories\DeviceRepositoryInterface;
use MetaFox\Authorization\Models\UserDevice;
use MetaFox\Platform\Traits\Helpers\InputCleanerTrait;

/**
 * Class DeviceRepository.
 * @method UserDevice getModel()
 * @method UserDevice find($id, $columns = ['*'])
 */
class DeviceRepository extends AbstractRepository implements DeviceRepositoryInterface
{
    use InputCleanerTrait;

    public function model(): string
    {
        return UserDevice::class;
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreateDevice(User $context, array $attributes = []): UserDevice
    {
        $deviceUID = Arr::get($attributes, 'device_uid');
        $token     = Arr::get($attributes, 'device_token');

        $devices = $this->getDevices($context, [
            'device_uid'   => $deviceUID,
            'device_token' => $token,
        ]);
        if ($devices->isEmpty()) {
            return $this->createDevice($context, $attributes);
        }

        // Update the token and device with new user.
        // or
        // Create new one
        /** @var UserDevice $newDevice */
        $newDevice = $this->getModel()
            ->newModelQuery()
            ->firstOrNew([
                'device_uid'   => $deviceUID,
                'device_token' => $token,
            ], array_merge($attributes, [
                'user_id'   => $context->entityId(),
                'user_type' => $context->entityType(),
            ]));

        $newDevice->save();

        return $newDevice->refresh();
    }

    /**
     * @inheritDoc
     */
    public function deleteDeviceById(User $context, string $deviceId): ?array
    {
        $devices = $this->getModel()->newModelQuery()
            ->where('user_id', '=', $context->entityId())
            ->where('device_uid', '=', $deviceId)
            ->get()
            ->collect();

        if ($devices->isEmpty()) {
            return null;
        }

        $tokens = $devices->pluck('device_token')->toArray();

        $devices->each(function (UserDevice $device) {
            $device->delete();
        });

        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function getDevices(User $context, array $attributes = []): Collection
    {
        $deviceUID = Arr::get($attributes, 'device_uid');
        $token     = Arr::get($attributes, 'device_token');

        $query = $this->getModel()
            ->newModelQuery()
            ->where('user_id', '=', $context->entityId());

        if ($deviceUID) {
            $query->where('device_uid', '=', $deviceUID);
        }

        if ($token) {
            $query->where('device_token', '=', $token);
        }

        return $query
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->collect();
    }

    /**
     * @inheritDoc
     */
    public function getUserActiveTokens(User $context, ?string $platform = null): array
    {
        $query = $this->getModel()->newModelQuery()
            ->where('user_id', '=', $context->entityId())
            ->where('is_active', '=', 1);

        if ($platform) {
            $query = $query->where('platform', '=', $platform);
        }

        return $query
            ->get(['device_token'])
            ->pluck('device_token')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function createDevice(User $context, array $attributes = []): UserDevice
    {
        $device = new UserDevice();
        $device->fill(array_merge($attributes, [
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'is_active' => 1,
        ]));

        $device->save();

        return $device;
    }
}
