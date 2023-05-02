<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Contracts;

use Illuminate\Support\Collection;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;

/**
 * Interface Payment.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
interface GatewayManagerInterface
{
    /**
     * @return Collection
     */
    public function getActiveGateways(): Collection;

    /**
     * getGatewayById.
     *
     * @param  int      $gatewayId
     * @return ?Gateway
     */
    public function getGatewayById(int $gatewayId): ?Gateway;

    /**
     * getGatewayByName.
     *
     * @param  string   $gatewayName
     * @return ?Gateway
     */
    public function getGatewayByName(string $gatewayName): ?Gateway;

    /**
     * getGatewayServiceById.
     *
     * @param  int              $gatewayId
     * @return GatewayInterface
     */
    public function getGatewayServiceById(int $gatewayId): GatewayInterface;

    /**
     * getGatewayServiceByName.
     *
     * @param  string           $gatewayName
     * @return GatewayInterface
     */
    public function getGatewayServiceByName(string $gatewayName): GatewayInterface;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $params
     * @return array<int, mixed>
     */
    public function getGatewaysForForm(User $context, array $params = []): array;
}
