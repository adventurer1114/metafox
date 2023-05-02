<?php

namespace MetaFox\Subscription\Support\Browse\Traits\SubscriptionInvoice\Admin;

use MetaFox\Subscription\Support\Helper;

trait ExtraTrait
{
    public function getExtra(): array
    {
        return [
            'can_activate'    => in_array($this->resource->payment_status, [Helper::getPendingPaymentStatus(), Helper::getCanceledPaymentStatus()]),
            'can_cancel'      => $this->resource->payment_status == Helper::getCompletedPaymentStatus(),
            'can_view_reason' => $this->resource->payment_status == Helper::getCanceledPaymentStatus(),
        ];
    }
}
