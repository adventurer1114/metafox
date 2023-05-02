<?php

namespace MetaFox\Sticker\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\File as FileSupport;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Support\FileSystem\UploadFile;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Models\StickerUserValue;
use MetaFox\Sticker\Policies\StickerSetPolicy;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use MetaFox\Sticker\Support\Browse\Scopes\NotDeleteScope;
use ZipArchive;

/**
 * Class StickerSetRepository.
 * @method StickerSet find($id, $columns = ['*'])
 * @method StickerSet getModel()
 * @ignore
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StickerSetRepository extends AbstractRepository implements StickerSetRepositoryInterface
{
    public function model(): string
    {
        return StickerSet::class;
    }

    public function viewStickerSetsForAdmin(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        $notDeleteScope = new NotDeleteScope();

        return $this->getModel()->newQuery()
            ->addScope($notDeleteScope)
            ->simplePaginate($attributes['limit']);
    }

    public function viewStickerSetsForFE(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        $notDeleteScope = new NotDeleteScope();

        return $this->getModel()->newQuery()
            ->addScope($notDeleteScope)
            ->with([
                'thumbnail',
                'stickers' => function (HasMany $query) use ($notDeleteScope) {
                    return $query->addScope($notDeleteScope);
                },
            ])
            ->where('sticker_sets.total_sticker', '>', 0)
            ->where('sticker_sets.is_active', StickerSet::IS_ACTIVE)
            ->simplePaginate($attributes['limit'], ['sticker_sets.*']);
    }

    public function viewStickerSetsUserForFE(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        return $this->getModel()->newModelQuery()
            ->with([
                'thumbnail',
                'stickers' => function (HasMany $query) {
                    return $query->addScope(new NotDeleteScope());
                },
            ])
            ->join('sticker_user_values as suv', function (JoinClause $join) use ($context) {
                $join->on('sticker_sets.id', '=', 'suv.set_id');
                $join->where('suv.user_id', $context->entityId());
                $join->where('sticker_sets.is_active', StickerSet::IS_ACTIVE);
                $join->where('sticker_sets.is_deleted', 0);
                $join->where('sticker_sets.total_sticker', '>', 0);
            })
            ->orderBy('suv.id')
            ->simplePaginate($attributes['limit'], ['sticker_sets.*']);
    }

    public function checkSickerSetAdded(int $userId, int $stickerSetId): bool
    {
        return StickerUserValue::query()
            ->where('user_id', $userId)
            ->where('set_id', $stickerSetId)
            ->exists();
    }

    public function createStickerSet(User $context, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'create', $context);

        if (isset($attributes['sticker_temp_file'])) {
            foreach ($attributes['sticker_temp_file'] as $tempFileId) {
                $tempFile = upload()->getFile($tempFileId);
                $sticker  = [
                    'image_file_id' => $tempFile->id,
                ];

                if (isset($attributes['view_only'])) {
                    $sticker = array_merge($sticker, ['view_only' => $attributes['view_only']]);
                }

                $attributes['stickers'][] = $sticker;

                upload()->rollUp($tempFileId);
            }
        }

        /** @var StickerSet $stickerSet */
        $stickerSet = parent::create($attributes);

        return $stickerSet->refresh();
    }

    public function updateStickerSet(User $context, int $id, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);

        $stickerSet = $this->find($id);

        $this->checkCanUpdate($stickerSet);

        if (isset($attributes['sticker_temp_file'])) {
            foreach ($attributes['sticker_temp_file'] as $tempFileId) {
                $tempFile                 = upload()->getFile($tempFileId);
                $attributes['stickers'][] = [
                    'image_file_id' => $tempFile->id,
                ];

                upload()->rollUp($tempFileId);
            }
        }

        $stickerSet->update($attributes);

        return $stickerSet->refresh();
    }

    public function viewStickerSet(User $context, int $id): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        $stickerSet->load([
            'stickers' => function (HasMany $query) {
                $notDeleteScope = new NotDeleteScope();

                return $query->addScope($notDeleteScope);
            },
        ]);

        return $stickerSet;
    }

    public function deleteStickerSet(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'delete', $context);

        $stickerSet = $this->find($id);
        $this->checkCanUpdate($stickerSet, 'delete');

        return $stickerSet->update(['is_deleted' => StickerSet::IS_DELETED]);
    }

    public function updateActive(User $context, int $id, int $isActive): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        return $stickerSet->update(['is_active' => $isActive]);
    }

    public function deleteSticker(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);

        /** @var Sticker $sticker */
        $sticker = Sticker::query()->findOrFail($id);

        $this->checkCanUpdate($sticker->stickerSet);

        return $sticker->update(['is_deleted' => Sticker::IS_DELETED]);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addUserStickerSet(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        $params = [
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'set_id'    => $id,
        ];

        $checkExist = StickerUserValue::query()
            ->where($params)
            ->exists();

        if ($checkExist) {
            throw ValidationException::withMessages([__p('sticker::validation.you_already_download_this_sticker_set')]);
        }

        $stickerUserSet = new StickerUserValue($params);

        return $stickerUserSet->save();
    }

    public function deleteUserStickerSet(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        /** @var StickerUserValue $stickerUserSet */
        $stickerUserSet = StickerUserValue::query()->where([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'set_id'    => $id,
        ])->firstOrFail();

        return (bool) $stickerUserSet->delete();
    }

    public function getStickers(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        $stickerSet = $this->find($attributes['sticker_set_id']);
        $this->checkIsDeleted($stickerSet);

        $notDeleteScope = new NotDeleteScope();

        return $stickerSet->stickers()
            ->addScope($notDeleteScope)
            ->simplePaginate($attributes['limit']);
    }

    public function markAsDefault(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        $countDefault = $this->getModel()->newQuery()
            ->where('is_default', StickerSet::IS_DEFAULT)
            ->count('id');

        if (StickerSet::MAX_DEFAULT == $countDefault) {
            throw ValidationException::withMessages([__p('sticker::validation.you_can_set_only_two_default_sticker_set')]);
        }

        return $stickerSet->update(['is_default' => StickerSet::IS_DEFAULT]);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function removeDefault(User $context, int $id): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);
        $stickerSet = $this->find($id);

        $this->checkIsDeleted($stickerSet);

        return $stickerSet->update(['is_default' => 0]);
    }

    public function updateThumbnail(StickerSet $stickerSet, int $thumbnailId = 0): void
    {
        if ($thumbnailId == 0) {
            $notDeleteScope = new NotDeleteScope();

            /** @var Sticker $sticker */
            $sticker = $stickerSet->stickers()
                ->addScope($notDeleteScope)
                ->first();

            if (null != $sticker) {
                $thumbnailId = $sticker->entityId();
            }
        }

        $stickerSet->update(['thumbnail_id' => $thumbnailId]);
    }

    public function orderingStickerSet(User $context, array $orders): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);

        foreach ($orders as $id => $order) {
            StickerSet::query()->where('id', $id)->update(['ordering' => $order]);
        }

        return true;
    }

    public function orderingSticker(User $context, array $orders): bool
    {
        policy_authorize(StickerSetPolicy::class, 'update', $context);

        foreach ($orders as $id => $order) {
            Sticker::query()->where('id', $id)->update(['ordering' => $order]);
        }

        return true;
    }

    /**
     * @param StickerSet $stickerSet
     * @param string     $action
     *
     * @throws ValidationException
     */
    private function checkCanUpdate(StickerSet $stickerSet, string $action = 'update'): void
    {
        $this->checkIsDeleted($stickerSet);

        if ($stickerSet->view_only) {
            throw ValidationException::withMessages([
                __p('sticker::validation.cant_action_default_sticker_set', ['action' => $action]),
            ]);
        }

        if ($action == 'delete') {
            if ($stickerSet->is_default) {
                throw ValidationException::withMessages([
                    __p('sticker::validation.cant_action_default_sticker_set', ['action' => $action]),
                ]);
            }
        }
    }

    /**
     * @param StickerSet $stickerSet
     *
     * @throws ValidationException
     */
    private function checkIsDeleted(StickerSet $stickerSet): void
    {
        if ($stickerSet->is_deleted) {
            throw ValidationException::withMessages([
                __p('sticker::validation.sticker_set_already_deleted'),
            ]);
        }
    }

    /**
     * @param int $stickerId
     *
     * @return Sticker|null
     */
    public function getSticker(int $stickerId): ?Sticker
    {
        /** @var Sticker $sticker */
        $sticker = Sticker::query()->find($stickerId);

        return $sticker;
    }

    public function installStickerSet(User $context, array $attributes): StickerSet
    {
        policy_authorize(StickerSetPolicy::class, 'create', $context);

        if (array_key_exists('file', $attributes)) {
            $attributes['stickers'] = [];
            if ($attributes['file'] instanceof UploadedFile) {
                $attributes['stickers'] = $this->uploadStickerByZip($attributes['file']);
            }
        }

        $stickerSet = parent::create($attributes);
        $stickerSet->refresh();

        return $stickerSet;
    }

    /**
     * @param  UploadedFile                     $file
     * @return array<int, array<string, mixed>>
     */
    public function uploadStickerByZip(UploadedFile $file): array
    {
        $filePath = $file->getRealPath();
        if ($filePath === false) {
            return [];
        }

        $tmpFolder = FileSupport::dirname($filePath);
        $extractTo = $tmpFolder . DIRECTORY_SEPARATOR . md5($file->getFilename());
        $archive   = new ZipArchive();
        if ($archive->open($filePath) === true) {
            $archive->extractTo($extractTo);
            $archive->close();
        }

        $stickers = [];
        foreach (FileSupport::files($extractTo) as $temp) {
            $uploadedTemp = UploadFile::pathToUploadedFile($temp->getRealPath() ?: MetaFoxConstant::EMPTY_STRING);
            if ($uploadedTemp === false) {
                continue;
            }
            $storageFile = upload()
                ->setPath('sticker')
                ->setStorage('asset')
                ->storeFile($uploadedTemp);

            $stickers[] = [
                'image_path'    => $storageFile->path,
                'server_id'     => $storageFile->storage_id,
                'image_file_id' => $storageFile->id,
                'view_only'     => false,
            ];
        }

        return $stickers;
    }
}
