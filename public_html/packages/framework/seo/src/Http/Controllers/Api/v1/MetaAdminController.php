<?php

namespace MetaFox\SEO\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\SEO\Http\Requests\v1\Meta\Admin\IndexRequest;
use MetaFox\SEO\Http\Requests\v1\Meta\Admin\StoreRequest;
use MetaFox\SEO\Http\Requests\v1\Meta\Admin\UpdateRequest;
use MetaFox\SEO\Http\Resources\v1\Meta\Admin\MetaItem as Detail;
use MetaFox\SEO\Http\Resources\v1\Meta\Admin\MetaItemCollection as ItemCollection;
use MetaFox\SEO\Http\Resources\v1\Meta\Admin\StoreMetaForm;
use MetaFox\SEO\Http\Resources\v1\Meta\Admin\UpdateMetaForm;
use MetaFox\SEO\Models\Meta;
use MetaFox\SEO\Repositories\MetaRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Core\Http\Controllers\Api\MetaAdminController::$controllers.
 */

/**
 * Class MetaAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class MetaAdminController extends ApiController
{
    /**
     * @var MetaRepositoryInterface
     */
    private MetaRepositoryInterface $repository;

    /**
     * MetaAdminController Constructor.
     *
     * @param MetaRepositoryInterface $repository
     */
    public function __construct(MetaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();

        $query = $this->repository->getModel()->newQuery();

        if (($q = $request->get('q'))) {
            $query = $query->addScope(new SearchScope($q, ['name', 'url']));
        }

        if ($package = $request->get('package_id')) {
            $query->where(['package_id' => $package]);
        }
        if ($resolution = $request->get('resolution', 'web')) {
            $query->where(['resolution' => $resolution]);
        }

        $data = $query->paginate($params['limit'] ?? 100);

        return new ItemCollection($data);
    }

    /**
     * Store item.
     *
     * @param StoreRequest $request
     *
     * @return Detail
     * @throws ValidatorException
     */
    public function store(StoreRequest $request): Detail
    {
        $params = $request->validated();
        $data   = $this->repository->create($params);

        return new Detail($data);
    }

    /**
     * View item.
     *
     * @param int $id
     *
     * @return Detail
     */
    public function show($id): Detail
    {
        $data = $this->repository->find($id);

        return new Detail($data);
    }

    /**
     * Update item.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->update($params, $id);

        /** @var Meta $model */
        $model = $this->repository->find($id);

        $phrases = [];

        $map = [
            'phrase_title'       => 'title',
            'phrase_heading'     => 'heading',
            'phrase_description' => 'description',
            'phrase_keywords'    => 'keywords',
        ];

        foreach ($map as $key => $name) {
            if (isset($params[$name]) && $model->{$key}) {
                $phrases[$model->{$key}] = $params[$name];
            }
        }

        Log::channel('dev')->info('update phrases', $phrases);

        app('phrases')->updatePhrases($phrases);

        return $this->success(new Detail($data));
    }

    /**
     * Delete item.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([
            'id' => $id,
        ]);
    }

    /**
     * Get the creation form.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return $this->success(new StoreMetaForm());
    }

    /**
     * Get updating form.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $resource = $this->repository->find($id);

        return $this->success(new UpdateMetaForm($resource));
    }

    /**
     * Get updating form.
     *
     * @param  Request      $request
     * @return JsonResponse
     */
    public function translate(Request $request): JsonResponse
    {
        $url  = $request->get('url');
        $path = 'sharing/' . trim($url, '/');

        defined('MFOX_SHARING_RETRY_ARRAY') or define('MFOX_SHARING_RETRY_ARRAY', true);

        $response =   Route::dispatch(Request::create($path, 'GET', []));

        $result = json_decode($response->getContent(), true);

        $name = Arr::get($result, 'data.meta:name');

        if (!$name) {
            $name = normalize_seo_meta_name($url);
            $this->repository->createSampleMeta($name);
        }

        $resource = $this->repository->getModel()
            ->newQuery()
            ->where('name', '=', $name)
            ->first();

        if (!$resource) {
            $resource = $this->repository->createSampleMeta($name);
        }

        return $this->success(new UpdateMetaForm($resource));
    }
}
