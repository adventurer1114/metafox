<?php

namespace MetaFox\Group\Observers;

use MetaFox\Group\Models\Request;

/**
 * Class RequestObserver.
 * @ignore
 */
class RequestObserver
{
    public function deleted(Request $request)
    {
        $request->answers()->delete();
    }
}
