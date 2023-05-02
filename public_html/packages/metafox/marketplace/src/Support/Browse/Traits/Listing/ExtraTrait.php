<?php

namespace MetaFox\Marketplace\Support\Browse\Traits\Listing;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;

trait ExtraTrait
{
    use HasExtra {
        getExtra as getMainExtra;
    }

    public function getExtra()
    {
        $policy = PolicyGate::getPolicyFor(Listing::class);

        $extra = $this->getMainExtra();

        if (null === $policy) {
            return $extra;
        }

        $context = user();

        $extra = array_merge($this->getMainExtra(), [
            'can_payment' => $policy->payment($context, $this->resource),
            'can_invite'  => $policy->invite($context, $this->resource),
            'can_message' => $policy->message($context, $this->resource),
            'can_reopen'  => $policy->reopen($context, $this->resource),
        ]);

        return $extra;
    }
}
