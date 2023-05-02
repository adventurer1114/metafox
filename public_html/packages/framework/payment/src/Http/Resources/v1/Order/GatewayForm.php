<?php

namespace MetaFox\Payment\Http\Resources\v1\Order;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Payment\Contracts\GatewayManagerInterface;
use MetaFox\Payment\Models\Order as Model;
use MetaFox\Payment\Repositories\OrderRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class GatewayForm.
 * @property ?Model $resource
 */
class GatewayForm extends AbstractForm
{
    protected function serviceManager(): GatewayManagerInterface
    {
        return resolve(GatewayManagerInterface::class);
    }

    public function boot(?int $id = null): void
    {
        if ($id) {
            $this->resource = resolve(OrderRepositoryInterface::class)->find($id);
        }
    }

    protected function prepare(): void
    {
        $this->title(__p('payment::phrase.select_payment_gateway'))
            ->action('payment-gateway/order' . $this->resource?->id)
            ->asPut();
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $field = Builder::radioGroup('payment_gateway')
            ->label(__p('payment::phrase.select_a_payment_gateway'))
            ->options($this->getGatewayOptions())
            ->color('text.secondary');

        if ($this->requiredGateway()) {
            $field->required()
                ->yup(
                    Yup::number()
                        ->required(__p('core::validation.this_field_is_a_required_field')),
                );
        }

        $basic->addField($field);

        $this->addMoreBasicFields($basic);

        $this->setFooterFields();
    }

    protected function addMoreBasicFields(Section $basic): void
    {
        /*
         * Extendable classes can implement here
         */
    }

    protected function setFooterFields(): void
    {
        $this->addDefaultFooter();
    }

    /**
     * @return array<int, mixed>
     * @throws AuthenticationException
     */
    protected function getGatewayOptions(): array
    {
        return $this->serviceManager()->getGatewaysForForm(user(), $this->getGatewayParams());
    }

    protected function getGatewayParams(): array
    {
        return [
            'entity_type' => $this->resource?->entityType(),
            'entity_id'   => $this->resource?->entityId(),
        ];
    }

    protected function requiredGateway(): bool
    {
        return false;
    }
}
