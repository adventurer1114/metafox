<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform;

use App\Providers\AppServiceProvider;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MetaFox\App\Models\Package;
use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * WARNING:
 * In this time, it saves data to Redis/file, it's not good performance as it should be.
 *
 * ModuleManager should be improved:
 * - Prefer Object Cache than network base caching may save 10ms/req.
 * - Flush cache content based on .env or something else.
 * - Do not cache on Dev mode.
 * - Prefer export than use as Modules:: facade instead of calling in the instance.
 *
 * Class ModuleManager
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) @todo consider to reduce complexity.
 */
class ModuleManager
{
    /** @var bool */
    protected $installed = true;

    /**
     * @var string[]
     */
    protected $moduleNames;

    /**
     * @var string[]
     */
    protected $providers;

    /**
     * @var ModuleManager
     */
    private static $singleton;

    /**
     * Any data access to listeners collect should be cached.
     *
     * @var BasePackageSettingListener[]
     */
    protected $listeners;

    /**
     * ModuleManager constructor.
     */
    public function __construct()
    {
        static::$singleton = $this;
        $this->initModuleNames();
    }

    public static function instance(): self
    {
        if (self::$singleton == null) {
            self::$singleton = new self();
        }

        return self::$singleton;
    }

    /**
     * @return Collection<Package>
     */
    private function getModules(): Collection
    {
        try {
            return Package::query()
                ->where('is_active', '=', MetaFoxConstant::IS_ACTIVE)
                ->get();
        } catch (Exception) {
        }

        return new Collection();
    }

    private function initModuleNames(): void
    {
        $this->moduleNames = localCacheStore()->rememberForever('packages.moduleNames', function () {
            $this->getModules()->map(function (Package $module) {
                return $module->name;
            });
        });
    }

    /**
     * @return BasePackageSettingListener[]
     */
    private function scanListeners(): array
    {
        $platformInstalled = config('app.mfox_installed');
        $activePackages    = $this->getModules()->pluck('name')->toArray();

        return PackageManager::pluck(function (array $package) use ($platformInstalled, $activePackages) {
            if ($platformInstalled && !in_array($package['name'], $activePackages)) {
                return;
            }

            $class = sprintf('%s\\Listeners\\PackageSettingListener', $package['namespace']);
            if (class_exists($class)) {
                return new $class();
            }
        });
    }

    /**
     * @return BasePackageSettingListener[]
     */
    private function getListeners(): array
    {
        if (!$this->listeners) {
            $this->listeners = $this->scanListeners();
        }

        return $this->listeners;
    }

    /**
     * Can be invoked before booted.
     *
     * @param string $name
     *
     * @return array<string, array<string, mixed>>
     */
    private function getSettings(string $name): array
    {
        $result = [];

        $listeners = $this->getListeners();

        foreach ($listeners as $package => $listener) {
            $data  = $listener->handle($name);
            $alias = PackageManager::getAlias($package);
            if (!empty($data)) {
                $result[$alias] = $data;
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return array<mixed>
     */
    public function discoverSettingsPackageKey(string $name): array
    {
        return localCacheStore()->rememberForever(sprintf('packages.%s', $name), function () use ($name) {
            $result    = [];
            $listeners = $this->getListeners();

            foreach ($listeners as $package => $listener) {
                $data = $listener->handle($name);
                if (!empty($data)) {
                    $result[$package] = $data;
                }
            }

            return $result;
        });
    }

    /**
     * discover module settings collect settings from all active module PackageSettingListener class
     * to an array.
     *
     * etc: ModuleManager::discoverSettings('getEvents')
     *
     * @param string $name
     * @param bool   $cache
     *
     * @return array<mixed>
     */
    public function discoverSettings(string $name, $cache = true): array
    {
        if (!$cache) {
            return $this->getSettings($name);
        }

        return localCacheStore()->rememberForever(sprintf('modules_settings_%s', $name), function () use ($name) {
            return $this->getSettings($name);
        });
    }

    /**
     * Get provider names in schema packages.
     * @return string[]
     * @see \App\Providers\AppServiceProvider::discoverPackageProviders()
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Get all packages events.
     *
     * @return array<string, array<int, mixed>> ["eventName"=> [Listener::class, ...]]
     *
     * @see \App\Providers\EventServiceProvider::discoverPackageEvents()
     */
    public function getEvents(): array
    {
        $response = $this->getSettings('getEvents');
        $data     = [];

        if (!$response) {
            return $data;
        }
        foreach ($response as $row) {
            if (!$row) {
                continue;
            }
            foreach ($row as $event => $listeners) {
                if (!$listeners) {
                    continue;
                }
                foreach ($listeners as $listener) {
                    if ($listener) {
                        $data[$event][] = $listener;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return string[]|null
     * @see \App\Providers\AppServiceProvider::boot()
     */
    public function getDatabaseMigrationsFrom()
    {
        $files = [];

        PackageManager::pluck(function ($package) use (&$files) {
            $modulePath    = $package['path'];
            $module        = $package['alias'];
            $migrationPath = $modulePath . PackageManager::MIGRATION_PATH;
            if (is_dir(base_path($migrationPath))) {
                $files[Str::lower($module)] = base_path($migrationPath);
            }
        });

        return $files;
    }

    /**
     * Scan packages validated in method "getPolicyHandlers"
     * to result array.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) - @todo consider to reduce this method.
     * @return array<string, string> ["Module\Post"=>"Module\PostPolicy", .. ]
     */
    public function getPolicyHandlers(): array
    {
        return localCacheStore()->rememberForever('packages.getPolicyHandlers', function () {
            $data     = [];
            $response = $this->getSettings('getPolicyHandlers');

            if ($response) {
                foreach ($response as $row) {
                    if ($row) {
                        foreach ($row as $model => $policy) {
                            if (is_string($model) && is_string($policy) && $model && $policy) {
                                $data[$model] = $policy;
                            }
                        }
                    }
                }
            }

            return $data;
        });
    }

    /**
     * This method is called to assign scheduler. It should be run in console only.
     *
     *
     * @param Schedule $schedule
     *
     * @see \App\Console\Kernel::schedule()
     */
    public function registerApplicationSchedule(Schedule $schedule): void
    {
        /*
         * This method should be call on console only.
         * remove this method to others, because we should not reduce load time.
         * Check its carefully because it's need instance all listeners agains.
         */
        foreach ($this->getListeners() as $listener) {
            if (method_exists($listener, 'registerApplicationSchedule')) {
                $listener->registerApplicationSchedule($schedule);
            }
        }
    }
}
