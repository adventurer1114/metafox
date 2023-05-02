<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Traits;

use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Exceptions\ValidateUserException;
use MetaFox\User\Models\User as ModelsUser;

/**
 * Trait HasValidateUserTrait.
 *
 * @mixin AbstractRepository
 */
trait HasValidateUserTrait
{
    public function validateUser(User $user): void
    {
        if ($this->isBanned($user->entityId())) {
            throw new ValidateUserException([
                'title'   => __p('user::phrase.banned_account'),
                'message' => __p('user::phrase.user_is_banned'),
            ]);
        }

        if (!$user->isApproved()) {
            throw new ValidateUserException([
                'title'   => __p('user::phrase.pending_accounts'),
                'message' => __p('user::phrase.your_account_is_now_waiting_for_approval'),
            ]);
        }

        if ($user instanceof ModelsUser && !$user->hasVerifiedEmail()) {
            throw new ValidateUserException([
                'title'   => __p('user::phrase.pending_email_verification_title'),
                'message' => __p('user::phrase.pending_email_verification'),
                'action'  => 'verify',
            ]);
        }
    }
}
