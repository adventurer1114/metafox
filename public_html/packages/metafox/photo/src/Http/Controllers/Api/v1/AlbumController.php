<?php

namespace MetaFox\Photo\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\Photo\Http\Requests\v1\Album\IndexRequest;
use MetaFox\Photo\Http\Requests\v1\Album\ItemsRequest;
use MetaFox\Photo\Http\Requests\v1\Album\StoreRequest;
use MetaFox\Photo\Http\Requests\v1\Album\UpdateRequest;
use MetaFox\Photo\Http\Requests\v1\Album\UploadPhotosRequest;
use MetaFox\Photo\Http\Resources\v1\Album\AlbumDetail;
use MetaFox\Photo\Http\Resources\v1\Album\AlbumItemCollection;
use MetaFox\Photo\Http\Resources\v1\Album\CreateAlbumForm;
use MetaFox\Photo\Http\Resources\v1\Album\EditAlbumForm;
use MetaFox\Photo\Http\Resources\v1\AlbumItem\AlbumItemItemCollection;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Support\Facades\Album as FacadesAlbum;
use MetaFox\Platform\Exceptions\PermissionDeniedException;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\Platform\Http\Requests\v1\SponsorRequest;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * Class AlbumController.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AlbumController extends ApiController
{
    public AlbumRepositoryInterface $repository;

    public function __construct(AlbumRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $context = $owner = user();

        if ($params['user_id']) {
            $owner = UserEntity::getById($params['user_id'])->detail;

            if (!policy_check(AlbumPolicy::class, 'viewOnProfilePage', $context, $owner)) {
                throw new AuthorizationException();
            }
        }

        $data = $this->repository->viewAlbums($context, $owner, $params);


        return new AlbumItemCollection($data);
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
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $context = $owner = user();

        app('flood')->checkFloodControlWhenCreateItem(user(), Album::ENTITY_TYPE);
        if ($params['owner_id'] > 0 && $params['owner_id'] != $context->entityId()) {
            $owner = UserEntity::getById($params['owner_id'])->detail;
        }

        $album   = $this->repository->createAlbum($context, $owner, $params);
        $message = __p(
            'core::phrase.resource_create_success',
            ['resource_name' => __p('photo::phrase.photo_album')]
        );

        return $this->success(new AlbumDetail($album), [], $message);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): JsonResponse
    {
        $album = $this->repository->viewAlbum(user(), $id);

        return $this->success(new AlbumDetail($album));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $album = $this->repository->find($id);

        if (FacadesAlbum::isDefaultAlbum($album->album_type)) {
            unset($params['name']);
        }

        $album = $this->repository->updateAlbum(user(), $id, $params);

        return $this->success(new AlbumDetail($album), [], __p('photo::phrase.photo_album_updated_successfully'));
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
        $this->repository->deleteAlbum(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('photo::phrase.photo_album_deleted_successfully'));
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
        $message   = __p($message, ['resource_name' => __p('photo::phrase.photo_album')]);

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
     * @throws AuthorizationException|AuthenticationException
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];
        $this->repository->feature(user(), $id, $feature);

        $message = __p('photo::phrase.photo_album_featured_successfully');
        if (!$feature) {
            $message = __p('photo::phrase.photo_album_unfeatured_successfully');
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
        $album = $this->repository->approve(user(), $id);

        return $this->success(new AlbumDetail($album));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ItemsRequest            $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function items(ItemsRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $data = $this->repository->viewAlbumItems($context, $id, $params);

        return $this->success(new AlbumItemItemCollection($data));
    }

    public function uploadMedias(UploadPhotosRequest $request): JsonResponse
    {
        $data = $request->validated();

        $context = user();

        $id = Arr::get($data, 'id', 0);

        unset($data['id']);

        $result = $this->repository->uploadMedias($context, $id, $data);

        return $this->success(
            new AlbumDetail(Arr::get($result, 'album')),
            [],
            $this->handleUploadMediasMessage($result)
        );
    }

    protected function handleUploadMediasMessage(array $result): string
    {
        $hasUploadedPhoto = Arr::get($result, 'uploaded_photo', 0);
        $hasUploadedVideo = Arr::get($result, 'uploaded_video', 0);
        $hasUpdatedPhoto  = Arr::get($result, 'updated_photo', 0);
        $hasUpdatedVideo  = Arr::get($result, 'updated_video', 0);

        if ($hasUploadedPhoto || $hasUploadedVideo) {
            return __p('photo::web.media_uploaded_successfully', [
                'hasPhoto' => (string) $hasUploadedPhoto,
                'hasVideo' => (string) $hasUploadedVideo,
            ]);
        }

        if ($hasUpdatedPhoto || $hasUpdatedVideo) {
            return __p('photo::web.media_updated_successfully');
        }

        return __p('photo::web.uploaded_failed');
    }

    public function create()
    {
        return new CreateAlbumForm();
    }

    public function edit(int $id)
    {
        $album = $this->repository->find($id);

        $form =   new EditAlbumForm($album);

        app()->call([$form, 'boot'], ['id'=>$id]);

        return $form;
    }
}
