<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Traits;

use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Trait CollectTotalItemStatTrait.
 *
 * @mixin AbstractRepository
 */
trait CollectTotalItemStatTrait
{
    public function getTotalItemByPeriod(?\Carbon\Carbon $after = null, ?\Carbon\Carbon $before = null): int
    {
        $query = $this->getModel()->newModelQuery();

        if ($after) {
            $query->where('created_at', '>=', $after);
        }

        if ($before) {
            $query->where('created_at', '<=', $before);
        }

        return $query->count();
    }
}
