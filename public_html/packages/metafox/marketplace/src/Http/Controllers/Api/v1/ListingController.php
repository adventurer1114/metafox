<?php

namespace MetaFox\Marketplace\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Marketplace\Http\Requests\v1\Listing\IndexRequest;
use MetaFox\Marketplace\Http\Requests\v1\Listing\StoreRequest;
use MetaFox\Marketplace\Http\Requests\v1\Listing\UpdateRequest;
use MetaFox\Marketplace\Http\Resources\v1\Listing\ListingDetail as Detail;
use MetaFox\Marketplace\Http\Resources\v1\Listing\ListingItemCollection;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\InviteRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingHistoryRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ListingController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @group marketplace
 * @authenticated
 */
class ListingController extends ApiController
{
    /**
     * @var ListingRepositoryInterface
     */
    private ListingRepositoryInterface $repository;

    /**
     * @param ListingRepositoryInterface $repository
     */
    public function __construct(ListingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse listing.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $owner = $context;

        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (!policy_check(ListingPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                throw new AuthorizationException();
            }

            if (!UserPrivacy::hasAccess($context, $owner, 'marketplace_view_browse_marketplace_listings')) {
                return $this->success([]);
            }

            if (UserPrivacy::hasAccess($context, $owner, 'marketplace.profile_menu') == false) {
                return $this->success([]);
            }
        }

        $data = $this->repository->viewMarketplaceListings($context, $owner, $params);

        return $this->success(new ListingItemCollection($data));
    }

    /**
     * View listing.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): Detail
    {
        $context = user();

        $data = $this->repository->viewMarketplaceListing($context, $id);

        /*
         * Mark as visited after accessing to detail page
         */
        resolve(InviteRepositoryInterface::class)->visitedAt($context, $data);

        /*
         * Mark as history
         */
        resolve(ListingHistoryRepositoryInterface::class)->createHistory(
            $context->entityId(),
            $context->entityType(),
            $id
        );

        return new Detail($data);
    }

    /**
     * Create listing.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidatorException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $context = $owner = user();

        $params = $request->validated();

        app('flood')->checkFloodControlWhenCreateItem($context, Listing::ENTITY_TYPE);

        app('quota')->checkQuotaControlWhenCreateItem($context, Listing::ENTITY_TYPE);

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $marketplace = $this->repository->createMarketplaceListing($context, $owner, $params);

        $message = __p('marketplace::phrase.listing_successfully_created');

        if (!$marketplace->is_approved) {
            $message = __p('core::phrase.thanks_for_your_item_for_approval');
        }

        $ownerPendingMessage = $marketplace->getOwnerPendingMessage();

        if (null !== $ownerPendingMessage) {
            $message = $ownerPendingMessage;
        }

        return $this->success(new Detail($marketplace), [], $message);
    }

    /**
     * Update listing.
     *
     * @param  UpdateRequest           $request
     * @param  int                     $id
     * @return Detail
     * @throws AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $marketplace = $this->repository->updateMarketplaceListing(user(), $id, $params);

        return $this->success(
            new Detail($marketplace),
            [],
            __p('marketplace::phrase.listing_has_been_updated_successfully')
        );
    }

    /**
     * Remove listing.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteMarketplaceListing(user(), $id);

        return $this->success([], [], __p('marketplace::phrase.successfully_deleted_listing'));
    }

    /**
     * Feature listing.
     *
     * @param FeatureRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];
        $this->repository->feature(user(), $id, $feature);

        $message = __p('marketplace::phrase.listing_featured_successfully');
        if (!$feature) {
            $message = __p('marketplace::phrase.listing_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * Sponsor listing.
     *
     * @param SponsorRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     */
    public function sponsor(SponsorRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsor(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;
        $message   = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message   = __p($message, ['resource_name' => __p('marketplace::phrase.listing')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * Approve listing.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function approve(int $id): JsonResponse
    {
        $context = user();

        $listing = $this->repository->approve($context, $id);

        $resource = ResourceGate::asDetail($listing);

        return $this->success($resource, [], __p('marketplace::phrase.listing_has_been_approved_successfully'));
    }

    /**
     * Reopen listing.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function reopen(int $id): JsonResponse
    {
        $context = user();

        $this->repository->reopenListing($context, $id);

        $listing = $this->repository->find($id);

        $resource = ResourceGate::asDetail($listing);

        $data = $resource->toArray(request());

        return $this->success([
            'id'         => $id,
            'is_expired' => false,
            'extra'      => Arr::get($data, 'extra', []),
        ], [], __p('marketplace::phrase.listing_successfully_reopened'));
    }

    /**
     * Sponsor in feed.
     *
     * @param SponsorInFeedRequest $request
     * @param int                  $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function sponsorInFeed(SponsorInFeedRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsorInFeed(user(), $id, $sponsor);

        $isSponsor = (bool) $sponsor;
        $message   = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message   = __p($message, ['resource_name' => __p('marketplace::phrase.listing')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }
}
