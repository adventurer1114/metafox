<?php

namespace MetaFox\App\Http\Controllers\Api\v1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use MetaFox\App\Http\Requests\v1\AppStoreProduct\Admin\IndexRequest;
use MetaFox\App\Http\Requests\v1\AppStoreProduct\Admin\SearchFormRequest;
use MetaFox\App\Http\Resources\v1\AppStoreProduct\Admin\SearchForm;
use MetaFox\App\Repositories\PackageRepositoryInterface;
use MetaFox\App\Support\MetaFoxStore;
use MetaFox\App\Support\PackageInstaller;
use MetaFox\Platform\Http\Controllers\Api\ApiController;

class StoreAdminController extends ApiController
{
    /**
     * @var MetaFoxStore
     */
    private MetaFoxStore $store;

    private PackageRepositoryInterface $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;

        $this->store = resolve(MetaFoxStore::class);
    }

    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $products = Cache::remember(
            'STORE_' . md5(implode('', $params)),
            10,
            function () use ($params) {
                return $this->store->browse($params);
            }
        );

        return $this->success($products);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success($this->store->show($id));
    }

    /**
     * View search form.
     *
     * @param  SearchFormRequest $request
     * @return JsonResponse
     */
    public function form(SearchFormRequest $request): JsonResponse
    {
        $params = $request->validated();

        return $this->success(new SearchForm($params));
    }

    public function install(Request $request): JsonResponse
    {
        $name        = $request->get('name');
        $app_version = $request->get('app_version');

        try {
            $this->packageRepository->setInstallationStatus($name, 'downloading');

            $filename = $this->store->downloadProduct($name, $app_version, config('app.mfox_app_channel'));

            $this->packageRepository->setInstallationStatus($name, 'installing');

            resolve(PackageInstaller::class)->install($filename);

            $this->packageRepository->setInstallationStatus($name, '');

            Artisan::call('optimize:clear');
        } catch (Exception $exception) {
            $this->packageRepository->setInstallationStatus($name, '');

            return $this->error($exception->getMessage(), 402);
        }

        $data = [
            'id'            => $name,
            'module_name'   => 'core',
            'resource_name' => 'app_store_product',
        ];

        return $this->success($data, [], __p('core::phrase.installed_successfully'));
    }
}
