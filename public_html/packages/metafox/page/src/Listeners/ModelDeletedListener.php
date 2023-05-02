<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageInviteRepositoryInterface;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class ModelDeletedListener.
 * @ignore
 */
class ModelDeletedListener
{
    /**
     * @param Model $model
     */
    public function handle(Model $model): void
    {
        if ($model instanceof Page) {
            return;
        }

        if ($model instanceof User) {
            $this->deleteUserData($model);
        }
    }

    protected function deleteUserData(User $model): void
    {
        $this->deletePage($model);
        $this->deleteInvites($model);
        $this->deleteMembers($model);
        $this->deleteClaims($model);
    }

    protected function deletePage(User $model): void
    {
        resolve(PageRepositoryInterface::class)->deleteUserData($model->entityId());
    }

    protected function deleteInvites(User $model): void
    {
        $inviteRepository = resolve(PageInviteRepositoryInterface::class);
        $inviteRepository->deleteOwnerData($model->entityId());

        $inviteRepository->deleteUserData($model->entityId());
    }

    protected function deleteMembers(User $model): void
    {
        resolve(PageMemberRepositoryInterface::class)->deleteUserData($model->entityId());
    }

    protected function deleteClaims(User $model): void
    {
        resolve(PageClaimRepositoryInterface::class)->deleteUserData($model->entityId());
    }
}
