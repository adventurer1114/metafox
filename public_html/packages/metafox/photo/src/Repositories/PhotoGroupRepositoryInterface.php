<?php

namespace MetaFox\Photo\Repositories;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Photo\Models\PhotoGroup as Model;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface PhotoRepositoryInterface.
 * @mixin BaseRepository
 * @method Model getModel()
 * @method Model find($id, $columns = ['*'])
 */
interface PhotoGroupRepositoryInterface
{
    /**
     * @param  User                   $context
     * @param  int                    $id
     * @return Model
     * @throws AuthorizationException
     * @throws Exception
     */
    public function viewPhotoGroup(User $context, int $id): Model;

    /**
     * @param  array       $files
     * @param  string|null $content
     * @return array
     */
    public function forceContentForGlobalSearch(array $files, ?string $content): array;

    /**
     * @param  Model       $group
     * @param  string|null $text
     * @param  int         $total
     * @param  array|null  $oldFiles
     * @return void
     */
    public function updateGlobalSearchForSingleMedia(Model $group, ?string $text, int $total, ?array $oldFiles = null): void;

    /**
     * @param  array $attributes
     * @return array
     */
    public function handleContent(array $attributes): array;

    /**
     * @param  int  $id
     * @return bool
     */
    public function updateApprovedStatus(int $id): bool;

    /**
     * @param  int  $id
     * @return bool
     */
    public function cleanUpGroup(int $id): bool;

    /**
     * @param  User $user
     * @return void
     */
    public function deleteUserPhotoGroups(User $user): void;
}
