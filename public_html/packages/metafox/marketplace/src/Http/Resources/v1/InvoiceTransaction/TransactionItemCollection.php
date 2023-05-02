<?php

namespace MetaFox\Marketplace\Http\Resources\v1\InvoiceTransaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionItemCollection extends ResourceCollection
{
    public $collects = TransactionItem::class;
}
