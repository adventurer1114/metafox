<?php

namespace MetaFox\Sms\Support;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use MetaFox\Sms\Support\Services\LogService;
use InvalidArgumentException;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Sms\Contracts\ManagerInterface;
use MetaFox\Sms\Contracts\ServiceInterface;
use Psr\Log\LoggerInterface;

class SmsManager implements ManagerInterface
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved services.
     *
     * @var array<ServiceInterface>
     */
    protected $services = [];

    /**
     * Create a new SMS manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get a service instance by name.
     *
     * @param  string|null      $name
     * @return ServiceInterface
     */
    public function service($name = null): ServiceInterface
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->services[$name] = $this->get($name);
    }

    /**
     * Attempt to get the service from the local cache.
     *
     * @param  string           $name
     * @return ServiceInterface
     */
    protected function get($name): ServiceInterface
    {
        try {
            $service = $this->resolve($name);
        } catch (Exception $e) {
            if (app()->isLocal()) {
                throw $e;
            }

            $service = $this->createFailoverService();
        }

        return $this->services[$name] ?? $service;
    }

    /**
     * Resolve the given service.
     *
     * @param  string           $name
     * @return ServiceInterface
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name): ServiceInterface
    {
        $config = $this->getConfig($name);

        if (null === $config) {
            throw new InvalidArgumentException("SMS service [{$name}] is not defined.");
        }

        [, $class]   = resolve(DriverRepositoryInterface::class)->loadDriver('sms-service', "$name");

        $service = new $class();
        if (!$service instanceof ServiceInterface) {
            throw new InvalidArgumentException("SMS service [{$name}] is not defined.");
        }

        return $service->setConfig($config);
    }

    /**
     * Create an instance of the Symfony Failover Service driver.
     *
     * @return ServiceInterface
     */
    protected function createFailoverService(): ServiceInterface
    {
        return $this->service('log');
    }

    /**
     * Create an instance of the Log Service driver.
     *
     * @param  array<mixed> $config
     * @return LogService
     */
    protected function createLogService(array $config): LogService
    {
        $logger = $this->app->make(LoggerInterface::class);

        if ($logger instanceof LogManager) {
            $logger = $logger->channel(
                $config['channel'] ?? $this->app['config']->get('mail.log_channel')
            );
        }

        return new LogService($logger);
    }

    /**
     * Get the mail connection configuration.
     *
     * @param  string        $name
     * @return ?array<mixed>
     */
    protected function getConfig(string $name): ?array
    {
        return $this->app['config']['sms.driver']
            ? $this->app['config']['sms']
            : $this->app['config']["sms.services.{$name}"];
    }

    /**
     * Get the default mail driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.driver'] ??
            $this->app['config']['sms.default'];
    }

    /**
     * Set the default mail driver name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultDriver(string $name)
    {
        if ($this->app['config']['sms.driver']) {
            $this->app['config']['sms.driver'] = $name;
        }

        $this->app['config']['sms.default'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string       $method
     * @param  array<mixed> $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->service()->$method(...$parameters);
    }
}
