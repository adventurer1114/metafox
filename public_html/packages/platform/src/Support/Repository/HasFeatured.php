<?php

namespace MetaFox\Platform\Support\Repository;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasPolicy;
use MetaFox\Platform\Contracts\User;

/**
 * Trait HasFeatured.
 */
trait HasFeatured
{
    /**
     * @param User $context
     * @param int  $id
     * @param int  $feature
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function feature(User $context, int $id, int $feature): bool
    {
        $model = $this->find($id);

        if ($model instanceof HasPolicy) {
            gate_authorize($context, 'feature', $model, $model, $feature);
        }

        return $model->update(['is_featured' => $feature]);
    }

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isFeature(Content $model): bool
    {
        if (!$model instanceof HasFeature) {
            return false;
        }

        return $model->is_featured == 1;
    }
}
