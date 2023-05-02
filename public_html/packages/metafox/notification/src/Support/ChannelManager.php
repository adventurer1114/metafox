<?php

namespace MetaFox\Notification\Support;

use Illuminate\Contracts\Container\BindingResolutionException;
use MetaFox\Notification\Channels\MobilepushChannel;
use MetaFox\Notification\Channels\SmsChannel;
use MetaFox\Notification\Channels\WebpushChannel;

class ChannelManager extends \Illuminate\Notifications\ChannelManager
{
    /**
     * Create an instance of the mobilepush driver.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function createMobilepushDriver(): mixed
    {
        return $this->container->make(MobilepushChannel::class);
    }

    /**
     * Create an instance of the mobilepush driver.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function createWebpushDriver(): mixed
    {
        return $this->container->make(WebpushChannel::class);
    }

    /**
     * Create an instance of the SMS driver.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function createSmsDriver(): mixed
    {
        return $this->container->make(SmsChannel::class);
    }
}
