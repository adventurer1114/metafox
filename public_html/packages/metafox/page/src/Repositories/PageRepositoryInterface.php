<?php

namespace MetaFox\Page\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Page\Models\Page;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Repository\Contracts\HasFeature;
use MetaFox\Platform\Support\Repository\Contracts\HasSponsor;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface Page.
 * @mixin BaseRepository
 * @method Page getModel()
 * @method Page find($id, $columns = ['*'])
 *
 * @mixin CollectTotalItemStatTrait
 */
interface PageRepositoryInterface extends HasSponsor, HasFeature
{
    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewPages(User $context, User $owner, array $attributes): Paginator;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Page
     * @throws AuthorizationException
     */
    public function viewPage(User $context, int $id): Page;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Page
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function createPage(User $context, array $attributes): Page;

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return Page
     * @throws AuthorizationException
     */
    public function updatePage(User $context, int $id, array $attributes): Page;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deletePage(User $context, int $id): bool;

    /**
     * @param User              $context
     * @param int               $id
     * @param UploadedFile|null $image
     * @param string            $imageCrop
     *
     * @return array<string,          mixed>
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function updateAvatar(User $context, int $id, ?UploadedFile $image, string $imageCrop): array;

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function updateCover(User $context, int $id, array $attributes): array;

    /**
     * @param int $limit
     *
     * @return Paginator
     */
    public function findFeature(int $limit = 4): Paginator;

    /**
     * @param int $limit
     *
     * @return Paginator
     */
    public function findSponsor(int $limit = 4): Paginator;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Content
     * @throws AuthorizationException
     */
    public function approve(User $context, int $id): Content;

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isPending(Content $model): bool;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function removeCover(User $context, int $id): bool;

    /**
     * @param User        $user
     * @param int         $id
     * @param string|null $message
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function claimPage(User $user, int $id, ?string $message = null): bool;

    /**
     * @param User         $context
     * @param array<mixed> $params
     * @param bool         $getEnoughLimit
     *
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getSuggestion(User $context, array $params = [], bool $getEnoughLimit = true): array;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     */
    public function getPageForMention(User $context, array $attributes): Paginator;

    /**
     * @param  User    $user
     * @return Builder
     */
    public function getPageBuilder(User $user): Builder;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Builder
     *
     * @return Paginator
     */
    public function viewSimilar(User $context, array $attributes): Paginator;

    /**
     * @param  int  $userId
     * @return void
     */
    public function deleteUserData(int $userId): void;
}
