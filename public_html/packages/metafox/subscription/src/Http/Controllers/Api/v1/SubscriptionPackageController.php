<?php

namespace MetaFox\Subscription\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionPackage\IndexRequest;
use MetaFox\Subscription\Http\Requests\v1\SubscriptionPackage\RenewRequest;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\PaymentSubscriptionPackageForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\RenewSubscriptionPackageForm;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\SubscriptionPackageDetail as Detail;
use MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\SubscriptionPackageItemCollection as ItemCollection;
use MetaFox\Subscription\Policies\SubscriptionInvoicePolicy;
use MetaFox\Subscription\Policies\SubscriptionPackagePolicy;
use MetaFox\Subscription\Repositories\SubscriptionInvoiceRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionPackageRepositoryInterface;
use MetaFox\Subscription\Support\Facade\SubscriptionPackage as Facade;
use MetaFox\Subscription\Support\Helper;
use MetaFox\User\Support\Facades\User;

/**
 | --------------------------------------------------------------------------
 |  Api Controller
 | --------------------------------------------------------------------------
 |
 | stub: /packages/controllers/api_controller.stub
 | Assign this class in $controllers of
 | @link \MetaFox\Subscription\Http\Controllers\Api\SubscriptionPackageController::$controllers;
 */

/**
 * Class SubscriptionPackageController.
 * @codeCoverageIgnore
 * @ignore
 */
class SubscriptionPackageController extends ApiController
{
    /**
     * @var SubscriptionPackageRepositoryInterface
     */
    private SubscriptionPackageRepositoryInterface $repository;

    /**
     * SubscriptionPackageController Constructor.
     *
     * @param SubscriptionPackageRepositoryInterface $repository
     */
    public function __construct(SubscriptionPackageRepositoryInterface $repository)
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

        $context = User::getGuestUser();

        if (Auth::id() != MetaFoxConstant::GUEST_USER_ID) {
            $context = user();
        }

        $data = $this->repository->viewPackages($context, $params);

        return $this->success(new ItemCollection($data));
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
        $context = user();

        $data = $this->repository->viewPackage($context, $id, [
            'view' => Helper::VIEW_FILTER,
        ]);

        return new Detail($data);
    }

    /**
     * @param  int          $id
     * @return JsonResponse
     */
    public function getPaymentPackageForm(int $id): JsonResponse
    {
        $package = $this->repository->find($id);

        $context = user();

        policy_authorize(SubscriptionPackagePolicy::class, 'purchase', $context, $package);

        $isFree = false;

        if (Facade::isFreePackageForUser($context, $package)) {
            $result = resolve(SubscriptionInvoiceRepositoryInterface::class)->createInvoice($context, [
                'id' => $id,
            ]);

            if (!Arr::get($result, 'is_free')) {
                return $this->error();
            }

            $isFree = true;
        }

        $form = new PaymentSubscriptionPackageForm($package);

        if ($isFree || !$package->is_recurring) {
            return $this->success($form);
        }

        $meta = [
            'continueAction' => [
                'type'    => 'multiStepForm/next',
                'payload' => [
                    'formName'               => 'subscription_payment_form',
                    'processChildId'         => 'subscription_get_gateway_form',
                    'previousProcessChildId' => null,
                ],
            ],
        ];

        $form->setSteps([
            'total_steps'  => 2,
            'current_step' => 1,
        ]);

        return $this->success($form, $meta);
    }

    /**
     * @param  RenewRequest                                   $request
     * @param  int                                            $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function getRenewForm(RenewRequest $request, int $id): JsonResponse
    {
        $package = $this->repository->find($id);

        $context = user();

        policy_authorize(SubscriptionInvoicePolicy::class, 'chooseRenewType', $context, $package);

        $meta = [
            'type'    => 'multiStepForm/next',
            'payload' => [
                'formName'               => 'subscription_payment_form',
                'processChildId'         => 'subscription_get_renew_form',
                'previousProcessChildId' => 'subscription_get_gateway_form',
            ],
        ];

        $form = new RenewSubscriptionPackageForm($package);

        $form->setSteps([
            'total_steps'  => 2,
            'current_step' => 2,
        ]);

        return $this->success($form, ['continueAction' => $meta]);
    }
}
