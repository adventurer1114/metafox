<?php

namespace MetaFox\Photo\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Photo\Models\AlbumItem;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Media;

class ModelUpdatedListener
{
    /**
     * @param  mixed $model
     * @return void
     */
    public function handle($model): void
    {
        if (!$model instanceof Model) {
            return;
        }

        $this->handleUpdateMedia($model);
        $this->handleApprovePhotoGroupOfOwner($model);
    }

    private function handleUpdateMedia(Model $model): void
    {
        if (!$model instanceof Media) {
            return;
        }

        if ($model->isDirty('album_id')) {
            $this->handleUpdateMediaAlbumId($model);
        }

        $this->handleUpdatePhotoGroupProcessingItems($model);
    }

    /**
     * This method only handles the approval process from inside User which has pending mode.
     * For case approve Media item from inside its app, see \MetaFox\Photo\Listeners\ModelApprovedListener.
     */
    private function handleApprovePhotoGroupOfOwner(Model $model): void
    {
        if (!$model instanceof PhotoGroup) {
            return;
        }

        $owner = $model->owner;
        if (!$owner->hasPendingMode()) {
            return;
        }

        if (!$model->isApproved()) {
            return;
        }

        $this->updatePhotoGroupItems($model);
    }

    private function handleUpdateMediaAlbumId(Media $model): void
    {
        $oldAlbumId = $model->getOriginal('album_id');
        if ($oldAlbumId > 0) {
            $albumItem = $model->albumItem;
            if (!$albumItem instanceof AlbumItem) {
                return;
            }

            if ($model->album_id > 0) {
                $albumItem->album_id = $model->album_id;
                $albumItem->save();
            }

            if ($model->album_id === 0) {
                $albumItem->delete();
            }
        }

        if ($oldAlbumId === 0 && $model->album_id > 0) {
            $this->createAlbumItemForMedia($model);
        }
    }

    private function createAlbumItemForMedia(Media $model): AlbumItem
    {
        $itemData = [
            'album_id'  => $model->album_id,
            'group_id'  => $model->group_id ?? 0,
            'item_type' => $model->entityType(),
            'item_id'   => $model->entityId(),
            'ordering'  => 0, //@todo: should be removed?
        ];

        $albumItem = new AlbumItem();
        $albumItem->fill($itemData);
        $albumItem->save();

        return $albumItem;
    }

    private function updatePhotoGroupItems(PhotoGroup $photoGroup): void
    {
        $pendingItems = $photoGroup->pendingItems()->get()->collect();

        $pendingItems->each(function (PhotoGroupItem $item) {
            $detail = $item->detail;

            // Skip if not Content
            if (!$detail instanceof Content) {
                return true;
            }

            $detail->fill(['is_approved' => 1]);
            $detail->save();
        });
    }

    private function handleUpdatePhotoGroupProcessingItems(Media $media): void
    {
        $photoGroup = $media->group;
        if (!$photoGroup instanceof PhotoGroup) {
            return;
        }

        if ($photoGroup->processingItems()->count()) {
            return;
        }

        app('events')->dispatch('activity.feed.create_from_resource', [$photoGroup], true);
    }
}
