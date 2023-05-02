<?php

namespace MetaFox\Platform\Contracts;

/**
 * Interface HasBackGroundStatus.
 * @package MetaFox\Platform\Contracts
 * @property int $status_background_id
 */
interface HasBackGroundStatus
{
    /**
     * @return array<string, mixed>|null
     */
    public function getBackgroundStatusImage(): ?array;
}
