<?php

namespace MetaFox\Localize\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use MetaFox\Localize\Models\Timezone;
use MetaFox\Localize\Policies\TimezonePolicy;
use MetaFox\Localize\Repositories\TimezoneRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * @method Timezone find($id, $columns = ['*'])
 * @method Timezone getModel()
 */
class TimezoneRepository extends AbstractRepository implements TimezoneRepositoryInterface
{
    public function model(): string
    {
        return Timezone::class;
    }

    public function getTimeZones(): array
    {
        return $this->getModel()->newQuery()
            ->where('is_active', Timezone::IS_ACTIVE)
            ->get()
            ->pluck([], 'id')
            ->toArray();
    }

    public function getActiveTimeZones(): Collection
    {
        return $this->getModel()->newQuery()
            ->where('is_active', Timezone::IS_ACTIVE)
            ->get();
    }

    public function getActiveTimeZonesForForm(): array
    {
        return $this->getModel()
            ->newQuery()
            ->get(['name', 'id', 'is_active'])
            ->where('is_active', 1)
            ->map(function (Timezone $timezone) {
                return ['label' => $timezone->name, 'value' => $timezone->id];
            })
            ->toArray();
    }

    /**
     * @return Timezone|null
     */
    public function getFirstActiveTimeZone(): ?Timezone
    {
        /** @var Timezone $timezone */
        $timezone = $this->getModel()->newQuery()
            ->where('is_active', Timezone::IS_ACTIVE)
            ->first();

        return $timezone;
    }

    /**
     * @throws AuthorizationException
     */
    public function updateActive(User $context, int $id, bool $isActive): bool
    {
        $timezone = $this->find($id);

        policy_authorize(TimezonePolicy::class, 'update', $context);

        $valueSetting = Settings::get('localize.default_timezone', '');

        if (strcmp($valueSetting, $timezone->name)) {
            Settings::save(['localize.default_timezone' => '']);
        }

        return $timezone->update(['is_active' => $isActive]);
    }
}
