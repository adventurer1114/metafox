<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointPackage;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Models\PointPackage as Model;
use MetaFox\ActivityPoint\Policies\PackagePolicy;
use MetaFox\ActivityPoint\Repositories\PointPackageRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Payment\Contracts\GatewayManagerInterface;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Yup\Yup;

/**
 * Class PurchasePointPackageForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_package.purchase
 * @driverType form
 */
class PurchasePointPackageForm extends AbstractForm
{
    private GatewayManagerInterface $gatewayManager;

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function boot(
        PointPackageRepositoryInterface $repository,
        GatewayManagerInterface $gatewayManager,
        ?int $id = null
    ): void {
        $this->resource       = $repository->find($id);
        $this->gatewayManager = $gatewayManager;

        policy_authorize(PackagePolicy::class, 'purchase', user(), $this->resource);
    }

    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.select_payment_gateway'))
            ->action(apiUrl('activitypoint.package.purchase', ['id' => $this->resource->entityId()]))
            ->secondAction(MetaFoxForm::FORM_ACTION_REDIRECT_TO)
            ->asPost()
            ->setValue([]);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::radioGroup('payment_gateway')
                ->required()
                ->options($this->getGatewayOptions())
                ->label(__p('payment::phrase.select_a_payment_gateway'))
                ->color('text.secondary'),
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('activitypoint::phrase.purchase'))
                    ->disableWhenClean(),
                Builder::cancelButton(),
            );
    }

    /**
     * @return array<int, mixed>
     * @throws AuthenticationException
     */
    protected function getGatewayOptions(): array
    {
        $context      = user();
        $price        = $this->resource->price;
        $userCurrency = app('currency')->getUserCurrencyId($context);

        return $this->gatewayManager->getGatewaysForForm($context, [
            'entity' => $this->resource->entityType(),
            'amount' => Arr::get($price, $userCurrency, 0),
        ]);
    }
}
