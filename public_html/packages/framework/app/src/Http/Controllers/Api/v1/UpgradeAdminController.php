<?php

namespace MetaFox\App\Http\Controllers\Api\v1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\App\Support\MetaFoxStore;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\MetaFox;
use RuntimeException;
use ZipArchive;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\App\Http\Controllers\Api\UpgradeAdminController::$controllers;.
 */

/**
 * Class UpgradeAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class UpgradeAdminController extends ApiController
{
    public const METAFOX_STORE_URL = 'https://api.phpfox.com';
    public const BUILD_SERVICE_URL = 'https://cloudcall-s01.phpfox.com/build-service';

    /**
     * @var string
     */
    private string $projectRoot;

    /**
     * @var string
     */
    private string $logFile;

    /**
     * @var string
     */
    private string $envFile;

    /**
     * Check is installed.
     * @var bool
     */
    private bool $platformInstalled = false;

    /** @var array */
    private array $input = [];

    /**
     * @var string
     */
    private string $platformVersion = '5.0.0';

    /**
     * @var string|mixed|null
     */
    private string $platformInstalledVersion = '5.0.0';

    /**
     * @var array
     */
    private array $envVars = [];

    /**
     * @var string
     */
    private string $lockName = 'unknown';

    public const DONE = 'done';

    public const PROCESSING = 'processing';

    public const FAILED = 'failed';
    /**
     * @var string
     */
    private string $downloadFrameworkFolder;

    /**
     * @var string
     */
    private string $downloadAppFolder;

    /**
     * @var string
     */
    private string $extractAppFolder;

    /**
     * @var string
     */
    private string $frameworkFilename;

    /**
     * @var string
     */
    private string $extractFrameworkFolder;

    /**
     * @var string
     */
    private string $lockFile;

    public function __construct()
    {
        $this->projectRoot             = base_path();
        $this->downloadFrameworkFolder = base_path('storage/install/download-framework');
        $this->extractFrameworkFolder  = base_path('storage/install/extract-framework');
        $this->extractAppFolder        = base_path('storage/install/extract-apps');
        $this->downloadAppFolder       = base_path('storage/install/download-apps');
        $this->lockFile                = base_path('storage/install/installation.lock');
        $this->logFile                 = base_path('storage/logs/installation-' . date('Y-m-d') . '.log');
        $this->frameworkFilename       = $this->downloadFrameworkFolder . '/metafox.zip';

        $this->ensureDir($this->downloadAppFolder);
        $this->ensureDir($this->extractAppFolder);
        $this->ensureDir($this->downloadFrameworkFolder);
        $this->ensureDir($this->extractFrameworkFolder);

        $this->getCurrentPlatformVersion();

        $this->envFile = implode(DIRECTORY_SEPARATOR, [$this->projectRoot, '.env']);
        if ($this->envFile &&
            file_exists($this->envFile) &&
            is_readable($this->envFile)) {
            $this->envVars = $this->parseEnvString(file_get_contents($this->envFile));

            $this->platformInstalledVersion = $this->getOnlyEnvVar('MFOX_APP_VERSION');
            $this->platformInstalled        = (bool) $this->getOnlyEnvVar('MFOX_APP_INSTALLED');
        }
    }

    public function stepStart()
    {
        $this->clearLockValue();

        $lockName = base_path('storage/install/installation.lock');

        if (file_exists($lockName)) {
            @unlink($lockName);
        }

        $files = app('files');

        $files->deleteDirectories(base_path('storage/install'));

        $files->makeDirectory($this->downloadFrameworkFolder);
        $files->makeDirectory($this->extractFrameworkFolder);
        $files->makeDirectory($this->extractAppFolder);
        $version = MetaFox::getVersion();

        $latestVersion = $this->getDownloadableFrameworkVersion();

        $canUpgrade = version_compare($version, $latestVersion, '<');

        $recommendApps = $this->getRecommendAppsToUpgrades();

        $mainSteps = [
            [
                'title'     => 'Prepare Backup',
                'id'        => 'prepare',
                'component' => 'app.step.PrepareUpgrade',
            ],
            count($recommendApps) ? [
                'title'     => 'Applications',
                'id'        => 'selectedApps',
                'component' => 'app.step.SelectApps',
                'props'     => [],
            ] : null,
            [
                'title'     => sprintf('Process Upgrade'),
                'id'        => 'download',
                'component' => 'app.step.ProcessUpgrade',
            ],
            [
                'title'     => sprintf('Done'),
                'id'        => 'done',
                'component' => 'app.step.UpgradeCompleted',
                'props'     => [
                    'baseUrl' => config('app.url'),
                ],
            ],
        ];
        $selectedApps = array_map(function ($app) {
            return [
                'identity' => $app['identity'],
                'name'     => $app['name'],
                'version'  => $app['version'],
            ];
        }, $recommendApps);

        return $this->success([
            'baseUrl'             => config('app.url'),
            'loaded'              => true,
            'currentVersion'      => $version,
            'latestVersion'       => $latestVersion,
            'canUpgrade'          => $canUpgrade,
            'recommendAppsLoaded' => true,
            'recommendApps'       => $recommendApps,
            'selectedApps'        => $selectedApps,
            'steps'               => array_values(array_filter($mainSteps, function ($step) {
                return (bool) $step;
            })),
        ]);
    }

    private function checkStepIsRetry($lockName, $verifier = null)
    {
        $status = get_installation_lock($lockName);

        switch ($status) {
            case 'done':
                return $this->success([]);
            case 'failed':
                return $this->error('Failed to process.');
            case 'processing':
                if ($verifier && $verifier()) {
                    return $this->success([]);
                }

                return $this->success(['retry' => true]);
            default:
                return false;
        }
    }

    private function stepRestartQueueWorker()
    {
        $this->execCommand(sprintf('%s artisan queue:restart', $this->getPhpPath()));

        return $this->success([]);
    }

    private function stepDownloadFramework()
    {
        if (file_exists($this->frameworkFilename)) {
            return $this->success([]);
        }

        $lockName = 'downloadFramework';

        $verifier = function () {
            $this->log('filename ' . $this->frameworkFilename);

            return file_exists($this->frameworkFilename);
        };

        if (($result = $this->checkStepIsRetry($lockName, $verifier))) {
            return $result;
        }

        set_installation_lock($lockName, 'processing');
        $channel = config('app.mfox_app_channel');

        app(MetaFoxStore::class)
            ->downloadFramework($channel, $this->frameworkFilename);

        return $this->success([]);
    }

    private function stepExtractFramework()
    {
        if (!file_exists($this->frameworkFilename)) {
            return $this->error('Failed loading ' . $this->frameworkFilename);
        }

        $archive = new \ZipArchive();

        if (!$archive->open($this->frameworkFilename, ZipArchive::RDONLY)) {
            return $this->error('Failed loading archive ' . $this->frameworkFilename);
        }

        $found = 'upload.zip';

        if (false === $archive->getFromName($found)) {
            $found = rtrim($archive->getNameIndex(0), '/') . '/' . $found;
        }

        $archive->extractTo($this->extractFrameworkFolder);

        $archive->close();

        $uploadFilename = $this->extractFrameworkFolder . '/' . $found;

        if (!file_exists($uploadFilename)) {
            return $this->error('Failed loading archive ' . $uploadFilename);
        }

        $archive = new \ZipArchive();

        if (!$archive->open($uploadFilename, ZipArchive::RDONLY)) {
            return $this->error('Failed loading archive ' . $uploadFilename);
        }

        $archive->extractTo(base_path());

        $archive->close();

        return $this->success([]);
    }

    private function stepClean()
    {
        $this->execCommand(sprintf('%s artisan optimize:clear ', $this->getPhpPath()));

        $files = app('files');
        $files->deleteDirectories(base_path('storage/install'));

        return $this->success([]);
    }

    private function ensureDir($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    private function setStepDone($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }
        $this->setLockValue($lockName, self::DONE);
    }

    private function setStepFailed($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }
        $this->setLockValue($lockName, self::FAILED);
    }

    private function setStepIsProcessing($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }

        $this->setLockValue($lockName, self::PROCESSING);
    }

    private function setLockName($lockName)
    {
        $this->lockName = $lockName;
    }

    private function getLockValue($lockName, $default = null)
    {
        if (file_exists($this->lockFile)) {
            $data = json_decode(file_get_contents($this->lockFile), true);

            return array_key_exists($lockName, $data) ? $data[$lockName] : $default;
        }

        return $default;
    }

    private function setLockValue($lockName, $value)
    {
        $data = [];
        if (file_exists($this->lockFile)) {
            $data = json_decode(file_get_contents($this->lockFile), true);
        }
        $data[$lockName] = $value;

        file_put_contents($this->lockFile, json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL);
    }

    private function clearLockValue()
    {
        file_put_contents($this->lockFile, json_encode([], JSON_PRETTY_PRINT) . PHP_EOL);
    }

    /**
     * Get existing environment from env var only.
     * @param  string     $name
     * @param  mixed      $default
     * @return mixed|null
     */
    private function getOnlyEnvVar($name, $default = null)
    {
        return isset($this->envVars[$name]) ? $this->envVars[$name] : $default;
    }

    /**
     * @param  string $method
     * @return mixed
     */
    private function executeStep(string $method)
    {
        $this->log(sprintf('Start %s (%s)', __METHOD__, $method));

        if (!method_exists($this, $method)) {
            return $this->error('Step not found');
        }

        return $this->{$method}();
    }

    public function execute(string $step, Request $request)
    {
        $this->input = $request->all();
        $this->log('---------------------------------------------');
        $this->log(sprintf('Start %s', __METHOD__));

        ignore_user_abort(true);
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        try {
            $step = 'step' . $this->studlyCase($step);

            register_shutdown_function(function () use ($step) {
                if (($error = error_get_last())) {
                    $this->log(var_export($error, true));
                } else {
                    $this->log("executed $step");
                }
            });

            $data = $this->executeStep($step);
            $this->log(sprintf('End %s', __METHOD__));

            return $data;
        } catch (Exception $error) {
            $message = $error->getMessage();
            $this->alert([
                'title'   => 'Alert',
                'message' => $message,
            ]);

            return $this->error($error->getMessage(), 400);
        }
    }

    /**
     * @return string|null
     */
    private function getPhpPath()
    {
        $pathToPhp = resolve(\Symfony\Component\Process\PhpExecutableFinder::class)->find();

        if ($pathToPhp && is_executable($pathToPhp)) {
            return $pathToPhp;
        }

        throw new RuntimeException('Failed finding php path');
    }

    private function stepWaitFrontend()
    {
        $lockName = 'stepBuildFrontend';
        if (($result = $this->checkStepIsRetry($lockName))) {
            return $result;
        }

        // where to get JobId.
        try {
            $this->execCommand(sprintf('%s artisan frontend:build --check', $this->getPhpPath()), getenv(), true);
        } catch (\Exception $exception) {
            $this->log($exception->getMessage());
        }

        return $this->success(['retry' => true]);
    }

    /**
     * @link /install/build-frontend
     */
    private function stepBuildFrontend()
    {
        $this->execCommand(sprintf('%s artisan frontend:build', $this->getPhpPath(), ), getenv());

        return $this->success([]);
    }

    private function ensureWritable($dirOrFileName)
    {
        $path = $this->projectRoot . $dirOrFileName;

        if (!is_dir($path) && !file_exists($path)) {
            return is_writable(dirname($path));
        }

        if (is_writable($path)) {
            return true;
        }

        return is_writable($path);
    }

    /**
     * @return array
     */
    private function getRecommendations()
    {
        $this->log(sprintf('Start %s', __METHOD__));
        $hasAPC = extension_loaded('apc') || extension_loaded('apcu');

        $items = [
            [
                'label'    => 'APC User Cache',
                'value'    => $hasAPC,
                'url'      => 'https://www.php.net/manual/en/book.apcu.php',
                'severity' => 'warning',
            ],
            [
                'label'    => 'Redis Cache',
                'value'    => class_exists('Redis'),
                'url'      => 'https://github.com/phpredis/phpredis',
                'severity' => 'warning',
            ],
            [
                'label'    => 'ImageMagick PHP Extension',
                'value'    => extension_loaded('imagick'),
                'url'      => 'https://www.php.net/manual/en/book.imagick.php',
                'severity' => 'warning',
            ],
        ];

        return [
            'title' => 'Recommendations',
            'items' => $items,
        ];
    }

    private function discoverExistedPackages(): array
    {
        $basePath = $this->projectRoot;
        $files    = [];
        $packages = [];
        $patterns = [
            'packages/*/composer.json',
            'packages/*/*/composer.json',
            'packages/*/*/*/composer.json',
        ];

        array_walk($patterns, function ($pattern) use (&$files, $basePath) {
            $dir = rtrim($basePath, DIRECTORY_SEPARATOR, ) . DIRECTORY_SEPARATOR . $pattern;
            foreach (glob($dir) as $file) {
                $files[] = $file;
            }
        });

        array_walk($files, function ($file) use (&$packages, $basePath) {
            try {
                $data = json_decode(file_get_contents($file), true);
                if (!isset($data['extra']) ||
                    !isset($data['extra']['metafox'])
                    || !is_array($data['extra']['metafox'])) {
                    return;
                }
                $extra = $data['extra']['metafox'];

                $packages[$data['name']] = [
                    'name'    => $data['name'],
                    'version' => $data['version'],
                    'path'    => trim(substr(dirname($file), strlen($basePath)), DIRECTORY_SEPARATOR),
                    'core'    => isset($extra['core']) ? $extra['core'] : false,
                ];
            } catch (Exception $exception) {
                //
            }
        });

        return $packages;
    }

    /**
     * @param  string $string
     * @return string
     */
    private function studlyCase($string)
    {
        return $string ? str_replace(' ', '', ucwords(preg_replace('#([^a-zA-Z\d]+)#m', ' ', $string))) : '';
    }

    /**
     * @return array
     */
    private function getRequirement()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $result = true;

        $response = [
            'sections' => [
                $this->getSystemRequirements(),
                $this->getRecommendations(),
            ],
        ];

        foreach ($response['sections'] as $section) {
            foreach ($section['items'] as $item) {
                if (!$item['value'] && $item['severity'] === 'error') {
                    $result = false;
                }
            }
        }

        $response['result'] = $result;

        /*
         * rollup error first
         */
        foreach ($response['sections'] as $key => $section) {
            usort($section['items'], function ($a, $b) {
                return $a['value'] > $b['value'] ? 1 : 0;
            });
            $response['sections'][$key] = $section;
        }

        $this->log(sprintf('End %s', __METHOD__));

        return $response;
    }

    /**
     * @return array[]
     * @link https://laravel.com/docs/9.x/deployment#server-requirements
     */
    private function getSystemRequirements()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $hasDb = extension_loaded('pdo_mysql') || extension_loaded('pdo_pgsql');

        $pathToPhp = $this->getPhpPath();

        $items = [
            [
                'label'    => sprintf('PHP Version > 8.1, Current %s (%s) ', phpversion(), php_sapi_name()),
                'value'    => version_compare(phpversion(), '8.1', '>='),
                'severity' => 'error',
            ],
            [
                'label'    => "PHP Path $pathToPhp",
                'value'    => (bool) $this->getPhpPath(),
                'severity' => 'error',
            ],
            [
                'label'    => 'JSON PHP Extension',
                'value'    => extension_loaded('json'),
                'url'      => 'https://www.php.net/manual/en/book.json.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'BCMath PHP Extension',
                'value'    => extension_loaded('bcmath'),
                'url'      => 'https://www.php.net/manual/en/book.bc.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Process Control Extension',
                'value'    => extension_loaded('pcntl') && function_exists('pcntl_signal'),
                'url'      => 'https://www.php.net/manual/en/book.pcntl.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'POSIX Extension',
                'value'    => extension_loaded('posix'),
                'url'      => 'https://www.php.net/manual/en/book.posix.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Ctype PHP Extension',
                'value'    => extension_loaded('ctype'),
                'url'      => 'https://www.php.net/manual/en/book.ctype.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Exif PHP Extension',
                'value'    => extension_loaded('exif'),
                'url'      => 'https://www.php.net/manual/en/book.exif.php',
                'text'     => 'exif',
                'severity' => 'error',
            ],
            [
                'label'    => 'Sodium PHP Extension',
                'value'    => extension_loaded('sodium'),
                'url'      => 'https://www.php.net/manual/en/book.sodium.php',
                'severity' => 'error',
            ],
            [
                'label'    => 'Intl PHP Extension',
                'value'    => extension_loaded('intl'),
                'url'      => 'https://www.php.net/manual/en/book.intl.php',
                'severity' => 'error',
            ],
            [
                'label'    => 'cURL PHP Extension',
                'value'    => extension_loaded('curl'),
                'link'     => 'https://php.net/manual/en/book.curl.php',
                'severity' => 'error',
            ],
            [
                'label'    => 'DOM PHP Extension',
                'value'    => extension_loaded('curl'),
                'url'      => 'https://php.net/manual/en/book.dom.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'OpenSSL PHP Extension',
                'value'    => extension_loaded('openssl'),
                'url'      => 'https://www.php.net/manual/en/book.openssl.php',
                'severity' => 'error',
            ],
            [
                'label'    => 'PCRE PHP Extension',
                'value'    => extension_loaded('openssl'),
                'url'      => 'https://www.php.net/manual/en/book.openssl.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Database Drivers (MySql/Postgres)',
                'value'    => $hasDb,
                'text'     => 'Database Driver',
                'severity' => 'error',
            ],
            [
                'label'    => 'Mbstring PHP Extension',
                'value'    => extension_loaded('mbstring'),
                'url'      => 'https://php.net/manual/en/book.mbstring.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Fileinfo PHP Extension',
                'value'    => extension_loaded('fileinfo'),
                'url'      => 'https://php.net/manual/en/book.fileinfo.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'PCRE PHP Extension',
                'value'    => extension_loaded('pcre'),
                'url'      => 'https://www.php.net/manual/en/pcre.configuration.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Tokenizer PHP Extension',
                'value'    => extension_loaded('tokenizer'),
                'url'      => 'https://www.php.net/manual/en/book.tokenizer.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'XML PHP Extension',
                'value'    => extension_loaded('xml'),
                'url'      => 'https://php.net/manual/en/book.xml.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Zip/Archive PHP Extension',
                'value'    => extension_loaded('zip'),
                'url'      => 'https://www.php.net/manual/en/book.zip.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Function exec, proc_open, proc_close',
                'value'    => function_exists('exec') && function_exists('proc_open') && function_exists('proc_close'),
                'link'     => 'https://php.net/manual/en/book.exec.php',
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Folder ./storage/* is writable ',
                'value'    => $this->ensureWritable('/storage'),
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Folder ./public/* is writable to create symlinks',
                'value'    => $this->ensureWritable('/public'),
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Folder ./bootstrap/cache/* is writable',
                'value'    => $this->ensureWritable('/bootstrap/cache'),
                'severity' => 'error',
                'skip'     => true,
            ],
            [
                'label'    => 'Folder ./config/* is writable',
                'value'    => $this->ensureWritable('/config/metafox.php'),
                'severity' => 'error',
                'skip'     => true,
            ],
        ];

        return [
            'title' => 'System Requirements',
            'items' => $items,
        ];
    }

    private function stepDownloadApp()
    {
        $this->log(sprintf('Start %s', __METHOD__));
        $id              = Arr::get($this->input, 'identity');
        $version         = Arr::get($this->input, 'version');
        $platformVersion = Arr::get($this->input, 'platformVersion');

        $filename = sprintf('%s/%s.zip', $this->downloadAppFolder, preg_replace("#\W+#", '-', $id));

        $verifier = function () use ($filename) {
            return file_exists($filename);
        };

        $lockName = sprintf('download' . $id);
        if (($result = $this->checkStepIsRetry($lockName, $verifier))) {
            return $result;
        }

        $this->setStepIsProcessing($lockName);

        $json = $this->httpRequest(self::METAFOX_STORE_URL . '/install', 'post', [
            'id'           => $id,
            'version'      => $platformVersion,
            'app_version'  => $version,
            'version_type' => 'backend',
        ]);

        if (!$json['download']) {
            throw new RuntimeException('Could not get download url');
        }

        $temporary = $filename . '.temp';
        register_shutdown_function(function () use ($temporary, $filename) {
            if (file_exists($temporary)) {
                copy($temporary, $filename);
                @unlink($temporary);
            }
        });

        // fix issue timeout request etc. request limit 15 sec but download need 30 sec.
        $source = fopen($json['download'], 'r');
        $dest   = fopen($temporary, 'w');
        stream_copy_to_stream($source, $dest);

        return $this->success([]);
    }

    private function log($message, $level = 'DEBUG')
    {
        $message = sprintf('[%s] production:%s: %s', strtoupper($level), date('Y-m-d H:i:s'), $message);

        file_put_contents($this->logFile, $message . PHP_EOL, FILE_APPEND);
    }

    private function stepProcessUpgrade()
    {
        $canUpgrade    = Arr::get($this->input, 'canUpgrade');
        $latestVersion = Arr::get($this->input, 'latestVersion');

        $selectedApps = Arr::get($this->input, 'selectedApps');

        $downloadApps = array_map(function ($app) {
            return [
                'title'      => 'Download ' . $app['name'] . '-v' . $app['version'],
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/download-app',
                    'apiMethod' => 'POST',
                ],
                'data' => [
                    'version'         => $app['version'],
                    'id'              => $app['identity'],
                    'platformVersion' => $this->platformVersion,
                ],
            ];
        }, $selectedApps);

        $upgradeSteps = [
            $canUpgrade ? [
                'title'      => sprintf('Download MetaFox-v%s', $latestVersion),
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/download-framework',
                    'apiMethod' => 'GET',
                ],
            ] : false,
            ...$downloadApps,
            $canUpgrade ? [
                'title'      => 'Extract Framework',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/extract-framework',
                    'apiMethod' => 'POST',
                ],
            ] : false,
            count($selectedApps) ? [
                'title'      => 'Extract Apps',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/extract-apps',
                    'apiMethod' => 'POST',
                ],
            ] : false,
            [
                'title'      => 'Update Dependencies',
                'dataSource' => [
                    'apiUrl' => '/admincp/app/upgrade/composer-install',
                ],
            ],
            [
                'title'      => 'Upgrade',
                'dataSource' => [
                    'apiUrl' => '/admincp/app/upgrade/metafox-upgrade',
                ],
            ],
            [
                'title'      => 'Clean files',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/clean',
                    'apiMethod' => 'GET',
                ],
            ],
            [
                'title'      => 'Restart Queues',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/restart-queue-worker',
                    'apiMethod' => 'GET',
                ],
            ],
            [
                'title'      => 'Build frontend',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/build-frontend',
                    'apiMethod' => 'GET',
                ],

            ],
            [
                'title'      => 'Waiting for frontend',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/wait-frontend',
                    'apiMethod' => 'GET',
                ],

            ],
            [
                'title'      => 'Launch Site',
                'dataSource' => [
                    'apiUrl'    => '/admincp/app/upgrade/up-site',
                    'apiMethod' => 'GET',
                ],
            ],
        ];

        return $this->success([
            'upgradeSteps' => array_values(array_filter($upgradeSteps, function ($step) {
                return (bool) $step;
            })),
        ]);
    }

    /**
     * Get collections of app to upgrades.
     */
    private function getRecommendAppsToUpgrades()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $existedApps = $this->discoverExistedPackages();

        $payload = $this->httpRequest(
            self::METAFOX_STORE_URL . '/purchased',
            'GET',
            ['Accept: application/json'],
        );

        foreach ($payload as $index => $latest) {
            $id = $latest['identity'];
            if (!isset($existedApps[$id])) {
                unset($payload[$index]);
            }
            $check = $existedApps[$id];
            if (!version_compare($latest['version'], $check['version'], '>')) {
                unset($payload[$index]);
            }
        }

        return $payload;
    }

    /**
     * @param  string $command
     * @param  array  $env
     * @param  bool   $throw
     * @return bool
     */
    private function execCommand($command, $env = [], $throw = true)
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $this->log(sprintf('exec command %s', $command));

        $output = [];
        $result = 0;

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes, $this->projectRoot, $env);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $output .= stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $this->log($output);

            $result = proc_close($process);

            $this->log($result);
        }

        if ($result != 0 && $throw) {
            throw new RuntimeException(sprintf(
                'command: %s, result=%s; command output: %s',
                $command,
                $result,
                $output
            ));
        }

        $this->log(sprintf('End %s', __METHOD__));

        return $result === 0;
    }

    private function getDownloadableFrameworkVersion()
    {
        $json = $this->httpRequest(self::METAFOX_STORE_URL . '/phpfox-download', 'post');

        return $json['version'];
    }

    private function clearDownloadApps()
    {
        $this->execCommand('rm -rf ' . $this->downloadFrameworkFolder);
    }

    private function stepExtractApps()
    {
        $files = app('files')->files($this->downloadAppFolder);

        foreach ($files as $file) {
            if (!str_ends_with($file, '.zip')) {
                continue;
            }
            $archive = new ZipArchive();
            $archive->open($file, ZipArchive::RDONLY);
            $archive->extractTo($this->extractAppFolder);
            $archive->close();
        }

        $this->execCommand(sprintf('cp -rf %s/backend/* %s', $this->extractAppFolder, $this->projectRoot), [], false);

        return $this->success([]);
    }

    private function stepComposerInstall()
    {
        $lockName = 'composerInstall';
        $autoload = sprintf('%s/vendor/autoload.php', $this->projectRoot);

        if (($result = $this->checkStepIsRetry($lockName, function () use ($autoload) {
            return file_exists($autoload);
        }))) {
            return $result;
        }

        $this->setStepIsProcessing($lockName);

        $this->log(sprintf('Start %s', __METHOD__));
        $env = array_merge(getenv(), [
            'COMPOSER_MEMORY_LIMIT' => -1,
            'COMPOSER_HOME'         => $this->projectRoot,
        ]);

        $this->execCommand(sprintf(
            '%s %s/composer install --ignore-platform-reqs -n -q',
            $this->getPhpPath(),
            $this->projectRoot,
        ), $env, true);

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success([], [], 'Install dependency successfully');
    }

    private function stepDownSite()
    {
        $this->execCommand(sprintf('%s artisan down', $this->getPhpPath()), getenv(), false);

        return $this->success([]);
    }

    private function stepUpSite()
    {
        $this->execCommand(sprintf('%s artisan up', $this->getPhpPath()));

        return $this->success([]);
    }

    private function isStepProcessing($lockName)
    {
        return $this->getLockValue($lockName) === self::PROCESSING;
    }

    /**
     * @throws Exception
     */
    private function stepMetafoxUpgrade()
    {
        $lockName = 'stepMetafoxInstall';

        if (($result = $this->checkStepIsRetry($lockName))) {
            return $result;
        }

        $this->setStepIsProcessing($lockName);

        $env = array_merge(getenv(), [
            'COMPOSER_HOME'         => $this->projectRoot,
            'COMPOSER_MEMORY_LIMIT' => -1,
        ]);

        $this->execCommand(sprintf(
            '%s %s/composer metafox:upgrade',
            $this->getPhpPath(),
            $this->projectRoot
        ), $env, true);

        $this->log(sprintf('End %s', __METHOD__));

        $this->setStepDone($lockName);

        return $this->success([]);
    }

    private function getRootUrl()
    {
        $https   = false;
        $host    = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $visitor = isset($_SERVER['HTTP_CF_VISITOR']) ? $_SERVER['HTTP_CF_VISITOR'] : null;

        if (@$_SERVER['HTTPS'] === 'on' ||
            @$_SERVER['SERVER_PORT'] == 443 ||
            @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ||
            ($visitor && strpos($visitor, 'https'))) {
            $https = true;
        }

        return sprintf('%s://%s', $https ? 'https' : 'http', $host);
    }

    /**
     * Get host path.
     *
     * @return string
     * @since 4.6.0 fix issue install from https on ec2, ...
     */
    private function getAppUrl()
    {
        $rootUrl = $this->getRootUrl();

        $baseUrl = preg_replace('/(.*)\/(public|install)(\/)*(.*)/m', '$1', $_SERVER['PHP_SELF']);

        return rtrim($rootUrl . '/' . $baseUrl, '/');
    }

    /**
     * @param  string $url
     * @param  string $method
     * @param  array  $params
     * @param  array  $headers
     * @return mixed
     */
    private function httpRequest(string $url, string $method, array $params = [], array $headers = [])
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $method = strtoupper($method);
        $post   = http_build_query($params);

        $curl_url = (($method == 'GET' && !empty($post)) ? $url . (strpos($url, '?') ? '&' : '?') . ltrim(
            $post,
            '&'
        ) : $url);

        // update api versioning
        $headers[] = 'X-Product: metafox';
        $headers[] = 'X-Namespace: phpfox';
        $headers[] = 'X-API-Version: 1.1';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curl_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($method != 'GET' || $method != 'POST') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        $licenseId  =  config('app.mfox_license_id');
        $licenseKey =  config('app.mfox_license_key');
        $headers[]  = 'Authorization: Basic ' . base64_encode($licenseId . ':' . $licenseKey);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);

        if ($method != 'GET') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        $response = curl_exec($curl);

        curl_close($curl);

        $response = trim($response);

        $response = json_decode($response, true);

        if (isset($response['error']) && $response['error']) {
            throw new RuntimeException($response['error']);
        }

        $this->log(sprintf('End %s', __METHOD__));

        return isset($response['data']) ? $response['data'] : $response;
    }

    private function formatEnvVar($value)
    {
        $var = trim(trim($value, '"'));

        switch (strtolower($var)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            default:
                return $var;
        }
    }

    private function parseEnvString($str)
    {
        $lines     = explode(PHP_EOL, $str);
        $variables = [];
        $re        = '/^(?<name>\w+)\s*=\s*(?<value>[^\n]+)$/';
        foreach ($lines as $line) {
            if (preg_match($re, $line, $match)) {
                $variables[$match['name']] = $this->formatEnvVar($match['value']);
            }
        }

        return $variables;
    }

    private function getCurrentPlatformVersion()
    {
        $constFile = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'packages', 'platform', 'src', 'MetaFoxConstant.php']
        );

        if (!file_exists($constFile)) {
            throw new RuntimeException('Could not find ' . $constFile);
        }

        preg_match(
            '/(.*)public const VERSION\s*=\s*\'(?<version>[^\']+)\'/mi',
            file_get_contents($constFile),
            $matches
        );

        if (!empty($matches)) {
            $this->platformVersion = $matches['version'];
        }
    }
}
