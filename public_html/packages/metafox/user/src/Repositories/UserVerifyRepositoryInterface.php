<?php

namespace MetaFox\User\Repositories;

use MetaFox\User\Models\User;
use MetaFox\User\Models\UserVerify;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface UserVerify.
 * @mixin BaseRepository
 */
interface UserVerifyRepositoryInterface
{
    public function generate(User $user): UserVerify;

    public function send(User $user);

    public function resend(User $user);

    public function checkResendDelay(User $user): bool;

    public function invalidatePending(User $user);

    public function cleanupPending();
}
