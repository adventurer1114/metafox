<?php

namespace MetaFox\Poll\Support\Facade;

use Illuminate\Support\Facades\Facade;
use MetaFox\Poll\Contracts\PollSupportInterface;

class Poll extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PollSupportInterface::class;
    }
}
