<?php

namespace MetaFox\Photo\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MetaFox\Photo\Http\Requests\v1\Photo\IndexRequest;
use MetaFox\Photo\Http\Requests\v1\Photo\MakeCoverRequest;
use MetaFox\Photo\Http\Requests\v1\Photo\StoreRequest;
use MetaFox\Photo\Http\Requests\v1\Photo\UpdateRequest;
use MetaFox\Photo\Http\Requests\v1\Photo\UploadFormRequest;
use MetaFox\Photo\Http\Requests\v1\PhotoTag\GetTaggedRequest;
use MetaFox\Photo\Http\Requests\v1\PhotoTag\PhotoTagRequest;
use MetaFox\Photo\Http\Resources\v1\Photo\EditPhotoForm;
use MetaFox\Photo\Http\Resources\v1\Photo\PhotoDetail;
use MetaFox\Photo\Http\Resources\v1\Photo\PhotoDetail as Detail;
use MetaFox\Photo\Http\Resources\v1\Photo\PhotoItemCollection as ItemCollection;
use MetaFox\Photo\Http\Resources\v1\Photo\PhotoTaggedFriend;
use MetaFox\Photo\Http\Resources\v1\Photo\PhotoTaggedFriendCollection;
use MetaFox\Photo\Http\Resources\v1\Photo\UploadPhotoForm;
use MetaFox\Photo\Http\Resources\v1\PhotoGroup\PhotoGroupDetail;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Policies\PhotoPolicy;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorInFeedRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class PhotoController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PhotoController extends ApiController
{
    /**
     * @var PhotoRepositoryInterface
     */
    public PhotoRepositoryInterface $repository;

    public function __construct(PhotoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();

        $context = $owner = user();

        if ($params['user_id']) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (!isset($params['feed_id'])) {
                if (!policy_check(PhotoPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                    throw new AuthorizationException();
                }
            }
        }

        $data = $this->repository->viewPhotos($context, $owner, $params);

        return $this->success(new ItemCollection($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws PermissionDeniedException
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $context = $owner = user();

        app('flood')->checkFloodControlWhenCreateItem(user(), Photo::ENTITY_TYPE);

        if ($params['owner_id'] > 0) {
            if ($context->entityId() != $params['owner_id']) {
                $owner = UserEntity::getById($params['owner_id'])->detail;
            }
        }

        $photoGroup = $this->repository->uploadMedias($context, $owner, $params);
        $message    = __p('photo::phrase.media_item_successfully_uploaded');
        if ($photoGroup->pendingItems()->exists()) {
            $message = __p('core::phrase.thanks_for_your_item_for_approval');
        }

        return $this->success(new PhotoGroupDetail($photoGroup), [], $message);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Detail
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function show(int $id): Detail
    {
        $photo = $this->repository->viewPhoto(user(), $id);

        return new Detail($photo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException | AuthorizationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $photo = $this->repository->updatePhoto(user(), $id, $request->validated());

        return $this->success(new Detail($photo), [], __p('photo::phrase.photo_item_successfully_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function destroy(int $id): JsonResponse
    {
        $context = user();
        $result  = $this->repository->deletePhoto($context, $id);

        return $this->success($result, [], __p('photo::phrase.photo_deleted_successfully'));
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

        $isSponsor = (bool) $sponsor;
        $message   = $isSponsor ? 'core::phrase.resource_sponsored_successfully' : 'core::phrase.resource_unsponsored_successfully';
        $message   = __p($message, ['resource_name' => __p('photo::phrase.photo')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * @param FeatureRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];
        $this->repository->feature(user(), $id, $feature);

        $message = __p('photo::phrase.photo_featured_successfully');
        if (!$feature) {
            $message = __p('photo::phrase.photo_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function approve(int $id): JsonResponse
    {
        $photo = $this->repository->approve(user(), $id);

        return $this->success(new PhotoDetail($photo), [], __p('photo::phrase.photo_has_been_approved'));
    }

    /**
     * @param MakeCoverRequest $request
     * @param int              $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function makeProfileCover(MakeCoverRequest $request, int $id): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        $result  = $this->repository->makeProfileCover($context, $id, $params);

        if (!$result) {
            return $this->error(__p('validation.something_went_wrong_please_try_again'));
        }

        $context->refresh();

        return $this->success([
            'id' => $context instanceof HasUserProfile ? $context->profile->cover_id : 0,
        ], [], __p('user::phrase.cover_picture_update_successfully'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function makeProfileAvatar(int $id): JsonResponse
    {
        $result = $this->repository->makeProfileAvatar(user(), $id);

        if (false == $result) {
            return $this->error(__p('validation.something_went_wrong_please_try_again'));
        }

        return $this->success([], [], __p('user::phrase.profile_picture_update_successfully'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function makeParentCover(int $id): JsonResponse
    {
        $result = $this->repository->makeParentCover(user(), $id);

        if (false == $result) {
            return $this->error(__p('validation.something_went_wrong_please_try_again'));
        }

        return $this->success([], [], __p('user::phrase.cover_picture_update_successfully'));
    }

    /**
     * @throws AuthenticationException
     */
    public function makeParentAvatar(int $id): JsonResponse
    {
        $result = $this->repository->makeParentAvatar(user(), $id);

        if (!$result) {
            return $this->error(__p('validation.something_went_wrong_please_try_again'));
        }

        return $this->success([], [], __p('user::phrase.profile_picture_update_successfully'));
    }

    /**
     * @return PhotoTaggedFriendCollection<PhotoTaggedFriend>
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function getTaggedFriends(GetTaggedRequest $request): PhotoTaggedFriendCollection
    {
        $params = $request->validated();

        $result = $this->repository->getTaggedFriends(user(), $params['item_id']);

        return new PhotoTaggedFriendCollection($result);
    }

    /**
     * @param PhotoTagRequest $request
     *
     * @return JsonResponse|PhotoTaggedFriend
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function tagFriend(PhotoTagRequest $request)
    {
        $params = $request->validated();

        $friend = UserEntity::getById($params['tag_user_id'])->detail;

        $result = $this->repository->tagFriend(user(), $friend, $params['item_id'], $params['px'], $params['py']);

        if ($result) {
            return new PhotoTaggedFriend($result);
        }

        return $this->error(__p('validation.something_went_wrong_please_try_again'));
    }

    /**
     * @param int $tagId
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function deleteTaggedFriend(int $tagId): JsonResponse
    {
        $photoId = $this->repository->deleteTaggedFriend(user(), $tagId);

        if ($photoId) {
            return $this->success([
                'id'       => $tagId,
                'photo_id' => $photoId,
            ]);
        }

        return $this->error(__p('validation.something_went_wrong_please_try_again'));
    }

    /**
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
        $message   = __p($message, ['resource_name' => __p('photo::phrase.photo')]);

        return $this->success([
            'id'         => $id,
            'is_sponsor' => $isSponsor,
        ], [], $message);
    }

    /**
     * @param  UploadFormRequest       $request
     * @param  int|null                $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function form(UploadFormRequest $request, ?int $id = null): JsonResponse
    {
        $photo   = new Photo();
        $context = user();

        $data            = $request->validated();
        $photo->owner_id = $data['owner_id'];

        if ($id !== null) {
            $photo = $this->repository->find($id);

            policy_authorize(PhotoPolicy::class, 'update', $context, $photo);

            return $this->success(new EditPhotoForm($photo), [], '');
        }

        return $this->success(new UploadPhotoForm(), [], '');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function download(int $id): BinaryFileResponse
    {
        $context = user();

        $photo = $this->repository->downloadPhoto($context, $id);

        return response()->download($photo->download_url, basename($photo->image_url))
            ->deleteFileAfterSend(true);
    }
}
