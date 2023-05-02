<?php

namespace MetaFox\Platform\Support\Eloquent\Appends;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use MetaFox\Platform\MetaFoxPrivacy;

/**
 * Trait AppendPrivacyListTrait.
 * @mixin Model
 * @mixin Builder
 * @property int $privacy
 */
trait AppendPrivacyListTrait
{
    /**
     * @var int[]
     */
    public array $privacy_list;

    /**
     * @param array $privacyList
     */
    public function setPrivacyListAttribute($privacyList = [])
    {
        if ($this->privacy == MetaFoxPrivacy::CUSTOM) {
            $this->privacy_list = $privacyList;
            $this->updated_at = Carbon::now(); //trigger event updated
        }
    }

    /**
     * @return array|null
     */
    public function getPrivacyListAttribute(): ?array
    {
        return $this->privacy == MetaFoxPrivacy::CUSTOM ? $this->privacy_list : null;
    }
}
