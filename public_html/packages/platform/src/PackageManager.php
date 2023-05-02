<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * Class PackageManager.
 */
class PackageManager
{
    public const MIGRATION_PATH = '/src/Database/Migrations';
    public const CONFIG_PATH    = '/config/config.php';
    public const ASSETS_FOLDER  = 'assets';
    public const ASSETS_PATH    = '/resources/assets';

    public static function checkActive(string $name): bool
    {
        $value = config("metafox.packages.$name");

        return !empty($value);
    }

    public static function isCore(string $name): bool
    {
        return config(sprintf('metafox.packages.%s.core', $name));
    }

    /**
     * @param  string                    $name
     * @return array<string, mixed>|null
     */
    public static function getInfo(string $name): ?array
    {
        return config("metafox.packages.$name");
    }

    public static function getName(string $name): string
    {
        $result = config(sprintf('metafox.packages.%s.name', $name));

        if (!$result) {
            $result = self::getByAlias($name);
        }

        return $result ?? '';
    }

    public static function getTitle(string $name): ?string
    {
        return config(sprintf('metafox.packages.%s.title', $name));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getNamespace(string $name): string
    {
        $value = config(sprintf('metafox.packages.%s.namespace', $name));

        return trim($value, '\\');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getAlias(string $name): string
    {
        $value = config(sprintf('metafox.packages.%s.alias', $name));

        return rtrim($value, '\\');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getFrontendAlias(string $name): string
    {
        $value = config(sprintf('metafox.packages.%s.frontendAlias', $name));

        return rtrim($value, '\\');
    }

    /**
     * @param  string      $name
     * @param  string|null $for
     * @return string
     */
    public static function getAliasFor(string $name, string $for = null): string
    {
        $config = config(sprintf('metafox.packages.%s', $name));
        $value  = null;

        switch ($for) {
            case 'mobile':
                $value = $config['mobileAlias'] ?? null;
                break;
            case 'admin':
            case 'web':
                $value = $config['frontendAlias'] ?? null;
                break;
        }

        if (!$value && $config) {
            $value = $config['alias'];
        }

        return rtrim($value, '\\');
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public static function getPath(string $name): ?string
    {
        $value = config(sprintf('metafox.packages.%s.path', $name));

        if ($value) {
            return rtrim($value, '\\');
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public static function getComposerJsonPath(string $name): ?string
    {
        $path = self::getPath($name);

        if (!is_string($path)) {
            return null;
        }

        $jsonFile = app()->basePath($path . DIRECTORY_SEPARATOR . 'composer.json');

        if (!file_exists($jsonFile)) {
            return null;
        }

        return $jsonFile;
    }

    /**
     * @param  string     $name
     * @return array|null
     */
    public static function getComposerJson(string $name): ?array
    {
        $file = self::getComposerJsonPath($name);

        if (!$file) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true);

        if (is_array($data)) {
            return $data;
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function getBasePath(string $name): string
    {
        $path = self::getPath($name);

        return base_path($path);
    }

    public static function getMigrationPath(string $name): string
    {
        return static::getPath($name) . self::MIGRATION_PATH;
    }

    public static function getConfigPath(string $name): string
    {
        return static::getPath($name) . self::CONFIG_PATH;
    }

    /**
     * Get internal assets path within packages directory.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getAssetPath(string $name): string
    {
        return static::getPath($name) . self::ASSETS_PATH;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public static function getConfig(string $name): array
    {
        $path = self::getConfigPath($name);

        if (!File::isFile($path)) {
            return [];
        }

        return app('files')->getRequire($path);
    }

    /**
     * @param string $name
     *
     * @return string[]
     */
    public static function getMigrations(string $name): array
    {
        $path = static::getMigrationPath($name) . DIRECTORY_SEPARATOR . '*.php';

        /** @var string[] $files */
        $files = app('files')->glob($path);

        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if (empty($files)) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);

        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        return $files;
    }

    /**
     * @return string[]
     */
    public static function getPackageNames(): array
    {
        $packageNames = [];

        /** @var array<string,mixed> $data */
        $data = config('metafox.packages');

        if (!empty($data)) {
            $packageNames = array_keys($data);
        }

        return $packageNames;
    }

    public static function getNameStudly(string $packageName): string
    {
        $value = Str::studly(config(sprintf('metafox.packages.%s.alias', $packageName)));

        return rtrim($value, '\\');
    }

    /**
     * Get the master seeder name in the database.
     *
     * @param string $packageName
     *
     * @return string[]
     */
    public static function getMasterSeederClasses(string $packageName): array
    {
        $namespace = static::getNamespace($packageName);

        if ($namespace) {
            $class = sprintf("%s\Database\Seeders\PackageSeeder", $namespace);

            if (class_exists($class)) {
                return [$class];
            }
        }

        return [];
    }

    /**
     * Get the master seeder name in the database.
     *
     * @param string $packageName
     *
     * @return string|null
     */
    public static function getSeeder(string $packageName): ?string
    {
        $namespace = static::getNamespace($packageName);

        if ($namespace) {
            $class = sprintf("%s\Database\Seeders\PackageSeeder", $namespace);

            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * @param Closure $callback
     *
     * @return array<mixed>
     */
    public static function pluck(Closure $callback): array
    {
        $result = [];

        $packages = config('metafox.packages');

        if (!empty($packages)) {
            foreach ($packages as $package) {
                $x = $callback($package);
                if (null !== $x) {
                    $result[$package['name']] = $x;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return string[]
     */
    public static function getResourceNames(string $name): array
    {
        $resources = [];
        $response  = ModuleManager::instance()->discoverSettingsPackageKey('getItemTypes');

        if (array_key_exists($name, $response)) {
            $resources = $response[$name];
        }

        return $resources;
    }

    /**
     * Get the listener class name of a package.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getListenerClass(string $name): string
    {
        return sprintf('%s\\Listeners\\PackageSettingListener', static::getNamespace($name));
    }

    /**
     * Resolve listener instance of a package.
     *
     * @param string $name Package name. Example: metafox/core
     *
     * @return BasePackageSettingListener|null
     */
    public static function getListener(string $name): ?BasePackageSettingListener
    {
        $class = self::getListenerClass($name);

        if (!class_exists($class, true)) {
            Log::channel('installation')->debug('Failed loading ' . $class);

            return null;
        }

        /* @var BasePackageSettingListener $listener */
        return resolve($class);
    }

    /**
     * Get package name by an alias name.
     *
     * @param string $alias
     *
     * @return string
     */
    public static function getByAlias(string $alias): string
    {
        $package = Arr::first(config('metafox.packages'), function ($item) use ($alias) {
            return $item['alias'] === $alias;
        });

        return $package ? $package['name'] : '';
    }

    /**
     * Create package name.
     *
     * @param  string $vendorName
     * @param  string $appName
     * @return string
     */
    public static function normalizePackageName(string $vendorName, string $appName): string
    {
        return Str::lower($vendorName) . '/' . Str::kebab($appName, '-');
    }

    /**
     * @param string       $package Package name to export file
     * @param string       $path    Path within package directory. Example: resources/lang/en.php
     * @param array<mixed> $data    Data to export. Example [[item=>1]]
     */
    public static function exportToFilesystem(string $package, string $path, array $data): string
    {
        $dir = self::getPath($package);

        if (!$dir) {
            throw new InvalidArgumentException(sprintf('Package not found %s', $package));
        }

        $baseFile = implode(DIRECTORY_SEPARATOR, [$dir, $path]);
        $filename = base_path() . DIRECTORY_SEPARATOR . $baseFile;

        // ensure dir is exists
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        if (file_exists($filename) && !is_writable($filename)) {
            throw new \RuntimeException('Could not write to ' . $filename);
        }

        export_to_file($filename, $data);

        return $baseFile;
    }

    /**
     * @param  string     $package  Package name. Example: metafox/blog
     * @param  string     $filename : related file name in under package. etc: resources/lang/en.php
     * @param  bool       $silent
     * @return array|null
     */
    public static function readFile(string $package, string $filename, bool $silent = false): ?array
    {
        try {
            $path = app()->basePath(implode(
                DIRECTORY_SEPARATOR,
                [self::getPath($package), $filename]
            ));

            if (!app('files')->exists($path)) {
                return null;
            }

            $data = app('files')->getRequire($path);

            if (!is_array($data)) {
                return null;
            }

            return $data;
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $exception) {
            Log::channel('installation')->debug($exception->getMessage());
            // skip error
        }

        return null;
    }

    public static function with(Closure $callback)
    {
        $data = config('metafox.packages', []);
        foreach ($data as $name => $info) {
            $callback($name, $info);
        }
    }

    public static function withActivePackages(Closure $callback)
    {
        $data = config('metafox.packages', []);

        $aliases = config('app.mfox_installed') ?
            resolve('core.packages')
                ->getActivePackageAliases() : null;

        foreach ($data as $info) {
            if ($aliases && !$info['core'] && !in_array($info['alias'], $aliases)) {
                continue;
            }
            $callback($info);
        }
    }
}
