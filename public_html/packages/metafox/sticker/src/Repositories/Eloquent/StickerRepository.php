<?php

namespace MetaFox\Sticker\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Policies\StickerSetPolicy;
use MetaFox\Sticker\Repositories\StickerRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use MetaFox\Sticker\Support\Browse\Scopes\RecentScope;

/**
 * Class StickerRepository.
 * @method Sticker find($id, $columns = ['*'])
 * @method Sticker getModel()
 * @ignore
 * @codeCoverageIgnore
 */
class StickerRepository extends AbstractRepository implements StickerRepositoryInterface
{
    public function model(): string
    {
        return Sticker::class;
    }

    /**
     * @param  User                                       $context
     * @param  array<string, mixed>                       $attributes
     * @return Paginator
     * @throws ValidationException|AuthorizationException
     */
    public function viewStickers(User $context, array $attributes): Paginator
    {
        return $this->getStickerSetRepository()->getStickers($context, $attributes);
    }

    /**
     * @param  User                                         $context
     * @param  int                                          $id
     * @return bool
     * @throws ValidationException | AuthorizationException
     */
    public function deleteSticker(User $context, int $id): bool
    {
        return $this->getStickerSetRepository()->deleteSticker($context, $id);
    }

    /**
     * @param  User                   $context
     * @param  array<string, mixed>   $attributes
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewRecentStickers(User $context, array $attributes): Paginator
    {
        policy_authorize(StickerSetPolicy::class, 'viewAny', $context);

        $recentScope = new RecentScope();

        return $this->getModel()
            ->newModelQuery()
            ->addScope($recentScope->setUser($context))
            ->simplePaginate($attributes['limit'], ['stickers.*']);
    }

    protected function getStickerSetRepository(): StickerSetRepositoryInterface
    {
        return resolve(StickerSetRepositoryInterface::class);
    }

    public function uploadStickers(User $context, StickerSet $stickerSet, array $attributes): void
    {
        $newStickers = array_filter($attributes, function ($item) {
            return $item['status'] == MetaFoxConstant::FILE_CREATE_STATUS;
        });

        $removedStickers = array_filter($attributes, function ($item) {
            return $item['status'] == MetaFoxConstant::FILE_REMOVE_STATUS;
        });

        $this->removeStickers($removedStickers);

        $this->createStickers($stickerSet->entityId(), $newStickers);
    }

    private function createStickers(int $setId, array $newStickers)
    {
        if (empty($newStickers)) {
            return;
        }

        $newOrdering = $this->getNextOrdering($setId);

        foreach ($newStickers as $sticker) {
            $tempFileId = Arr::get($sticker, 'temp_file');

            if (!$tempFileId) {
                continue;
            }

            $tempFile = upload()->getFile($tempFileId);
            $model    = new Sticker();

            $model->fill([
                'set_id'        => $setId,
                'image_file_id' => $tempFile->entityId(),
                'ordering'      => $newOrdering,
                'image_path'    => $tempFile->path,
                'server_id'     => $tempFile->storage_id,
                'view_only'     => false,
            ]);

            $success = $model->save();

            if ($success) {
                $newOrdering++;
            }

            upload()->rollUp($tempFileId);
        }
    }

    private function removeStickers(array $removedStickers)
    {
        $stickerIds = Arr::pluck($removedStickers, 'id');

        if (empty($stickerIds)) {
            return;
        }

        $stickers = $this->getModel()->newModelQuery()
            ->whereIn('id', $stickerIds)
            ->get();

        if (0 === $stickers->count()) {
            return;
        }

        foreach ($stickers as $sticker) {
            $sticker->update(['is_deleted' => 1]);
        }
    }

    private function getNextOrdering(int $setId)
    {
        $lastStickers = $this->getModel()->newModelQuery()
            ->where([
                'set_id' => $setId,
            ])
            ->orderByDesc('ordering')
            ->first();

        if (null === $lastStickers) {
            return 1;
        }

        return (int) $lastStickers->ordering + 1;
    }
}
