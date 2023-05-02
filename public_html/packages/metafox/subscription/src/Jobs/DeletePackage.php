<?php

namespace MetaFox\Subscription\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use MetaFox\Payment\Support\Facades\Payment;
use MetaFox\Subscription\Models\SubscriptionInvoice;
use MetaFox\Subscription\Notifications\DeletePackage as ActionNotification;

class DeletePackage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var SubscriptionInvoice
     */
    protected $invoice;

    public function __construct(SubscriptionInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function handle()
    {
        if (null !== $this->invoice && $this->invoice->isCompleted() && null !== $this->invoice->package) {
            $params = [$this->invoice->user, new ActionNotification($this->invoice->package)];

            Notification::send(...$params);

            $order = $this->invoice->order;

            if (null !== $order) {
                Payment::cancelSubscription($order);
            }
        }
    }
}
