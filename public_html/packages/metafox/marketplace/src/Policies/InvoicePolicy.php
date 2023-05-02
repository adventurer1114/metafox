<?php

namespace MetaFox\Marketplace\Policies;

use MetaFox\Marketplace\Models\Invoice;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Platform\Contracts\User;
use MetaFox\Marketplace\Support\Facade\Listing as ListingFacade;

class InvoicePolicy
{
    public function beforePayment(User $user, ?Listing $listing): bool
    {
        if (!policy_check(ListingPolicy::class, 'payment', $user, $listing)) {
            return false;
        }

        return true;
    }

    public function payment(User $user, ?Listing $listing, ?int $gatewayId): bool
    {
        if (!policy_check(ListingPolicy::class, 'payment', $user, $listing)) {
            return false;
        }

        if (null === $gatewayId) {
            return false;
        }

        $hasAccess = app('events')->dispatch('payment.user_configuration.has_access', [$listing->userId(), $gatewayId], true);

        if (null === $hasAccess) {
            return true;
        }

        return (bool) $hasAccess;
    }

    public function repayment(User $user, ?Invoice $invoice, bool $checkChangePrice = false): bool
    {
        if (null === $invoice) {
            return false;
        }

        if ($invoice->status != ListingFacade::getInitPaymentStatus()) {
            return false;
        }

        if (!policy_check(ListingPolicy::class, 'payment', $user, $invoice->listing)) {
            return false;
        }

        if ($checkChangePrice) {
            if ($this->change($user, $invoice)) {
                return false;
            }
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        return true;
    }

    public function view(User $user, ?Invoice $invoice): bool
    {
        if (null === $invoice) {
            return false;
        }

        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if ($user->entityId() == $invoice->userId()) {
            return true;
        }

        return false;
    }

    public function change(User $user, ?Invoice $invoice, bool $checkRepayment = true): bool
    {
        if ($checkRepayment && !$this->repayment($user, $invoice)) {
            return false;
        }

        $listing = $invoice->listing;

        if (null === $listing) {
            return false;
        }

        if (!count($listing->price)) {
            return false;
        }

        $currentPrice = ListingFacade::getPriceByCurrency($invoice->currency, $listing->price);

        if (!is_numeric($currentPrice)) {
            return false;
        }

        if ($currentPrice != $invoice->price) {
            return true;
        }

        return false;
    }
}
