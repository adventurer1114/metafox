<?php

namespace MetaFox\Advertise\Http\Resources\v1\Advertise;

use MetaFox\Advertise\Policies\AdvertisePolicy;
use MetaFox\Advertise\Repositories\AdvertiseRepositoryInterface;
use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Form\AbstractForm;
use MetaFox\Yup\Yup;
use MetaFox\Form\Builder as Builder;
use MetaFox\Advertise\Models\Advertise as Model;
use MetaFox\Payment\Http\Resources\v1\Order\GatewayForm;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class PaymentAdvertiseForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class PaymentAdvertiseForm extends GatewayForm
{
    /**
     * @var bool
     */
    protected bool $isChangePrice = false;

    protected ?float $newPrice = null;

    protected function prepare(): void
    {
        $this->title(__p('payment::phrase.select_payment_gateway'))
            ->action($this->isChangePrice ? 'advertise/invoice/change' : 'advertise/invoice/payment')
            ->asPost()
            ->setValue([
                'item_id'    => $this->resource->entityId(),
                'item_type'  => $this->resource->entityType(),
                'invoice_id' => $this->resource->latestUnpaidInvoice?->entityId(),
            ]);

        match ($this->isChangePrice) {
            true => $this->title(__p('advertise::phrase.change_invoice'))
                ->secondAction('@redirectTo'),
            false => $this->secondAction('@redirectTo'),
        };
    }

    protected function initialize(): void
    {
        if ($this->isChangePrice) {
            $this->addChangedPriceFields();
        }

        parent::initialize();
    }

    protected function addChangedPriceFields(): void
    {
        $context = user();

        $currencyId = app('currency')->getUserCurrencyId($context);

        $placementPrice = Support::getPlacementPriceByCurrencyId($this->resource->placement_id, $currencyId);

        $price = Support::calculateAdvertisePrice($this->resource, $placementPrice);

        $this->newPrice = $price;

        $this->addBasic()
            ->addFields(
                Builder::typography('description')
                    ->plainText($this->resource->getChangePriceMessage($price, $currencyId))
            );
    }

    protected function getGatewayParams(): array
    {
        return array_merge(parent::getGatewayParams(), [
            'price' => $this->newPrice ?? $this->resource->latestUnpaidInvoice->price,
        ]);
    }

    public function boot(?int $id = null): void
    {
        $this->resource = resolve(AdvertiseRepositoryInterface::class)->find($id);

        $context = user();

        policy_authorize(AdvertisePolicy::class, 'payment', $context, $this->resource);

        $this->isChangePrice = Support::isAdvertiseChangePrice($this->resource);
    }

    protected function requiredGateway(): bool
    {
        return true;
    }
}
