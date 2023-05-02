<?php

namespace MetaFox\Subscription\Listeners;

use MetaFox\Payment\Models\Order;
use MetaFox\Subscription\Models\SubscriptionInvoice;
use MetaFox\Subscription\Repositories\SubscriptionInvoiceRepositoryInterface;
use MetaFox\Subscription\Support\Helper;

class SubscriptionActivatedProcessedListener
{
    public function handle(?Order $order)
    {
        if ($order->itemType() != SubscriptionInvoice::ENTITY_TYPE) {
            return null;
        }

        $invoice = $order->item;

        if (null === $invoice) {
            return false;
        }

        if ($invoice->payment_status == Helper::getCompletedPaymentStatus()) {
            return false;
        }

        if ((float) $invoice->initial_price > 0 || $invoice->activeTransactions()->count()) {
            return false;
        }

        $params = [
            'total_paid'     => $invoice->initial_price,
            'transaction_id' => null,
        ];

        resolve(SubscriptionInvoiceRepositoryInterface::class)->updatePayment($order->itemId(), Helper::getCompletedPaymentStatus(), $params);
    }
}
