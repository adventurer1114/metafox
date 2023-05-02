<?php

namespace MetaFox\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use MetaFox\Payment\Contracts\IsBillable;
use MetaFox\Payment\Database\Factories\OrderFactory;
use MetaFox\Payment\Support\Payment;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Traits\Eloquent\Model\HasEntity;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * stub: /packages/models/model.stub.
 */

/**
 * Class Order.
 *
 * @property int    $id
 * @property int    $gateway_id
 * @property int    $user_id
 * @property string $user_type
 * @property int    $item_id
 * @property string $item_type
 * @property string $title
 * @property float  $total
 * @property string $currency
 * @property string $payment_type
 * @property string $status
 * @property string $recurring_status
 * @property string $gateway_order_id
 * @property string $gateway_subscription_id
 * @property string $created_at
 * @property string $updated_at
 * @method   static OrderFactory factory(...$parameters)
 */
class Order extends Model implements Entity
{
    use HasEntity;
    use HasFactory;
    use HasItemMorph;
    use HasUserMorph;

    public const ENTITY_TYPE = 'payment_order';

    public const STATUS_ALL              = 'all';
    public const STATUS_INIT             = 'init';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_PENDING_PAYMENT  = 'pending_payment';
    public const STATUS_COMPLETED        = 'completed';
    public const STATUS_FAILED           = 'failed';

    public const RECURRING_STATUS_UNSET     = 'unset';
    public const RECURRING_STATUS_PENDING   = 'pending';
    public const RECURRING_STATUS_ACTIVE    = 'active';
    public const RECURRING_STATUS_FAILED    = 'failed';
    public const RECURRING_STATUS_ENDED     = 'ended';
    public const RECURRING_STATUS_CANCELLED = 'cancelled';

    public const ALLOW_STATUS = [
        'core::phrase.all'                        => self::STATUS_ALL,
        'payment::phrase.status_init'             => self::STATUS_INIT,
        'payment::phrase.status_pending_approval' => self::STATUS_PENDING_APPROVAL,
        'payment::phrase.status_pending_payment'  => self::STATUS_PENDING_PAYMENT,
        'payment::phrase.status_completed'        => self::STATUS_COMPLETED,
        'payment::phrase.status_failed'           => self::STATUS_FAILED,
    ];

    /**
     * contains rules to validate status updating from one to another.
     *
     * @var array<string, mixed>
     */
    protected $statusRules = [
        self::STATUS_PENDING_APPROVAL => [
            self::STATUS_INIT,
        ],
        self::STATUS_PENDING_PAYMENT => [
            self::STATUS_INIT,
            self::STATUS_PENDING_APPROVAL,
        ],
        self::STATUS_COMPLETED => [
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_PENDING_PAYMENT,
        ],
        self::STATUS_FAILED => [
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_PENDING_PAYMENT,
        ],
    ];

    protected $table = 'payment_orders';

    /** @var string[] */
    protected $fillable = [
        'gateway_id',
        'user_id',
        'user_type',
        'item_id',
        'item_type',
        'title',
        'total',
        'currency',
        'payment_type',
        'status',
        'recurring_status',
        'gateway_order_id',
        'gateway_subscription_id',
    ];

    /**
     * @return OrderFactory
     */
    protected static function newFactory()
    {
        return OrderFactory::new();
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'order_id');
    }

    /**
     * toGatewayOrder.
     *
     * @return ?array<string, mixed>
     */
    public function toGatewayOrder(): ?array
    {
        $item = $this->item;
        $user = $this->user;
        if (!$item instanceof IsBillable || !$user) {
            return null;
        }

        return array_merge($item->toOrder(), [
            'user_title' => $user->toTitle(),
            'email'      => $user->email,
        ]);
    }

    public function isRecurringOrder(): bool
    {
        return $this->payment_type == Payment::PAYMENT_RECURRING;
    }

    public function isStatusInitialized(): bool
    {
        return $this->status == self::STATUS_INIT;
    }

    public function isStatusPendingApproval(): bool
    {
        return $this->status == self::STATUS_PENDING_APPROVAL;
    }

    public function isStatusPendingPayment(): bool
    {
        return $this->status == self::STATUS_PENDING_PAYMENT;
    }

    public function isStatusFailed(): bool
    {
        return $this->status == self::STATUS_FAILED;
    }

    public function isStatusCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    public function isRecurringStatusPending(): bool
    {
        return $this->recurring_status == self::RECURRING_STATUS_PENDING;
    }

    public function isRecurringStatusActive(): bool
    {
        return $this->recurring_status == self::RECURRING_STATUS_ACTIVE;
    }

    public function isRecurringStatusFailed(): bool
    {
        return $this->recurring_status == self::RECURRING_STATUS_FAILED;
    }

    public function isRecurringStatusEnded(): bool
    {
        return $this->recurring_status == self::RECURRING_STATUS_ENDED;
    }

    public function isRecurringStatusCancelled(): bool
    {
        return $this->recurring_status == self::RECURRING_STATUS_CANCELLED;
    }

    /**
     * Validate if we can update the current status to the target status.
     *
     * @param  string $targetStatus
     * @return bool
     */
    public function canUpdateToStatus(string $targetStatus): bool
    {
        $statuses = Arr::get($this->statusRules, $targetStatus, []);

        return in_array($this->status, $statuses);
    }
}

// end
