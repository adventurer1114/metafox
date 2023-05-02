<?php

namespace MetaFox\Marketplace\Support\Browse\Traits\Invoice;

use MetaFox\Marketplace\Models\Invoice;
use MetaFox\Platform\Facades\PolicyGate;

trait ExtraTrait
{
    public function getExtra()
    {
        $policy = PolicyGate::getPolicyFor(Invoice::class);

        if (null === $policy) {
            return [];
        }

        $context = user();

        return [
            'can_payment' => $policy->repayment($context, $this->resource),
        ];
    }
}
