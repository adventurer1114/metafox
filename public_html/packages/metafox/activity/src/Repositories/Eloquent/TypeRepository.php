<?php

namespace MetaFox\Activity\Repositories\Eloquent;

use Illuminate\Support\Arr;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\Type;
use MetaFox\Activity\Policies\TypePolicy;
use MetaFox\Activity\Repositories\TypeRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

class TypeRepository extends AbstractRepository implements TypeRepositoryInterface
{
    public function model()
    {
        return Type::class;
    }

    public function updateType(User $context, int $id, array $attributes): Type
    {
        /** @var Type $resource */
        $resource = $this->find($id);

        policy_authorize(TypePolicy::class, 'update', $context, $resource);

        $fields = $this->getModel()->getFillable();

        $data = Arr::only($attributes, $fields);

        $data['value_actual'] = Arr::except($attributes, $fields);

        $resource->fill($data);

        $resource->save();

        $activityTypeManager = resolve(TypeManager::class);

        $activityTypeManager->refresh();

        $activityTypeManager->cleanData();

        return $resource;
    }

    public function deleteType(User $context, int $id): int
    {
        $resource = $this->find($id);

        policy_authorize(TypePolicy::class, 'delete', $context, $resource);

        $response = $this->delete($id);

        $activityTypeManager = resolve(TypeManager::class);

        $activityTypeManager->refresh();

        $activityTypeManager->cleanData();

        return $response;
    }

    public function getTypeByType(string $type): ?Type
    {
        return $this->getModel()->newQuery()->where('type', $type)->first();
    }
}
