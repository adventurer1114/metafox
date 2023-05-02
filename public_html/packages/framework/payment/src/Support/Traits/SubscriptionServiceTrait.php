<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Support\Traits;

use MetaFox\Payment\Contracts\HasSupportSubscription;
use MetaFox\Payment\Models\Order;
use RuntimeException;

/**
 * Trait SubscriptionServiceTrait.
 */
trait SubscriptionServiceTrait
{
    public function cancelSubscription(Order $order): array
    {
        if ($order->isRecurringStatusCancelled()) {
            throw new RuntimeException('The subscription has already been cancelled.');
        }

        app('events')->dispatch('payment.cancel_recurring_processing', [$order]);

        $order->refresh();

        if (!$order->isRecurringOrder()) {
            throw new RuntimeException('Invalid recurring order.');
        }

        $service = $this->gatewayManager->getGatewayServiceById($order->gateway_id);
        if (!$service instanceof HasSupportSubscription) {
            throw new RuntimeException('Gateway does not support subscription payment');
        }

        $result = $service->cancelGatewaySubscription($order);
        if (!$result['status']) {
            throw new RuntimeException('Could not cancel recurring profile.');
        }

        $this->onSubscriptionCancelled($order);

        app('events')->dispatch('payment.cancel_recurring_processed', [$order]);

        return $result;
    }

    public function onSubscriptionActivated(Order $order, ?array $data = []): void
    {
        app('events')->dispatch('payment.subscription_activated_processing', [$order, $data]);

        $order->refresh();

        if (!$order->isRecurringStatusActive()) {
            $order->recurring_status = Order::RECURRING_STATUS_ACTIVE;
            $order->save();

            app('events')->dispatch('payment.subscription_activated', [$order]);
        }

        app('events')->dispatch('payment.subscription_activated_processed', [$order]);
    }

    public function onSubscriptionExpired(Order $order, ?array $data = []): void
    {
        app('events')->dispatch('payment.subscription_expired_processing', [$order, $data]);

        $order->refresh();

        if (!$order->isRecurringStatusEnded()) {
            $order->recurring_status = Order::RECURRING_STATUS_ENDED;
            $order->save();

            app('events')->dispatch('payment.subscription_expired', [$order]);
        }

        app('events')->dispatch('payment.subscription_expired_processed', [$order]);
    }

    public function onSubscriptionCancelled(Order $order, ?array $data = []): void
    {
        app('events')->dispatch('payment.subscription_cancelled_processing', [$order, $data]);

        $order->refresh();

        if (!$order->isRecurringStatusCancelled()) {
            $order->recurring_status = Order::RECURRING_STATUS_CANCELLED;
            $order->save();

            app('events')->dispatch('payment.subscription_cancelled_processed', [$order]);
        }

        app('events')->dispatch('payment.subscription_expired_processed', [$order]);
    }

    public function onRecurringPaymentFailure(Order $order, ?array $data = []): void
    {
        app('events')->dispatch('payment.recurring_payment_failure_processing', [$order, $data]);

        $order->refresh();

        if (!$order->isRecurringStatusFailed()) {
            $order->recurring_status = Order::RECURRING_STATUS_FAILED;
            $order->save();

            app('events')->dispatch('payment.recurring_payment_failed', [$order]);
        }

        app('events')->dispatch('payment.recurring_payment_failure_processed', [$order]);
    }
}
