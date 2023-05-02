<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice;

use Illuminate\Support\Arr;
use MetaFox\Form\Builder;
use MetaFox\Payment\Http\Resources\v1\Order\GatewayForm;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Repositories\SubscriptionInvoiceRepositoryInterface;
use MetaFox\Subscription\Support\Helper;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class PaymentSubscriptionPackageForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class PaymentSubscriptionInvoiceForm extends GatewayForm
{
    /**
     * @var string
     */
    protected $customUrl;

    /**
     * @var string
     */
    protected $actionType;

    /**
     * @var bool
     */
    protected $isMultiStep;

    /**
     * @var array|null
     */
    protected ?array $steps = null;

    /**
     * @param $resource
     */
    public function __construct($resource = null, string $actionType = null)
    {
        parent::__construct($resource);

        $this->actionType = $actionType;

        $this->isMultiStep = $resource->is_recurring && $actionType == Helper::UPGRADE_FORM_ACTION;

        if ($this->isMultiStep) {
            $this->customUrl = 'subscription-invoice/renew-method-form/' . $resource->entityId();
        }
    }

    protected function prepare(): void
    {
        parent::prepare();

        $values = [
            'action_type' => $this->actionType,
        ];

        if ($this->isMultiStep) {
            Arr::set($values, 'id', $this->resource->entityId());
        }

        switch ($this->isMultiStep) {
            case true:
                $this->asPost();
                break;
            default:
                $this->asPatch()
                    ->secondAction('@redirectTo');
                break;
        }

        $this->action($this->customUrl ?: '/subscription-invoice/upgrade/' . $this->resource->entityId())
            ->setValue($values);
    }

    protected function initialize(): void
    {
        parent::initialize();

        $basic = $this->getSectionByName('basic');

        $basic->addField(
            Builder::hidden('id')
        );

        if (null !== $this->actionType) {
            $basic->addField(
                Builder::hidden('action_type')
            );
        }
    }

    protected function setFooterFields(): void
    {
        $submitLabel = __p('core::phrase.submit');

        if ($this->isMultiStep) {
            $submitLabel = __p('subscription::phrase.next');
        }

        $footer = $this->addFooter();

        if (is_array($this->steps)) {
            $footer->setMultiStepDescription($this->steps);
        }

        $footer->addFields(
            Builder::cancelButton(),
            Builder::submit()
                    ->label($submitLabel)
        );
    }

    public function setSteps(array $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    protected function getGatewayOptions(): array
    {
        $price = $this->resource->initial_price;

        if (resolve(SubscriptionInvoiceRepositoryInterface::class)->hasCompletedTransactions($this->resource->entityId())) {
            $price = $this->resource->recurring_price;
        }

        return $this->serviceManager()->getGatewaysForForm(user(), [
            'entity' => $this->resource->entityType(),
            'price'  => $price,
        ]);
    }
}
