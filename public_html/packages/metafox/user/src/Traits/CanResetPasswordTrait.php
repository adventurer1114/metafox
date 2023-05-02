<?php

namespace MetaFox\User\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use MetaFox\User\Contracts\CanResetPassword;
use MetaFox\User\Models\PasswordResetToken;
use MetaFox\User\Models\User;
use MetaFox\User\Notifications\ResetPasswordTokenNotification;

/**
 * @property Collection $resetTokens
 * @mixin CanResetPassword
 * @mixin User
 */
trait CanResetPasswordTrait
{
    public function sendPasswordResetToken(PasswordResetToken $token, string $channel = 'mail', string $as = 'token'): void
    {
        $this->notify(new ResetPasswordTokenNotification($token, $channel, $as));
    }

    public function resetTokens(): HasMany
    {
        return $this->hasMany(PasswordResetToken::class, 'user_id', 'id');
    }

    public function getEmailForPasswordReset(): string
    {
        $randomSeed = Str::random(random_int(1, 10));

        $parts = explode('@', $this->email);

        if (empty($parts)) {
            return '********';
        }

        $parts = collect($parts)
            ->map(function ($part, $key) use ($randomSeed) {
                if ($key === 0) {
                    return Str::mask($part . $randomSeed, '*', 3);
                }

                return '****.***';
            })
            ->values()
            ->toArray();

        return implode('@', $parts);
    }

    /**
     * @inheritDoc
     */
    public function getResetMethods(): array
    {
        return [
            [
                'label' => $this->getEmailForPasswordReset(),
                'value' => 'mail',
            ],
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
    }
}
