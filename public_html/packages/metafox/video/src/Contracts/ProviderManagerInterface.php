<?php

namespace MetaFox\Video\Contracts;

use Illuminate\Support\Collection;
use MetaFox\Video\Models\VideoService;

interface ProviderManagerInterface
{
    /**
     * @return VideoService
     */
    public function getDefaultService(): VideoService;

    /**
     * @return Collection<VideoService>
     */
    public function getAllActiveServices(): Collection;

    /**
     * @return VideoServiceInterface
     */
    public function getDefaultServiceClass(): VideoServiceInterface;

    /**
     * @param  string       $driver
     * @return VideoService
     */
    public function getVideoServiceByDriver(string $driver): VideoService;

    /**
     * @param  string                $driver
     * @return VideoServiceInterface
     */
    public function getVideoServiceClassByDriver(string $driver): VideoServiceInterface;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getServicesForForm(): array;
}
