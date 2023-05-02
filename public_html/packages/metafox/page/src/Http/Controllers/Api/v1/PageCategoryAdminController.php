<?php

namespace MetaFox\Page\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use MetaFox\Page\Http\Requests\v1\PageCategory\DeleteRequest;
use MetaFox\Page\Http\Requests\v1\PageCategory\IndexRequest;
use MetaFox\Page\Http\Requests\v1\PageCategory\StoreRequest;
use MetaFox\Page\Http\Requests\v1\PageCategory\UpdateRequest;
use MetaFox\Page\Http\Resources\v1\PageCategory\Admin\CategoryItemCollection as ItemCollection;
use MetaFox\Page\Http\Resources\v1\PageCategory\Admin\DestroyCategoryForm;
use MetaFox\Page\Http\Resources\v1\PageCategory\Admin\StoreCategoryForm;
use MetaFox\Page\Http\Resources\v1\PageCategory\Admin\UpdateCategoryForm;
use MetaFox\Page\Http\Resources\v1\PageCategory\PageCategoryDetail as Detail;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Traits\Http\Controllers\OrderCategoryTrait;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PageCategoryController.
 */
class PageCategoryAdminController extends ApiController
{
    use OrderCategoryTrait;

    public PageCategoryRepositoryInterface $repository;

    public function __construct(PageCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $data   = $this->repository->viewForAdmin(user(), $params);

        return new ItemCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     * @throws AuthorizationException|AuthenticationException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var Category $data */
        $data   = $this->repository->createCategory(user(), $params);

        if ($data->parent_id) {
            $url = sprintf('/admincp/page/category/%s/category/browse?parent_id=%s', $data->parent_id, $data->parent_id);
        } else {
            $url = '/admincp/event/category/browse';
        }

        $this->navigate($url);

        return $this->success(new Detail($data), [], __p('core::phrase.resource_create_success', [
            'resource_name' => __p('core::phrase.category'),
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewCategory(user(), $id);

        return new Detail($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->updateCategory(user(), $id, $params);

        return $this->success(new Detail($data), [], __p('core::phrase.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function destroy(DeleteRequest $request, int $id): JsonResponse
    {
        $params        = $request->validated();
        $newCategoryId = $params['new_category_id'];
        $newTypeId     = $params['new_type_id'] ?? 0;

        $this->repository->deleteCategory(user(), $id, $newCategoryId, $newTypeId);

        return $this->success([], [], __p('page::phrase.successfully_deleted_the_category'));
    }

    /**
     * View creation form.
     *
     * @return StoreCategoryForm
     */
    public function create(): StoreCategoryForm
    {
        return new StoreCategoryForm();
    }

    public function edit(int $id): JsonResponse
    {
        $form = new UpdateCategoryForm($this->repository->find($id));

        return $this->success($form);
    }

    public function delete(int $id): JsonResponse
    {
        $form = new DestroyCategoryForm($this->repository->find($id));

        app()->call([$form, 'boot'], ['id' => $id]);

        return $this->success($form);
    }

    /**
     * Update active status.
     *
     * @param  int          $id
     * @return JsonResponse
     */
    public function toggleActive(int $id): JsonResponse
    {
        $item = $this->repository->find($id);
        $item->update(['is_active' => $item->is_active ? 0 : 1]);

        return $this->success([new Detail($item)], [], __p('core::phrase.already_saved_changes'));
    }

    public function default(int $id): JsonResponse
    {
        $item = $this->repository->find($id);
        $data = [
            'page.default_category' => $id,
        ];
        Settings::save($data);

        Artisan::call('cache:reset');

        return $this->success([new Detail($item)], [], __p('core::phrase.updated_successfully'));
    }
}
