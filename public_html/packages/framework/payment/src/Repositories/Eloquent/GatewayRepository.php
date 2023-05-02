<?php

namespace MetaFox\Payment\Repositories\Eloquent;

use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Payment\Models\GatewayFilter;
use MetaFox\Payment\Policies\GatewayPolicy;
use MetaFox\Payment\Repositories\GatewayRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class GatewayRepository.
 * @method Gateway getModel()
 * @method Gateway find($id, $columns = ['*'])
 * @ignore
 * @codeCoverageIgnore
 */
class GatewayRepository extends AbstractRepository implements GatewayRepositoryInterface
{
    public function model(): string
    {
        return Gateway::class;
    }

    public function viewGateway(User $context, int $id): Gateway
    {
        policy_authorize(GatewayPolicy::class, 'view', $context);

        return $this->find($id);
    }

    public function viewGateways(User $context, array $attributes): Paginator
    {
        policy_authorize(GatewayPolicy::class, 'viewAny', $context);
        $limit = $attributes['limit'] ?? 0;

        return $this->getModel()->newQuery()
            ->orderByDesc('id')
            ->simplePaginate($limit);
    }

    public function updateGateway(User $context, int $id, array $attributes): Gateway
    {
        policy_authorize(GatewayPolicy::class, 'update', $context);

        $gateway = $this->find($id);
        $gateway->update($attributes);
        $gateway->refresh();

        return $gateway;
    }

    public function updateActive(User $context, int $id, int $isActive): bool
    {
        policy_authorize(GatewayPolicy::class, 'update', $context);
        $gateway = $this->find($id);

        return $gateway->update(['is_active' => $isActive]);
    }

    public function updateTestMode(User $context, int $id, int $isTestMode): bool
    {
        policy_authorize(GatewayPolicy::class, 'update', $context);
        $gateway = $this->find($id);

        return $gateway->update(['is_test' => $isTestMode]);
    }

    public function getActiveGateways(): Collection
    {
        return $this->getModel()->newQuery()
            ->with(['filters'])
            ->where('is_active', '=', 1)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getGatewaysForForm(User $context, array $params = []): Collection
    {
        $gateways = $this->getActiveGateways();

        return collect($gateways)
            ->filter(function (Gateway $gateway) use ($context, $params) {
                return $gateway->getService()->hasAccess($context, $params);
            });
    }

    public function getGatewayByService(string $service): ?Gateway
    {
        return $this->getModel()->newModelQuery()
            ->where('service', '=', $service)
            ->first();
    }

    public function getConfigurationGateways(): Collection
    {
        $services = resolve(DriverRepositoryInterface::class)
            ->getNamesHasHandlerClass(Constants::DRIVER_TYPE_USER_GATEWAY_FORM);

        if (!count($services)) {
            return collect();
        }

        $services = array_map(function ($service) {
            return str_replace('.gateway.user_form', '', $service);
        }, $services);

        return $this->getModel()->newModelQuery()
            ->whereIn('service', $services)
            ->where('is_active', '=', 1)
            ->get();
    }

    public function setupPaymentGateways(array $configs = []): void
    {
        foreach ($configs as $config) {
            try {
                $gateway = Gateway::query()->firstOrCreate([
                    'service' => $config['service'],
                ], $config);

                if (!$gateway instanceof Gateway) {
                    continue;
                }

                $this->addFilters($gateway, $config);
            } catch (Exception $e) {
                // silent
            }
        }
    }

    /**
     * @param  Gateway       $gateway
     * @param  array<string> $params
     * @return void
     */
    protected function addFilters(Gateway $gateway, array $params): void
    {
        $ids         = [];
        $filtersData = Arr::get($params, 'filters', []);

        if (!is_array($filtersData)) {
            return;
        }

        foreach ($filtersData as $entity) {
            $filter = GatewayFilter::query()->firstOrCreate(['entity_type' => $entity]);

            if (!$filter instanceof GatewayFilter) {
                continue;
            }

            $ids[] = $filter->entityId();
        }

        $gateway->filters()->sync($ids);
    }
}
