<?php

namespace MetaFox\Activity\Traits;

use MetaFox\Platform\Facades\Settings;

trait HasCheckinTrait
{
    public function isEnableCheckin(): bool
    {
        return Settings::get('activity.feed.enable_check_in', false) === true;
    }

    /**
     * @param array<string, mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function applyLocationRules(array $rules): array
    {
        if ($this->isEnableCheckin()) {
            $rules['location'] = ['sometimes', 'array'];
            $rules['location.address'] = ['string'];
            $rules['location.lat'] = ['numeric'];
            $rules['location.lng'] = ['numeric'];
        }

        return $rules;
    }
}
