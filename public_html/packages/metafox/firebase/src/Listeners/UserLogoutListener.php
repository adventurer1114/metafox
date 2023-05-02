<?php

namespace MetaFox\Firebase\Listeners;

use Illuminate\Http\Request;
use MetaFox\Authorization\Repositories\DeviceRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserLogoutListener
{
    public function handle(User $user, Request $request): void
    {
        $deviceId = $request->get('device_id');

        if (!$deviceId) {
            return;
        }

        resolve(DeviceRepositoryInterface::class)->deleteDeviceById($user, $deviceId);
    }
}
