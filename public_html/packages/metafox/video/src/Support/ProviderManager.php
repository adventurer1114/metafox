<?php

namespace MetaFox\Video\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Video\Contracts\ProviderManagerInterface;
use MetaFox\Video\Contracts\VideoServiceInterface;
use MetaFox\Video\Models\VideoService;
use MetaFox\Video\Repositories\VideoServiceRepositoryInterface;

class ProviderManager implements ProviderManagerInterface
{
    private VideoServiceRepositoryInterface $serviceRepository;

    public function __construct(VideoServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @return VideoServiceRepositoryInterface
     */
    public function getServiceRepository(): VideoServiceRepositoryInterface
    {
        return $this->serviceRepository;
    }

    public function getDefaultService(): VideoService
    {
        $defaultService = Settings::get('video.video_service_to_process_video');
        $service        = $this->getServiceRepository()
            ->getModel()
            ->newModelQuery()
            ->where('driver', '=', $defaultService)
            ->first();

        if (!$service instanceof VideoService) {
            abort(400, __p('video::phrase.no_active_video_service'));
        }

        return $service;
    }

    public function getDefaultServiceClass(): VideoServiceInterface
    {
        $service      = $this->getDefaultService();
        $serviceClass = $service->service_class;

        Log::channel('dev')->info('Loading Video Service: ' . $serviceClass);

        $serviceClass = new $serviceClass();

        if (!$serviceClass instanceof VideoServiceInterface) {
            abort(400, __p('video::phrase.no_active_video_service'));
        }

        return $serviceClass;
    }

    public function getVideoServiceByDriver(string $driver): VideoService
    {
        $service = $this->getServiceRepository()
            ->getModel()
            ->newModelQuery()
            ->where('driver', $driver)
            ->first();

        if (!$service instanceof VideoService) {
            abort(400, __p('video::phrase.no_active_video_service'));
        }

        return $service;
    }

    public function getVideoServiceClassByDriver(string $driver): VideoServiceInterface
    {
        $service = $this->getVideoServiceByDriver($driver);

        $serviceClass = resolve($service->service_class, ['extra' => $service->extra]);

        if (!$serviceClass instanceof VideoServiceInterface) {
            abort(400, __p('video::phrase.no_active_video_service'));
        }

        return $serviceClass;
    }

    public function getAllActiveServices(): Collection
    {
        return $this->getServiceRepository()
            ->getModel()
            ->newModelQuery()
            ->where('is_active', 1)
            ->get()
            ->collect();
    }

    /**
     * @return array<int, array<string, mixed>>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getServicesForForm(): array
    {
        return $this->getAllActiveServices()
            ->map(function (VideoService $service, $key) {
                return [
                    'label' => $service->name,
                    'value' => $service->driver,
                ];
            })->toArray();
    }
}
