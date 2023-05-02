<?php

namespace MetaFox\Marketplace\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Marketplace\Http\Requests\v1\Invoice\ChangeRequest;
use MetaFox\Marketplace\Http\Requests\v1\Invoice\PaymentRequest;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Marketplace\Http\Resources\v1\Invoice\InvoiceItemCollection as ItemCollection;
use MetaFox\Marketplace\Http\Resources\v1\Invoice\InvoiceDetail as Detail;
use MetaFox\Marketplace\Repositories\InvoiceRepositoryInterface;
use MetaFox\Marketplace\Http\Requests\v1\Invoice\IndexRequest;
use MetaFox\Marketplace\Http\Requests\v1\Invoice\StoreRequest;
use MetaFox\Marketplace\Http\Requests\v1\Invoice\UpdateRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Marketplace\Http\Controllers\Api\InvoiceController::$controllers;
 */

/**
 * Class InvoiceController.
 * @codeCoverageIgnore
 * @ignore
 */
class InvoiceController extends ApiController
{
    /**
     * @var InvoiceRepositoryInterface
     */
    private InvoiceRepositoryInterface $repository;

    /**
     * InvoiceController Constructor.
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

        $data   = $this->repository->viewInvoices($context, $params);

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
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $listingId = Arr::get($params, 'id', 0);

        $gatewayId = Arr::get($params, 'payment_gateway', 0);

        $data = $this->repository->createInvoice($context, $listingId, $gatewayId);

        $status = Arr::get($data, 'status', false);

        if (false === $status) {
            return $this->error(__p('marketplace::phrase.can_not_create_order_for_listing_purchasement'));
        }

        return $this->success([
            'url' => Arr::get($data, 'gateway_redirect_url'),
        ]);
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
        $context = user();

        $data = $this->repository->viewInvoice($context, $id);

        return new Detail($data);
    }

    /**
     * Update item.
     *
     * @param  UpdateRequest      $request
     * @param  int                $id
     * @return Detail|null
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, int $id): ?Detail
    {
        return null;
    }

    /**
     * Delete item.
     *
     * @param int $id
     *
     * @return JsonResponse|null
     */
    public function destroy(int $id): ?JsonResponse
    {
        return null;
    }

    public function change(ChangeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $context = user();

        $id = Arr::get($data, 'id');

        $invoice = $this->repository->changeInvoice($context, $id);

        if (null === $invoice) {
            return $this->error(__p('marketplace::phrase.can_not_change_the_invoice'));
        }

        return $this->success(ResourceGate::asResource($invoice, 'item'));
    }

    public function repayment(PaymentRequest $request, int $id): JsonResponse
    {
        $context = user();

        $params = $request->validated();

        $gatewayId = Arr::get($params, 'payment_gateway');

        $data = $this->repository->repaymentInvoice($context, $id, $gatewayId);

        $status = Arr::get($data, 'status', false);

        if (false === $status) {
            return $this->error(__p('marketplace::phrase.can_not_create_order_for_listing_purchasement'));
        }

        return $this->success([
            'url' => Arr::get($data, 'gateway_redirect_url'),
        ]);
    }
}
