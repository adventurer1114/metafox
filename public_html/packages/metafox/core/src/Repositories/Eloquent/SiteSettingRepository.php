<?php

namespace MetaFox\Core\Repositories\Eloquent;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MetaFox\Core\Constants;
use MetaFox\Core\Models\SiteSetting;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Contracts\SiteSettingRepositoryInterface;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\PackageManager;

/**
 * Class SiteSettingRepository.
 */
class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    /**
     * Keep name=> setting id, of settings with `is_auto` = 0.
     *
     * @var array<string, mixed>
     */
    protected array $nonAutoKeys = [];

    /**
     * Keep associate `key`=> `id`.
     *
     * @code
     * [ 'blog.privacy_id'=>true,
     *  'core.setting_version_id'=>4,
     *  ''
     * ]
     * @encode
     * @var array<string, mixed>
     */
    protected array $cachedValueBag = [];

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        try {
            $this->loadCachedValueBag();
            $this->loadNonAutoKeys();
        } catch (Exception) {
        }
    }

    /**
     * @param mixed  $value
     * @param string $type
     *
     * @return mixed
     */
    private function actualValue(mixed $value, string $type): mixed
    {
        switch ($type) {
            case MetaFoxDataType::BOOLEAN:
                return (bool) $value;
            case MetaFoxDataType::STRING:
                return (string) $value;
            case MetaFoxDataType::INTEGER:
                return (int) $value;
            default:
                return $value;
        }
    }

    public function createSetting(
        string $module,
        string $name,
        ?string $configName,
        ?string $envVar,
        mixed $value,
        string $type,
        bool $public,
        bool $auto
    ): bool {
        $model = $this->getByName($name);

        if (!$model) {
            $model = SiteSetting::query()->create([
                'module_id' => $module,
                'name'      => $name,
            ]);
        }

        $model->fill([
            'config_name'   => $configName,
            'package_id'    => PackageManager::getByAlias($module),
            'value_default' => $value,
            'env_var'       => $envVar,
            'type'          => $type,
            'is_auto'       => $auto,
            'is_public'     => $public,
        ]);

        $model->save();

        return true;
    }

    public function updateSetting(
        string $module,
        string $name,
        ?string $configName,
        ?string $envVar,
        mixed $value,
        string $type,
        bool $public,
        bool $auto
    ): bool {
        $model = $this->getByName($name);

        if (!$model) {
            $model = SiteSetting::query()->create([
                'module_id'     => $module,
                'name'          => $name,
                'value_default' => $value,
            ]);
        }

        $model->fill([
            'package_id'   => PackageManager::getByAlias($module),
            'name'         => $name,
            'env_var'      => $envVar,
            'value_actual' => $value,
            'config_name'  => $configName,
            'type'         => $type,
            'is_auto'      => $auto,
            'is_public'    => $public,
        ]);

        $model->save();

        return true;
    }

    public function setupPackageSettings(string $module, array $settings): array
    {
        $response = [];

        foreach ($settings as $name => $data) {
            // delete dirty settings.
            if ($data['is_deleted'] ?? false) {
                SiteSetting::query()->where([
                    'name' => $name,
                ])->delete();
                continue;
            }

            $configName   =  $data['config_name'] ?? null;
            $envVar       = $data['env_var'] ?? null;
            $valueDefault = null;
            if ($configName) {
                $valueDefault = config($configName);
            }
            if (null === $valueDefault && $envVar) {
                $valueDefault = env($envVar);
            }

            if (null === $valueDefault) {
                $valueDefault = $data['value'] ?? null;
            }

            // force map to another modules. by 'module_id'
            $name = sprintf('%s.%s', $data['module_id'] ?? $module, $name);
            // get by default value instead of env_value to correct typeof
            $type        = $data['type'] ?? gettype($valueDefault ?? 'string');
            $actualValue = $this->actualValue($valueDefault, $type);
            $this->createSetting(
                $module,
                $name,
                $configName,
                $envVar,
                $actualValue,
                $type,
                $data['is_public'] ?? true,
                $data['is_auto'] ?? true
            );

            Arr::set($response, $name, $actualValue);
        }

        return $response;
    }

    public function destroy(string $module, ?array $names = null): bool
    {
        if (null !== $names && empty($names)) {
            return true;
        }

        $query = SiteSetting::query();

        $query = $query->where('module_id', '=', $module);

        if (null !== $names) {
            $query = $query->whereIn('name', $names);
        }

        return (bool) $query->delete();
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->nonAutoKeys)) {
            return true;
        }

        return Arr::has($this->cachedValueBag, $key);
    }

    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->nonAutoKeys)) {
            return $this->getValueFromDatabase($this->nonAutoKeys[$key]);
        }

        return Arr::get($this->cachedValueBag, $key, $default);
    }

    public function getNames(string $module)
    {
    }

    private function getByName(string $name): ?SiteSetting
    {
        /** @var SiteSetting $model */
        $model = SiteSetting::query()
            ->where('name', '=', $name)
            ->first();

        return $model;
    }

    /**
     * Calculate dotted key of array which value is array.
     *
     * @param  string $prefix
     * @param  array  $values
     * @param  array  $keys
     * @return void
     */
    private function getArrayNestedKeys(string $prefix, array $values, array &$keys): void
    {
        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            $keys[] = $newKey;
            $this->getArrayNestedKeys($newKey, $value, $keys);
        }
    }

    public function save(array $values): array
    {
        $response = [];

        $keys = [];
        $this->getArrayNestedKeys('', $values, $keys);

        $typeArray = SiteSetting::query()
            ->whereIn('name', $keys)
            ->where('type', '=', 'array')
            ->pluck('name');

        foreach ($typeArray as $name) {
            /** @var SiteSetting|null $model */
            $model = $this->getByName($name);

            if (!$model) {
                continue;
            }

            $value               = Arr::get($values, $name);
            $model->value_actual = $value;
            $model->save();

            Arr::set($values, $name, false);
        }

        $dotted = Arr::dot($values);

        foreach ($dotted as $settingName => $value) {
            /** @var SiteSetting|null $model */
            $model = $this->getByName($settingName);

            if (!$model instanceof SiteSetting) {
                continue;
            }

            // process array merged before
            if ($model->type === 'array') {
                continue;
            }

            $value               = $this->actualValue($value, $model->type);
            $model->value_actual = $value;
            $model->save();

            Arr::set($response, $settingName, $value);
        }

        return $response;
    }

    public function reset(string $module, ?array $names = null): bool
    {
        if (null !== $names && empty($names)) {
            return true;
        }

        $query = SiteSetting::query();

        $query = $query->where('module_id', '=', $module);

        if (null !== $names) {
            $query = $query->whereIn('name', $names);
        }

        return (bool) $query->update(['value_actual' => null]);
    }

    public function refresh(): void
    {
        try {
            $this->save([
                'core.setting_version_id' => Carbon::now()->timestamp,
                'core.setting_updated_at' => Carbon::now(),
            ]);

            $this->loadCachedValueBag();
            $this->loadNonAutoKeys();
        } catch (Exception $e) {
            Log::channel('dev')->info($e->getMessage());
        }
    }

    public function versionId(): int
    {
        return (int) $this->get('core.setting_version_id');
    }

    public function updatedAt(): string
    {
        return (string) $this->get('core.setting_updated_at');
    }

    private function loadNonAutoKeys(): void
    {
        $this->nonAutoKeys = SiteSetting::query()
            ->where('is_auto', '=', 0)
            ->pluck('id', 'name')->toArray();
    }

    public function bootingKernelConfigs(): void
    {
        $versionId = self::versionId();

        if (config('core.setting_version_id') == $versionId) {
            return;
        }

        $config = $this->loadConfigValues();

        Config::set($config);
    }

    public function loadConfigValues(): array
    {
        // should run php artisan config:cache to speed up optimization
        Log::channel('dev')->debug(sprintf('%s update', 'loadConfigValues'));

        /** @var SiteSetting[]|Collection $settings */
        $settings = SiteSetting::query()
            ->whereNotNull('config_name')
            ->orderBy('config_name', 'asc')
            ->cursor();

        $arr = [];

        foreach ($settings as $setting) {
            $key = $setting->config_name;
            if (!$key) {
                continue;
            }

            $value = $setting->value;

            if (empty($value)) {
                continue;
            }

            $arr[$key] = $value;
        }

        $arr['core.setting_version_id'] = $this->versionId();

        return $arr;
    }

    private function loadCachedValueBag(): void
    {
        $this->cachedValueBag = localCacheStore()->rememberForever(
            __METHOD__,
            function () {
                /** @var SiteSetting[]|Collection $settings */
                $settings = SiteSetting::query()
                    ->where('is_auto', '=', 1)
                    ->get([
                        'name',
                        'type',
                        'value_actual',
                        'value_default',
                    ]);

                $arr = [];

                foreach ($settings as $setting) {
                    Arr::set($arr, $setting->name, $setting->value);
                }

                return $arr;
            }
        );
    }

    /**
     * @param SiteSetting $setting
     *
     * @return mixed
     */
    private function getActualValue(SiteSetting $setting)
    {
        return null === $setting->value_actual ? $setting->value_default : $setting->value_actual;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    private function getValueFromDatabase(int $id): mixed
    {
        /** @var SiteSetting $setting */
        $setting = SiteSetting::query()->find($id, ['value_actual', 'value_default']);

        return $setting?->value;
    }

    public function keys(): array
    {
        return array_keys($this->cachedValueBag) + array_keys($this->nonAutoKeys);
    }

    public function getSiteSettings(string $for, bool $loadFromDriver): array
    {
        $aliases = resolve('core.packages')->getActivePackageAliases();
        $bag     = [];

        /** @var SiteSetting[]|Collection $settings */
        $settings = SiteSetting::query()
            ->whereIn('module_id', $aliases)
            ->where('is_public', '=', 1)
            ->get(['type', 'name', 'value_actual', 'value_default']);

        foreach ($aliases as $alias) {
            Arr::set($bag, $alias, new \stdClass());
        }

        Arr::set($bag, 'app.env', config('app.env'));
        foreach ($settings as $setting) {
            Arr::set($bag, $setting->name, $setting->value);
        }

        if ($loadFromDriver) {
            $this->loadFromDriver($for, $bag);
        }

        return $bag;
    }

    private function loadFromDriver(string $for, array &$result): void
    {
        $drivers = resolve(DriverRepositoryInterface::class)
            ->loadDrivers(Constants::DRIVER_TYPE_PACKAGE_SETTING, null);

        $method = 'getWebSettings';

        if ($for === 'mobile') {
            $method = 'getMobileSettings';
        }

        foreach ($drivers as $driver) {
            [, $class, , , $packageId] = $driver;

            if (!class_exists($class)) {
                continue;
            }

            $setting = resolve($class);

            if (!method_exists($setting, $method)) {
                continue;
            }

            $alias = PackageManager::getAliasFor($packageId, $for);

            $data = app()->call([$setting, $method]);

            foreach ($data as $name => $value) {
                Arr::set($result, sprintf('%s.%s', $alias, $name), $value);
            }
        }
    }

    public function mockValues(array $values)
    {
        foreach ($values as $name => $value) {
            Arr::set($this->cachedValueBag, $name, $value);
        }
    }
}
