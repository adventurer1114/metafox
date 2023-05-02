<?php

namespace MetaFox\Video\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Video\Models\VideoService as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Interface VideoServiceRepositoryInterface.
 *
 * @method Model getModel()
 * @method Model find($id, $columns = ['*'])
 */
interface VideoServiceRepositoryInterface
{
    /**
     * @param  User                 $context
     * @param  array<string, mixed> $params
     * @return Paginator
     */
    public function viewServices(User $context, array $params = []): Paginator;

    /**
     * @param  User                 $context
     * @param  int                  $id
     * @param  array<string, mixed> $params
     * @return Model
     */
    public function updateService(User $context, int $id, array $params = []): Model;

    /**
     * @return array<int, mixed>
     */
    public function getServicesOptions(): array;
}
