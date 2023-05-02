<?php

namespace MetaFox\Platform\Traits\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Trait HasFeed
 * @package MetaFox\Platform\Traits\Eloquent\Model
 */
trait HasFeed
{
    use HasSetActivityTypeIdTrait;

    /**
     * Morph Relation.
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function activity_feed(): ?MorphOne
    {
        if (!app_active('metafox/activity')) {
            return null;
        }

        /** @var string $related */
        $related = Relation::getMorphedModel('feed');

        return $this->morphOne($related, 'item', 'item_type', 'item_id');
    }
}
