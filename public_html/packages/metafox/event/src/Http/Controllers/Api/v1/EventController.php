<?php

namespace MetaFox\Event\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use MetaFox\Event\Http\Requests\v1\Event\CreateFormRequest;
use MetaFox\Event\Http\Requests\v1\Event\IndexRequest;
use MetaFox\Event\Http\Requests\v1\Event\MassEmailRequest;
use MetaFox\Event\Http\Requests\v1\Event\ShowRequest;
use MetaFox\Event\Http\Requests\v1\Event\StoreRequest;
use MetaFox\Event\Http\Requests\v1\Event\UpdateRequest;
use MetaFox\Event\Http\Resources\v1\Event\EventDetail as Detail;
use MetaFox\Event\Http\Resources\v1\Event\EventItemCollection as ItemCollection;
use MetaFox\Event\Http\Resources\v1\Event\EventStatDetail;
use MetaFox\Event\Http\Resources\v1\Event\StoreEventForm;
use MetaFox\Event\Http\Resources\v1\Event\UpdateEventForm;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Event\Http\Controllers\Api\EventController::$controllers.
 */

/**
 * Class EventController.
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group event
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventController extends ApiController
{
    /**
     * @var EventRepositoryInterface
     */
    public $repository;

    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return mixed
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();
        $owner  = $context = user();
        if ($params['user_id'] > 0) {
            $owner = UserEntity::getById($params['user_id'])->detail;
            if (!policy_check(EventPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                throw new AuthorizationException();
            }

            if (!UserPrivacy::hasAccess($context, $owner, 'event.profile_menu')) {
                return $this->success([]);
            }
        }

        $data = $this->repository->viewEvents($context, $owner, $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws PermissionDeniedException
     * @throws ValidatorException
     */
    public function store(StoreRequest $request)
    {
        $owner  = $context = user();
        $params = $request->validated();

        app('flood')->checkFloodControlWhenCreateItem(user(), Event::ENTITY_TYPE);
        app('quota')->checkQuotaControlWhenCreateItem(user(), Event::ENTITY_TYPE);
        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $event = $this->repository->createEvent($context, $owner, $params);

        $message = __p(
            'core::phrase.resource_create_success',
            ['resource_name' => __p('event::phrase.event')]
        );

        $ownerPendingMessage = $event->getOwnerPendingMessage();

        if (null !== $ownerPendingMessage) {
            $message = $ownerPendingMessage;
        }

        return $this->success(new Detail($event), [], $message);
    }

    /**
     * Display the specified resource.
     *
     * @param ShowRequest $request
     * @param int         $id
     *
     * @return Detail
     */
    public function show(ShowRequest $request, int $id)
    {
        $params = $request->validated();
        $event  = $this->repository->getEvent(user(), $id);

        $resource = new Detail($event);
        if (isset($params['invite_code'])) {
            $resource->setInviteCode($params['invite_code']);
        }

        return $resource;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function update(UpdateRequest $request, $id)
    {
        $params = $request->validated();

        $data = $this->repository->updateEvent(user(), $id, $params);

        return $this->success(new Detail($data), [], __p('event::phrase.event_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->repository->deleteEvent(user(), $id);

        return $this->success(['id' => $id], [], __p('event::phrase.event_deleted_successfully'));
    }

    /**
     * @param SponsorRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function sponsor(SponsorRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $sponsor = $params['sponsor'];
        $this->repository->sponsor(user(), $id, $sponsor);

        $message = $sponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message = __p($message, ['resource_name' => __p('event::phrase.event')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => (int) $sponsor,
        ], [], $message);
    }

    /**
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

        $message = $feature ? 'core::phrase.resource_featured_successfully' : 'core::phrase.resource_unfeatured_successfully';
        $message = __p($message, ['resource_name' => __p('event::phrase.event')]);

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function approve(int $id): JsonResponse
    {
        $event = $this->repository->approve(user(), $id);
        $this->repository->handleSendInviteNotification($id);

        return $this->success(new Detail($event), [], __p('event::phrase.event_has_been_approved'));
    }

    /**
     * @param CreateFormRequest $request
     * @param int|null          $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function form(CreateFormRequest $request, ?int $id = null): JsonResponse
    {
        $event   = new Event();
        $context = user();

        $data            = $request->validated();
        $event->owner_id = $data['owner_id'];
        $owner           = null;
        if ($data['owner_id'] != 0) {
            $userEntity = UserEntity::getById($data['owner_id']);
            $owner      = $userEntity->detail;
        }
        if ($id !== null) {
            $event = $this->repository->find($id);
            policy_authorize(EventPolicy::class, 'update', $context, $event);

            return $this->success(new UpdateEventForm($event), [], '');
        }

        policy_authorize(EventPolicy::class, 'create', $context, $owner);

        return $this->success(new StoreEventForm($event), [], '');
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function getStats(int $id): JsonResponse
    {
        $event = $this->repository->find($id);

        $context = user();

        policy_authorize(EventPolicy::class, 'view', $context, $event);

        return $this->success(new EventStatDetail($event));
    }

    /**
     * @throws AuthenticationException
     */
    public function massEmail(MassEmailRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $context = user();
        $this->repository->massEmail($context, $id, $params);

        return $this->success([], [], __p('event::phrase.email_sent_successfully'));
    }
}
