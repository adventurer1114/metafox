<?php

namespace MetaFox\Announcement\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Announcement\Models\Announcement;
use MetaFox\Announcement\Models\Style;
use MetaFox\Announcement\Policies\AnnouncementPolicy;
use MetaFox\Announcement\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Announcement\Repositories\HiddenRepositoryInterface;
use MetaFox\Announcement\Support\CacheManager;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\User\Support\Facades\UserValue;

/**
 * Class AnnouncementRepository.
 * @property Announcement $model
 * @method   Announcement getModel()
 * @method   Announcement find($id, $columns = ['*'])
 * @ignore
 * @codeCoverageIgnore
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AnnouncementRepository extends AbstractRepository implements AnnouncementRepositoryInterface
{
    public function model(): string
    {
        return Announcement::class;
    }

    public function getHiddenRepository(): HiddenRepositoryInterface
    {
        return resolve(HiddenRepositoryInterface::class);
    }

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Paginator|array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAnnouncements(User $context, array $attributes): Paginator|array
    {
        $limit = $attributes['limit'] ?? 10;

        $query = $this->buildQuery($context);

        $query->where(function (Builder $whereQuery) {
            $whereQuery
                ->whereNull('announcement_views.id')
                ->orWhere('announcements.can_be_closed', '=', 0);
        });

        $timestamp = UserValue::getUserValueSettingByName($context, CacheManager::ANNOUNCEMENT_CLOSE_SETTING);

        $first = $query->first();

        if ($first?->start_date <= Carbon::parse($timestamp)) {
            return [];
        }

        return $query
            ->with(['announcementText', 'style', 'views'])
            ->paginate($limit);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Announcement
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAnnouncement(User $context, int $id): Announcement
    {
        $resource = $this->with(['announcementText', 'style', 'userEntity'])->find($id);

        policy_authorize(AnnouncementPolicy::class, 'view', $context, $resource);

        return $resource;
    }

    public function getTotalUnread(User $context): int
    {
        $query = $this->buildQuery($context);
        $query->whereNull('announcement_views.id');

        return $query->count();
    }

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Announcement
     * @throws AuthorizationException
     * @throws Exception
     */
    public function createAnnouncement(User $context, array $attributes): Announcement
    {
        policy_authorize(AnnouncementPolicy::class, 'create', $context);

        $attributes = $this->cleanData($attributes);

        $attributes = array_merge($attributes, [
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ]);

        /** @var Announcement $announcement */
        $announcement = $this->getModel()->newModelInstance();
        $announcement->fill($attributes);
        $announcement->save();
        $announcement->refresh();
        $announcement->loadMissing(['announcementText', 'roles', 'style']);

        return $announcement;
    }

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return Announcement
     * @throws AuthorizationException | Exception
     */
    public function updateAnnouncement(User $context, int $id, array $attributes): Announcement
    {
        $announcement = $this->find($id);
        policy_authorize(AnnouncementPolicy::class, 'update', $context, $announcement);

        $attributes = $this->cleanData($attributes);

        $announcement->fill($attributes);

        //assign style to announcement
        if (isset($attributes['style'])) {
            $isNewStyle = $attributes['style'] != $announcement->style->entityId();
            if ($isNewStyle) {
                $style = Style::query()->findOrFail($attributes['style']);
                $announcement->style()->associate($style);
            }
        }

        $announcement->save();
        $announcement->refresh();

        return $announcement;
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return int
     * @throws AuthorizationException
     */
    public function deleteAnnouncement(User $context, int $id): int
    {
        $announcement = $this->find($id);
        policy_authorize(AnnouncementPolicy::class, 'delete', $context, $announcement);

        return $this->delete($id);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Announcement
     */
    public function hideAnnouncement(User $context, int $id): Announcement
    {
        $announcement = $this->find($id);
        $this->getHiddenRepository()->createHidden($context, $announcement);

        return $announcement;
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    protected function cleanData(array $attributes): array
    {
        $parser = parse_input();

        // @todo implement phrase language here
        if (isset($attributes['subject_var'])) {
            $attributes['subject_var'] = $parser->clean($attributes['subject_var']);
        }

        // @todo implement phrase language here
        if (isset($attributes['intro_var'])) {
            $attributes['intro_var'] = $parser->clean($attributes['intro_var']);
        }

        // @todo implement phrase language here
        if (isset($attributes['text'])) {
            $attributes['text'] = $parser->clean($attributes['text']);
        }

        return $attributes;
    }

    /**
     * @inheritDoc
     */
    public function viewAnnouncementsForAdmin(User $context, array $attributes): Paginator
    {
        $limit       = $attributes['limit'];
        $search      = Arr::get($attributes, 'q');
        $roleId      = Arr::get($attributes, 'role_id');
        $style       = Arr::get($attributes, 'style');
        $startFrom   = Arr::get($attributes, 'start_from');
        $startTo     = Arr::get($attributes, 'start_to');
        $createdFrom = Arr::get($attributes, 'created_from');
        $createdTo   = Arr::get($attributes, 'created_to');

        $query = $this->getModel()->newModelQuery();

        if ($search) {
            $query = $query->addScope(new SearchScope($search, ['subject_var']));
        }

        if ($style) {
            $query->where('style_id', '=', $style);
        }

        if ($roleId) {
            $query->where(function ($innerQuery) use ($roleId) {
                $innerQuery->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('role_id', '=', $roleId);
                });

                $innerQuery->orWhereDoesntHave('roles');
            });
        }

        if ($startFrom) {
            $query->where('start_date', '>=', $startFrom);
        }

        if ($startTo) {
            $query->where('start_date', '<=', $startTo);
        }

        if ($createdFrom) {
            $query->where('created_at', '>=', $createdFrom);
        }

        if ($createdTo) {
            $query->where('created_at', '<=', $createdTo);
        }

        return $query
            ->with(['announcementText', 'style', 'roles'])
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->paginate();
    }

    protected function getPhraseRepository(): PhraseRepositoryInterface
    {
        return resolve(PhraseRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function activateAnnouncement(User $context, int $id): Announcement
    {
        $announcement = $this->find($id);

        policy_check(AnnouncementPolicy::class, 'update', $context, $announcement);

        $announcement->update(['is_active' => 1]);

        return $announcement->refresh();
    }

    /**
     * @inheritDoc
     */
    public function deactivateAnnouncement(User $context, int $id): Announcement
    {
        $announcement = $this->find($id);

        policy_check(AnnouncementPolicy::class, 'update', $context, $announcement);

        $announcement->update(['is_active' => 0]);

        return $announcement->refresh();
    }

    /**
     * @param  User    $context
     * @return Builder
     */
    private function buildQuery(User $context): Builder
    {
        $query = $this->getModel()
            ->newModelQuery()
            ->select(['announcements.*'])
            ->from('announcements')
            ->leftJoin('announcement_views', function (JoinClause $join) use ($context) {
                $join->on('announcements.id', '=', 'announcement_views.announcement_id')
                    ->where('announcement_views.user_id', '=', $context->entityId());
            })
            ->where('announcements.is_active', '=', 1)
            ->where('announcements.start_date', '<=', Carbon::now());

        if (!$context->hasSuperAdminRole()) {
            $query = $this->applyRoleQuery($query, $context);
            $query = $this->applyLocationQuery($query, $context);
            $query = $this->applyGenderQuery($query, $context);
        }

        return $query
            ->orderBy('announcements.can_be_closed', 'desc')
            ->orderBy('announcements.id', 'desc');
    }

    private function applyRoleQuery(Builder $query, User $user): Builder
    {
        $contextRole = resolve(RoleRepositoryInterface::class)->roleOf($user);
        if (!$contextRole instanceof Role) {
            return $query;
        }

        return $query->where(function (Builder $whereQuery) use ($contextRole) {
            $whereQuery->doesntHave('roles')
                ->orWhereHas('roles', function (Builder $hasQuery) use ($contextRole) {
                    $hasQuery->where('role_id', '=', $contextRole->entityId());
                });
        });
    }

    private function applyLocationQuery(Builder $query, User $context): Builder
    {
        if (!$context instanceof HasUserProfile || !$context->profile->country_iso) {
            return $query->whereNull('announcements.country_iso');
        }

        return $query->where(function (Builder $where) use ($context) {
            $where->whereNull('announcements.country_iso')
                ->orWhere('announcements.country_iso', '=', $context->profile->country_iso);
        });
    }

    private function applyGenderQuery(Builder $query, User $context): Builder
    {
        if (!$context instanceof HasUserProfile || !$context->profile->gender_id) {
            return $query->where('announcements.gender', '=', 0);
        }

        return $query->where(function (Builder $where) use ($context) {
            $where->where('announcements.gender', '=', 0)
                ->orWhere('announcements.gender', '=', $context->profile->gender_id);
        });
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function closeAnnouncement(User $context): bool
    {
        policy_authorize(AnnouncementPolicy::class, 'close', $context);
        $timestamp = Carbon::now()->timestamp;

        return UserValue::updateUserValueSetting($context, [CacheManager::ANNOUNCEMENT_CLOSE_SETTING => $timestamp]);
    }
}
