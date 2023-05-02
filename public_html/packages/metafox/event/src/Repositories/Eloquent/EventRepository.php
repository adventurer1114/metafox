<?php

namespace MetaFox\Event\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Repositories\AttachmentRepositoryInterface;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Event\Mails\EventMail;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\HostInvite;
use MetaFox\Event\Models\MassEmail;
use MetaFox\Event\Models\Member;
use MetaFox\Event\Policies\CategoryPolicy;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Repositories\HostInviteRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
use MetaFox\Event\Support\Browse\Scopes\Event\OnlineScope;
use MetaFox\Event\Support\Browse\Scopes\Event\SortScope;
use MetaFox\Event\Support\Browse\Scopes\Event\ViewScope;
use MetaFox\Event\Support\Browse\Scopes\Event\WhenScope;
use MetaFox\Event\Support\CacheManager;
use MetaFox\Event\Support\Facades\Event as EventFacades;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BoundsScope;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\LocationScope;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;

/**
 * Class EventRepository.
 * @method Event getModel()
 * @method Event find($id, $columns = ['*'])()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventRepository extends AbstractRepository implements EventRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasApprove;
    use CollectTotalItemStatTrait;

    public function model()
    {
        return Event::class;
    }

    private function attachmentRepository(): AttachmentRepositoryInterface
    {
        return resolve(AttachmentRepositoryInterface::class);
    }

    private function memberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    private function hostInviteRepository(): HostInviteRepositoryInterface
    {
        return resolve(HostInviteRepositoryInterface::class);
    }

    public function createEvent(User $context, User $owner, array $attributes): Event
    {
        policy_authorize(EventPolicy::class, 'create', $context, $owner);

        $attributes = array_merge($attributes, [
            'module_id'   => Event::ENTITY_TYPE,
            'user_id'     => $context->entityId(),
            'user_type'   => $context->entityType(),
            'owner_id'    => $owner->entityId(),
            'owner_type'  => $owner->entityType(),
            'is_approved' => $context->hasPermissionTo('event.auto_approved'),
        ]);

        // In case owner controls own item pending status
        if ($owner->hasPendingMode()) {
            $attributes['is_approved'] = true;
        }

        if (!empty($attributes['temp_file'])) {
            $tempFile                    = upload()->getFile($attributes['temp_file']);
            $attributes['image_file_id'] = $tempFile->id;

            // Delete temp file after done
            upload()->rollUp($attributes['temp_file']);
        }

        /** @var Event $event */
        $event = $this->getModel()->newModelInstance();

        $event->fill($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $event->setPrivacyListAttribute($attributes['list']);
        }

        $event->save();

        if (!empty($attributes['attachments'])) {
            $this->attachmentRepository()->updateItemId($attributes['attachments'], $event);
        }

        if (!empty($attributes['host']) && $attributes['privacy'] != MetaFoxPrivacy::ONLY_ME) {
            $this->hostInviteRepository()->inviteHosts($context, $event, $attributes['host']);
        }

        $this->memberRepository()->joinEvent($event, $context, Member::ROLE_HOST);

        return $this->with(['eventText', 'attachments'])
            ->find($event->entityId());
    }

    /**
     * @throws AuthorizationException
     */
    public function viewEvents(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(EventPolicy::class, 'viewAny', $context, $owner);

        $limit     = $attributes['limit'] ?? Pagination::DEFAULT_ITEM_PER_PAGE;
        $view      = $attributes['view'] ?? Browse::VIEW_ALL;
        $profileId = Arr::get($attributes, 'user_id');

        if ($view == Browse::VIEW_FEATURE) {
            return $this->findFeature();
        }

        if ($view == Browse::VIEW_SPONSOR) {
            return $this->findSponsor();
        }

        if ($context->entityId() && $profileId == $context->entityId() && $view != Browse::VIEW_PENDING) {
            $attributes['view'] = $view = Browse::VIEW_MY;
        }

        if (Browse::VIEW_PENDING == $view) {
            if ($context->isGuest() || !$context->hasPermissionTo('event.approve')) {
                throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
            }
        }
        $categoryId = Arr::get($attributes, 'category_id', 0);

        if ($categoryId > 0) {
            $category = resolve(CategoryRepositoryInterface::class)->find($categoryId);

            policy_authorize(CategoryPolicy::class, 'viewActive', $context, $category);
        }

        $relations = ['eventText', 'user', 'userEntity', 'activeCategories'];
        $query     = $this->buildQueryViewEvents($context, $owner, $attributes);
        $eventData = $query
            ->with($relations)
            ->simplePaginate($limit, ['events.*']);

        $attributes['current_page'] = $eventData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes)) {
            return $eventData;
        }

        $userId    = $context->entityId();
        $cacheKey  = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);
        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_ITEM;

        return $this->transformPaginatorWithSponsor($eventData, $cacheKey, $cacheTime);
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function buildQueryViewEvents(User $context, User $owner, array $attributes): Builder
    {
        $view       = Arr::get($attributes, 'view', Browse::VIEW_ALL);
        $sort       = Arr::get($attributes, 'sort', SortScope::SORT_DEFAULT);
        $sortType   = Arr::get($attributes, 'sort_type', SortScope::SORT_TYPE_DEFAULT);
        $when       = Arr::get($attributes, 'when', Browse::WHEN_ALL);
        $search     = Arr::get($attributes, 'q');
        $categoryId = Arr::get($attributes, 'category_id');
        $country    = Arr::get($attributes, 'where');
        $profileId  = Arr::get($attributes, 'user_id');
        $eventId    = Arr::get($attributes, 'event_id');
        $isOnline   = Arr::get($attributes, 'is_online');
        $bounds     = [
            'west'  => Arr::get($attributes, 'bounds_west'),
            'east'  => Arr::get($attributes, 'bounds_east'),
            'south' => Arr::get($attributes, 'bounds_south'),
            'north' => Arr::get($attributes, 'bounds_north'),
        ];

        $query = $this->getModel()->newQuery();

        if ($view == Browse::VIEW_SIMILAR && $eventId) {
            $event = $this->find($eventId);
            if ($event) {
                $categoryId = $event->categories->pluck('id')->toArray();
            }

            $query->whereKeyNot($eventId);
        }

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($context->entityId())
            ->setModerationPermissionName('event.moderate');

        $sortScope = new SortScope();

        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();

        $whenScope->setWhen($when);

        $viewScope = new ViewScope();

        $viewScope->setUserContext($context)->setView($view)->setProfileId($profileId);

        $locationScope = new LocationScope();

        $locationScope
            ->setCountry($country);

        $boundsScope = new BoundsScope();
        $boundsScope->setBounds($bounds);

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['name']));
        }

        if ($owner->entityId() != $context->entityId()) {
            $privacyScope->setOwnerId($owner->entityId());

            $viewScope->setIsViewOwner(true);

            if (!$context->can('approve', [Event::class, resolve(Event::class)])) {
                $query->where('events.is_approved', '=', 1);
            }
        }

        if ($categoryId > 0) {
            if (!is_array($categoryId)) {
                $categoryId = [$categoryId];
            }

            $categoryScope = new CategoryScope();
            $categoryScope->setCategories($categoryId);
            $query->addScope($categoryScope);
        }

        if (isset($isOnline)) {
            $onlineScope = new OnlineScope();
            $onlineScope->setIsOnline($isOnline);

            $query->addScope($onlineScope);
        }

        $query = $this->applyDisplayEventSetting($query, $owner, $view);

        return $query->addScope($privacyScope)
            ->addScope($locationScope)
            ->addScope($boundsScope)
            ->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($viewScope);
    }

    public function getEvent(User $context, int $id): Event
    {
        $event = $this->with(['user', 'categories', 'attachments', 'eventText'])
            ->find($id);

        policy_authorize(EventPolicy::class, 'view', $context, $event);

        $event->incrementTotalView();
        $event->refresh();

        return $event;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function updateEvent(User $context, int $id, array $attributes): Event
    {
        /** @var Event $event */
        $event = $this->find($id);

        policy_authorize(EventPolicy::class, 'update', $context, $event);

        if (isset($attributes['title'])) {
            $attributes['title'] = $this->cleanTitle($attributes['title']);
        }

        if (!empty($attributes['remove_image'])) {
            if ($event->image_file_id) {
                app('storage')->rollDown($event->image_file_id);
            }
            $attributes['image_file_id'] = null;
        }

        if (!empty($attributes['temp_file'])) {
            $tempFile                    = upload()->getFile($attributes['temp_file']);
            $attributes['image_file_id'] = $tempFile->id;

            // Delete temp file after done
            upload()->rollUp($tempFile->id);
        }

        $event->fill($attributes);

        if (isset($attributes['privacy']) && $attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $event->setPrivacyListAttribute($attributes['list']);
        }

        $event->save();

        $event->refresh();

        if (!empty($attributes['attachments'])) {
            $this->attachmentRepository()->updateItemId($attributes['attachments'], $event);
        }

        $this->handleUpdateHosts($context, $event, Arr::get($attributes, 'host'));

        $this->updateFeedStatus($event);

        return $this->with(['eventText', 'attachments'])->find($event->entityId());
    }

    protected function handleUpdateHosts(User $context, Event $event, ?array $hosts = null): void
    {
        if ($event->privacy == MetaFoxPrivacy::ONLY_ME) {
            resolve(HostInviteRepositoryInterface::class)->deleteHostPendingInvites($event->entityId());

            return;
        }

        if (null === $hosts) {
            return;
        }

        $currentIds = $this->memberRepository()->getEventHostsForForm($event)
            ->pluck('id')
            ->toArray();

        $removeIds = array_diff($currentIds, $hosts);

        $insertIds = array_diff($hosts, $currentIds);

        if (count($insertIds)) {
            $this->hostInviteRepository()->inviteHosts($context, $event, $insertIds);
        }

        if (count($removeIds)) {
            $this->memberRepository()->removeHostByIds($context, $event, $removeIds);
        }
    }

    protected function updateFeedStatus(Event $event): void
    {
        app('events')->dispatch('activity.feed.mark_as_pending', [$event]);
    }

    public function deleteEvent(User $context, int $id): bool
    {
        $event = $this->find($id);

        policy_authorize(EventPolicy::class, 'delete', $context, $event);

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleting', [$event]);

        $event->delete();

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleted', [$event]);

        return true;
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', 1)
            ->where('is_approved', 1)
            ->simplePaginate($limit);
    }

    public function findFeature(int $limit = 6): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', 1)
            ->where('is_approved', 1)
            ->orderByDesc('featured_at')
            ->simplePaginate($limit);
    }

    /**
     * @param  Builder $query
     * @param  User    $owner
     * @param  string  $view
     * @return Builder
     */
    private function applyDisplayEventSetting(Builder $query, User $owner, string $view): Builder
    {
        $checkViewAll = in_array($view, [Browse::VIEW_ALL, Browse::VIEW_ALL_DEFAULT, Browse::VIEW_LATEST]);

        if (!$owner instanceof HasPrivacyMember && $checkViewAll) {
            $query->where('events.owner_type', '=', $owner->entityType());
        }

        return $query;
    }

    public function getExtraStatistics(User $context, Event $event): array
    {
        $statistics = [];

        if (policy_check(EventPolicy::class, 'update', $context, $event)) {
            $statistics = array_merge($statistics, [
                'total_pending_posts' => $this->countTotalPendingPosts($event),
            ]);
        }

        return $statistics;
    }

    protected function countTotalPendingPosts(Event $event, ?User $user = null): int
    {
        $userId = null;

        if ($user instanceof User) {
            $userId = $user->entityId();
        }

        $total = app('events')->dispatch(
            'activity.feed.count',
            [$event->entityType(), $event->entityId(), MetaFoxConstant::ITEM_STATUS_PENDING, $userId],
            true
        );

        if (null === $total) {
            return 0;
        }

        return (int) $total;
    }

    public function getUserExtraStatistics(User $context, User $user, int $id): array
    {
        $event = $this->find($id);

        policy_authorize(EventPolicy::class, 'view', $context, $event);

        return [
            'total_pending_posts' => $this->countTotalPendingPosts($event, $user),
        ];
    }

    public function deleteUserData(int $userId): void
    {
        $events = $this->getModel()->newModelQuery()
            ->where([
                'user_id' => $userId,
            ])
            ->get();

        if ($events->count()) {
            $events->each(function ($event) {
                $event->delete();
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function handleSendInviteNotification(int $eventId): void
    {
        /** @var Event $event */
        $event   = $this->find($eventId);
        $invites = $event->hostInvites;

        foreach ($invites as $invite) {
            if ($invite instanceof HostInvite) {
                Notification::send(...$invite->toNotification());
            }
        }
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function massEmail(User $context, int $eventId, array $attributes): void
    {
        /** @var Event $event */
        $event = $this->find($eventId);

        policy_authorize(EventPolicy::class, 'massEmail', $context, $event);

        $isSpam = EventFacades::checkPermissionMassEmail($context, $event->entityId());

        if ($isSpam) {
            throw new AuthorizationException();
        }

        $subject    = Arr::get($attributes, 'subject');
        $text       = Arr::get($attributes, 'text');
        $from       = $context->getEmailForVerification();
        $recipients = $this->memberRepository()
            ->getAllMembers($eventId)
            ->pluck('user.email')
            ->toArray();

        Mail::to($recipients)
            ->send(new EventMail([
                'subject' => $subject,
                'from'    => $from,
                'html'    => $text,
            ]));

        MassEmail::query()->create([
            'event_id'  => $eventId,
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ]);
    }

    /**
     * @param  User        $user
     * @param  int         $eventId
     * @return string|null
     */
    public function getLatestMassEmailByUser(User $user, int $eventId): ?string
    {
        $minutes   = $user->getPermissionValue('event.how_long_time_must_wait_send_mass_email');
        $massEmail = new MassEmail();
        $query     = $massEmail->newModelQuery()
            ->where('user_id', $user->entityId())
            ->where('event_id', $eventId)
            ->orderByDesc('created_at')
            ->first();

        if (!$query) {
            return null;
        }

        if ($minutes == null || $minutes == 0) {
            return null;
        }

        return Carbon::make($query?->created_at)->addMinutes($minutes);
    }

    public function toPendingNotifiables(Event $event, User $context): array
    {
        return $event->admins()
            ->with(['user'])
            ->get()
            ->map(function ($admin) {
                return $admin->user;
            })
            ->all();
    }

    public function getMissingLocationEvent(): Collection
    {
        return $this->getModel()
            ->newQuery()
            ->whereNotNull('events.location_name')
            ->where(function (Builder $subQuery) {
                $subQuery->whereNull('events.location_latitude')
                    ->orWhereNull('events.location_longitude');
            })->get();
    }
}
