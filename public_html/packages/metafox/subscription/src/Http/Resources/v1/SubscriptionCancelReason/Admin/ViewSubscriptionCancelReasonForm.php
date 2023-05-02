<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionCancelReason\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Subscription\Models\SubscriptionCancelReason as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class ViewSubscriptionCancelReasonForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class ViewSubscriptionCancelReasonForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('subscription::admin.cancel_reason_of_subscription'));
    }

    protected function initialize(): void
    {
        $userReason = $this->resource->userCanceledReason;

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::description('id')
                    ->label(__p('subscription::admin.order_id'))
                    ->description($this->resource->entityId()),
            Builder::description('canceled_on')
                    ->label(__p('subscription::admin.cancelled_on'))
                    ->description($userReason->created_at)
        );

        if (null !== $userReason && null !== $userReason->reason) {
            $basic->addField(
                Builder::description('title')
                    ->label(__p('subscription::admin.reason'))
                    ->description($userReason->reason->title)
            );
        }
    }
}
