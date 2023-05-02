<?php

namespace App;

use Exception;
use RuntimeException;

/*
 * This file run OUT OF laravel framework installed and vendor has exists.
 * So do not import any source code from platform and others.
 *
 * Every post request contains configuration from front ends.
 */

/**
 * Class SetupWizard.
 */
class SetupWizard
{
    public const METAFOX_STORE_URL = 'https://api.phpfox.com';
    public const BUILD_SERVICE_URL = 'https://cloudcall-s01.phpfox.com/build-service';

    public const SIMULATE_PASS = false;
    /**
     * @var string
     */
    private $projectRoot;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @var string
     */
    private $envFile;

    /**
     * Check is installed.
     * @var bool
     */
    private $platformInstalled = false;

    /** @var array */
    private $input = [];

    /**
     * @var string
     */
    private $platformVersion = '5.0.0';

    private $platformInstalledVersion = null;

    /**
     * @var array
     */
    private $envVars = [];

    /** @var string */
    private $lockFile;

    private $lockName = 'unknown';

    public const DONE = 'done';

    public const PROCESSING = 'processing';

    public const FAILED = 'failed';

    private $uprading = false;

    /**
     * @var string
     */
    private $downloadFrameworkFolder;

    /**
     * @var string
     */
    private $downloadAppFolder;

    /**
     * @var string
     */
    private $extractAppFolder;

    /**
     * @var string
     */
    private $extractFrameworkFolder;

