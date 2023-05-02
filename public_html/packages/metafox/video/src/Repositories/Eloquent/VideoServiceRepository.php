<?php

namespace MetaFox\Video\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Video\Models\VideoService as Model;
use MetaFox\Video\Repositories\VideoServiceRepositoryInterface;

/**
 * @method Model getModel()
 * @method Model find($id, $columns = ['*'])
 */
class VideoServiceRepository extends AbstractRepository implements VideoServiceRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }

    /**
     * @inheritDoc
     */
    public function viewServices(User $context, array $params = []): Paginator
    {
        $limit = $params['limit'];

        return $this->getModel()->newModelQuery()->paginate($limit);
    }

    /**
     * @inheritDoc
     */
    public function updateService(User $context, int $id, array $params = []): Model
    {
        $service = $this->find($id);

        $service->fill($params);
        $service->save();

        return $service;
    }

    /**
     * @inheritDoc
     */
    public function getServicesOptions(): array
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('is_active', '=', 1)
            ->get()
            ->collect()
            ->map(function (Model $service) {
                return [
                    'label' => $service->name,
                    'value' => $service->driver,
                ];
            })
            ->values()
            ->toArray();
    }
}
