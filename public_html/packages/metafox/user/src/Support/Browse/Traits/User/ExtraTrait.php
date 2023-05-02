<?php

namespace MetaFox\User\Support\Browse\Traits\User;

use MetaFox\Platform\Traits\Http\Resources\HasExtra;

trait ExtraTrait
{
    use HasExtra {
        getExtra as getMainExtra;
    }

    public function getExtra(): array
    {
        if (empty($this->resource)) {
            return [];
        }

        $permissions      = $this->getMainExtra();
        $context          = user();
        $extraPermissions = app('events')->dispatch('user.permissions.extra', [$context, $this->resource]);

        if (is_array($extraPermissions)) {
            foreach ($extraPermissions as $extraPermission) {
                if (is_array($extraPermission) && count($extraPermission)) {
                    $permissions = array_merge($permissions, $extraPermission);
                }
            }
        }

        return $permissions;
    }
}
