<?php

namespace MetaFox\Announcement\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use MetaFox\Announcement\Models\AnnouncementView as Model;
use MetaFox\Announcement\Policies\AnnouncementPolicy;
use MetaFox\Announcement\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Announcement\Repositories\AnnouncementViewRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class AnnouncementViewRepository.
 *
 * @method Model getModel()
 * @method Model find($id, $column = ['*'])
 */
class AnnouncementViewRepository extends AbstractRepository implements AnnouncementViewRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }

    /**
     * @inheritDoc
     */
    public function viewAnnouncementViews(User $context, array $params): Paginator
    {
        $limit = Arr::get($params, 'limit');
        $id    = Arr::get($params, 'announcement_id', 0);

        return $this->getModel()
            ->newModelQuery()
            ->with(['user', 'userEntity'])
            ->where('announcement_id', '=', $id)
            ->simplePaginate($limit);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function createAnnouncementView(User $context, array $params): Model
    {
        $id           = Arr::get($params, 'announcement_id', 0);
        $announcement = $this->announcementRepository()
            ->with(['announcementText', 'style', 'views'])
            ->find($id);

        policy_authorize(AnnouncementPolicy::class, 'markAsRead', $context, $announcement);

        return $this->getModel()->newModelQuery()->firstOrCreate([
            'user_id'         => $context->entityId(),
            'user_type'       => $context->entityType(),
            'announcement_id' => $id,
        ]);
    }

    protected function announcementRepository(): AnnouncementRepositoryInterface
    {
        return resolve(AnnouncementRepositoryInterface::class);
    }

    public function checkViewAnnouncement(int $userId, int $announcementId): bool
    {
        return $this->getModel()
            ->newModelQuery()
            ->where([
                'user_id'         => $userId,
                'announcement_id' => $announcementId,
            ])
            ->exists();
    }
}
