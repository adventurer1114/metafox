<?php

namespace MetaFox\Marketplace\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Marketplace\Contracts\ListingSupportContract;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Payment\Models\Order;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Contracts\User;

class ListingSupport implements ListingSupportContract
{
    public const INVITE_TYPE_USER  = 'user';
    public const INVITE_TYPE_EMAIL = 'email';
    public const INVITE_TYPE_PHONE = 'phone';

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function getPaymentStatus(): array
    {
        return [
            $this->getInitPaymentStatus(),
            $this->getCompletedPaymentStatus(),
            $this->getPendingPaymentStatus(),
            $this->getCanceledPaymentStatus(),
        ];
    }

    public function getCompletedPaymentStatus(): string
    {
        return Order::STATUS_COMPLETED;
    }

    public function getPendingPaymentStatus(): string
    {
        return Order::STATUS_PENDING_PAYMENT;
    }

    public function getInitPaymentStatus(): string
    {
        return Order::STATUS_INIT;
    }

    public function getCanceledPaymentStatus(): string
    {
        return Order::RECURRING_STATUS_CANCELLED;
    }

    public function getMaximumTitleLength(): int
    {
        return Settings::get('marketplace.maximum_title_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);
    }

    public function getMinimumTitleLength(): int
    {
        return Settings::get('marketplace.minimum_title_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
    }

    public function getInviteMethodTypes(): array
    {
        return [self::INVITE_TYPE_EMAIL, self::INVITE_TYPE_PHONE, self::INVITE_TYPE_USER];
    }

    public function getActiveStatus(): string
    {
        return self::STATUS_ACTIVE;
    }

    public function getInactiveStatus(): string
    {
        return self::STATUS_INACTIVE;
    }

    public function getInviteLinkStatus(): array
    {
        return [
            $this->getActiveStatus(),
            $this->getInactiveStatus(),
        ];
    }

    public function getPriceFormat(string $currency, float $price): ?string
    {
        $format = app('currency')->getPriceFormatByCurrencyId($currency, $price);

        if (null === $format) {
            return null;
        }

        return $format;
    }

    public function getUserPriceFormat(User $user, array $prices): ?string
    {
        $price = $this->getPriceByUserCurrency($user, $prices);

        if (null === $price) {
            return null;
        }

        $userCurrency = app('currency')->getUserCurrencyId($user);

        return $this->getPriceFormat($userCurrency, $price);
    }

    public function getUserPrice(User $user, array $prices): ?float
    {
        $price = $this->getPriceByUserCurrency($user, $prices);

        if (null === $price) {
            return null;
        }

        if ($price < 0) {
            return null;
        }

        return $price;
    }

    public function getUserPaymentInformation(User $user, array $prices): ?array
    {
        $userCurrency = app('currency')->getUserCurrencyId($user);

        $price = Arr::get($prices, $userCurrency);

        if (null === $price) {
            return null;
        }

        $price = (float) $price;

        if ($price < 0) {
            return null;
        }

        return [$price, $userCurrency];
    }

    public function getPriceByCurrency(string $currency, array $price): ?float
    {
        $price = Arr::get($price, $currency);

        if (null === $price) {
            return null;
        }

        return (float) $price;
    }

    protected function getPriceByUserCurrency(User $user, array $prices): ?float
    {
        $userCurrency = app('currency')->getUserCurrencyId($user);

        return $this->getPriceByCurrency($userCurrency, $prices);
    }

    public function getInviteUserType(): string
    {
        return self::INVITE_TYPE_USER;
    }

    public function getStatusLabel(string $status): ?string
    {
        return match ($status) {
            $this->getPendingPaymentStatus()   => __p('marketplace::phrase.payment_status.pending_payment'),
            $this->getInitPaymentStatus()      => __p('marketplace::phrase.payment_status.pending_action'),
            $this->getCompletedPaymentStatus() => __p('marketplace::phrase.payment_status.completed'),
            $this->getCanceledPaymentStatus()  => __p('marketplace::phrase.payment_status.canceled'),
            default                            => null
        };
    }

    public function isExpired(?Listing $listing): bool
    {
        if (null === $listing) {
            return false;
        }

        $expiredDays = (int) Settings::get('marketplace.days_to_expire', 30);

        if ($expiredDays <= 0) {
            return false;
        }

        $startExpiredAt = $listing->start_expired_at;

        /*
         * Migration for old data
         */
        if (null === $startExpiredAt) {
            $startExpiredAt = $listing->created_at;
        }

        if (null === $startExpiredAt) {
            return false;
        }

        $expiredAt = Carbon::parse($startExpiredAt);

        $expiredAt->addDays($expiredDays);

        return $expiredAt->lessThanOrEqualTo(Carbon::now());
    }

    public function isFree(User $user, array $prices): bool
    {
        $price = $this->getPriceByUserCurrency($user, $prices);

        if (null === $price) {
            return false;
        }

        return $price == 0;
    }

    public function getExpiredLabel(Listing $listing, bool $isListing = true): ?string
    {
        $daysToExpire = Settings::get('marketplace.days_to_expire', 30);

        if (!$daysToExpire) {
            return null;
        }

        if (Settings::get('marketplace.days_to_notify_before_expire')) {
            return null;
        }

        $listingExpired = Carbon::parse($listing->start_expired_at)->addDays($daysToExpire);

        $now = Carbon::now();

        if ($now->greaterThanOrEqualTo($listingExpired)) {
            return null;
        }

        $remainedDays = $listingExpired->diffInDays($now);

        if ($isListing) {
            return __p('marketplace::phrase.expires_in_total_days', [
                'total' => $remainedDays ?: 1,
            ]);
        }

        if ($remainedDays >= 1) {
            return __p('marketplace::phrase.expires_in_total_days', [
                'total' => $remainedDays,
            ]);
        }

        $remainedHours = $listingExpired->diffInHours($now);

        if ($remainedHours >= 1) {
            return __p('marketplace::phrase.expires_in_total_hours', [
                'total' => $remainedHours,
            ]);
        }

        $remainedMinutes = $listingExpired->diffInMinutes($now);

        if ($remainedMinutes >= 1) {
            return __p('marketplace::phrase.expires_in_total_minutes', [
                'total' => $remainedMinutes,
            ]);
        }

        $remainedSeconds = $listingExpired->diffInSeconds($now);

        if ($remainedSeconds >= 1) {
            return __p('marketplace::phrase.expires_in_total_seconds', [
                'total' => $remainedSeconds,
            ]);
        }

        return null;
    }
}
