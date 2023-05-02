<?php

namespace MetaFox\Marketplace\Listeners;

use Illuminate\Support\Str;
use MetaFox\Marketplace\Models\Invoice;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Platform\MetaFoxConstant;

class ListingRouteListener
{
    public function handle(string $url): ?array
    {
        if (!Str::startsWith($url, 'marketplace/')) {
            return null;
        }

        $url = trim(str_replace('marketplace/', MetaFoxConstant::EMPTY_STRING, $url), '/');

        $segments = explode('/', $url);

        $first = array_shift($segments);

        $second = count($segments) ? array_shift($segments) : 0;

        if ($first == 'invoice') {
            return $this->handleInvoice($second);
        }

        if (!is_numeric($first)) {
            return null;
        }

        return $this->handleListing($first);
    }

    protected function handleListing(int $id): ?array
    {
        $listing = Listing::query()
            ->find($id);

        if (null === $listing) {
            return null;
        }

        return [
            'path' => '/' . implode('/', ['marketplace', $id]),
        ];
    }

    protected function handleInvoice(int $id): ?array
    {
        $invoice = Invoice::query()
            ->find($id);

        if (null === $invoice) {
            return null;
        }

        return [
            'path' => '/' . implode('/', ['marketplace', 'marketplace_invoice', $id]),
        ];
    }
}
