<?php

namespace MetaFox\Page\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageClaim;
use MetaFox\Page\Models\PageMember;
use MetaFox\Page\Notifications\ApproveRequestClaimNotification;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PageRepository.
 * @method PageClaim getModel()
 * @method PageClaim find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class PageClaimRepository extends AbstractRepository implements PageClaimRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasApprove;
    use CollectTotalItemStatTrait;

    public function model(): string
    {
        return PageClaim::class;
    }

    private function pageRepository(): PageRepositoryInterface
    {
        return resolve(PageRepositoryInterface::class);
    }

    private function memberRepository(): PageMemberRepositoryInterface
    {
        return resolve(PageMemberRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function viewPageClaims(int $limit = Pagination::DEFAULT_ITEM_PER_PAGE): Paginator
    {
        $query = $this->getModel()->newQuery();

        return $query->where('status_id', PageClaim::STATUS_PENDING)
            ->simplePaginate($limit);
    }

    /**
     * @param  int                     $id
     * @param  int                     $status
     * @return Page
     * @throws ValidatorException
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function updatePageClaim(int $id, int $status): Page
    {
        $context   = user();
        $pageClaim = $this->find($id);
        $page      = $pageClaim->page;
        $status    = $status ? PageClaim::STATUS_APPROVE : PageClaim::STATUS_DENY;
        $oldUser   = $page->user;

        $pageClaim->update(['status_id' => $status]);
        $this->deleteNotification($pageClaim);

        if ($status == PageClaim::STATUS_DENY) {
            return $page->refresh();
        }

        $notification = new ApproveRequestClaimNotification($pageClaim);
        $notification->setUserId($context->entityId())
            ->setUserType($context->entityType());

        $response = [[$pageClaim->user, $oldUser], $notification];

        Notification::send(...$response);

        $page->update(['user_id' => $pageClaim->userId()]);

        $this->memberRepository()
            ->addPageMember($page, $pageClaim->userId(), PageMember::ADMIN);

        $this->memberRepository()
            ->updatePageMember($context, $page->entityId(), $oldUser->entityId(), PageMember::MEMBER);

        return $page->refresh();
    }

    /**
     * @throws AuthorizationException
     */
    public function createClaimPage(User $user, int $id, ?string $message = null): bool
    {
        $page = $this->pageRepository()->with(['pageClaim'])->find($id);

        policy_authorize(PagePolicy::class, 'claim', $user, $page);

        return (new PageClaim([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
            'page_id'   => $page->entityId(),
            'message'   => $message,
        ]))->save();
    }

    /**
     * @inheritDoc
     */
    public function isPendingRequest(User $user, int $pageId): bool
    {
        return $this->getModel()->newQuery()
            ->where('user_id', $user->entityId())
            ->where('page_id', $pageId)
            ->where('status_id', PageClaim::STATUS_PENDING)
            ->exists();
    }

    /**
     * @inheritDoc
     */
    public function deleteUserData(int $userId): void
    {
        $claims = $this->getModel()->newQuery()
            ->where([
                'user_id' => $userId,
            ])
            ->get();

        foreach ($claims as $claim) {
            $claim->delete();
            $this->deleteNotification($claim);
        }
    }

    public function deleteNotification(PageClaim $claim): void
    {
        app('events')->dispatch('notification.delete_mass_notification_by_item', [$claim], true);
    }

    public function deleteClaimByUser(User $user, int $pageId): void
    {
        $claim = $this->getModel()->newQuery()
            ->where('user_id', $user->entityId())
            ->where('page_id', $pageId)
            ->first();

        if ($claim instanceof PageClaim) {
            $claim->delete();
            $this->deleteNotification($claim);
        }
    }
}
