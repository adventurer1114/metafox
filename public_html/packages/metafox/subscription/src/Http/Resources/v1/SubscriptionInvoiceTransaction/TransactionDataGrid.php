<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoiceTransaction;

use MetaFox\Platform\Resource\GridConfig as Grid;

class TransactionDataGrid extends Grid
{
    protected string $appName = 'subscription';

    protected function initialize(): void
    {
        $this->setDataSource('/admincp/subscription/invoice/:id/transaction', [], []);

        $this->addColumn('created_at')
            ->header(__p('subscription::phrase.transaction_date'))
            ->asDateTime();

        $this->addColumn('amount')
            ->header(__p('subscription::phrase.amount'))
            ->width(120);

        $this->addColumn('payment_method')
            ->header(__p('subscription::phrase.payment_method'))
            ->width(150);

        $this->addColumn('transaction_id')
            ->header(__p('subscription::phrase.id'))
            ->flex();

        $this->addColumn('payment_status')
            ->header(__p('subscription::admin.payment_status'))
            ->width(120);
    }
}
