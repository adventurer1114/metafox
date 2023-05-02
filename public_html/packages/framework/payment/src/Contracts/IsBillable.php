<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Payment\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use MetaFox\Platform\Contracts\Entity;

/**
 * Interface IsBillable.
 * @mixin Model
 */
interface IsBillable extends Entity
{
    /**
     * toOrder.
     *
     * @return array<string,mixed>
     */
    public function toOrder(): array;

    /**
     * order.
     *
     * @return MorphOne
     */
    public function order(): MorphOne;
}