    /**
     * @param string $projectRoot
     */
    public function __construct($projectRoot)
    {
        $this->projectRoot = $projectRoot;

        $this->downloadFrameworkFolder = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'install', 'download-framework']
        );

        $this->downloadAppFolder = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'install', 'download-apps']
        );

        $this->extractAppFolder = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'install', 'extract-apps']
        );

        $this->extractFrameworkFolder = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'install', 'extract-framework']
        );

        $this->ensureDir($this->downloadAppFolder);
        $this->ensureDir($this->extractAppFolder);
        $this->ensureDir($this->downloadFrameworkFolder);
        $this->ensureDir($this->extractFrameworkFolder);

        $this->lockFile = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'install', 'installation.lock']
        );

        $this->logFile = implode(
            DIRECTORY_SEPARATOR,
            [$this->projectRoot, 'storage', 'logs', sprintf('installation-%s.log', date('Y-m-d'))]
        );

        $this->getCurrentPlatformVersion();

        $content = file_get_contents('php://input');

        if ($content) {
            $this->input = json_decode($content, true);
        }

        $this->envFile = implode(DIRECTORY_SEPARATOR, [$this->projectRoot, '.env']);
        if ($this->envFile &&
            file_exists($this->envFile) &&
            is_readable($this->envFile)) {
            $this->envVars = $this->parseEnvString(file_get_contents($this->envFile));

            $this->platformInstalledVersion = $this->getOnlyEnvVar('MFOX_APP_VERSION');
            $this->platformInstalled        = (bool) $this->getOnlyEnvVar('MFOX_APP_INSTALLED');
        }

        $this->uprading = $this->platformInstalled && ($this->platformInstalledVersion != $this->platformVersion);
    }

    public function ensureDir($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * @param  array $data
     * @return void
     */
    public function checkDownloadFrameworkSteps(&$data)
    {
        if (version_compare($this->platformInstalledVersion, $this->platformVersion, '>=')) {
            return;
        }
        $frameworkVersion = $this->getDownloadableFrameworkVersion();

        if (version_compare($this->platformVersion, $frameworkVersion, '>=')) {
            return;
        }
        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=download-framework',
                'apiMethod' => 'POST',
            ],
            'title' => 'Download metafox ' . $frameworkVersion,
        ];
        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=extract-framework',
                'apiMethod' => 'POST',
            ],
            'title' => 'Extract metafox ' . $frameworkVersion,
        ];
    }

    public function setStepDone($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }
        $this->setLockValue($lockName, self::DONE);
    }

    public function setStepFailed($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }
        $this->setLockValue($lockName, self::FAILED);
    }

    public function setStepIsProcessing($lockName = null)
    {
        if (!$lockName) {
            $lockName = $this->lockName;
        }

        $this->setLockValue($lockName, self::PROCESSING);
    }

    public function setLockName($lockName)
    {
        $this->lockName = $lockName;
    }

    public function getLockValue($lockName, $default = null)
    {
        if (file_exists($this->lockFile)) {
            $data = json_decode(file_get_contents($this->lockFile), true);

            return array_key_exists($lockName, $data) ? $data[$lockName] : $default;
        }

        return $default;
    }

    public function setLockValue($lockName, $value)
    {
        $data = [];
        if (file_exists($this->lockFile)) {
            $data = json_decode(file_get_contents($this->lockFile), true);
        }
        $data[$lockName] = $value;

        file_put_contents($this->lockFile, json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL);
    }

    public function clearLockValue()
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
    private function executeStep($method)
    {
        $this->log(sprintf('Start %s (%s)', __METHOD__, $method));

        if (!method_exists($this, $method)) {
            return $this->failure(400, [], 'Step not found');
        }

        return $this->{$method}();
    }

    /**
     * defined MFOX_ROOT at ./public/index.php.
     *
     * @return void
     */
    public function execute()
    {
        $this->log('---------------------------------------------');
        $this->log(sprintf('Start %s', __METHOD__));

        ignore_user_abort(true);
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        header('content-type: application/json');
        ob_start();
        chdir($this->projectRoot);

        try {
            $step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 'start';

            $step = 'step' . $this->studlyCase($step);

            // does not allow then installed.
            if ($this->platformInstalled) {
//                $step = 'start';
            }

            register_shutdown_function(function () use ($step) {
                if (($error = error_get_last())) {
                    $this->log(var_export($error, true));
                } else {
                    $this->log("executed $step");
                }
            });

            $data = $this->executeStep($step);

            ob_get_clean();

            $this->log(sprintf('End %s', __METHOD__));

            echo json_encode($data);
        } catch (Exception $error) {
            ob_get_clean();
            http_response_code(400);
            $message = $error->getMessage();
            $this->log($message);
            $this->setStepFailed();
            echo json_encode([
                'status' => 'failed',
                'error'  => $message,
                'alert'  => [
                    'title'   => 'Alert',
                    'message' => $message,
                ],
            ]);
        }
    }

    /**
     * @return string|null
     */
    private function getPhpPath()
    {
        $pathToPhp = null;

        if (defined('PHP_BINDIR')) {
            $pathToPhp = sprintf('%s/php', PHP_BINDIR);
        } elseif (defined('PHP_BINARY')) {
            return PHP_BINARY;
        } elseif (getenv('PATH_TO_PHP_BIN')) {
            $pathToPhp = getenv('PATH_TO_PHP_BIN');
        }

        if ($pathToPhp && is_executable($pathToPhp)) {
            return $pathToPhp;
        }

        throw new RuntimeException('Failed finding php path');
    }

    private function getComposerPath()
    {
        return $this->projectRoot . DIRECTORY_SEPARATOR . 'composer';
    }

    private function setupComposer()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $pathToComposer = $this->getComposerPath();

        if (!file_exists($pathToComposer)) {
            $installer = file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar');
            file_put_contents($pathToComposer, $installer);
        }

        @chmod($pathToComposer, 0755);

        $this->log(sprintf('End %s', __METHOD__));
    }

    /**
     * @param  int         $code
     * @param  array|null  $errors
     * @param  string|null $message
     * @param  array|null  $alert
     * @return array
     */
    private function failure($code, $errors, $message = null, $alert = null)
    {
        http_response_code($code);

        $response = [
            'errors'  => $errors,
            'message' => $message,
            'status'  => 'failure',
        ];

        if ($alert) {
            $response['alert'] = $alert;
        }

        return $response;
    }

    /**
     * @param  array       $data
     * @param  string|null $message
     * @return array
     */
    private function success($data, $message = null)
    {
        return [
            'data'    => $data,
            'message' => $message,
            'status'  => 'success',
        ];
    }

    public function stepWaitFrontend()
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
     * @return array
     * @link /install/build-frontend
     */
    public function stepBuildFrontend()
    {
        $this->execCommand(sprintf('%s artisan frontend:build', $this->getPhpPath(), ), getenv());

        return $this->success([]);
    }

    /**
     * @return array
     */
    private function stepVerifyVendor()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $this->setupComposer();

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success([], './vendor/autoload.php has already exists.');
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

    public function discoverExistedPackages()
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
     * @param  \PDO  $pdo
     * @param  array $config
     * @return void
     */
    private function verifyDatabaseAvaiable(\PDO $pdo, $config)
    {
        try {
            $prefix    = isset($config['prefix']) ? $config['prefix'] : '';
            $tableName = $prefix . 'packages';
            $sql       = sprintf('select * from %s LIMIT 1', $tableName);
            $pdo->query($sql)->fetchAll();

            $version    = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $minVersion = $config['driver'] === 'mysql' ? '5.6' : '13';

            if (version_compare($version, $minVersion, '<')) {
                throw new \InvalidArgumentException(sprintf(
                    'Required %s version >= %s > %s.',
                    $config['driver'],
                    $version,
                    $minVersion
                ));
            }

            throw new \InvalidArgumentException(
                sprintf(
                    'Database %s is not available, Drop all tables then continue.',
                    $config['name']
                )
            );
        } catch (\PDOException $exception) { // OK, TABLE DOES NOT exist
            // do nothing
        }
    }

    private function stepVerifyDatabase()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $input = $this->getInput();

        $config = $input['database'];
        $driver = isset($config['driver']) ? $config['driver'] : 'pgsql';

        $this->log(sprintf('Start %s', __METHOD__));

        if (!(isset($config['port']) ? $config['port'] : null)) {
            $config['port'] = $driver === 'pgsql' ? 5432 : 3306;
        }

        $dns = sprintf('%s:host=%s;port=%s;dbname=%s', $driver, $config['host'], $config['port'], $config['name']);

        try {
            $pdo = new \PDO(
                $dns,
                $config['user'],
                $config['password'],
                [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );

            $this->verifyDatabaseAvaiable($pdo, $config);
        } catch (\PDOException $exception) {
            $message = sprintf(
                'Could not connect to database server %s %s %s ',
                $dns,
                PHP_EOL,
                $exception->getMessage()
            );

            throw new \InvalidArgumentException($message);
        }

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success([], 'Configure database successfully.');
    }

    private function getInput()
    {
        return $this->input;
    }

    /**
     * @return array
     * @link  ?step=verify-license
     */
    private function stepVerifyLicense()
    {
        $this->clearLockValue();
        $this->log(sprintf('Start %s', __METHOD__));

        $input = $this->getInput();

        if (!$input || !$input['license']) {
            return $this->failure(400, [], 'Missing license key/id');
        }

        $params = [
            'url'               => $input['general']['app_url'],
            'installation_path' => '',
        ];

        try {
            $this->httpRequest(
                static::METAFOX_STORE_URL . '/verify',
                'post',
                $params,
                ['Accept: application/json'],
                $input['license']
            );

            $this->log(sprintf('End %s', __METHOD__));

            return $this->success([], 'Configured license key');
        } catch (Exception $exception) {
            return $this->failure(400, [
                'license' => [
                    'id' => $exception->getMessage(),
                ],
            ], $exception->getMessage());
        }
    }

    private function stepVerifyGeneralInfo()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success([]);
    }

    private function stepCleanup()
    {
        $this->execCommand(sprintf('rm -rf %s/storage/install', $this->projectRoot));

        return $this->success([]);
    }

    private function stepCleanCache()
    {
        $this->execCommand(sprintf('%s artisan optimize:clear', $this->getPhpPath(), ), getenv(), false);

        return $this->success([]);
    }

    private function stepOptimize()
    {
        $this->execCommand(sprintf('%s artisan optimize', $this->getPhpPath(), ), getenv(), false);

        return $this->success([]);
    }

    public function getRecommendAppsForInstall($license)
    {
        $existedApps = $this->discoverExistedPackages();

        if (static::SIMULATE_PASS) {
            unset($existedApps['metafox/chatplus']);
        }

        $payload = $this->httpRequest(
            self::METAFOX_STORE_URL . '/purchased',
            'GET',
            ['Accept: application/json'],
            [],
            $license
        );

        foreach ($payload as $index => $latest) {
            $id = $latest['identity'];
            if (isset($existedApps[$id])) {
                unset($payload[$index]);
            }

            // check app compatibilities
            if (!version_compare($this->platformVersion, $latest['compatible'], '>=')) {
                unset($payload[$index]);
            }
        }

        return array_values($payload);
    }

    public function stepSelectApps()
    {
        $license = $this->getInput()['license'];
        $this->log(sprintf('Start %s', __METHOD__));

        $recommendApps = $this->getRecommendAppsForInstall($license);

        $this->log(var_export($recommendApps, true));

        return $this->success([
            'loadedAppsLoaded' => true,
            'recommendApps'    => $recommendApps,
        ]);
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
                'label'    => 'Folder ./storage/* is writable ',
                'value'    => $this->ensureWritable('/storage/logs'),
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

    /**
     * @return array
     */
    public function stepDownloadApp()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $input   = $this->getInput();
        $id      = $input['id'];
        $version = $input['version'];

        $filename = sprintf('%s/%s.zip', $this->downloadAppFolder, preg_replace("#\W+#", '-', $id));

        if (file_exists($filename)) {
            return $this->success([]);
        }

        $json = $this->httpRequest(self::METAFOX_STORE_URL . '/install', 'post', [
            'id'           => $id,
            'version'      => $this->platformVersion,
            'app_version'  => $version,
            'version_type' => 'backend',
        ], [], [
            'id'  => $this->getEnv('MFOX_LICENSE_ID'),
            'key' => $this->getEnv('MFOX_LICENSE_KEY'),
        ]);

        if (!isset($json['download']) || !$json['download']) {
            throw new RuntimeException('Could not get download url');
        }

        $temporary = $filename . '.temp';
        register_shutdown_function(function () use ($temporary, $filename) {
            if (file_exists($temporary)) {
                copy($temporary, $filename);
                unlink($temporary);
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

    /**
     * Get collections of app to upgrades.
     * @return array
     */
    public function getRecommendAppsToUpgrades()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $existedApps = $this->discoverExistedPackages();

        $payload = $this->httpRequest(
            self::METAFOX_STORE_URL . '/purchased',
            'GET',
            ['Accept: application/json'],
            [],
            [
                'id'  => $this->getEnv('MFOX_LICENSE_ID'),
                'key' => $this->getEnv('MFOX_LICENSE_KEY'),
            ]
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

    /**
     * @return array
     * @link: /extract-framework
     */
    private function stepExtractFramework()
    {
        if (static::SIMULATE_PASS) {
            return $this->success([]);
        }

        $destination = $this->getDownloadFrameworkDestination();

        $archive = new \ZipArchive();

        if ($archive->open($destination) !== true) {
            throw new RuntimeException('Could not open archive file');
        }

        $found = 'upload.zip';

        if (false === $archive->getFromName($found)) {
            $found = rtrim($archive->getNameIndex(0), '/') . '/' . $found;
        }

        $archive->extractTo($this->extractFrameworkFolder);
        $archive->close();

        $uploadZipFilename = $this->extractFrameworkFolder . '/' . $found;

        if (!file_exists($uploadZipFilename)) {
            throw new RuntimeException('Missing file ' . $uploadZipFilename);
        }

        $upload = new \ZipArchive();

        if ($upload->open($uploadZipFilename) !== true) {
            throw new RuntimeException('Could not open archive file');
        }

        for ($index = 0; $index < $upload->numFiles; $index++) {
            $this->log('extract overwrite ' . $upload->getNameIndex($index));
        }

        // overwrite to project root
        $upload->extractTo($this->projectRoot);

        return $this->success([]);
    }

    private function getDownloadFrameworkDestination()
    {
        return $this->downloadFrameworkFolder . '/metafox.zip';
    }

    private function getDownloadableFrameworkVersion()
    {
        $input = $this->getInput();
        $json  = $this->httpRequest(self::METAFOX_STORE_URL . '/phpfox-download', 'post', [], [], $input['license']);

        if (!$json['download']) {
            throw new RuntimeException('Failed getting download url.');
        }

        return $json['version'];
    }

    /**
     * @return array
     * @link ?step=download-framework
     */
    private function stepDownloadFramework()
    {
        $filename = $this->getDownloadFrameworkDestination();

        if (file_exists($filename)) {
            return $this->success([]);
        }

        $lockName = 'downloadFramework';

        if (($result = $this->checkStepIsRetry($lockName))) {
            return $result;
        }

        $this->setStepIsProcessing($lockName);

        $json = $this->httpRequest(self::METAFOX_STORE_URL . '/phpfox-download', 'post', [], [], [
            'id'  => $this->getEnv('MFOX_LICENSE_ID'),
            'key' => $this->getEnv('MFOX_LICENSE_KEY'),
        ]);

        if (!$json['download']) {
            throw new RuntimeException('Failed getting download url.');
        }

        $temporary = $filename . '.temp';
        register_shutdown_function(function () use ($temporary, $filename) {
            if (file_exists($temporary)) {
                copy($temporary, $filename);
                unlink($temporary);
            }
        });

        // fix issue timeout request etc. request limit 15 sec but download need 30 sec.
        $source = fopen($json['download'], 'r');
        $dest   = fopen($temporary, 'w');
        stream_copy_to_stream($source, $dest);

        return $this->success([]);
    }

    private function stepProcessInstall()
    {
        $this->clearDownloadApps();

        $this->log(sprintf('Start %s', __METHOD__));

        $input = $this->getInput();

        $data = [];

        if ($this->hasEnv('MFOX_LICENSE_ID')) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=verify-license',
                    'apiMethod' => 'POST',
                ],
                'title' => 'Verify License.',
            ];
        }

        if ($this->hasEnv('MFOX_DAT_PW')) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=verify-database',
                    'apiMethod' => 'POST',
                ],
                'title' => 'Verify Database',
            ];
        }

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=configure-env-file',
                'apiMethod' => 'POST',
            ],
            'title' => 'Verify Environment',
        ];

        $apps = $input['selectedApps'];
        foreach ($apps as $app) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=download-app',
                    'apiMethod' => 'POST',
                ],
                'data' => [
                    'id'      => $app['identity'],
                    'version' => $app['version'],
                    'name'    => $app['name'],
                ],
                'title' => 'Download ' . $app['name'] . ' - ' . $app['version'],
            ];
        }

        if (count($apps)) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=extract-apps',
                    'apiMethod' => 'POST',
                ],
                'title' => 'Extract Apps',
            ];
        }

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=composer-install',
                'apiMethod' => 'POST',
            ],
            'title' => 'Install Dependencies',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=metafox-install',
                'apiMethod' => 'POST',
            ],
            'title' => 'Process Install',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=clean-cache',
                'apiMethod' => 'GET',
            ],
            'title' => 'Clean Cache',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=optimize',
                'apiMethod' => 'GET',
            ],
            'title' => 'Generate bootstrap files',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=restart-queue-worker',
                'apiMethod' => 'GET',
            ],
            'title' => 'Restart Queues',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=build-frontend',
                'apiMethod' => 'GET',
            ],
            'title' => 'Build Frontend',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=wait-frontend',
                'apiMethod' => 'GET',
            ],
            'title' => 'Waiting for frontend',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=cleanup',
                'apiMethod' => 'GET',
            ],
            'title' => 'Clean files',
        ];

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success($data);
    }

    public function clearDownloadApps()
    {
        $this->execCommand('rm -rf ' . $this->downloadFrameworkFolder);
    }

    public function stepExtractApps()
    {
        $files = scandir($this->downloadAppFolder);

        if (!$files) {
            return $this->success([]);
        }

        try {
            foreach ($files as $filename) {
                if (!str_ends_with($filename, '.zip')) {
                    continue;
                }
                $filename  = $this->downloadAppFolder . '/' . $filename;
                $archive   = new \ZipArchive();
                if (true !== $archive->open($filename, \ZipArchive::RDONLY)) {
                    return $this->failure(400, [], 'Could not unzip ' . $filename);
                }
                $archive->extractTo($this->extractAppFolder);
                $archive->close();
            }

            $this->execCommand(
                sprintf('cp -rf %s/backend/* %s', $this->extractAppFolder, $this->projectRoot),
                [],
                false
            );
        } catch (\Exception $exceptoin) {
            return $this->failure(400, [], $exceptoin->getMessage());
        }

        return $this->success([]);
    }

    public function stepRestartQueueWorker()
    {
        $this->execCommand(sprintf('%s artisan queue:restart', $this->getPhpPath()));

        return $this->success([]);
    }

    /**
     * @return array
     * @link /install?step=process-upgrade
     */
    private function stepProcessUpgrade()
    {
        $this->clearDownloadApps();
        $this->log(sprintf('Start %s', __METHOD__));

        $input = $this->getInput();

        $data = [];

        if ($this->hasEnv('MFOX_LICENSE_ID')) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=verify-license',
                    'apiMethod' => 'POST',
                ],
                'title' => 'Verify License',
            ];
        }

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=down-site',
                'apiMethod' => 'GET',
            ],
            'title' => 'Down site',
        ];

        $this->checkDownloadFrameworkSteps($data);

        $apps = $input['selectedApps'];
        foreach ($apps as $app) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=download-app',
                    'apiMethod' => 'POST',
                ],
                'data' => [
                    'id'      => $app['identity'],
                    'version' => $app['version'],
                    'name'    => $app['name'],
                ],
                'title' => 'Download ' . $app['name'] . ' - ' . $app['version'],
            ];
        }

        if (count($apps)) {
            $data[] = [
                'dataSource' => [
                    'apiUrl'    => '/install?step=extract-apps',
                    'apiMethod' => 'GET',
                ],
                'title' => 'Extract Apps',
            ];
        }

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=composer-install',
                'apiMethod' => 'GET',
            ],
            'title' => 'Update Dependencies',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=metafox-upgrade',
                'apiMethod' => 'GET',
            ],
            'title' => 'Upgrade',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=clean-cache',
                'apiMethod' => 'GET',
            ],
            'title' => 'Clean Cache',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=optimize',
                'apiMethod' => 'GET',
            ],
            'title' => 'Generate bootstrap files',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=restart-queue-worker',
                'apiMethod' => 'GET',
            ],
            'title' => 'Restart Queues',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=build-frontend',
                'apiMethod' => 'GET',
            ],
            'title' => 'Rebuild frontend',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=wait-frontend',
                'apiMethod' => 'GET',
            ],
            'title' => 'Waiting for frontend',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=cleanup',
                'apiMethod' => 'GET',
            ],
            'title' => 'Clean files',
        ];

        $data[] = [
            'dataSource' => [
                'apiUrl'    => '/install?step=up-site',
                'apiMethod' => 'GET',
            ],
            'title' => 'Launch Site',
        ];

        $this->log(sprintf('End %s', __METHOD__));

        return $this->success($data);
    }

    private function hasEnv($name)
    {
        return !empty(getenv($name)) || isset($this->envVars[$name]);
    }

    private function getEnv($name, $fallback = null)
    {
        $value = getenv($name);
        if (!empty($value)) {
            return $value;
        }

        if (isset($this->envVars[$name])) {
            return $this->envVars[$name];
        }

        return $fallback;
    }

    public function stepStart()
    {
        $this->execCommand(sprintf('rm -rf %s/storage/install', $this->projectRoot));

        if ($this->uprading) {
            return $this->getStartForUpgrade();
        }

        return $this->getStartForInstallation();
    }

    /**
     * @return array
     */
    private function getStartForUpgrade()
    {
        $requirement = $this->getRequirement();

        $steps = [];

        if (!$requirement['result']) {
            $steps[] = ['title' => 'Requirements', 'id' => 'requirements'];
        }

        $recommendApps = $this->getRecommendAppsToUpgrades();
        $selectApps    = array_map(function ($app) {
            return [
                'identity' => $app['identity'],
                'name'     => $app['name'],
                'version'  => $app['version'],
            ];
        }, $recommendApps);

        $steps = [
            ['title' => 'Prepare', 'id' => 'prepare-upgrade'],
            count($recommendApps) ? ['title' => 'Applications', 'id' => 'choose-upgrade-apps'] : false,
            ['title' => 'Upgrade', 'id' => 'process-upgrade'],
            ['title' => 'Done', 'id' => 'upgraded'],
        ];

        $data = [
            'baseUrl' => $this->getRootUrl(),
            'root'    => $this->projectRoot,
            'license' => [
                'id'  => $this->getEnv('MFOX_LICENSE_ID', ''),
                'key' => $this->getEnv('MFOX_LICENSE_KEY', ''),
            ],
            'recommendAppsLoaded' => true,
            'recommendApps'       => $recommendApps,
            'selectedApps'        => $selectApps,
            'steps'               => array_values(array_filter($steps, function ($step) {
                return (bool) $step;
            })),
            'requirement'     => $requirement,
            'platformVersion' => $this->getPlatformVersion(),
            'legal'           => 'MetaFox',
            'helpBlock'       => $this->getUpgradeHelpBlock(),
            'succeed'         => false,
            'failure'         => false, // return true when error in started,
        ];

        if ($this->platformInstalled) {
            $data['installing'] = true;
        }

        if ($this->platformInstalledVersion == $this->platformVersion) {
            $data['forceStep'] = 'uptodate';
        }

        // prevent leak information whenever the site is installed.
        if ($this->platformInstalled) {
            unset($data['database'], $data['general'], $data['administrator']);
        }

        return $this->success($data);
    }

    private function getStartForInstallation()
    {
        $requirement = $this->getRequirement();
        $this->log(sprintf('Start %s', __METHOD__));

        $driver = extension_loaded('pdo_pgsql') ? 'pgsql' : 'mysql';

        $hasEnv = file_exists($this->getEnvFile());

        if ($this->getEnv('MFOX_DAT_DRIVER')) {
            $driver = $this->getenv('MFOX_DAT_DRIVER');
        }

        $this->log(sprintf('End %s', __METHOD__));
        $recommendApps       = [];
        $recommendAppsLoaded = false;

        if ($this->getEnv('MFOX_LICENSE_ID')) {
            $recommendAppsLoaded = true;
            $recommendApps       = $this->getRecommendAppsForInstall([
                'id'  => $this->getEnv('MFOX_LICENSE_ID'),
                'key' => $this->getEnv('MFOX_LICENSE_KEY'),
            ]);
        }

        $steps = [
            ['title' => 'Requirements', 'id' => 'requirements'],
            $hasEnv && $this->hasEnv('MFOX_LICENSE_ID') ? false : ['title' => 'License', 'id' => 'license'],
            $hasEnv && $this->hasEnv('MFOX_DAT_PW') ? false : ['title' => 'Database', 'id' => 'database'],
            ['title' => 'Information', 'id' => 'info'],
            ['title' => 'Applications', 'id' => 'apps'],
            ['title' => 'Install', 'id' => 'process-install'],
            ['title' => 'Done', 'id' => 'installed'],
        ];

        $data = [
            'baseUrl' => $this->getRootUrl(),
            'root'    => $this->projectRoot,
            'license' => [
                'id'  => $this->getEnv('MFOX_LICENSE_ID', ''),
                'key' => $this->getEnv('MFOX_LICENSE_KEY', ''),
            ],
            'administrator' => [
                'username' => $this->getEnv('SITE_USERNAME', 'admin'),
                'password' => $this->getEnv('SITE_PASSWORD', ''),
                'email'    => $this->getEnv('SITE_EMAIL', ''),
            ],
            'general' => [
                'site_name' => $this->getEnv('MFOX_SITE_NAME', 'Social Network'),
                'app_url'   => $this->getEnv('APP_URL', $this->getRootUrl()),
                'app_env'   => $this->getEnv('APP_ENV', 'production'), // available options: production, local
                'app_key'   => $this->getEnv('APP_KEY', ''),
                'app_debug' => $this->getEnv('APP_DEBUG', false),
            ],
            'database' => [
                'driver'   => $this->getEnv('MFOX_DAT_DRIVER', $driver),
                'host'     => $this->getEnv('MFOX_DAT_HOST', 'localhost'),
                'name'     => $this->getEnv('MFOX_DAT_DBNAME', 'metafox'),
                'user'     => $this->getEnv('MFOX_DAT_USR', 'metafox'),
                'password' => $this->getEnv('MFOX_DAT_PW', ''),
                'prefix'   => $this->getEnv('MFOX_DAT_DBPREFIX', ''),
                'socket'   => $this->getEnv('MFOX_DAT_SOCKET', ''),
                'port'     => $this->getEnv('MFOX_DAT_PORT', ''),
            ],
            'steps' => array_values(array_filter($steps, function ($step) {
                return (bool) $step;
            })),
            'recommendAppsLoaded' => $recommendAppsLoaded,
            'recommendApps'       => $recommendApps,
            'selectedApps'        => [],
            'requirement'         => $requirement,
            'platformVersion'     => $this->getPlatformVersion(),
            'legal'               => 'MetaFox',
            'helpBlock'           => $this->getInstallHelpBlock(),
            'succeed'             => $this->platformInstalled,
            'failure'             => false, // return true when error in started,
        ];

        if ($this->platformInstalled) {
            $data['installing'] = true;
        }

        if ($this->platformInstalled && $this->platformInstalledVersion == $this->platformVersion) {
            $data['forceStep'] = 'uptodate';
        }

        // prevent leak information whenever the site is installed.
        if ($this->platformInstalled) {
            unset($data['database'], $data['general'], $data['administrator'], $data['license'], $data['steps']);
        }

        return $this->success($data);
    }

    /**
     * @return string
     */
    private function getPlatformVersion()
    {
        $content = file_get_contents($this->projectRoot . '/packages/platform/src/MetaFoxConstant.php');
        preg_match('/public const VERSION\s+=\s+\'(?<version>.+)\';/m', $content, $match);
        $version = is_array($match) ? $match['version'] : '5.0.0';

        return "v$version";
    }

    private function getInstallHelpBlock()
    {
        return <<<'HELP_BLOCK'
If you encounter any problems with the installation, please feel free to
      <a
        target="_blank" rel="noopener noreferrer"
        href="https://clients.phpfox.com/">
        contact us</a>.
HELP_BLOCK;
    }

    private function getUpgradeHelpBlock()
    {
        return <<<'HELP_BLOCK'
If you encounter any problems with the upgrading, please feel free to
      <a
        target="_blank" rel="noopener noreferrer"
        href="https://clients.phpfox.com/">
        contact us</a>.
HELP_BLOCK;
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

        $this->execCommand(sprintf('rm -rf %s/vendor', $this->projectRoot), [], false);

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

        return $this->success([], 'Install dependency successfully');
    }

    /**
     * @return string
     */
    private function getEnvFile()
    {
        return $this->projectRoot . DIRECTORY_SEPARATOR . '.env';
    }

    private function stepConfigureEnvFile()
    {
        $this->log(sprintf('Start %s', __METHOD__));

        $envFile = $this->getEnvFile();

        $input = $this->getInput();

        $content = $this->getEnvValues($input);

        file_put_contents($envFile, $content);

        @chmod($envFile, 0644);
        $this->log(sprintf('End %s', __METHOD__));

        return $this->success([]);
    }

    public function stepDownSite()
    {
        $this->execCommand(sprintf('%s artisan down', $this->getPhpPath()), getenv(), false);

        return $this->success([]);
    }

    public function stepUpSite()
    {
        $this->execCommand(sprintf('%s artisan up', $this->getPhpPath()));

        return $this->success([]);
    }

    public function isStepProcessing($lockName)
    {
        return $this->getLockValue($lockName) === self::PROCESSING;
    }

    public function checkStepIsRetry($lockName, $verify = null)
    {
        $this->setLockName($lockName);
        $status = $this->getLockValue($lockName);

        switch ($status) {
            case self::DONE:
                return $this->success([], 'Install successfully');
            case self::FAILED:
                return $this->failure(400, [], 'Failed to process.');
            case self::PROCESSING:
                if ($verify && $verify()) {
                    return $this->success([]);
                }

                return $this->success(['retry' => true], 'Processing');
            default:
                return false;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    private function stepMetafoxInstall()
    {
        $lockName = 'stepMetafoxInstall';
        if (($result = $this->checkStepIsRetry($lockName))) {
            return $result;
        }

        $this->setStepIsProcessing($lockName);

        $env = array_merge(getenv(), [
            'COMPOSER_MEMORY_LIMIT' => -1,
            'COMPOSER_HOME'         => $this->projectRoot,
        ]);

        $this->execCommand(
            sprintf(
                '%s %s/composer metafox:install -n -q',
                $this->getPhpPath(),
                $this->projectRoot
            ),
            $env,
            true
        );

        $this->log('Installed Successfully');

        $this->log(sprintf('End %s', __METHOD__));

        $this->setStepDone($lockName);

        return $this->success([], 'Install successfully');
    }

    /**
     * @return array
     * @throws Exception
     * @link /install?step=metafox-upgrade
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

        return $this->success([], 'Install successfully');
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
     * @param  string     $url
     * @param  string     $method
     * @param  array|null $params
     * @param  array|null $headers
     * @param  array|null $license
     * @return mixed
     */
    private function httpRequest($url, $method, $params, $headers, $license)
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

        if ($license) {
            $headers[] = 'Authorization: Basic ' . base64_encode($license['id'] . ':' . $license['key']);
        }

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

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

    private function getEnvValues($input)
    {
        if (!(isset($input['database']['port']) ? $input['database']['port'] : null)) {
            $input['database']['port'] = $input['database']['driver'] === 'pgsql' ? 5432 : 3306;
        }
        /** @var array<string,array> $variables */
        $variables = [
            ['section' => 'MetaFox Information'],
            ['name'    => 'MFOX_APP_INSTALLED', 'value' => false],
            ['name'    => 'MFOX_APP_VERSION', 'value' => $this->platformVersion],
            ['section' => 'License Key'],
            ['name'    => 'MFOX_LICENSE_ID', 'value' => @$input['license']['id']],
            ['name'    => 'MFOX_LICENSE_KEY', 'value' => @$input['license']['key']],
            ['section' => 'Api Authenticate'],
            ['name'    => 'APP_KEY', 'value' => ''],
            ['name'    => 'MFOX_API_KEY'],
            ['name'    => 'MFOX_API_SECRET'],
            ['section' => 'Supper Administrator'],
            ['name'    => 'SITE_USERNAME', 'value' => @$input['administrator']['username']],
            ['name'    => 'SITE_EMAIL', 'value' => @$input['administrator']['email']],
            ['name'    => 'SITE_PASSWORD', 'value' => @$input['administrator']['password']],
            ['section' => 'Site Configure'],
            ['name'    => 'APP_ENV', 'value' => @$input['general']['app_env']],
            ['name'    => 'APP_DEBUG', 'value' => (bool) @$input['general']['app_debug']],
            ['name'    => 'APP_URL', 'value' => @$input['general']['app_url']],
            ['name'    => 'MFOX_SITE_NAME', 'value' => @$input['general']['site_name']],
            ['name'    => 'FRONTEND_DEV_URL'],
            ['section' => 'Configure Logging'],
            ['name'    => 'LOG_CHANNEL'],
            ['name'    => 'LOG_LEVEL'],
            ['section' => 'Configure Database'],
            ['name'    => 'MFOX_DAT_DRIVER', 'value' => $input['database']['driver']],
            ['name'    => 'MFOX_DAT_HOST', 'value' => $input['database']['host']],
            ['name'    => 'MFOX_DAT_PORT', 'value' => $input['database']['port']],
            ['name'    => 'MFOX_DAT_DBNAME', 'value' => $input['database']['name']],
            ['name'    => 'MFOX_DAT_USR', 'value' => $input['database']['user']],
            ['name'    => 'MFOX_DAT_PW', 'value' => $input['database']['password']],
            ['name'    => 'MFOX_DAT_DBPREFIX', 'value' => $input['database']['prefix']],
            ['name'    => 'MFOX_DAT_SOCKET', 'value' => $input['database']['socket']],
            ['section' => 'Message Queue'],
            ['name'    => 'BROADCAST_DRIVER', 'value' => 'log'],
            ['name'    => 'QUEUE_CONNECTION'],
            ['section' => 'Configure Session'],
            ['name'    => 'SESSION_DRIVER'],
            ['section' => 'Configure Cache'],
            ['name'    => 'MFOX_CACHE_DRIVER'],
            ['section' => 'AWS Configuration'],
            ['name'    => 'AWS_ACCESS_KEY_ID'],
            ['name'    => 'AWS_SECRET_ACCESS_KEY'],
            ['name'    => 'AWS_DEFAULT_REGION'],
            ['name'    => 'AWS_BUCKET'],
            ['section' => 'Configure SMTP Email'],
            ['name'    => 'MFOX_MAIL_PROVIDER'],
            ['name'    => 'MFOX_MAIL_SMTP_HOST'],
            ['name'    => 'MFOX_MAIL_SMTP_PORT'],
            ['name'    => 'MFOX_MAIL_SMTP_ENCRYPTION'],
            ['name'    => 'MFOX_MAIL_SMTP_USR'],
            ['name'    => 'MFOX_MAIL_SMTP_PW'],
            ['name'    => 'MFOX_MAIL_FROM_NAME'],
            ['name'    => 'MFOX_MAIL_FROM_ADDRESS'],
            ['section' => 'Configure Redis'],
            ['name'    => 'REDIS_CLUSTER'],
            ['name'    => 'REDIS_PREFIX'],
            ['name'    => 'REDIS_URL'],
            ['name'    => 'REDIS_HOST'],
            ['name'    => 'REDIS_USERNAME'],
            ['name'    => 'REDIS_PASSWORD'],
            ['name'    => 'REDIS_PORT'],
            ['name'    => 'REDIS_DB'],
            ['name'    => 'REDIS_CACHE_DB'],
            ['name'    => 'REDIS_SESSION_DB'],
            ['name'    => 'REDIS_QUEUE_DB'],
            ['section' => 'Configure Memcache'],
            ['name'    => 'MEMCACHED_PERSISTENT_ID'],
            ['name'    => 'MEMCACHED_USERNAME'],
            ['name'    => 'MEMCACHED_PASSWORD'],
            ['name'    => 'MEMCACHED_HOST'],
            ['name'    => 'MEMCACHED_PORT'],
        ];

        return $this->formatEnvString($variables);
    }

    private function formatEnvString($variables)
    {
        $lines = [];
        $pad   = str_pad('#', 77, '#');

        foreach ($variables as $row) {
            $value   = isset($row['value']) ? $row['value'] : null;
            $comment = isset($row['comment']) ? $row['comment'] : false;
            $name    = trim(isset($row['name']) ? $row['name'] : '');

            if ($value === null && $name) {
                $value   = $this->getEnv($name, '');
                $comment = !$this->hasEnv($name);
            }

            if (isset($row['section']) ? $row['section'] : false) {
                $line = PHP_EOL . sprintf('%s# %s%s', $pad . PHP_EOL, $row['section'], PHP_EOL . $pad);
            } elseif (is_bool($value)) {
                $line = sprintf('%s=%s', $name, $value ? 'true' : 'false');
            } elseif (is_numeric($value)) {
                $line = sprintf('%s=%s', $name, $value);
            } elseif (null === $value) {
                $line = sprintf('%s=%s', $name, 'null');
            } elseif (!empty($value)) {
                $line = sprintf('%s="%s"', $name, $value);
            } else {
                $line = sprintf('%s=', $name);
            }

            if ($comment) {
                $line = '#' . $line;
            }
            // add new lines
            $lines[] = $line;
        }

        return implode(PHP_EOL, $lines);
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
