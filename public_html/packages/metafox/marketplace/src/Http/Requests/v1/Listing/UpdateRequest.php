<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Listing;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

class UpdateRequest extends StoreRequest
{
    public function validated($key = null, $default = null)
    {
        return parent::validated($key, $default);
    }
}
