<?php

namespace MetaFox\User\Support;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\User\Models\UserEntity as Model;

class UserEntity
{
    /**
     * @param int $entityId
     *
     * @return Model
     */
    public function getById(int $entityId): Model
    {
        /** @var Model $entity */
        $entity = Model::query()->findOrFail($entityId);

        return $entity;
    }

    /**
     * @param int                  $entityId
     * @param array<string, mixed> $params
     *
     * @return int
     */
    public function createEntity(int $entityId, array $params): int
    {
        $params['id'] = $entityId;
        /** @var Model $entity */
        $entity = Model::query()->create($params);

        return $entity->entityId();
    }

    /**
     * @param int                  $entityId
     * @param array<string, mixed> $params
     *
     * @return Model
     */
    public function updateEntity(int $entityId, array $params): Model
    {
        $entity = $this->getById($entityId);
        $entity->fill($params);
        $entity->save();

        return $entity;
    }

    /**
     * @param int $entityId
     *
     * @return bool
     */
    public function deleteEntity(int $entityId): bool
    {
        $entity = $this->getById($entityId);

        return (bool) $entity->delete();
    }

    /**
     * @param int[] $ids
     *
     * @return Collection
     */
    public function getByIds(array $ids): Collection
    {
        return Model::query()->whereIn('id', $ids)->get();
    }

    public function forceDeleteEntity(int $id): int
    {
        $entity = Model::onlyTrashed()->where('id', '=', $id)->first();

        if (!$entity instanceof Model) {
            return $id;
        }

        $entity->forceDelete();

        return $entity->entityId();
    }
}
