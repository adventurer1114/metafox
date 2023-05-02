<?php

namespace MetaFox\User\Repositories;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\SocialAccount;

/**
 * Interface SocialAccountRepositoryInterface.
 * @mixin AbstractRepository
 */
interface SocialAccountRepositoryInterface
{
    /**
     * Check if social account exists.
     *
     * @param string       $providerUserId
     * @param string       $provider
     * @param array<mixed> $with           - relations.
     *
     * @return SocialAccount|null
     */
    public function findSocialAccount(string $providerUserId, string $provider, array $with = []): ?SocialAccount;

    /**
     * Create social account.
     *
     * @param string $providerUserId
     * @param string $provider
     * @param int    $userId
     *
     * @return SocialAccount
     */
    public function createSocialAccount(string $providerUserId, string $provider, int $userId): SocialAccount;

    /**
     * @param int $userId
     *
     * @return void
     */
    public function deleteSocialAccountsByUserId(int $userId);
}
