<?php

namespace MetaFox\Advertise\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Advertise\Http\Requests\v1\Invoice\PaymentRequest;
use MetaFox\Advertise\Http\Resources\v1\Invoice\InvoiceItem;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Advertise\Http\Resources\v1\Invoice\InvoiceItemCollection as ItemCollection;
use MetaFox\Advertise\Http\Resources\v1\Invoice\InvoiceDetail as Detail;
use MetaFox\Advertise\Repositories\InvoiceRepositoryInterface;
use MetaFox\Advertise\Http\Requests\v1\Invoice\IndexRequest;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Advertise\Http\Controllers\Api\InvoiceController::$controllers;
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
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $data = $this->repository->viewInvoices($context, $params);

        return $this->success(new ItemCollection($data));
    }

    public function payment(PaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $invoiceId = Arr::get($data, 'invoice_id');

        $itemType = Arr::get($data, 'item_type');

        $itemId = Arr::get($data, 'item_id');

        $gatewayId = Arr::get($data, 'payment_gateway');

        $context = user();

        $result = $this->repository->paymentInvoice($context, $itemId, $itemType, $gatewayId, $invoiceId);

        $status = Arr::get($result, 'status', false);

        if (false === $status) {
            abort(403, __p('advertise::phrase.can_not_pay_this_invoice'));
        }

        return $this->success([
            'url' => Arr::get($result, 'gateway_redirect_url'),
        ], [], Arr::get($result, 'message'));
    }

    public function change(PaymentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $context   = user();
        $itemId    = Arr::get($data, 'item_id');
        $itemType  = Arr::get($data, 'item_type');
        $gatewayId = Arr::get($data, 'payment_gateway');

        $result = $this->repository->paymentInvoice($context, $itemId, $itemType, $gatewayId);

        $status = Arr::get($result, 'status', false);

        if (false === $status) {
            abort(403, __p('advertise::phrase.can_not_pay_this_invoice'));
        }

        return $this->success([
            'url' => Arr::get($result, 'gateway_redirect_url'),
        ], [], Arr::get($result, 'message'));
    }

    public function cancel(int $id): JsonResponse
    {
        $context = user();

        $invoice = $this->repository->cancelInvoice($context, $id);

        return $this->success(new InvoiceItem($invoice), [], __p('advertise::phrase.invoice_successfully_cancelled'));
    }
}
