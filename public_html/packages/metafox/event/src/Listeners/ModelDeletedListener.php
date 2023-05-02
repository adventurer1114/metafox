<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Event\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Repositories\HostInviteRepositoryInterface;
use MetaFox\Event\Repositories\InviteRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
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
        if ($model instanceof User) {
            $this->deleteUserData($model);
        }
    }

    protected function deleteUserData(Model $model): void
    {
        $this->deleteEvents($model);

        $this->deleteInvites($model);

        $this->deleteHostInvites($model);

        $this->deleteMembers($model);
    }

    protected function deleteMembers(Model $model): void
    {
        resolve(MemberRepositoryInterface::class)->deleteUserData($model->entityId());
    }

    protected function deleteHostInvites(Model $model): void
    {
        $inviteRepository = resolve(HostInviteRepositoryInterface::class);

        $inviteRepository->deleteInvited($model->entityId());

        $inviteRepository->deleteInviteByUser($model->entityId());
    }

    protected function deleteEvents(Model $model): void
    {
        resolve(EventRepositoryInterface::class)->deleteUserData($model->entityId());
    }

    protected function deleteInvites(Model $model): void
    {
        $inviteRepository = resolve(InviteRepositoryInterface::class);

        $inviteRepository->deleteInvited($model->entityId());

        $inviteRepository->deleteInvite($model->entityId());
    }
}
