<?php

namespace MetaFox\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Localize\Contracts\TimezoneSupportContract;
use MetaFox\Localize\Support\Timezone as TimezoneSupport;

/**
 * class Timezone.
 * @method static array getActiveOptions()
 * @method static string|null getName(?int $id)
 * @method static string|null getTimezoneByName(?string $name)
 * @method static int getDefaultTimezoneId()
 * @see TimezoneSupport
 */
class Timezone extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TimezoneSupportContract::class;
    }
}
