<?php

namespace MetaFox\Notification\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Notification\Models\Type;
use MetaFox\Notification\Models\TypeChannel;
use MetaFox\Notification\Repositories\TypeChannelRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class TypeChannelRepository.
 ** @method TypeChannel getModel()
 * @method TypeChannel find($id, $columns = ['*'])()
 */
class TypeChannelRepository extends AbstractRepository implements TypeChannelRepositoryInterface
{
    public function model()
    {
        return TypeChannel::class;
    }

    /**
     * @param  string     $channel
     * @return Collection
     */
    public function getTypesByChannel(string $channel = 'mail'): Collection
    {
        return $this->getModel()->newQuery()
            ->where('is_active', Type::IS_ACTIVE)
            ->with(['type'])
            ->where('channel', $channel)
            ->orderBy('ordering')
            ->get();
    }
}
