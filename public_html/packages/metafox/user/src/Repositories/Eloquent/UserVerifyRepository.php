<?php

namespace MetaFox\User\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserVerify;
use MetaFox\User\Notifications\VerifyEmail;
use MetaFox\User\Repositories\UserVerifyRepositoryInterface;

class UserVerifyRepository extends AbstractRepository implements UserVerifyRepositoryInterface
{
    public function model()
    {
        return UserVerify::class;
    }

    public function generate(User $user): UserVerify
    {
        $hash = sha1($user->email . $user->id . $user->entityType() . mt_rand(0, 1000));

        $timeout = Settings::get('user.verify_email_timeout', 60);

        if (!$timeout) {
            $timeout = 60;
        }

        $verification = $this->create([
            'user_id'    => $user->entityId(),
            'user_type'  => $user->entityType(),
            'action'     => 'verify_email',
            'email'      => $user->email,
            'hash_code'  => $hash,
            'expires_at' => Carbon::now()->addMinutes($timeout),
        ]);

        $verification->save();

        return $verification;
    }

    public function send(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            abort(400, __p('user::validation.verification_email'));
        }

        Notification::send($user, new VerifyEmail($user));
    }

    public function resend(User $user)
    {
        if (!$this->checkResendDelay($user)) {
            abort(400, __p('user::phrase.must_wait_to_resend_verification_email', [
                'duration' => Settings::get('user.resend_verification_email_delay_time'),
            ]));
        }

        $this->invalidatePending($user);

        $this->send($user);
    }

    public function checkResendDelay(User $user): bool
    {
        $delay = Settings::get('user.resend_verification_email_delay_time', 15);
        if ($delay <= 0) {
            return true;
        }

        return $this->getModel()->newQuery()
            ->where('created_at', '>=', Carbon::now()->subMinute($delay))
            ->where([
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
            ])->doesntExist();
    }

    public function invalidatePending(User $user)
    {
        return $this->getPendingQuery($user)->update([
            'expires_at' => Carbon::now(),
        ]);
    }

    public function cleanupPending()
    {
        $maxPendingVerificationDuration = (int) Settings::get('user.days_for_delete_pending_user_verification', 0);

        if (!$maxPendingVerificationDuration) {
            return;
        }

        User::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', Carbon::now()->subDays($maxPendingVerificationDuration))
            ->each(function ($user) {
                $user?->delete();
            });
    }

    private function getPendingQuery(User $user): Builder
    {
        return $this->query()
            ->where('expires_at', '>=', Carbon::now())
            ->where([
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
            ]);
    }
}
