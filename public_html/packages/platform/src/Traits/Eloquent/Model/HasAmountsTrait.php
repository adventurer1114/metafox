<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Traits\Eloquent\Model;

use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Contracts\HasTotalItem;
use MetaFox\Platform\Contracts\HasTotalView;

/**
 * Trait HasIncrementAmountColumns.
 * @mixin HasAmounts
 */
trait HasAmountsTrait
{
    /**
     * @param string $column
     * @param int    $amount
     *
     * @return int
     */
    public function incrementAmount(string $column, int $amount = 1): int
    {
        $hasTimestamps = $this->timestamps == true;

        if ($hasTimestamps) {
            $this->timestamps = false;
        }

        $result = $this->incrementQuietly($column, $amount, []);

        if ($hasTimestamps) {
            $this->timestamps = true;
        }

        app('events')->dispatch("core.{$column}_updated", [$this, 'increment']);

        return $result;
    }

    /**
     * @param string $column
     * @param int    $amount
     *
     * @return int
     */
    public function decrementAmount(string $column, int $amount = 1): int
    {
        $result = $this->decrementQuietly($column, $amount, []);

        app('events')->dispatch("core.{$column}_updated", [$this, 'decrement']);

        return $result;
    }

    public function incrementTotalView(): void
    {
        if ($this instanceof HasTotalView) {
            $this->incrementAmount('total_view');
        }
    }

    public function incrementTotalItem(): void
    {
        if ($this instanceof HasTotalItem) {
            $this->incrementAmount('total_item');
        }
    }

    public function decrementTotalItem(): void
    {
        if ($this instanceof HasTotalItem) {
            $this->decrementAmount('total_item');
        }
    }
}
