<?php

namespace MetaFox\Marketplace\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use MetaFox\Marketplace\Models\Invoice;
use MetaFox\Marketplace\Models\InvoiceTransaction;
use MetaFox\Marketplace\Notifications\OwnerPaymentSuccessNotification;
use MetaFox\Marketplace\Notifications\PaymentPendingNotification;
use MetaFox\Marketplace\Notifications\PaymentSuccessNotification;
use MetaFox\Marketplace\Policies\InvoicePolicy;
use MetaFox\Marketplace\Repositories\InvoiceRepositoryInterface;
use MetaFox\Marketplace\Repositories\InvoiceTransactionRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Marketplace\Support\Facade\Listing;
use MetaFox\Payment\Support\Facades\Payment;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Marketplace\Support\Facade\Listing as ListingFacade;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * Class InvoiceRepository.
 * @ignore
 * @codeCoverageIgnore
 */
class InvoiceRepository extends AbstractRepository implements InvoiceRepositoryInterface
{
    public function model()
    {
        return Invoice::class;
    }

    public function createInvoice(User $context, int $id, int $gatewayId): array
    {
        $listing = resolve(ListingRepositoryInterface::class)->find($id);

        policy_authorize(InvoicePolicy::class, 'payment', $context, $listing, $gatewayId);

        $invoice = new Invoice();

        [$price, $currencyId] = ListingFacade::getUserPaymentInformation($context, $listing->price);

        $invoice->fill([
            'listing_id'      => $listing->entityId(),
            'user_id'         => $context->entityId(),
            'user_type'       => $context->entityType(),
            'price'           => $price,
            'currency_id'     => $currencyId,
            'payment_gateway' => $gatewayId,
            'status'          => ListingFacade::getInitPaymentStatus(),
            'paid_at'         => null,
        ]);

        $invoice->save();

        return $this->paymentWithInvoice($invoice, $listing->userId(), $gatewayId);
    }

    protected function paymentWithInvoice(Invoice $invoice, int $ownerId, int $gatewayId): array
    {
        $order = Payment::initOrder($invoice);

        if (null === $order) {
            return [];
        }

        $url = $invoice->toUrl();

        return Payment::placeOrder($order, $gatewayId, [
            'return_url' => $url,
            'cancel_url' => $url,
            'payee_id'   => $ownerId,
        ]);
    }

    public function updateSuccessPayment(int $id, ?string $transactionId = null): void
    {
        $status = ListingFacade::getCompletedPaymentStatus();

        $invoice = $this->updatePaymentStatus($id, $status);

        if (null === $invoice) {
            return;
        }

        $invoice->fill([
            'paid_at' => $invoice->freshTimestamp(),
        ]);

        $invoice->saveQuietly();

        $transaction = resolve(InvoiceTransactionRepositoryInterface::class)->createTransaction([
            'invoice_id'      => $id,
            'status'          => $status,
            'price'           => $invoice->price,
            'currency_id'     => $invoice->currency_id,
            'transaction_id'  => $transactionId,
            'payment_gateway' => $invoice->payment_gateway,
        ]);

        if (null === $transaction) {
            return;
        }

        if (null === $invoice->user) {
            return;
        }

        if (null !== $invoice->listing) {
            resolve(ListingRepositoryInterface::class)->closeListingAfterPayment($invoice->listing_id);

            if (null !== $invoice->listing->user) {
                $this->toOwnerSuccessNotification($invoice->listing->user, $transaction);
            }
        }

        $this->toSuccessNotification($invoice->user, $transaction);
    }

    public function updatePendingPayment(int $id, ?string $transactionId = null): void
    {
        $status = ListingFacade::getPendingPaymentStatus();

        $invoice = $this->updatePaymentStatus($id, $status);

        if (null === $invoice) {
            return;
        }

        $transaction = resolve(InvoiceTransactionRepositoryInterface::class)->createTransaction([
            'invoice_id'      => $id,
            'status'          => $status,
            'price'           => $invoice->price,
            'currency_id'     => $invoice->currency_id,
            'transaction_id'  => $transactionId,
            'payment_gateway' => $invoice->payment_gateway,
        ]);

        if (null === $transaction) {
            return;
        }

        if (null === $invoice->user) {
            return;
        }

        $this->toPendingNotification($invoice->user, $transaction);
    }

    protected function updatePaymentStatus(int $id, string $status): ?Invoice
    {
        $invoice = $this->find($id);

        if ($invoice->status == $status) {
            return null;
        }

        $invoice->fill([
            'status' => $status,
        ]);

        $invoice->saveQuietly();

        return $invoice;
    }

