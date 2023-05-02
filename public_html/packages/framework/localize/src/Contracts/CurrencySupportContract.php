<?php

namespace MetaFox\Localize\Contracts;

use MetaFox\Localize\Models\Currency as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Interface CurrencySupportContract.
 */
interface CurrencySupportContract
{
    /**
     * @return array<string, Model>
     */
    public function getAllActiveCurrencies(): array;

    /**
     * @return array<string, Model>
     */
    public function getCurrencies(): array;

    /**
     * @param string|null $code
     *
     * @return ?string
     */
    public function getName(?string $code);

    /**
     * @param  User   $context
     * @return string
     */
    public function getUserCurrencyId(User $context): string;

    /**
     * @return string
     */
    public function getDefaultCurrencyId(): string;

    /**
     * @param  string      $currencyId
     * @param  float       $price
     * @param  string|null $precision
     * @return string|null
     */
    public function getPriceFormatByCurrencyId(string $currencyId, float $price, ?string $precision = null): ?string;

    /**
     * @param  string               $name
     * @param  array<string, mixed> $rules
     * @return array<string, mixed>
     */
    public function rules(string $name, array $rules = []): array;
}
