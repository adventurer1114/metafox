<?php

namespace MetaFox\Page\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageClaim;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\Contracts\HasFeature;
use MetaFox\Platform\Support\Repository\Contracts\HasSponsor;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Page.
 * @mixin BaseRepository
 * @method PageClaim getModel()
 * @method PageClaim find($id, $columns = ['*'])
 *
 * @mixin CollectTotalItemStatTrait
 */
interface PageClaimRepositoryInterface extends HasSponsor, HasFeature
{
    /**
     * @param  int                    $limit
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewPageClaims(int $limit = Pagination::DEFAULT_ITEM_PER_PAGE): Paginator;

    /**
     * @param  int                    $id
     * @param  int                    $status
     * @return Page
     * @throws AuthorizationException
     */
    public function updatePageClaim(int $id, int $status): Page;

    /**
     * @param User        $user
     * @param int         $id
     * @param string|null $message
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function createClaimPage(User $user, int $id, ?string $message = null): bool;

    /**
     * @param  User $user
     * @param  int  $pageId
     * @return bool
     */
    public function isPendingRequest(User $user, int $pageId): bool;

    /**
     * @param  int  $userId
     * @return void
     */
    public function deleteUserData(int $userId): void;

    /**
     * @param  PageClaim $claim
     * @return void
     */
    public function deleteNotification(PageClaim $claim): void;

    public function deleteClaimByUser(User $user, int $pageId): void;
}
