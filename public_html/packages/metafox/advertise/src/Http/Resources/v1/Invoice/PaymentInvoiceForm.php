<?php

namespace MetaFox\Advertise\Http\Resources\v1\Invoice;

use Illuminate\Support\Arr;
use MetaFox\Advertise\Policies\InvoicePolicy;
use MetaFox\Advertise\Repositories\InvoiceRepositoryInterface;
use MetaFox\Form\Builder as Builder;
use MetaFox\Advertise\Models\Invoice as Model;
use MetaFox\Payment\Http\Resources\v1\Order\GatewayForm;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class PaymentInvoiceForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class PaymentInvoiceForm extends GatewayForm
{
    protected function prepare(): void
    {
        $this->title(__p('payment::phrase.select_payment_gateway'))
            ->action($this->isChangePrice ? 'advertise/invoice/change' : 'advertise/invoice/payment')
            ->asPost()
            ->setValue([
                'item_id'    => $this->resource->itemId(),
                'item_type'  => $this->resource->itemType(),
                'invoice_id' => $this->resource->entityId(),
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

            return;
        }

        parent::initialize();
    }

    protected function addChangedPriceFields(): void
    {
        $context = user();

        $data = $this->resource->item->toPayment($context);

        if (!count($data)) {
            abort(403);
        }

        $this->addBasic()
            ->addFields(
                Builder::typography('description')
                    ->plainText($this->resource->item->getChangePriceMessage(Arr::get($data, 'price'), Arr::get($data, 'currency_id')))
            );

        $this->addDefaultFooter();
    }

    public function boot(?int $id = null): void
    {
        $this->resource = resolve(InvoiceRepositoryInterface::class)->find($id);

        $context = user();

        policy_authorize(InvoicePolicy::class, 'prepayment', $context, $this->resource);

        $this->isChangePrice = $this->resource->item->isPriceChanged($this->resource);
    }

    protected function getGatewayParams(): array
    {
        return array_merge(parent::getGatewayParams(), [
            'price' => $this->resource->price,
        ]);
    }

    protected function requiredGateway(): bool
    {
        return true;
    }
}
