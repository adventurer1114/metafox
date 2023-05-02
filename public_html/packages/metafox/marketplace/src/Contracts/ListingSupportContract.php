<?php

namespace MetaFox\Marketplace\Contracts;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Platform\Contracts\User;

interface ListingSupportContract
{
    /**
     * @return array
     */
    public function getPaymentStatus(): array;

    /**
     * @return string
     */
    public function getCompletedPaymentStatus(): string;

    /**
     * @return string
     */
    public function getPendingPaymentStatus(): string;

    /**
     * @return string
     */
    public function getInitPaymentStatus(): string;

    /**
     * @return string
     */
    public function getCanceledPaymentStatus(): string;

    /**
     * @return int
     */
    public function getMaximumTitleLength(): int;

    /**
     * @return int
     */
    public function getMinimumTitleLength(): int;

    /**
     * @return array
     */
    public function getInviteMethodTypes(): array;

    /**
     * @return string
     */
    public function getActiveStatus(): string;

    /**
     * @return string
     */
    public function getInactiveStatus(): string;

    /**
     * @return array
     */
    public function getInviteLinkStatus(): array;

    /**
     * @param  string      $currency
     * @param  float       $price
     * @return string|null
     */
    public function getPriceFormat(string $currency, float $price): ?string;

    /**
     * @param  User        $user
     * @param  array       $prices
     * @return string|null
     */
    public function getUserPriceFormat(User $user, array $prices): ?string;

    /**
     * @param  User       $user
     * @param  array      $prices
     * @return float|null
     */
    public function getUserPrice(User $user, array $prices): ?float;

    /**
     * @param  User       $user
     * @param  array      $prices
     * @return array|null
     */
    public function getUserPaymentInformation(User $user, array $prices): ?array;

    /**
     * @param  string     $currency
     * @param  array      $price
     * @return float|null
     */
    public function getPriceByCurrency(string $currency, array $price): ?float;

    /**
     * @return string
     */
    public function getInviteUserType(): string;

    /**
     * @param  string      $status
     * @return string|null
     */
    public function getStatusLabel(string $status): ?string;

    /**
     * @param  Listing|null $listing
     * @return bool
     */
    public function isExpired(?Listing $listing): bool;

    /**
     * @param  User  $user
     * @param  array $prices
     * @return bool
     */
    public function isFree(User $user, array $prices): bool;

    /**
     * @param  Listing     $listing
     * @param  bool        $isListing
     * @return string|null
     */
    public function getExpiredLabel(Listing $listing, bool $isListing = true): ?string;
}
