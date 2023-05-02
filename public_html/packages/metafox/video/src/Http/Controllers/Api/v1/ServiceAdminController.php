<?php

namespace MetaFox\Video\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Video\Http\Requests\v1\VideoService\Admin\IndexRequest;
use MetaFox\Video\Http\Resources\v1\VideoService\Admin\VideoServiceItem as Item;
use MetaFox\Video\Http\Resources\v1\VideoService\Admin\VideoServiceItemCollection as ItemCollection;
use MetaFox\Video\Repositories\VideoServiceRepositoryInterface;

/**
 * Class ServiceAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group admincp/video
 */
class ServiceAdminController extends ApiController
{
    /**
     * @var VideoServiceRepositoryInterface
     */
    public VideoServiceRepositoryInterface $repository;

    /**
     * @param VideoServiceRepositoryInterface $repository
     */
    public function __construct(VideoServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse category.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection<Item>
     * @throws AuthenticationException
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $context = user();
        $params  = $request->validated();

        $data = $this->repository->viewServices($context, $params);

        return new ItemCollection($data);
    }

    /**
     * View editing form.
     *
     * @param  Request      $request
     * @param  int          $service
     * @return JsonResponse
     */
    public function edit(Request $request, int $service): JsonResponse
    {
        $videoService = $this->repository->find($service);
        [,$driver,,]  = resolve(DriverRepositoryInterface::class)->loadDriver(
            Constants::DRIVER_TYPE_FORM_SETTINGS,
            sprintf('video.%s', $videoService->driver),
            'admin'
        );

        $form = resolve($driver);
        if (method_exists($form, 'boot')) {
            app()->call([$form, 'boot'], $request->route()->parameters());
        }

        return $this->success($form);
    }
}
