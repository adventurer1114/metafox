<?php

namespace MetaFox\Advertise\Support\Contracts;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Platform\Contracts\User;

interface SupportInterface
{
    /**
     * @return array
     */
    public function getPlacementTypes(): array;

    /**
     * @return string
     */
    public function getPendingActionStatus(): string;

    /**
     * @return array
     */
    public function getDisallowedUserRoleOptions(): array;

    /**
     * @return array
     */
    public function getUserRoleOptions(): array;

    /**
     * @return array
     */
    public function getDeleteOptions(): array;

    /**
     * @return array
     */
    public function getAdvertiseTypes(): array;

    /**
     * @param  User        $context
     * @param  bool        $isFree
     * @param  string|null $currencyId
     * @param  bool|null   $isActive
     * @return array
     */
    public function getPlacementOptions(User $context, bool $isFree = false, ?string $currencyId = null, ?bool $isActive = true): array;

    /**
     * @return array
     */
    public function getGenderOptions(): array;

    /**
     * @return array
     */
    public function getLanguageOptions(): array;

    /**
     * @return string
     */
    public function getCancelledPaymentStatus(): string;

    /**
     * @return string
     */
    public function getCompletedPaymentStatus(): string;

    /**
     * @return string
     */
    public function getPendingPaymentStatus(): string;

    /**
     * @return array
     */
    public function getAdvertiseStatusOptions(): array;

    /**
     * @return array
     */
    public function getActiveOptions(): array;

    /**
     * @return array
     */
    public function getAllowedViews(): array;

    /**
     * @param  Advertise $advertise
     * @return bool
     */
    public function isAdvertiseChangePrice(Advertise $advertise): bool;

    /**
     * @param  int        $placementId
     * @param  string     $currencyId
     * @return float|null
     */
    public function getPlacementPriceByCurrencyId(int $placementId, string $currencyId): ?float;

    /**
     * @param  User      $user
     * @param  bool|null $isActive
     * @return array
     */
    public function getAvailablePlacements(User $user, ?bool $isActive = true): array;

    /**
     * @param  Advertise  $advertise
     * @param  float      $placementPrice
     * @return float|null
     */
    public function calculateAdvertisePrice(Advertise $advertise, float $placementPrice): ?float;

    /**
     * @return array
     */
    public function getInvoiceStatuses(): array;

    /**
     * @return array
     */
    public function getAdvertiseStatuses(): array;

    /**
     * @return array
     */
    public function getInvoiceStatusOptions(): array;

    /**
     * @return array
     */
    public function getAllowedLocations(): array;

    /**
     * @param  Advertise $advertise
     * @return int|null
     */
    public function getAmount(Advertise $advertise): ?int;

    /**
     * @param  Advertise $advertise
     * @return int|null
     */
    public function getCurrentAmount(Advertise $advertise): ?int;

    /**
     * @return array
     */
    public function getInvoiceStatusColors(): array;

    /**
     * @param  string     $status
     * @return array|null
     */
    public function getInvoiceStatusInfo(string $status): ?array;

    /**
     * @return array
     */
    public function getActivePlacementsForSetting(): array;
}
