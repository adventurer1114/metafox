<?php

namespace MetaFox\Layout\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use MetaFox\App\Repositories\Eloquent\PackageRepository;
use MetaFox\Layout\Http\Requests\v1\Build\Admin\IndexRequest;
use MetaFox\Layout\Http\Requests\v1\Build\Admin\StoreRequest;
use MetaFox\Layout\Http\Requests\v1\Build\Admin\UpdateRequest;
use MetaFox\Layout\Http\Resources\v1\Build\Admin\BuildDetail as Detail;
use MetaFox\Layout\Http\Resources\v1\Build\Admin\BuildItem;
use MetaFox\Layout\Http\Resources\v1\Build\Admin\CreateBuild as CreateBuildForm;
use MetaFox\Layout\Jobs\CheckBuild;
use MetaFox\Layout\Jobs\CreateBuild;
use MetaFox\Layout\Models\Build;
use MetaFox\Layout\Repositories\BuildRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Layout\Http\Controllers\Api\BuildAdminController::$controllers;.
 */

/**
 * Class BuildAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class BuildAdminController extends ApiController
{
    /**
     * @var BuildRepositoryInterface
     */
    private BuildRepositoryInterface $repository;

    /**
     * BuildAdminController Constructor.
     *
     * @param BuildRepositoryInterface $repository
     */
    public function __construct(BuildRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->repository->checkExpiredTasks();
    }

    // does not need to verfy bundle task per id. scan it all.`
    public function check(): JsonResponse
    {
        $job = resolve(CheckBuild::class);

        $this->dispatchSync($job);

        $this->navigate('reload');

        return $this->success([]);
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $data   = $this->repository->orderBy('id', 'desc')
            ->paginate($params['limit'] ?? 20);

        return $this->success(BuildItem::collection($data));
    }

    public function create(): JsonResponse
    {
        return $this->success(new CreateBuildForm());
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $job = new CreateBuild('Rebuild site');
        $this->dispatchSync($job);

        $this->navigate('/admincp/layout/build/browse', true);

        set_installation_lock('stepBuildFrontend', 'none');

        return $this->success([
            'data' => $job->getResponse(),
        ], [], 'Waiting to done.');
    }

    /**
     * View item.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update item.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return Detail
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        return new Detail($data);
    }

    /**
     * Delete item.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        /** @var Build $task */
        $task = $this->repository->find((int) $id);

        $task->delete();

        return $this->success([
            'id' => $id,
        ]);
    }

    public function wizard()
    {
        $steps        = [];
        $env          = resolve(PackageRepository::class)->getBuildEnvironments();
        $buildService = preg_replace('/https?:\/\/([^\/]+)(\/?)/m', '$1', config('app.mfox_bundle_service_url'));
        $info         = view('layout::wizard.info', compact('env', 'buildService'))->render();

        // step 1.
        $steps[] = [
            'title'     => 'Checking Environment',
            'component' => 'ui.step.info',
            'expanded'  => true,
            'props'     => [
                'html'        => $info,
                'submitLabel' => __p('core::phrase.continue'),
                'hasSubmit'   => true,
            ],
        ];

        // step 2.

        $steps[] = [
            'title'     => 'Processing',
            'component' => 'ui.step.processes',
            'props'     => [
                'steps' => [
                    [
                        'title'      => 'Post Build',
                        'dataSource' => ['apiUrl' => '/admincp/layout/build', 'apiMethod' => 'POST'],
                    ],
                    [
                        'title'            => 'Waiting Build Service Callback',
                        'disableUserAbort' => true,
                        'dataSource'       => ['apiUrl' => '/admincp/layout/build/waiting', 'apiMethod' => 'GET'],
                    ],
                ],
            ],
        ];

        $steps[] = [
            'title'     => 'Done',
            'component' => 'ui.step.info',
            'props'     => [],
        ];

        $data = [
            'title'       => __p('core::phrase.rebuild_site'),
            'description' => __p('layout::phrase.rebuite_site_guide'),
            'component'   => 'ui.step.steppers',
            'props'       => [
                'steps' => $steps,
            ],

        ];

        return $this->success($data);
    }

    public function waiting()
    {
        $lockName = 'stepBuildFrontend';
        if (($result = $this->checkStepIsRetry($lockName))) {
            return $result;
        }

        CheckBuild::dispatchSync();

        return $this->success(['retry' => true]);
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
}
