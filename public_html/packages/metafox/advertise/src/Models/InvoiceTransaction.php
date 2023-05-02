<?php

namespace MetaFox\Advertise\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Advertise\Database\Factories\InvoiceTransactionFactory;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class InvoiceTransaction.
 *
 * @property        int                       $id
 * @method   static InvoiceTransactionFactory factory(...$parameters)
 */
class InvoiceTransaction extends Model implements Entity
{
    use HasEntity;
    use HasFactory;

    public const ENTITY_TYPE = 'advertise_invoice_transaction';

    protected $table = 'advertise_invoice_transactions';

    /** @var string[] */
    protected $fillable = [
        'invoice_id',
        'status',
        'price',
        'currency_id',
        'transaction_id',
    ];

    /**
     * @return InvoiceTransactionFactory
     */
    protected static function newFactory()
    {
        return InvoiceTransactionFactory::new();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
