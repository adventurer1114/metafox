<?php

namespace MetaFox\Platform\Traits\Http\Resources;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Support\AppSetting\ResourceExtraTrait;

/**
 * Trait HasExtra
 * @package MetaFox\Platform\Traits\Http\Request
 * @property Content|null $resource
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
trait HasExtra
{
    use ResourceExtraTrait;

    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    protected function getExtra(): array
    {
        $context = user();

        $resource = $this->resource;

        if (!$this->resource instanceof Content) {
            return [];
        }

        return $this->getResourceExtra($resource, $context);
    }
}