    protected function toPendingNotification(User $user, InvoiceTransaction $transaction)
    {
        $params = [$user, new PaymentPendingNotification($transaction)];

        Notification::send(...$params);
    }

    protected function toSuccessNotification(User $user, InvoiceTransaction $transaction)
    {
        $params = [$user, new PaymentSuccessNotification($transaction)];

        Notification::send(...$params);
    }

    protected function toOwnerSuccessNotification(User $user, InvoiceTransaction $transaction)
    {
        $params = [$user, new OwnerPaymentSuccessNotification($transaction)];

        Notification::send(...$params);
    }

    public function viewInvoices(User $context, array $attributes = []): Enumerable
    {
        policy_authorize(InvoicePolicy::class, 'viewAny', $context);

        $limit = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        return $this->getModel()->newModelQuery()
            ->select(['marketplace_invoices.*'])
            ->with(['listing'])
            ->where([
                'user_id'   => $context->entityId(),
                'user_type' => $context->entityType(),
            ])
            ->orderByRaw(DB::raw('
                CASE
                    WHEN marketplace_invoices.paid_at IS NOT NULL THEN 1
                    ELSE 2
                END ASC
            '))
            ->orderByDesc('marketplace_invoices.created_at')
            ->limit($limit)
            ->get();
    }

    public function viewInvoice(User $context, int $id): ?Invoice
    {
        $invoice = $this
            ->with(['transactions'])
            ->find($id);

        policy_authorize(InvoicePolicy::class, 'view', $context, $invoice);

        return $invoice;
    }

    public function changeInvoice(User $context, int $id): ?Invoice
    {
        $invoice = $this->find($id);

        policy_authorize(InvoicePolicy::class, 'change', $context, $invoice);

        $listing = $invoice->listing;

        $price = ListingFacade::getPriceByCurrency($invoice->currency, $listing->price);

        if ($price == $invoice->price) {
            return null;
        }

        $attributes = [
            'listing_id'      => $listing->entityId(),
            'user_id'         => $invoice->userId(),
            'user_type'       => $invoice->userType(),
            'price'           => $price,
            'currency_id'     => $invoice->currency,
            'payment_gateway' => $invoice->payment_gateway,
            'status'          => ListingFacade::getInitPaymentStatus(),
        ];

        $newInvoice = new Invoice();

        $newInvoice->fill($attributes);

        $success = $newInvoice->save();

        if (!$success) {
            return null;
        }

        $oldInvoices = $this->getModel()->newModelQuery()
            ->where([
                'listing_id'  => $listing->entityId(),
                'status'      => ListingFacade::getInitPaymentStatus(),
                'currency_id' => $invoice->currency,
                'user_id'     => $invoice->userId(),
                'user_type'   => $invoice->userType(),
            ])
            ->where('price', '<>', $price)
            ->get();

        foreach ($oldInvoices as $oldInvoice) {
            $oldInvoice->update(['status' => ListingFacade::getCanceledPaymentStatus()]);
        }

        return $newInvoice;
    }

    public function repaymentInvoice(User $context, int $id, int $gatewayId): array
    {
        $invoice = $this->find($id);

        if (!policy_check(InvoicePolicy::class, 'repayment', $context, $invoice, true)) {
            $response = [
                'status' => false,
            ];

            if (policy_check(InvoicePolicy::class, 'change', $context, $invoice, false)) {
                Arr::set($response, 'error_message', __p('marketplace::phrase.listing_change_price_error'));
            }

            return $response;
        }

        if (null === $invoice->listing) {
            return [];
        }

        if ($invoice->payment_gateway != $gatewayId) {
            $invoice->fill([
                'payment_gateway' => $gatewayId,
            ]);

            $invoice->save();
        }

        return $this->paymentWithInvoice($invoice, $invoice->listing->userId(), $gatewayId);
    }

    public function getTransactionTableFields(): array
    {
        return [
            [
                'label'  => __p('subscription::phrase.transaction_date'),
                'value'  => 'creation_date',
                'isDate' => true,
            ],
            [
                'label' => __p('subscription::phrase.amount'),
                'value' => 'price',
            ],
            [
                'label' => __p('subscription::phrase.payment_method'),
                'value' => 'payment_method',
            ],
            [
                'label' => __p('subscription::phrase.id'),
                'value' => 'transaction_id',
            ],
            [
                'label' => __p('subscription::phrase.payment_status_label'),
                'value' => 'status',
            ],
        ];
    }
}
