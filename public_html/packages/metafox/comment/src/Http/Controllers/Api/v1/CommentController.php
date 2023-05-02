<?php

namespace MetaFox\Comment\Http\Controllers\Api\v1;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MetaFox\Comment\Http\Requests\v1\Comment\HideRequest;
use MetaFox\Comment\Http\Requests\v1\Comment\IndexRequest;
use MetaFox\Comment\Http\Requests\v1\Comment\StoreRequest;
use MetaFox\Comment\Http\Requests\v1\Comment\UpdateRequest;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentDetail as Detail;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentItemCollection as ItemCollection;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentListingItemCollection;
use MetaFox\Comment\Http\Resources\v1\CommentHistory\CommentHistoryCollection;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Policies\CommentPolicy;
use MetaFox\Comment\Repositories\CommentHistoryRepositoryInterface;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityItemCollection;

/**
 * Class CommentController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 * @authenticated
 * @group comment
 */
class CommentController extends ApiController
{
    /**
     * @var CommentRepositoryInterface
     */
    private CommentRepositoryInterface $repository;
    private CommentHistoryRepositoryInterface $historyRepository;

    /**
     * @param CommentRepositoryInterface        $repository
     * @param CommentHistoryRepositoryInterface $historyRepository
     */
    public function __construct(
        CommentRepositoryInterface $repository,
        CommentHistoryRepositoryInterface $historyRepository
    ) {
        $this->repository        = $repository;
        $this->historyRepository = $historyRepository;
    }

    /**
     * Browse comments.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException|AuthorizationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $data = $this->repository->viewComments(user(), $params);

        return new CommentListingItemCollection($data);
    }

    /**
     * Create comment.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        app('flood')->checkFloodControlWhenCreateItem($context, Comment::ENTITY_TYPE);

        $data = $this->repository->createComment($context, $params);

        $item = $data->item;

        $feedId = null;

        if ($item instanceof ActivityFeedSource) {
            try {
                /** @var Content $feed */
                $feed   = app('events')->dispatch('activity.get_feed', [$context, $item], true);
                $feedId = $feed->entityId();
            } catch (Exception $e) {
                // Silent.
                Log::error($e->getMessage());
            }
        }

        $message = '';

        if (!$context->hasPermissionTo('comment.auto_approved')) {
            $message = __p('comment::phrase.your_comment_has_been_added_successfully_it_is_waiting_for_an_admin_approval');
        }

        return $this->success(new Detail($data), [
            'feed_id' => $feedId,
        ], $message);
    }

    /**
     * View comment.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->repository->viewComment(user(), $id);

        return $this->success(new Detail($data));
    }

    /**
     * Update comment.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->updateComment(user(), $id, $params);

        $message = __p('comment::phrase.comment_successfully_updated');

        return $this->success(new Detail($data), [], $message);
    }

    /**
     * Remove comment.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        $data    = $this->repository->deleteCommentById(user(), $id);

        $message = __p('comment::phrase.comment_successfully_deleted');

        return $this->success($data, [], $message);
    }

    /**
     * @param  HideRequest             $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function hide(HideRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $id = Arr::get($params, 'comment_id');

        $isHidden = Arr::get($params, 'is_hidden');

        $isGlobal = Arr::get($params, 'is_global', false);

        $success = match ($isGlobal) {
            true  => $this->repository->hideCommentGlobal($context, $id, $isHidden),
            false => $this->repository->hideComment($context, $id, $isHidden)
        };
        $data = $this->repository->viewComment(user(), $id);
        if ($success) {
            return $this->success(
                new Detail($data),
                [],
                __p('comment::phrase.' . ($isHidden ? 'comment_successfully_hidden' : 'comment_successfully_unhidden'))
            );
        }

        return $this->error();
    }

    /**
     * @throws AuthenticationException
     */
    public function getUsersComment(IndexRequest $request)
    {
        $params = $request->validated();

        $context = user();

        [$total, $result] = $this->repository->getUsersCommentByItem($context, $params);

        return $this->success(new UserEntityItemCollection($result), ['total' => $total]);
    }

    /**
     * @param  IndexRequest            $request
     * @return JsonResource
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function getRelatedComments(IndexRequest $request): JsonResource
    {
        $data = $request->validated();

        $context = user();

        policy_authorize(CommentPolicy::class, 'viewAny', $context);

        $collection = $this->repository->getRelatedCommentsByType(
            $context,
            Arr::get($data, 'item_type'),
            Arr::get($data, 'item_id'),
            [
                'sort_type' => Arr::get($data, 'sort_type'),
            ]
        );

        return new ItemCollection($collection);
    }

    public function getCommentHistories(int $id): JsonResponse
    {
        $comment = $this->repository->find($id);
        $data    = $this->historyRepository->viewHistory($comment);

        return $this->success(new CommentHistoryCollection($data));
    }

    public function previewComment(int $id): JsonResponse
    {
        $data = $this->repository->find($id);

        $response = new Detail($data);

        return $this->success($response->setIsPreview(true));
    }

    public function removeLinkPreview(int $id): JsonResponse
    {
        $comment = $this->repository->find($id);
        $context = user();

        policy_authorize(CommentPolicy::class, 'removeLinkPreview', $context, $comment);

        $this->repository->removeLinkPreview($comment);

        $comment->refresh();

        return $this->success(ResourceGate::asItem($comment, null));
    }
}
