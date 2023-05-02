<?php

namespace MetaFox\Sms\Support;

use Illuminate\Support\Arr;
use MetaFox\Sms\Contracts\ServiceInterface;

abstract class AbstractService implements ServiceInterface
{
    /**
     * @var array<mixed>
     */
    private array $config = [];

    public function getConfig(?string $key)
    {
        if (!empty($key)) {
            return Arr::get($this->config, $key);
        }

        return $this->config;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}
