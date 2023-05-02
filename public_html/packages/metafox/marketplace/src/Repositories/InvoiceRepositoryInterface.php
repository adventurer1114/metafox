<?php

namespace MetaFox\Marketplace\Repositories;

use Illuminate\Support\Enumerable;
use MetaFox\Marketplace\Models\Invoice;
use Prettus\Repository\Eloquent\BaseRepository;
use MetaFox\Platform\Contracts\User;

/**
 * Interface InvoiceRepositoryInterface.
 * @mixin BaseRepository
 */
interface InvoiceRepositoryInterface
{
    /**
     * @param  User  $context
     * @param  int   $id
     * @param  int   $gatewayId
     * @return array
     */
    public function createInvoice(User $context, int $id, int $gatewayId): array;

    /**
     * @param  int         $id
     * @param  string|null $transactionId
     * @return void
     */
    public function updateSuccessPayment(int $id, ?string $transactionId = null): void;

    /**
     * @param  int         $id
     * @param  string|null $transactionId
     * @return void
     */
    public function updatePendingPayment(int $id, ?string $transactionId = null): void;

    /**
     * @param  User       $context
     * @return Enumerable
     */
    public function viewInvoices(User $context, array $attributes = []): Enumerable;

    /**
     * @param  User         $context
     * @param  int          $id
     * @return Invoice|null
     */
    public function viewInvoice(User $context, int $id): ?Invoice;

    /**
     * @param  User         $context
     * @param  int          $id
     * @return Invoice|null
     */
    public function changeInvoice(User $context, int $id): ?Invoice;

    /**
     * @param  User  $context
     * @param  int   $id
     * @param  int   $gatewayId
     * @return array
     */
    public function repaymentInvoice(User $context, int $id, int $gatewayId): array;

    /**
     * @return array
     */
    public function getTransactionTableFields(): array;
}
