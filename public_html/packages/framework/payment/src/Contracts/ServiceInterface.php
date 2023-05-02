<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Contracts;

use MetaFox\Payment\Http\Resources\v1\Gateway\Admin\GatewayForm;
use MetaFox\Payment\Models\Order;
use RuntimeException;

/**
 * Class Payment.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
interface ServiceInterface
{
    /**
     * getManager.
     *
     * @return GatewayManagerInterface
     */
    public function getManager(): GatewayManagerInterface;

    /**
     * getGatewayAdminFormById.
     *
     * @param  int          $gatewayId
     * @return ?GatewayForm
     */
    public function getGatewayAdminFormById(int $gatewayId): ?GatewayForm;

    /**
     * getGatewayAdminFormByName.
     *
     * @param  string       $formName
     * @return ?GatewayForm
     */
    public function getGatewayAdminFormByName(string $formName): ?GatewayForm;

    /**
     * initOrder.
     *
     * @param  IsBillable $billable
     * @return Order
     */
    public function initOrder(IsBillable $billable): Order;

    /**
     * place recurring/onetime order
     * will be placed accordingly to the payment_type in toOrder().
     *
     * @param  Order            $order
     * @param  int              $gatewayId
     * @param  array<mixed>     $params    additional parameters
     * @return array<mixed>
     * @throws RuntimeException
     */
    public function placeOrder(Order $order, int $gatewayId, array $params = []): array;

    /**
     * cancelSubscription.
     *
     * @param  Order            $order
     * @return array<mixed>
     * @throws RuntimeException
     */
    public function cancelSubscription(Order $order): array;

    /**
     * onSubscriptionActivated.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onSubscriptionActivated(Order $order, ?array $data = []): void;

    /**
     * onSubscriptionExpired.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onSubscriptionExpired(Order $order, ?array $data = []): void;

    /**
     * onSubscriptionCancelled.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onSubscriptionCancelled(Order $order, ?array $data = []): void;

    /**
     * onRecurringPaymentFailure.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onRecurringPaymentFailure(Order $order, ?array $data = []): void;

    /**
     * onPaymentSuccess.
     *
     * @param  Order            $order
     * @param  array<mixed>     $transactionData
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onPaymentSuccess(Order $order, array $transactionData = [], ?array $data = []): void;

    /**
     * onPaymentPending.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $transactionData
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onPaymentPending(Order $order, ?array $transactionData = [], ?array $data = []): void;

    /**
     * onPaymentFailure.
     *
     * @param  Order            $order
     * @param  ?array<mixed>    $transactionData
     * @param  ?array<mixed>    $data
     * @return void
     * @throws RuntimeException
     */
    public function onPaymentFailure(Order $order, ?array $transactionData = [], ?array $data = []): void;

    /**
     * onWebhook.
     *
     * @param  array<mixed> $payload
     * @return void
     */
    public function onWebhook(array $payload = []): void;
}
