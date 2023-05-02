<?php

namespace MetaFox\ActivityPoint\Contracts\Support;

use MetaFox\ActivityPoint\Models\PointStatistic;
use MetaFox\ActivityPoint\Models\PointTransaction as Transaction;
use MetaFox\Payment\Models\Order;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;

interface ActivityPoint
{
    /**
     * @param  User $context
     * @return int
     */
    public function getTotalActivityPoints(User $context): int;

    /**
     * @param  User $context
     * @param  int  $amount
     * @return bool
     */
    public function updateActivityPoints(User $context, int $amount): bool;

    /**
     * @param  User                 $context
     * @param  User                 $owner
     * @param  int                  $amount
     * @param  int|null             $type
     * @param  array<string, mixed> $extra
     * @return int
     */
    public function addPoints(User $context, User $owner, int $amount, ?int $type, array $extra = []): int;

    /**
     * @param  User           $context
     * @param  int            $type
     * @param  int            $amount
     * @return PointStatistic
     */
    public function updateStatistic(User $context, int $type, int $amount): PointStatistic;

    /**
     * @param  Entity $content
     * @param  User   $user
     * @param  string $action
     * @param  int    $type
     * @return int
     */
    public function updateUserPoints(User $user, Entity $content, string $action, int $type): int;

    /**
     * @param  User                 $user
     * @param  User                 $owner
     * @param  array<string, mixed> $data
     * @return Transaction
     */
    public function createTransaction(User $user, User $owner, array $data): Transaction;

    /**
     * @param  User     $context
     * @param  User     $user
     * @param  int      $type
     * @param  int      $amount
     * @return int|null
     */
    public function adjustPoints(User $context, User $user, int $type, int $amount): ?int;

    /**
     * @param  Order $order
     * @return bool
     */
    public function proceedPayment(Order $order): bool;

    /**
     * @param  string $currency
     * @param  float  $amount
     * @return int
     */
    public function convertPointFromPrice(string $currency, float $amount): int;

    /**
     * @param  string $currency
     * @return float
     */
    public function getConversionRate(string $currency): float;

    /**
     * @param  array<string, mixed> $default
     * @return void
     */
    public function installCustomPointSettings(array $default = []): void;

    /**
     * @param  string $packageId
     * @return bool
     */
    public function isCustomInstalled(string $packageId): bool;

    /**
     * @param  User $context
     * @param  User $owner
     * @param  int  $points
     * @return bool
     */
    public function giftPoints(User $context, User $owner, int $points): bool;

    /**
     * @param  array $userIds
     * @return int
     */
    public function getMinPointByIds(array $userIds): int;

    /**
     * @param  int  $type
     * @return bool
     */
    public function isSubtracted(int $type): bool;

    /**
     * @param  int  $type
     * @return bool
     */
    public function isAdded(int $type): bool;
}
