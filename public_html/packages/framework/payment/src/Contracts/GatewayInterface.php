<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Contracts;

use MetaFox\Payment\Models\Gateway;
use MetaFox\Payment\Models\Order;
use MetaFox\Platform\Contracts\User;

/**
 * Interface GatewayInterface.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
interface GatewayInterface
{
    /**
     * getGatewayServiceName.
     * @return string
     */
    public static function getGatewayServiceName(): string;

    /**
     * Set gateway config.
     * @param  Gateway $gateway
     * @return self
     */
    public function setGateway(Gateway $gateway): self;

    /**
     * createGatewayOrder.
     *
     * @param  Order                $order
     * @param  array<string, mixed> $params additional parameters
     * @return array<string, mixed>
     */
    public function createGatewayOrder(Order $order, array $params = []): array;

    /**
     * getGatewayTransaction.
     *
     * @param  string        $gatewayTransactionId
     * @return ?array<mixed>
     */
    public function getGatewayTransaction(string $gatewayTransactionId): ?array;

    /**
     * getGatewayOrder.
     *
     * @param  string        $gatewayOrderId
     * @return ?array<mixed>
     */
    public function getGatewayOrder(string $gatewayOrderId): ?array;

    /**
     * @param  User                 $context
     * @param  array<string ,mixed> $params
     * @return bool
     */
    public function hasAccess(User $context, array $params): bool;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $params
     * @return bool
     */
    public function isDisabled(User $context, array $params): bool;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $params
     * @return string|null
     */
    public function describe(User $context, array $params): ?string;

    /**
     * @return string|null
     */
    public function getFormApiUrl(): ?string;

    /**
     * @return array
     */
    public function getFormFieldRules(): array;
}
