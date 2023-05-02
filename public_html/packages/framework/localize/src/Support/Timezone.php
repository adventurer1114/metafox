<?php

namespace MetaFox\Localize\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use MetaFox\Core\Support\CacheManager;
use MetaFox\Core\Support\Facades\Timezone as TimezoneFacade;
use MetaFox\Localize\Contracts\TimezoneSupportContract;
use MetaFox\Localize\Models\Timezone as Model;
use MetaFox\Platform\Facades\Settings;

class Timezone implements TimezoneSupportContract
{
    /**
     * @var array<int, Model>
     */
    private array $timezones;

    public function __construct()
    {
        $this->init();
    }

    public function getCacheName(): string
    {
        return CacheManager::CORE_TIMEZONE_CACHE;
    }

    public function clearCache(): void
    {
        Cache::forget($this->getCacheName());
    }

    /**
     * @return array<int, Model>
     */
    public function getTimezones(): array
    {
        return $this->timezones;
    }

    public function getTimezone(int $timezoneId): ?Model
    {
        return $this->timezones[$timezoneId] ?? null;
    }

    public function getAllActiveTimezones(): array
    {
        return Arr::where($this->timezones, function (Model $value) {
            return $value->is_active;
        });
    }

    /**
     * @return array<int, mixed>
     */
    public function getActiveOptions(): array
    {
        $collection = collect($this->timezones)->filter(function (Model $item) {
            return $item->is_active;
        });

        return $collection->map(function (Model $item) {
            return ['label' => $item->name, 'value' => $item->id];
        })->values()->toArray();
    }

    protected function init(): void
    {
        $this->timezones = Cache::remember(
            $this->getCacheName(),
            3000,
            function () {
                return Model::query()
                    ->orderBy('offset')
                    ->orderBy('id')
                    ->get()
                    ->keyBy('id')
                    ->all();
            }
        );
    }

    public function getName(?int $id)
    {
        if (!$id) {
            return null;
        }

        return Cache::rememberForever('timezone_' . $id, function () use ($id) {
            /** @var ?Model $model */
            $model = Model::query()->find($id);

            return $model?->name;
        });
    }

    public function getTimezoneByName(?string $name): ?Model
    {
        if (null === $name) {
            return null;
        }

        $collection = collect($this->timezones)->filter(function (Model $item) use ($name) {
            return $item->name == $name;
        });

        if ($collection->count()) {
            return $collection->first();
        }

        return null;
    }

    public function getDefaultTimezoneId(): int
    {
        $defaultTimezone = Settings::get('localize.default_timezone');

        if (is_string($defaultTimezone)) {
            $defaultTimezone = TimezoneFacade::getTimezoneByName($defaultTimezone);

            if (null !== $defaultTimezone) {
                return $defaultTimezone->entityId();
            }
        }

        return 0;
    }
}
