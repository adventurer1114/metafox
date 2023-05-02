<?php

namespace MetaFox\Layout\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Layout\Http\Requests\v1\Snippet\IndexRequest;
use MetaFox\Layout\Http\Requests\v1\Snippet\UpdateThemeRequest;
use MetaFox\Layout\Http\Requests\v1\Snippet\UpdateVariantRequest;
use MetaFox\Layout\Http\Resources\v1\Revision\Admin\RevisionDetail;
use MetaFox\Layout\Http\Resources\v1\Revision\Admin\RevisionItemCollection;
use MetaFox\Layout\Http\Resources\v1\Snippet\SnippetItem;
use MetaFox\Layout\Jobs\CreateBuild;
use MetaFox\Layout\Models\Revision;
use MetaFox\Layout\Models\Snippet;
use MetaFox\Layout\Repositories\SnippetRepositoryInterface;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Models\User;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\Layout\Http\Controllers\Api\SnippetController::$controllers.
 */

/**
 * Class SnippetController.
 * @codeCoverageIgnore
 * @ignore
 */
class SnippetController extends ApiController
{
    /**
     * @var SnippetRepositoryInterface
     */
    private SnippetRepositoryInterface $repository;

    /**
     * SnippetController Constructor.
     *
     * @param SnippetRepositoryInterface $repository
     */
    public function __construct(SnippetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse item.
     *
     * @param  IndexRequest $request
     * @return mixed
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = $this->repository->paginate($params['limit'] ?? 100);

        return $this->success(SnippetItem::collection($data));
    }

    public function ping()
    {
        return $this->success(['pong' => true]);
    }

    /**
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function publish(): JsonResponse
    {
        /** @var User $user */
        $user = user();
        CreateBuild::dispatchSync(sprintf('%s Publish Layout', $user->full_name));

        $url        = config('app.url') . '/admincp/layout/build/browse';

        $this->alert([
            'message' => 'Processing build site. visit <a href="' . $url . '" target="_blank">Build Site</a>',
        ]);

        return $this->success([]);
    }

    /**
     * Store snippet layout.
     *
     * @param  UpdateThemeRequest                       $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function saveTheme(UpdateThemeRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var User $user */
        $user = user();
        extract($params);

        if (!isset($active)) {
            $active = false;
        }

        if (!isset($name)) {
            $name = sprintf('%s changes layout', $user->full_name);
        }

        if (!isset($variant)) {
            $variant = null;
        }

        $this->repository->saveTheme($user, $theme, $variant, $files, $active);

        return $this->success([], [], __p('core::phrase.save_changes'));
    }

    /**
     * Store snippet layout.
     *
     * @param  UpdateVariantRequest                     $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function saveVariant(UpdateVariantRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var User $user */
        $user = user();
        extract($params);

        if (!isset($active)) {
            $active = false;
        }

        $this->repository->saveVariant($user, $theme, $variant, $files, $active);

        return $this->success([], [], __p('core::phrase.save_changes'));
    }

    /**
     * @return JsonResponse
     */
    public function purgeAll(): JsonResponse
    {
        $this->repository->purge();

        return $this->success([], [], __p('core::phrase.save_changes'));
    }

    public function revert(int $id)
    {
        /** @var Revision $revision */
        $revision = Revision::query()->find($id);

        $revision->revert();

        return $this->success(new RevisionDetail($revision), [], __('layout::phrase.revert_changes_successfully'));
    }

    public function history(string $name): JsonResponse
    {
        /** @var ?Snippet $snippet */
        $snippet = Snippet::query()->firstWhere('snippet', '=', $name);

        if (!$snippet) {
            return $this->success([]);
        }

        $items = Revision::query()->whereIn('snippet_id', [$snippet->id])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $this->success(new RevisionItemCollection($items));
    }

    public function purgeHistory(string $name): JsonResponse
    {
        try {
            /** @var ?Snippet $snippet */
            $snippet = Snippet::query()->where('snippet', '=', $name)->first();
            $snippet?->delete();
        } catch (\Exception $exception) {
            // avoid not found
        }

        return $this->success([], [], __p('layout::phrase.clear_changes'));
    }
}
