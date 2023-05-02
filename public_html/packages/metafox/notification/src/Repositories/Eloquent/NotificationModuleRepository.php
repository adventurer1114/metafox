<?php

namespace MetaFox\Notification\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Notification\Models\NotificationModule;
use MetaFox\Notification\Models\Type;
use MetaFox\Notification\Repositories\NotificationModuleRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\PackageScope;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class NotificationModuleRepository.
 */
class NotificationModuleRepository extends AbstractRepository implements NotificationModuleRepositoryInterface
{
    public function model()
    {
        return NotificationModule::class;
    }

    /**
     * @inheritDoc
     */
    public function getModulesByChannel(string $channel = 'mail'): Collection
    {
        return $this->getModel()->newQuery()
            ->where('is_active', Type::IS_ACTIVE)
            ->addScope(resolve(PackageScope::class, [
                'table' => $this->getModel()->getTable(),
            ]))
            ->where('channel', $channel)
            ->get();
    }
}
