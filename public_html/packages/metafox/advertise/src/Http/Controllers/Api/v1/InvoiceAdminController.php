<?php

namespace MetaFox\Advertise\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Advertise\Http\Resources\v1\Invoice\Admin\InvoiceItemCollection as ItemCollection;
use MetaFox\Advertise\Http\Resources\v1\Invoice\Admin\InvoiceDetail as Detail;
use MetaFox\Advertise\Repositories\InvoiceRepositoryInterface;
use MetaFox\Advertise\Http\Requests\v1\Invoice\Admin\IndexRequest;
use MetaFox\Advertise\Http\Requests\v1\Invoice\Admin\StoreRequest;
use MetaFox\Advertise\Http\Requests\v1\Invoice\Admin\UpdateRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Advertise\Http\Controllers\Api\InvoiceAdminController::$controllers;
 */

/**
 * Class InvoiceAdminController.
 * @codeCoverageIgnore
 * @ignore
 */
class InvoiceAdminController extends ApiController
{
    /**
     * @var InvoiceRepositoryInterface
     */
    private InvoiceRepositoryInterface $repository;

    /**
     * InvoiceAdminController Constructor.
     *
     * @param InvoiceRepositoryInterface $repository
     */
    public function __construct(InvoiceRepositoryInterface $repository)
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

        $context = user();

        $data   = $this->repository->viewInvoicesAdminCP($context, $params);

        return new ItemCollection($data);
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
        $context = user();

        $this->repository->deleteInvoice($context, $id);

        return $this->success([], [], __p('advertise::phrase.invoice_successfully_deleted'));
    }
}
