<?php

namespace MetaFox\Form\Support;

use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;

class MobileFormBuilder
{
    /**
     * @var array<string,string>
     */
    protected array $config = [];

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
            ->loadDrivers(Constants::DRIVER_TYPE_FORM_FIELD, 'mobile');

        foreach ($data as $item) {
            [$name, $value] = $item;
            $map[$name]     = $value;
        }

        return $map;
    }

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
