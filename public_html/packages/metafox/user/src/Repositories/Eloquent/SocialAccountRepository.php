<?php

namespace MetaFox\User\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\SocialAccount;
use MetaFox\User\Repositories\SocialAccountRepositoryInterface;

/**
 * Class SocialAccountRepository.
 *
 * @property SocialAccount $model
 */
class SocialAccountRepository extends AbstractRepository implements SocialAccountRepositoryInterface
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return SocialAccount::class;
    }

    public function findSocialAccount(string $providerUserId, string $provider, $with = []): ?SocialAccount
    {
        /** @var SocialAccount $socialAccount */
        $socialAccount = SocialAccount::query()
            ->with($with)
            ->where('provider_user_id', '=', $providerUserId)
            ->where('provider', '=', $provider)
            ->first();

        if ($socialAccount == null) {
            return null;
        }

        return $socialAccount;
    }

    public function createSocialAccount(string $providerUserId, string $provider, int $userId): SocialAccount
    {
        // Save to social account.
        $socialAccount                   = new SocialAccount();
        $socialAccount->provider_user_id = $providerUserId;
        $socialAccount->provider         = $provider;
        $socialAccount->user_id          = $userId;
        $socialAccount->save();

        $socialAccount->refresh();

        return $socialAccount;
    }

    public function deleteSocialAccountsByUserId(int $userId)
    {
        $this->deleteWhere([
            'user_id' => $userId,
        ]);
    }
}
