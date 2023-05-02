<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage;

use Illuminate\Support\Arr;
use MetaFox\Form\Builder;
use MetaFox\Payment\Http\Resources\v1\Order\GatewayForm;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Support\Facade\SubscriptionPackage;

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
class PaymentSubscriptionPackageForm extends GatewayForm
{
    /**
     * @var string
     */
    protected $customUrl;

    /**
     * @var
     */
    protected $isFree;

    /**
     * @var array|null
     */
    protected ?array $steps = null;

    /**
     * @param $resource
     */
    public function __construct($resource = null)
    {
        parent::__construct($resource);

        if ($resource->is_recurring) {
            $this->customUrl = 'subscription-package/renew-form/' . $resource->entityId();
        }

        $this->isFree = SubscriptionPackage::isFreePackageForUser(user(), $resource);
    }

    protected function prepare(): void
    {
        parent::prepare();

        if (!$this->isFree) {
            $this->action($this->customUrl ?: '/subscription-invoice')
                ->asPost()
                ->setValue([
                    'id' => $this->resource->entityId(),
                ]);

            if (!$this->resource->is_recurring) {
                $this->secondAction('@redirectTo');
            }
        }
    }

    protected function initialize(): void
    {
        switch ($this->isFree) {
            case true:
                $this->addBasic()->addField(
                    Builder::description('free')
                        ->label(__p('subscription::phrase.your_membership_has_successfully_been_upgraded'))
                );

                $this->setFooterFields();
                break;
            default:
                parent::initialize();

                $basic = $this->getSectionByName('basic');

                $basic->addField(
                    Builder::hidden('id')
                );

                break;
        }
    }

    protected function setFooterFields(): void
    {
        $footer = $this->addFooter();

        if (is_array($this->steps)) {
            $footer->setMultiStepDescription($this->steps);
        }

        switch ($this->isFree) {
            case true:
                $footer->addField(
                    Builder::cancelButton()
                        ->label(__p('subscription::phrase.close'))
                );
                break;
            default:
                $submitLabel = __p('subscription::phrase.purchase');

                if ($this->resource->is_recurring) {
                    $submitLabel = __p('subscription::phrase.next');
                }

                $footer->addFields(
                    Builder::submit()
                        ->label($submitLabel),
                    Builder::cancelButton(),
                );

                break;
        }
    }

    public function setSteps(array $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    protected function getGatewayOptions(): array
    {
        $price        = json_decode($this->resource->price, true);
        $userCurrency = app('currency')->getUserCurrencyId(user());

        return $this->serviceManager()->getGatewaysForForm(user(), [
            'entity' => $this->resource?->entityType(),
            'price'  => Arr::get($price, $userCurrency, 0),
        ]);
    }
}
