<?php

namespace MetaFox\Page\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageInvite;
use MetaFox\Page\Models\PageMember;
use MetaFox\Page\Notifications\AssignOwnerNotification;
use MetaFox\Page\Notifications\PageInvite as PageInviteNotification;
use MetaFox\Page\Policies\PageMemberPolicy;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageInviteRepositoryInterface;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Page\Support\Browse\Scopes\PageMember\ViewScope;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class PageMemberRepository.
 * @method PageMember getModel()
 * @method PageMember find($id, $columns = ['*'])
 */
class PageMemberRepository extends AbstractRepository implements PageMemberRepositoryInterface
{
    public function model(): string
    {
        return PageMember::class;
    }

    private function pageRepository(): PageRepositoryInterface
    {
        return resolve(PageRepositoryInterface::class);
    }

    private function claimRepository(): PageClaimRepositoryInterface
    {
        return resolve(PageClaimRepositoryInterface::class);
    }

    private function inviteRepository(): PageInviteRepositoryInterface
    {
        return resolve(PageInviteRepositoryInterface::class);
    }

    private function userRepository(): UserRepositoryInterface
    {
        return resolve(UserRepositoryInterface::class);
    }

    public function viewPageMembers(User $context, int $pageId, array $attributes): Paginator
    {
        $page = $this->pageRepository()->find($pageId);

        policy_authorize(PageMemberPolicy::class, 'viewAny', $context, $page);

        $search         = $attributes['q'];
        $limit          = $attributes['limit'];
        $view           = $attributes['view'];
        $excludedUserId = $attributes['excluded_user_id'] ?? null;
        $query          = $this->getModel()->newQuery();

        if (in_array($view, [ViewScope::VIEW_ADMIN])) {
            policy_authorize(PageMemberPolicy::class, 'viewAdmins', $context, $page);
        }

        $viewScope = new ViewScope();
        $viewScope
            ->setView($view)
            ->setPageId($pageId)
            ->setUserContext($context);

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['full_name'], 'users'));
        }

        if ($excludedUserId != null) {
            $query->whereNot('page_members.user_id', $excludedUserId);
        }

        return $query->with(['user', 'page'])
            ->addScope($viewScope)
            ->simplePaginate($limit, ['page_members.*']);
    }

    public function viewPageAdmins(User $context, int $pageId, array $attributes): Paginator
    {
        $page = $this->pageRepository()->find($pageId);
        policy_authorize(PageMemberPolicy::class, 'viewAny', $context, $page);

        $search         = $attributes['q'];
        $limit          = $attributes['limit'];
        $excludedUserId = $attributes['excluded_user_id'] ?? null;

        $query = $this->userRepository()->getModel()->newQuery();

        $viewScope = new ViewScope();
        $viewScope->setPageId($pageId)->setIsViewAdmin(true);

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['full_name']));
        }

        if ($excludedUserId != null) {
            $query->whereNot('page_members.user_id', $excludedUserId);
        }

        return $query->with('profile')
            ->addScope($viewScope)
            ->simplePaginate($limit);
    }

    /**
     * @throws AuthenticationException
     */
    public function addPageAdmin(Page $page, int $userId): bool
    {
        $context = user();
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$this->isPageMember($page->entityId(), $user->entityId())) {
            return false;
        }

        $this->inviteRepository()->createInvite($context, $user, $page->entityId(), PageInvite::INVITE_ADMIN);

        return true;
    }

    public function addPageMember(Page $page, int $userId, int $memberType = PageMember::MEMBER): bool
    {
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if ($page->isMember($user) && $memberType == PageMember::ADMIN) {
            return $this->getPageMember($page->entityId(), $user->entityId())->update([
                'member_type' => PageMember::ADMIN,
            ]);
        }

        // Create page member.
        parent::create([
            'page_id'     => $page->entityId(),
            'user_id'     => $user->entityId(),
            'user_type'   => $user->entityType(),
            'member_type' => $memberType,
        ]);

        return true;
    }

    public function isPageMember(int $pageId, int $userId): bool
    {
        return $this->getModel()->newQuery()
            ->where('page_id', $pageId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function removePageMember(Page $page, int $userId): bool
    {
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$this->isPageMember($page->entityId(), $user->entityId())) {
            return false;
        }

        // Need to get data into model class to use deleted observe.
        $record = $this->getModel()->newQuery()
            ->where('page_id', $page->entityId())
            ->where('user_id', $user->entityId())
            ->firstOrFail();

        $this->handleRemoveMemberInvite($page, $user);

        return (bool) $record->delete();
    }

    public function likePage(User $context, int $pageId): array
    {
        $page = $this->pageRepository()->find($pageId);

        policy_authorize(PageMemberPolicy::class, 'likePage', $context, $page);

        $this->addPageMember($page, $context->entityId());

        $page->refresh();

        $this->inviteRepository()->acceptInviteOnly($page, $context);

        return [
            'id'         => $page->entityId(),
            'total_like' => $page->total_member,
            'membership' => PageMember::LIKED,
        ];
    }

    public function unLikePage(User $context, int $pageId): array
    {
        $page = $this->pageRepository()->find($pageId);

        policy_authorize(PageMemberPolicy::class, 'unlikePage', $context, $page);

        $this->removePageMember($page, $context->entityId());
        $this->inviteRepository()->handelInviteUnLikedPage($pageId, $context, false);
        $page->refresh();

        return [
            'id'         => $page->entityId(),
            'total_like' => $page->total_member,
            'membership' => PageMember::NO_LIKE,
        ];
    }

    /**
     * @throws AuthorizationException
     */
    private function checkAddPageAdminPermission(User $context, Page $page): void
    {
        policy_authorize(PageMemberPolicy::class, 'addPageAdmin', $context, $page);
    }

    /**
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function addPageAdmins(User $context, int $pageId, array $userIds): bool
    {
        $page = $this->pageRepository()->find($pageId);

        $this->checkAddPageAdminPermission($context, $page);

        foreach ($userIds as $userId) {
            $this->addPageAdmin($page, $userId);
        }

        return true;
    }

    public function deletePageAdmin(User $context, int $pageId, int $userId): bool
    {
        $page = $this->pageRepository()->find($pageId);

        $this->checkAddPageAdminPermission($context, $page);

        return $this->removePageMember($page, $userId);
    }

    public function updatePageMember(User $context, int $pageId, int $userId, int $memberType): bool
    {
        $page = $this->pageRepository()->find($pageId);

        if ($memberType == PageMember::ADMIN) {
            $this->checkAddPageAdminPermission($context, $page);
        }

        $pageMember = $this->getModel()->newQuery()
            ->where('page_id', $page->entityId())
            ->where('user_id', $userId)
            ->firstOrFail();

        $pageMember->update(['member_type' => $memberType]);

        return true;
    }

    public function reassignOwner(User $context, int $pageId, int $userId): bool
    {
        $page    = $this->pageRepository()->find($pageId);
        $oldUser = $page->user;
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        policy_authorize(PagePolicy::class, 'moderate', $context, $page);

        if (!$page->isAdmin($user)) {
            abort(403, __p('page::phrase.the_user_is_not_a_page_admin'));
        }

        $this->getPageMember($pageId, $oldUser->entityId())->update(['member_type' => PageMember::MEMBER]);

        $result = $page->update([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ]);

        if ($result) {
            $this->claimRepository()->deleteClaimByUser($user, $pageId);

            $notification = new AssignOwnerNotification($page->refresh());
            $this->sendNotificationAllMembers($pageId, $notification->setContext($context), $context);
        }

        return $result;
    }

    public function deletePageMember(User $context, int $pageId, int $userId): bool
    {
        $page = $this->pageRepository()->find($pageId);

        policy_authorize(PageMemberPolicy::class, 'deletePageMember', $context, $page);

        return $this->removePageMember($page, $userId);
    }

    public function removePageAdmin(User $context, int $pageId, int $userId, bool $isDelete): bool
    {
        $page   = $this->pageRepository()->find($pageId);
        $member = $this->getPageMember($pageId, $userId);
        policy_authorize(PageMemberPolicy::class, 'removeAsAdmin', $context, $member);

        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$page->isAdmin($user)) {
            abort(403, __p('page::phrase.the_user_is_not_a_page_admin'));
        }

        if ($isDelete) {
            return $this->deletePageAdmin($context, $pageId, $userId);
        }

        return $this->updatePageMember($context, $pageId, $userId, PageMember::MEMBER);
    }

    /**
     * @inheritDoc
     */
    public function getPageMembers(int $pageId): Collection
    {
        return $this->getModel()->newQuery()
            ->where('page_id', $pageId)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getPageMember(int $pageId, int $userId): PageMember
    {
        return $this->getModel()->newModelQuery()
            ->where('user_id', $userId)
            ->where('page_id', $pageId)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function cancelAdminInvite(User $context, int $pageId, int $userId): bool
    {
        return $this->inviteRepository()->deleteInvite($context, $pageId, $userId);
    }

    private function handleRemoveMemberInvite(Page $page, User $user): void
    {
        $invites = $this->inviteRepository()->getModel()->newModelQuery()
            ->where('page_id', $page->entityId())
            ->where('owner_id', $user->entityId())->get();
        if (empty($invites)) {
            return;
        }
        foreach ($invites as $invite) {
            /* @var PageInvite $invite */
            $invite->update(['status_id' => PageInvite::STATUS_NOT_USE]);

            $typeNotification = (new PageInviteNotification())->getType();

            app('events')->dispatch(
                'notification.delete_notification_by_type_and_item',
                [$typeNotification, $invite->entityId(), $invite->entityType()],
                true
            );
        }
    }

    public function deleteUserData(int $userId): void
    {
        $members = $this->getModel()->newQuery()
            ->where([
                'user_id' => $userId,
            ])
            ->get();

        foreach ($members as $member) {
            $member->delete();
        }
    }

    private function sendNotificationAllMembers(int $pageId, mixed $notifiable, User $context)
    {
        $members = $this->getPageMembers($pageId);

        $users = [];
        foreach ($members as $member) {
            if ($context->entityId() == $member->userId()) {
                continue;
            }

            $users[] = $member->user;
        }

        $response = [$users, $notifiable];
        Notification::send(...$response);
    }
}
