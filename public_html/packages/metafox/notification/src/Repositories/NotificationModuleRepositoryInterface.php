<?php

namespace MetaFox\Notification\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface NotificationModule.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface NotificationModuleRepositoryInterface
{
    /**
     * @param  string     $channel
     * @return Collection
     */
    public function getModulesByChannel(string $channel = 'mail'): Collection;
}
