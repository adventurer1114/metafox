<?php

namespace MetaFox\Advertise\Contracts;

use MetaFox\Advertise\Models\Invoice;
use MetaFox\Platform\Contracts\User;

interface AdvertisePaymentInterface
{
    /**
     * @param  Invoice $invoice
     * @return bool
     */
    public function isPriceChanged(Invoice $invoice): bool;

    /**
     * @param  User $user
     * @return bool
     */
    public function isFree(User $user): bool;

    /**
     * @param  User  $user
     * @return array
     */
    public function toPayment(User $user): array;

    /**
     * @param  Invoice $invoice
     * @return bool
     */
    public function toCompleted(Invoice $invoice): bool;

    /**
     * @param  float  $price
     * @param  string $currencyId
     * @return string
     */
    public function getChangePriceMessage(float $price, string $currencyId): string;

    /**
     * @return string
     */
    public function getFreePriceMessage(): string;
}
