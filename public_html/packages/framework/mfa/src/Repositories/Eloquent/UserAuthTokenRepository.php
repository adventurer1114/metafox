<?php

namespace MetaFox\Mfa\Repositories\Eloquent;

use Carbon\Carbon;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Mfa\Repositories\UserAuthTokenRepositoryInterface;
use MetaFox\Mfa\Models\UserAuthToken;
use MetaFox\Platform\Contracts\User;
use Illuminate\Support\Str;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class UserAuthTokenRepository.
 */
class UserAuthTokenRepository extends AbstractRepository implements UserAuthTokenRepositoryInterface
{
    public function model()
    {
        return UserAuthToken::class;
    }

    public function generateTokenForUser(User $user, int $lifetime = 5): UserAuthToken
    {
        /** @var UserAuthToken $userAuthToken */
        $userAuthToken = $this->getModel()->fill([
            'user_id'    => $user->entityId(),
            'user_type'  => $user->entityType(),
            'value'      => $this->generateTokenValue(),
            'expired_at' => Carbon::now()->addMinutes($lifetime),
        ]);

        $userAuthToken->save();

        return $userAuthToken;
    }

    public function findByTokenValue(string $mfaToken): ?UserAuthToken
    {
        return $this->getModel()->where('value', $mfaToken)->first();
    }

    private function generateTokenValue(): string
    {
        do {
            $token = Str::random(100);
        } while ($this->findByTokenValue($token));

        return $token;
    }

    public function deleteTokensByUserId(int $userId)
    {
        $this->deleteWhere([
            'user_id' => $userId,
        ]);
    }
}
