<?php

namespace MetaFox\Marketplace\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Repositories\AttachmentRepositoryInterface;
use MetaFox\Marketplace\Jobs\DeleteListingJob;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Notifications\ExpiredNotification;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\ImageRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Marketplace\Support\Browse\Scopes\Listing\ViewScope;
use MetaFox\Marketplace\Support\CacheManager;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BoundsScope;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Browse\Scopes\TagScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\Platform\Support\Repository\HasSponsorInFeed;
use MetaFox\User\Traits\UserMorphTrait;

/**
 * Class ListingRepository.
 * @property Listing $model
 * @method   Listing getModel()
 * @method   Listing find($id, $columns = ['*'])()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class ListingRepository extends AbstractRepository implements ListingRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasSponsorInFeed;
    use HasApprove;
    use UserMorphTrait;

    public function model(): string
    {
        return Listing::class;
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewMarketplaceListings(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(ListingPolicy::class, 'viewAny', $context, $owner);

        $view = $attributes['view'];

        $limit = $attributes['limit'];

        $profileId = Arr::get($attributes, 'user_id', 0);

        if ($view == Browse::VIEW_FEATURE) {
            return $this->findFeature($limit);
        }

        if ($view == Browse::VIEW_SPONSOR) {
            return $this->findSponsor($limit);
        }

        if (!$this->hasPendingView($context, $view, $profileId)) {
            throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
        }

        if ($view === ViewScope::VIEW_EXPIRE) {
            policy_authorize(ListingPolicy::class, 'viewExpire', $context, $owner, $profileId);
        }

        if ($profileId > 0 && $profileId == $context->entityId()) {
            if (!in_array($view, [Browse::VIEW_PENDING, ViewScope::VIEW_EXPIRE])) {
                $attributes['view'] = Browse::VIEW_MY;
            }
        }
        $query = $this->buildQueryViewListings($context, $owner, $attributes);

        $relation = ['marketplaceText', 'userEntity', 'ownerEntity'];

        $listingData = $query
            ->with($relation)
            ->simplePaginate($limit, ['marketplace_listings.*']);

        $attributes['current_page'] = $listingData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes)) {
            return $listingData;
        }

        $userId = $context->entityId();

        $cacheKey = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);

        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_ITEM;

        return $this->transformPaginatorWithSponsor($listingData, $cacheKey, $cacheTime, 'id', $relation);
    }

    protected function hasPendingView(User $context, string $view, int $profileId): bool
    {
        if ($view !== Browse::VIEW_PENDING) {
            return true;
        }

        if ($profileId == $context->entityId()) {
            return true;
        }

        if (!$context->hasPermissionTo('marketplace.approve')) {
            return false;
        }

        return true;
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Listing
     * @throws AuthorizationException
     */
    public function viewMarketplaceListing(User $context, int $id): Listing
    {
        $listing = $this
            ->with(['marketplaceText', 'activeCategories', 'attachments', 'ownerEntity', 'userEntity'])
            ->find($id);

        policy_authorize(ListingPolicy::class, 'view', $context, $listing);

        $listing->with(['marketplaceText', 'activeCategories', 'attachments', 'ownerEntity', 'userEntity']);

        $listing->incrementTotalView();

        $listing->refresh();

        return $listing;
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Listing
     * @throws AuthorizationException
     */
    public function createMarketplaceListing(User $context, User $owner, array $attributes): Listing
    {
        policy_authorize(ListingPolicy::class, 'create', $context, $owner);

        $price = Arr::get($attributes, 'price');

        if (is_array($price)) {
            Arr::set($attributes, 'price', json_encode($price));
        }

        $attributes = array_merge($attributes, [
            'user_id'          => $context->entityId(),
            'user_type'        => $context->entityType(),
            'owner_id'         => $owner->entityId(),
            'owner_type'       => $owner->entityType(),
            'is_approved'      => 1,
            'start_expired_at' => Carbon::now()->timestamp,
        ]);

        if (
            $context->entityId() == $owner->entityId()
            && !$context->hasPermissionTo('marketplace.auto_approved', true)
        ) {
            $attributes['is_approved'] = 0;
        }

        $attributes['title'] = $this->cleanTitle($attributes['title']);

        if (null !== Arr::get($attributes, 'short_description')) {
            $attributes['short_description'] = $this->cleanContent($attributes['short_description']);
        }

        $marketplace = new Listing();

        $marketplace->fill($attributes);

        if ($attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $marketplace->setPrivacyListAttribute($attributes['list']);
        }

        $marketplace->save();

        $this->handleAttachments($marketplace, Arr::get($attributes, 'attachments'));

        $this->handleThumbnailPhoto($marketplace, Arr::get($attributes, 'thumbnail_photo'));

        $this->handleAttachedPhotos($context, $marketplace, Arr::get($attributes, 'attached_photos'), false);

        $marketplace->refresh();

        return $marketplace;
    }

    protected function handleAttachedPhotos(
        User $context,
        Listing $marketplace,
        ?array $attachedPhotos,
        bool $isUpdated = true
    ): void {
        resolve(ImageRepositoryInterface::class)->updateImages(
            $context,
            $marketplace->entityId(),
            $attachedPhotos,
            $isUpdated
        );
    }

    protected function handleThumbnailPhoto(Listing $marketplace, ?array $photo): void
    {
        if (null === $photo) {
            return;
        }

        $status = Arr::get($photo, 'status', 'update');

        $tempFileId = (int) Arr::get($photo, 'temp_file', 0);

        if ($status == 'update') {
            if ($marketplace->image_file_id) {
                app('storage')->rollDown($marketplace->image_file_id);
            }

            if ($tempFileId > 0) {
                $tempFile = upload()->getFile($tempFileId);

                $marketplace->image_file_id = $tempFile->entityId();

                $marketplace->save();

                upload()->rollUp($tempFileId);
            }

            return;
        }

        if (!$marketplace->image_file_id) {
            return;
        }

        app('storage')->rollDown($marketplace->image_file_id);

        $marketplace->image_file_id = 0;

        $marketplace->save();
    }

    protected function handleAttachments(Listing $marketplace, ?array $attachments): void
    {
        if (null === $attachments) {
            return;
        }

        resolve(AttachmentRepositoryInterface::class)->updateItemId($attachments, $marketplace);
    }

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return Listing
     * @throws AuthorizationException
     */
    public function updateMarketplaceListing(User $context, int $id, array $attributes): Listing
    {
        $listing = $this->find($id);

        policy_authorize(ListingPolicy::class, 'update', $context, $listing);

        if (null !== Arr::get($attributes, 'title')) {
            $attributes['title'] = $this->cleanTitle($attributes['title']);
        }

        if (null !== Arr::get($attributes, 'short_description')) {
            $attributes['short_description'] = $this->cleanContent($attributes['short_description']);
        }

        if (!$listing->is_approved) {
            // Disallow marking as Sold when item is pending
            if (Arr::has($attributes, 'is_sold')) {
                unset($attributes['is_sold']);
            }
        }

        $listing->fill($attributes);

        if (isset($attributes['privacy']) && $attributes['privacy'] == MetaFoxPrivacy::CUSTOM) {
            $listing->setPrivacyListAttribute($attributes['list']);
        }

        $listing->save();

        $this->handleAttachments($listing, Arr::get($attributes, 'attachments'));

        $this->handleThumbnailPhoto($listing, Arr::get($attributes, 'thumbnail_photo'));

        $this->handleAttachedPhotos($context, $listing, Arr::get($attributes, 'attached_photos'));

        $listing->refresh();

        return $listing;
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteMarketplaceListing(User $context, int $id): bool
    {
        $listing = $this->find($id);

        policy_authorize(ListingPolicy::class, 'delete', $context, $listing);

        if (!$this->delete($id)) {
            return false;
        }

        DeleteListingJob::dispatch($id);

        return true;
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     */
    private function buildQueryViewListings(User $context, User $owner, array $attributes): Builder
    {
        $sort       = Arr::get($attributes, 'sort');
        $sortType   = Arr::get($attributes, 'sort_type');
        $when       = Arr::get($attributes, 'when');
        $view       = Arr::get($attributes, 'view');
        $search     = Arr::get($attributes, 'q');
        $searchTag  = Arr::get($attributes, 'tag', MetaFoxConstant::EMPTY_STRING);
        $categoryId = Arr::get($attributes, 'category_id');
        $profileId  = Arr::get($attributes, 'user_id', 0);
        $countryIso = Arr::get($attributes, 'country_iso');
        $bounds     = [
            'west'  => Arr::get($attributes, 'bounds_west'),
            'east'  => Arr::get($attributes, 'bounds_east'),
            'south' => Arr::get($attributes, 'bounds_south'),
            'north' => Arr::get($attributes, 'bounds_north'),
        ];

        // Scopes.
        $privacyScope = new PrivacyScope();
        $privacyScope->setUserId($context->entityId());
        $privacyScope->setModerationPermissionName('marketplace.moderate');

        $sortScope = new SortScope();
        $sortScope->setSort($sort)
            ->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)
            ->setView($view)
            ->setProfileId($profileId);

        $query = $this->getModel()->newQuery();

        $boundsScope = new BoundsScope();
        $boundsScope->setBounds($bounds);

        if (MetaFoxConstant::EMPTY_STRING !== $search) {
            $query->addScope(new SearchScope($search, ['title']));
        }

        if (MetaFoxConstant::EMPTY_STRING !== $searchTag) {
            $tagScope = new TagScope($searchTag);

            $query->addScope($tagScope);
        }

        if ($owner->entityId() != $context->entityId()) {
            $privacyScope->setOwnerId($owner->entityId());

            $viewScope->setIsViewOwner(true);

            if (!$context->hasPermissionTo('marketplace.approve')) {
                $query->where('marketplace_listings.is_approved', '=', 1);
            }
        }

        if ($categoryId > 0) {
            $categoryScope = new CategoryScope();

            $categoryScope->setCategories([$categoryId]);

            $query->addScope($categoryScope);
        }

        $this->applyDisplaySetting($query, $owner, $view);

        if (null !== $countryIso) {
            $query->where('marketplace_listings.country_iso', '=', $countryIso);
        }

        return $query
            ->addScope($privacyScope)
            ->addScope($whenScope)
            ->addScope($viewScope)
            ->addScope($sortScope);
    }

    /**
     * @param  Builder $query
     * @param  User    $owner
     * @param  string  $view
     * @return void
     */
    private function applyDisplaySetting(Builder $query, User $owner, string $view): void
    {
        if ($view == Browse::VIEW_MY) {
            return;
        }

        if ($owner instanceof HasPrivacyMember) {
            return;
        }

        $query->where('marketplace_listings.owner_type', '=', $owner->entityType());
    }

    public function findFeature(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', Listing::IS_FEATURED)
            ->where('is_approved', '=', 1)
            ->orderByDesc(HasFeature::FEATURED_AT_COLUMN)
            ->simplePaginate($limit);
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', Listing::IS_SPONSOR)
            ->where('is_approved', '=', 1)
            ->simplePaginate($limit);
    }

    public function forceDeleteListing(int $id): void
    {
        $listing = Listing::onlyTrashed()
            ->where('id', '=', $id)
            ->first();

        if (null === $listing) {
            return;
        }

        if ($listing->paidInvoices()->count()) {
            $this->deleteUnusedListingData($listing);

            return;
        }

        $listing->forceDelete();
    }

    public function deleteUnusedListingData(Listing $listing): void
    {
        $listing->marketplaceText()->delete();

        $listing->invites()->each(function ($data) {
            $data->delete();
        });

        $listing->categories()->sync([]);

        $listing->photos()->each(function ($data) {
            $data->delete();
        });

        $listing->attachments()->each(function ($data) {
            $data->delete();
        });

        $listing->histories()->delete();

        $listing->pendingInvoices()->delete();
    }

    public function closeListingAfterPayment(int $id): bool
    {
        $listing = $this->find($id);

        if (!$listing->auto_sold) {
            return false;
        }

        $listing->fill([
            'is_sold' => true,
        ]);

        $listing->save();

        return true;
    }

    public function reopenListing(User $context, int $id): bool
    {
        $listing = $this->find($id);

        policy_authorize(ListingPolicy::class, 'reopen', $context, $listing);

        $attributes = [
            'start_expired_at' => Carbon::now()->timestamp,
            'is_notified'      => true,
            'is_sold'          => false,
        ];

        $listing->fill($attributes);

        $listing->save();

        return true;
    }

    protected function checkingConditionsBeforeSendingNotifications(): bool
    {
        $expiredDays = (int) Settings::get('marketplace.days_to_expire', 30);

        if (0 === $expiredDays) {
            return false;
        }

        $notifiedDays = (int) Settings::get('marketplace.days_to_notify_before_expire', 0);

        if (0 === $notifiedDays) {
            return false;
        }

        if ($expiredDays == $notifiedDays) {
            return false;
        }

        return true;
    }

    protected function getNotifiedListings(): Enumerable
    {
        $expiredDays = (int) Settings::get('marketplace.days_to_expire', 30);

        $notifiedDays = (int) Settings::get('marketplace.days_to_notify_before_expire', 0);

        $now = Carbon::now();

        $expiredTimestamp = $expiredDays * 86400;

        $notifiedTimestamp = ($expiredDays - $notifiedDays) * 86400;

        return $this->getModel()->newModelQuery()
            ->where([
                'is_notified' => 0,
            ])
            ->whereRaw('start_expired_at + ? > ?', [$expiredTimestamp, $now->timestamp])
            ->whereRaw('start_expired_at + ? <= ?', [$notifiedTimestamp, $now->timestamp])
            ->get();
    }

    public function sendExpiredNotifications(): void
    {
        if (!$this->checkingConditionsBeforeSendingNotifications()) {
            return;
        }

        $notifiedListings = $this->getNotifiedListings();

        if (0 == $notifiedListings->count()) {
            return;
        }

        $this->processNotifiedListings($notifiedListings);
    }

    protected function processNotifiedListings(Enumerable $notifiedListings)
    {
        $successListingIds = [];

        $days = (int) Settings::get('marketplace.days_to_notify_before_expire', 0);

        foreach ($notifiedListings as $notifiedListing) {
            if (null === $notifiedListing->user) {
                continue;
            }

            $this->toExpiredNotification($notifiedListing, $days);

            $successListingIds[] = $notifiedListing->entityId();
        }

        if (!count($successListingIds)) {
            return;
        }

        $successListingIds = array_unique($successListingIds);

        $this->getModel()->newModelQuery()
            ->whereIn('id', $successListingIds)
            ->update([
                'is_notified' => 1,
            ]);
    }

    protected function toExpiredNotification(Listing $listing, int $days): void
    {
        $notification = new ExpiredNotification($listing);

        $notification->setExpiredDays($days);

        $params = [$listing->user, $notification];

        Notification::send(...$params);
    }
}
