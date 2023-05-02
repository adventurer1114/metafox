<?php

namespace MetaFox\User\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\User\Models\User as ModelsUser;
use MetaFox\User\Policies\UserPolicy;

class UserExtraPermissionListener
{
    /**
     * @param  User                 $context
     * @param  User|null            $user
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(User $context, ?User $user): array
    {
        /**
         * @var UserPolicy $userPolicy
         */
        $userPolicy = PolicyGate::getPolicyFor(ModelsUser::class);

        return [
            'can_add_cover'    => $userPolicy->uploadCover($context, $user),
            'can_edit_cover'   => $userPolicy->editCover($context, $user),
            'has_admin_access' => $context->hasPermissionTo('admincp.has_admin_access'),
        ];
    }
}
