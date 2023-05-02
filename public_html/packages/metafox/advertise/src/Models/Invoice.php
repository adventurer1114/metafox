<?php

namespace MetaFox\Advertise\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Payment\Contracts\IsBillable;
use MetaFox\Payment\Traits\BillableTrait;
use MetaFox\Platform\Contracts\HasTitle;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Advertise\Database\Factories\InvoiceFactory;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Advertise\Support\Facades\Support as Facade;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Invoice.
 *
 * @property        int            $id
 * @method   static InvoiceFactory factory(...$parameters)
 */
class Invoice extends Model implements
    Entity,
    IsBillable,
    HasUrl,
    HasTitle
{
    use HasEntity;
    use HasFactory;
    use BillableTrait;
    use HasItemMorph;
    use HasUserMorph;

    public const ENTITY_TYPE = 'advertise_invoice';

    protected $table = 'advertise_invoices';

    /** @var string[] */
    protected $fillable = [
        'item_id',
        'item_type',
        'user_id',
        'user_type',
        'currency_id',
        'price',
        'payment_gateway',
        'payment_status',
        'paid_at',
    ];

    /**
     * @return InvoiceFactory
     */
    protected static function newFactory()
    {
        return InvoiceFactory::new();
    }

    public function getTotalAttribute(): float
    {
        return Arr::get($this->attributes, 'price', 0);
    }

    public function getCurrencyAttribute(): string
    {
        return Arr::get($this->attributes, 'currency_id');
    }

    public function toLink(): ?string
    {
        if (null === $this->item) {
            return url_utility()->makeApiUrl('advertise/invoice/' . $this->entityId());
        }

        return $this->item->toLink();
    }

    public function toUrl(): ?string
    {
        if (null === $this->item) {
            return url_utility()->makeApiFullUrl('advertise/invoice/' . $this->entityId());
        }

        return $this->item->toUrl();
    }

    public function toRouter(): ?string
    {
        if (null === $this->item) {
            return url_utility()->makeApiMobileUrl('advertise/invoice' . $this->entityId());
        }

        return $this->item->toRouter();
    }

    public function getIsCompletedAttribute(): bool
    {
        return Arr::get($this->attributes, 'payment_status') == Support::getCompletedPaymentStatus();
    }

    public function getIsCancelledAttribute(): bool
    {
        return Arr::get($this->attributes, 'payment_status') == Support::getCancelledPaymentStatus();
    }

    public function getUnavailablePaymentAttribute(): bool
    {
        return in_array(Arr::get($this->attributes, 'payment_status'), [Support::getCompletedPaymentStatus(), Support::getCancelledPaymentStatus()]);
    }

    public function toTitle(): string
    {
        if ($this->item instanceof HasTitle) {
            return $this->item->toTitle();
        }

        return MetaFoxConstant::EMPTY_STRING;
    }

    public function completedTransaction(): HasOne
    {
        return $this->hasOne(InvoiceTransaction::class, 'invoice_id')
            ->where([
                'status' => Support::getCompletedPaymentStatus(),
            ]);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        $info = Facade::getInvoiceStatusInfo(Arr::get($this->attributes, 'payment_status'));

        if (null === $info) {
            return MetaFoxConstant::EMPTY_STRING;
        }

        return Arr::get($info, 'label', MetaFoxConstant::EMPTY_STRING);
    }

    public function getPaymentStatusInformationAttribute(): ?array
    {
        return Facade::getInvoiceStatusInfo(Arr::get($this->attributes, 'payment_status'));
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvoiceTransaction::class, 'invoice_id');
    }
}
