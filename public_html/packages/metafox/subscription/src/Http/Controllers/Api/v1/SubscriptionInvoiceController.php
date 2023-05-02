<?php

namespace MetaFox\Subscription\Http\Controllers\Api\v1;

use Facebook\Exception\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\CancelRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\IndexRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\PaymentRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\RenewMethodRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\RenewRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\StoreRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice\UpgradeRequest;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\CancelSubscriptionInvoiceForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\PaymentSubscriptionInvoiceForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\RenewMethodForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\RenewSubscriptionInvoiceForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\SubscriptionInvoiceDetail as Detail;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice\SubscriptionInvoiceItemCollection as ItemCollection;
use MetaFox\Subscription\Policies\SubscriptionInvoicePolicy;
use MetaFox\Subscription\Repositories\SubscriptionInvoiceRepositoryInterface;
use MetaFox\Subscription\Support\Helper;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Subscription\Http\Controllers\Api\SubscriptionInvoiceController::$controllers;
 */

/**
 * Class SubscriptionInvoiceController.
 * @codeCoverageIgnore
 * @ignore
 */
class SubscriptionInvoiceController extends ApiController
{
    /**
     * @var SubscriptionInvoiceRepositoryInterface
     */
    private SubscriptionInvoiceRepositoryInterface $repository;

    /**
     * SubscriptionInvoiceController Constructor.
     *
     * @param SubscriptionInvoiceRepositoryInterface $repository
     */
    public function __construct(SubscriptionInvoiceRepositoryInterface $repository)
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

        $data = $this->repository->viewInvoices($context, $params);

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

        $result = $this->repository->createInvoice($context, $params);

        if (!count($result)) {
            return $this->error(__p('subscription::validation.can_not_create_order_for_package_purchasement'));
        }

        return $this->success([
            'url' => Arr::get($result, 'gateway_redirect_url'),
        ], ['continueAction' => ['type' => 'multiStepForm/subscription/done']], Arr::get($result, 'message'));
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
     * @param  int                                            $id
     * @return CancelSubscriptionInvoiceForm
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function getCancelSubscriptionForm(int $id): CancelSubscriptionInvoiceForm
    {
        $invoice = $this->repository->find($id);

        $context = user();

        policy_authorize(SubscriptionInvoicePolicy::class, 'cancel', $context, $invoice);

        return new CancelSubscriptionInvoiceForm($invoice);
    }

    /**
     * @param  CancelRequest                            $request
     * @param  int                                      $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function cancel(CancelRequest $request, int $id): JsonResponse
    {
        $context = user();

        $params = $request->validated();

        $this->repository->cancelSubscriptionByUser($context, $id, $params);

        $invoice = $this->repository->find($id);

        return $this->success(new Detail($invoice), [], __p('subscription::phrase.subscription_successfully_cancelled'));
    }

    /**
     * @param  int                                            $id
     * @return RenewSubscriptionInvoiceForm
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function getRenewSubscriptionForm(int $id): RenewSubscriptionInvoiceForm
    {
        $invoice = $this->repository
            ->with(['package'])
            ->find($id);

        $context = user();

        policy_authorize(SubscriptionInvoicePolicy::class, 'renew', $context, $invoice);

        return new RenewSubscriptionInvoiceForm($invoice);
    }

    /**
     * @param  RenewRequest                             $request
     * @param  int                                      $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function renew(RenewRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $context = user();

        $result = $this->repository->renewInvoice($context, $id, $data);

        if (!count($result)) {
            return $this->error(__p('subscription::phrase.renew_progress_has_been_failed'));
        }

        return $this->success([
            'url' => Arr::get($result, 'gateway_redirect_url'),
        ]);
    }

    /**
     * @param  int                                      $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function change(int $id): JsonResponse
    {
        $context = user();

        $invoice = $this->repository->changeInvoice($context, $id);

        if (null === $invoice) {
            return $this->error(__p('subscription::phrase.can_not_change_invoice'));
        }

        return $this->success(ResourceGate::asResource($invoice, 'item'));
    }

    /**
     * @param  PaymentRequest $request
     * @param  int            $id
     * @return JsonResponse
     */
    public function getPaymentForm(PaymentRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $actionType = Arr::get($data, 'action_type');

        $invoice = $this->repository->find($id);

        $context = user();

        switch ($actionType) {
            case Helper::UPGRADE_FORM_ACTION:
                policy_authorize(SubscriptionInvoicePolicy::class, 'upgrade', $context, $invoice);
                break;
            case Helper::PAY_NOW_FORM_ACTION:
                policy_authorize(SubscriptionInvoicePolicy::class, 'payNow', $context, $invoice);
                break;
            default:
                throw new AuthorizationException();
        }

        $form = new PaymentSubscriptionInvoiceForm($invoice, $actionType);

        $meta = [];

        if ($invoice->is_recurring && $actionType == Helper::UPGRADE_FORM_ACTION) {
            $meta = [
                'continueAction' => [
                    'type'    => 'multiStepForm/next',
                    'payload' => [
                        'formName'               => 'subscription_invoice_payment_form',
                        'processChildId'         => 'subscription_invoice_get_gateway_form',
                        'previousProcessChildId' => null,
                    ],
                ],
            ];

            $form->setSteps([
                'total_steps'  => 2,
                'current_step' => 1,
            ]);
        }

        return $this->success($form, $meta);
    }

    /**
     * @param  RenewMethodRequest                             $request
     * @param  int                                            $id
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function getRenewMethodForm(RenewMethodRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $invoice = $this->repository->find($id);

        $actionType = Arr::get($data, 'action_type');

        $context = user();

        switch ($actionType) {
            case Helper::UPGRADE_FORM_ACTION:
                policy_authorize(SubscriptionInvoicePolicy::class, 'upgrade', $context, $invoice);
                break;
            case Helper::PAY_NOW_FORM_ACTION:
                policy_authorize(SubscriptionInvoicePolicy::class, 'payNow', $context, $invoice);
                break;
            default:
                throw new AuthorizationException();
        }

        $form = new RenewMethodForm($invoice, $actionType);

        $form->setSteps([
            'total_steps'  => 2,
            'current_step' => 2,
        ]);

        return $this->success($form, [
            'continueAction' => [
                'type'    => 'multiStepForm/next',
                'payload' => [
                    'formName'               => 'subscription_invoice_payment_form',
                    'processChildId'         => 'subscription_invoice_get_renew_form',
                    'previousProcessChildId' => 'subscription_invoice_get_gateway_form',
                ],
            ],
        ]);
    }

    /**
     * @param  UpgradeRequest $request
     * @param  int            $id
     * @return JsonResponse
     */
    public function upgrade(UpgradeRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        $context = user();

        $result = $this->repository->upgrade($context, $id, $data);

        if (null === $result) {
            return $this->error(__p('subscription::phrase.can_not_upgrade_this_invoice'));
        }

        $isFree = Arr::get($result, 'is_free');

        if ($isFree === true) {
            return $this->success([], [
                'continueAction' => ['type' => 'multiStepForm/subscription/forceReload'],
            ], __p('subscription::phrase.your_membership_has_successfully_been_upgraded'));
        }

        return $this->success([
            'url' => Arr::get($result, 'gateway_redirect_url'),
        ], ['continueAction' => ['type' => 'multiStepForm/subscription/done']]);
    }
}
