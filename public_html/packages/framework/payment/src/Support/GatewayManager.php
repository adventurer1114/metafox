<?php

namespace MetaFox\Payment\Support;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use MetaFox\Payment\Contracts\GatewayInterface;
use MetaFox\Payment\Contracts\GatewayManagerInterface;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Payment\Repositories\GatewayRepositoryInterface;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;

/**
 * Class Payment.
 */
class GatewayManager implements GatewayManagerInterface
{
    public GatewayRepositoryInterface $gatewayRepository;

    public function __construct(GatewayRepositoryInterface $gatewayRepository)
    {
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * @return Collection
     */
    public function getActiveGateways(): Collection
    {
        return $this->gatewayRepository->getActiveGateways();
    }

    public function getGatewayById(int $gatewayId): ?Gateway
    {
        return Gateway::find($gatewayId);
    }

    public function getGatewayByName(string $gatewayName): ?Gateway
    {
        return Gateway::firstWhere('service', $gatewayName);
    }

    public function getGatewayServiceById(int $gatewayId): GatewayInterface
    {
        try {
            $gateway = $this->getGatewayById($gatewayId);
            if (!$gateway instanceof Gateway) {
                throw new ItemNotFoundException('Payment gateway not found');
            }

            return $gateway->getService();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getGatewayServiceByName(string $gatewayName): GatewayInterface
    {
        try {
            $gateway = $this->getGatewayByName($gatewayName);
            if (!$gateway instanceof Gateway) {
                throw new ItemNotFoundException('Payment gateway not found');
            }

            return $gateway->getService();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function getGatewaysForForm(User $context, array $params = []): array
    {
        $options = $this->gatewayRepository->getGatewaysForForm($context, $params);

        return collect($options)->map(function (Gateway $gateway) use ($context, $params) {
            return [
                'label'       => $gateway->title,
                'value'       => $gateway->entityId(),
                'icon'        => $gateway->icon,
                'disabled'    => $gateway->getService()->isDisabled($context, $params),
                'description' => $gateway->getService()->describe($context, $params),
            ];
        })->values()->toArray();
    }
}
