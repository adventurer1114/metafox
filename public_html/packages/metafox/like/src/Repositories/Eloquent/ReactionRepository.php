<?php

namespace MetaFox\Like\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use MetaFox\Like\Models\Reaction;
use MetaFox\Like\Policies\ReactionPolicy;
use MetaFox\Like\Repositories\ReactionRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class ReactionRepository.
 * @method Reaction getModel()
 * @method Reaction find($id, $columns = ['*'])
 * @ignore
 * @codeCoverageIgnore
 */
class ReactionRepository extends AbstractRepository implements ReactionRepositoryInterface
{
    public function model(): string
    {
        return Reaction::class;
    }

    public function viewReactionsForAdmin(User $context, array $attributes): Paginator
    {
        policy_authorize(ReactionPolicy::class, 'viewAny', $context);

        $limit = $attributes['limit'];

        return $this->getModel()->newQuery()
            ->orderBy('ordering')
            ->simplePaginate($limit);
    }

    public function viewReactionsForFE(User $context): Collection
    {
        policy_authorize(ReactionPolicy::class, 'viewAny', $context);

        return $this->getModel()->newQuery()
            ->where('is_active', Reaction::IS_ACTIVE)
            ->orderBy('ordering')
            ->orderBy('id')
            ->get();
    }

    public function createReaction(User $context, array $attributes): Reaction
    {
        policy_authorize(ReactionPolicy::class, 'create', $context);

        $storageFile = upload()
            ->setPath('like')
            ->storeFile($attributes['icon']);

        $attributes = array_merge($attributes, [
            'server_id'     => $storageFile->storage_id,
            'icon_path'     => $storageFile->path,
            'image_file_id' => $storageFile->id,
        ]);

        /** @var Reaction $reaction */
        $reaction = $this->create($attributes);
        $reaction->refresh();

        return $reaction;
    }

    public function updateReaction(User $context, int $id, array $attributes): Reaction
    {
        policy_authorize(ReactionPolicy::class, 'update', $context);

        $reaction = $this->find($id);

        //Todo: upload svg
        $attributes = array_merge($attributes, [
            'server_id' => 'public',
            'icon_path' => 'path.svg',
        ]);

        $reaction->update($attributes);
        $reaction->refresh();

        return $reaction;
    }

    public function viewReaction(User $context, int $id): Reaction
    {
        policy_authorize(ReactionPolicy::class, 'view', $context);

        return $this->find($id);
    }

    /**
     * @inheritDoc
     */
    public function getReactionsForConfig(): Collection
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('is_active', '=', 1)
            ->get();
    }
}
