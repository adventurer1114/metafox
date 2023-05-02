<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Support;

use Illuminate\Support\Arr;
use MetaFox\Payment\Contracts\GatewayInterface;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Payment\Repositories\OrderRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class Notification.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class AbstractPaymentGateway implements GatewayInterface
{
    /** @var array<string, string> */
    protected array $billingFrequency = [];

    protected Gateway $gateway;

    protected OrderRepositoryInterface $orderRepository;

    public function __construct(Gateway $gateway, OrderRepositoryInterface $orderRepository)
    {
        $this->setGateway($gateway);

        $this->orderRepository = $orderRepository;
    }

    protected function getReturnUrl(): string
    {
        return url_utility()->makeApiFullUrl('payment/return');
    }

    protected function getCancelUrl(): string
    {
        return url_utility()->makeApiFullUrl('payment/cancel');
    }

    public function setGateway(Gateway $gateway): GatewayInterface
    {
        $this->gateway = $gateway;

        return $this;
    }

    protected function getGateway(): Gateway
    {
        return $this->gateway;
    }

    /**
     * @inheritDoc
     */
    public function hasAccess(User $context, array $params): bool
    {
        $entityType = Arr::get($params, 'entity_type');

        /*
         * Migration for old definition
         */
        if (null === $entityType) {
            $entityType = Arr::get($params, 'entity');
        }

        $entityId   = Arr::get($params, 'entity_id');

        if (!$this->hasAccessViaFilterMode($entityType)) {
            return false;
        }

        if (!$this->hasAccessViaItem($context, $entityType, $entityId)) {
            return false;
        }

        return true;
    }

    protected function hasAccessViaItem(User $context, ?string $entityType, ?int $entityId): bool
    {
        if (null === $entityType) {
            return false;
        }

        /*
         * If not define entityId, we will not verify permission
         */
        if (null === $entityId) {
            return true;
        }

        $gateway = $this->getGateway();

        $access = app('events')->dispatch('payment.gateway.has_access', [$context, $entityType, $entityId, $gateway], true);

        if (null === $access) {
            return true;
        }

        return (bool) $access;
    }

    protected function hasAccessViaFilterMode(?string $entityType): bool
    {
        $gateway    = $this->getGateway();

        $filterMode = $gateway->filter_mode;

        $filters    = $gateway->filter_list;

        if (null === $filters) {
            return true;
        }

        if (null === $entityType) {
            return false;
        }

        return match ($filterMode) {
            'whitelist' => in_array($entityType, $filters, true),
            'blacklist' => !in_array($entityType, $filters, true),
            default     => false,
        };
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(User $context, array $params): bool
    {
        return false;
    }

    public function describe(User $context, array $params): ?string
    {
        return $this->getGateway()?->description;
    }

    public function getFormApiUrl(): ?string
    {
        return null;
    }

    public function getFormFieldRules(): array
    {
        return [];
    }
}
