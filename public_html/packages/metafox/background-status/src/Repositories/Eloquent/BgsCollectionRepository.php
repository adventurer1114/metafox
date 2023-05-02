<?php

namespace MetaFox\BackgroundStatus\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use MetaFox\BackgroundStatus\Models\BgsBackground;
use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\BackgroundStatus\Policies\BgsCollectionPolicy;
use MetaFox\BackgroundStatus\Repositories\BgsCollectionRepositoryInterface;
use MetaFox\BackgroundStatus\Support\Browse\Scopes\NotDeleteScope;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class BgsCollectionRepository.
 *
 * @method BgsCollection getModel()
 * @method BgsCollection find($id, $columns = ['*'])
 * @ignore
 * @codeCoverageIgnore
 */
class BgsCollectionRepository extends AbstractRepository implements BgsCollectionRepositoryInterface
{
    public function model(): string
    {
        return BgsCollection::class;
    }

    public function viewBgsCollectionsForAdmin(User $context, array $attributes): Paginator
    {
        policy_authorize(BgsCollectionPolicy::class, 'viewAny', $context);

        return $this->getModel()->newQuery()
            ->where('is_deleted', 0)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->simplePaginate($attributes['limit']);
    }

    public function viewBgsCollectionsForFE(User $context, array $attributes): Paginator
    {
        policy_authorize(BgsCollectionPolicy::class, 'viewAny', $context);

        return $this->getModel()->newModelInstance()
            ->with([
                'backgrounds' => function (HasMany $query) {
                    $query->where('is_deleted', 0);
                    $query->orderBy('ordering')->orderBy('id');
                },
            ])
            ->where('total_background', '>', 0)
            ->where('is_deleted', 0)
            ->where('is_active', BgsCollection::IS_ACTIVE)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->simplePaginate($attributes['limit']);
    }

    public function getBackgrounds(User $context, array $attributes): Paginator
    {
        policy_authorize(BgsCollectionPolicy::class, 'viewAny', $context);

        $bgsCollection = $this->find($attributes['collection_id']);
        $this->checkIsDeleted($bgsCollection);

        $notDeleteScope = new NotDeleteScope();

        return $bgsCollection->backgrounds()
            ->addScope($notDeleteScope)
            ->simplePaginate($attributes['limit']);
    }

    public function viewBgsCollection(User $context, int $id): BgsCollection
    {
        policy_authorize(BgsCollectionPolicy::class, 'viewAny', $context);
        $bgsCollection = $this->find($id);

        $this->checkIsDeleted($bgsCollection);

        $bgsCollection->load([
            'backgrounds' => function (HasMany $query) {
                $notDeleteScope = new NotDeleteScope();

                return $query->addScope($notDeleteScope);
            },
        ]);

        return $bgsCollection;
    }

    public function createBgsCollection(User $context, array $attributes): BgsCollection
    {
        policy_authorize(BgsCollectionPolicy::class, 'create', $context);

        if (isset($attributes['background_temp_file'])) {
            foreach ($attributes['background_temp_file'] as $tempFileId) {
                $tempFile = upload()->getFile($tempFileId);
                $backgrounds = [
                    'image_file_id' => $tempFile->id,
                ];

                if (isset($attributes['view_only'])) {
                    $backgrounds = array_merge($backgrounds, ['view_only' => $attributes['view_only']]);
                }

                $attributes['backgrounds'][] = $backgrounds;

                upload()->rollUp($tempFileId);
            }
        }

        /** @var BgsCollection $bgsCollection */
        $bgsCollection = parent::create($attributes);

        return $bgsCollection->refresh();
    }

    public function updateBgsCollection(User $context, int $id, array $attributes): BgsCollection
    {
        policy_authorize(BgsCollectionPolicy::class, 'update', $context);
        $bgsCollection = $this->find($id);

        $this->checkCanUpdate($bgsCollection);

        if ($bgsCollection->is_default) {
            unset($attributes['is_default'], $attributes['is_active']);
        }

        if (isset($attributes['background_temp_file'])) {
            foreach ($attributes['background_temp_file'] as $tempFileId) {
                $tempFile = upload()->getFile($tempFileId);
                $attributes['backgrounds'][] = [
                    'image_file_id' => $tempFile->id,
                ];

                upload()->rollUp($tempFileId);
            }
        }

        $bgsCollection->update($attributes);

        return $bgsCollection->refresh();
    }

    public function updateMainBackground(BgsCollection $bgsCollection, int $mainBackgroundId = 0): void
    {
        if ($mainBackgroundId == 0) {
            $notDeleteScope = new NotDeleteScope();

            /** @var BgsBackground $background */
            $background = $bgsCollection->backgrounds()
                ->addScope($notDeleteScope)
                ->first();

            if (null != $background) {
                $mainBackgroundId = $background->entityId();
            }
        }

        $bgsCollection->update(['main_background_id' => $mainBackgroundId]);
    }

    public function deleteBgsCollection(User $context, int $id): bool
    {
        policy_authorize(BgsCollectionPolicy::class, 'delete', $context);

        $bgsCollection = $this->find($id);
        $this->checkCanUpdate($bgsCollection, 'delete');

        return $bgsCollection->update(['is_deleted' => BgsCollection::IS_DELETED]);
    }

    public function deleteBackground(User $context, int $id): bool
    {
        policy_authorize(BgsCollectionPolicy::class, 'update', $context);

        /** @var BgsBackground $bgsBackground */
        $bgsBackground = BgsBackground::query()->findOrFail($id);

        $this->checkCanUpdate($bgsBackground->bgsCollection);

        return $bgsBackground->update(['is_deleted' => BgsBackground::IS_DELETED]);
    }

    /**
     * @param BgsCollection $bgsCollection
     * @param string        $action
     *
     * @throws ValidationException
     */
    private function checkCanUpdate(BgsCollection $bgsCollection, string $action = 'update'): void
    {
        $this->checkIsDeleted($bgsCollection);

        if ($bgsCollection->view_only) {
            throw ValidationException::withMessages([
                __p('backgroundstatus::validation.cant_action_default_background_collection', ['action' => $action]),
            ]);
        }

        if ($action == 'delete') {
            if ($bgsCollection->is_default) {
                throw ValidationException::withMessages([
                    __p('backgroundstatus::validation.cant_action_default_background_collection', ['action' => $action]),
                ]);
            }
        }
    }

    /**
     * @param BgsCollection $bgsCollection
     *
     * @throws ValidationException
     */
    private function checkIsDeleted(BgsCollection $bgsCollection): void
    {
        if ($bgsCollection->is_deleted) {
            throw ValidationException::withMessages([
                __p('backgroundstatus::validation.background_collection_already_deleted'),
            ]);
        }
    }
}
