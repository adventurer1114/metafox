<?php

namespace MetaFox\BackgroundStatus\Repositories\Eloquent;

use Illuminate\Support\Arr;
use MetaFox\BackgroundStatus\Models\BgsBackground;
use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\BackgroundStatus\Repositories\BgsBackgroundRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class BgsBackgroundRepository.
 *
 * @method BgsBackground getModel()
 * @method BgsBackground find($id, $columns = ['*'])
 * @ignore
 * @codeCoverageIgnore
 */
class BgsBackgroundRepository extends AbstractRepository implements BgsBackgroundRepositoryInterface
{
    public function model(): string
    {
        return BgsBackground::class;
    }

    public function uploadBackgrounds(User $context, BgsCollection $bgsCollection, array $attributes): void
    {
        $newBackgrounds = array_filter($attributes, function ($item) {
            return $item['status'] == MetaFoxConstant::FILE_CREATE_STATUS;
        });

        $removedBackgrounds = array_filter($attributes, function ($item) {
            return $item['status'] == MetaFoxConstant::FILE_REMOVE_STATUS;
        });

        $this->removeBackgrounds($bgsCollection->entityId(), $removedBackgrounds);

        $this->createBackgrounds($bgsCollection->entityId(), $newBackgrounds);
    }

    protected function createBackgrounds(int $collectionId, array $newBackgrounds): void
    {
        if (empty($newBackgrounds)) {
            return;
        }

        $newOrdering = $this->getNextOrdering($collectionId);

        foreach ($newBackgrounds as $newBackground) {
            $tempFileId = Arr::get($newBackground, 'temp_file');

            if (!$tempFileId) {
                continue;
            }

            $tempFile = upload()->getFile($tempFileId);
            $model    = new BgsBackground();

            $model->fill([
                'collection_id' => $collectionId,
                'image_file_id' => $tempFile->entityId(),
                'ordering'      => $newOrdering,
            ]);

            $success = $model->save();

            if ($success) {
                $newOrdering++;
            }

            upload()->rollUp($tempFileId);
        }
    }

    protected function removeBackgrounds(int $collectionId, array $removedBackgrounds): void
    {
        $backgroundIds = Arr::pluck($removedBackgrounds, 'id');

        if (empty($backgroundIds)) {
            return;
        }

        $backgrounds = $this->getModel()->newModelQuery()
            ->whereIn('id', $backgroundIds)
            ->get();

        if (0 === $backgrounds->count()) {
            return;
        }

        foreach ($backgrounds as $background) {
            $background->update(['is_deleted' => 1]);
        }
    }

    protected function getNextOrdering(int $collectionId): int
    {
        $lastBackgrounds = $this->getModel()->newModelQuery()
            ->where([
                'collection_id' => $collectionId,
            ])
            ->orderByDesc('ordering')
            ->first();

        if (null === $lastBackgrounds) {
            return 1;
        }

        return (int) $lastBackgrounds->ordering + 1;
    }
}
