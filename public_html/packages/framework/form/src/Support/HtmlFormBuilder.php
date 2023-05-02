<?php

namespace MetaFox\Form\Support;

use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;

class HtmlFormBuilder
{
    /**
     * @var array<string,string>
     */
    protected array $config = [];

    protected array $fallbacks = [];

    public function __construct()
    {
        $this->config = localCacheStore()->rememberForever(__CLASS__, function () {
            return $this->loadConfig();
        });
    }

    /**
     * @return array<string,string>
     */
    private function loadConfig(): array
    {
        $map  = [];
        $data = resolve(DriverRepositoryInterface::class)
            ->loadDrivers(Constants::DRIVER_TYPE_FORM_FIELD, 'web');

        foreach ($data as $item) {
            [$name, $value] = $item;
            $map[$name]     = $value;
        }

        return $map;
    }

    public function refresh(): void
    {
        $this->config = $this->loadConfig();
        localCacheStore()->put(__CLASS__, $this->config);
    }

    /**
     * Get All Support field.
     *
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->config;
    }

    public function getCreator(string $name): ?string
    {
        $creator = $this->config[$name] ?? $this->fallbacks[$name] ?? null;

        if (!$creator || !class_exists($creator)) {
            return null;
        }

        return $creator;
    }

    public function __call(string $name, array $arguments)
    {
        $creator = $this->getCreator($name);

        if(!$creator) return null;

        $name = $arguments[0] ?? null;

        $params = [];
        if ($name) {
            $params['name'] = $name;
        }

        return new $creator($params);
    }
}
