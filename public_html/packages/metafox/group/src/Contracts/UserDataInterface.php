<?php

namespace MetaFox\Group\Contracts;

use MetaFox\Platform\Contracts\DataPrivacy\UserDataInterface as Base;
use MetaFox\Platform\Contracts\User;

interface UserDataInterface extends Base
{
    public function deleteRequestsBelongToUser(User $user): void;
}
