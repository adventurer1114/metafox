<?php

namespace MetaFox\Subscription\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Subscription\Models\SubscriptionInvoice;

class SubscriptionRouteListener
{
    public function handle(string $url): ?array
    {
        if (!Str::startsWith($url, 'subscription/')) {
            return null;
        }

        $segments = explode('/', $url);

        $id = Arr::last($segments);

        if (!is_numeric($id)) {
            return null;
        }

        $invoice = SubscriptionInvoice::query()
            ->find($id);

        if (null === $invoice) {
            return null;
        }

        return [
            'path' => '/' . implode('/', ['subscription-invoice', $id]),
        ];
    }
}
