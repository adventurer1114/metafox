<?php

namespace MetaFox\Music\Repositories\Eloquent;

use MetaFox\Music\Jobs\DeleteGenreJob;
use MetaFox\Music\Models\Album;
use MetaFox\Music\Models\Genre;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Repositories\GenreRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractCategoryRepository;

/**
 * Class GenreRepository.
 * @ignore
 * @codeCoverageIgnore
 */
class GenreRepository extends AbstractCategoryRepository implements GenreRepositoryInterface
{
    public function model()
    {
        return Genre::class;
    }

    public function moveToNewCategory(Genre $category, int $newCategoryId, bool $isDelete = false): void
    {
        $totalItem = $category->total_item;
        $parent    = $category?->parentCategory;
        $this->decrementTotalItemCategories($parent, $totalItem);

        $newCategory = $this->find($newCategoryId);
        $songIds     = $category->songs()->pluck('music_songs.id')->toArray();
        $albumsIds   = $category->albums()->pluck('music_albums.id')->toArray();

        //Move blog
        if (!empty($songIds) && $isDelete) {
            $newCategory->songs()->sync($songIds, false);
            $newCategory->albums()->sync($albumsIds, false);
        }

        //update parent_id
        Genre::query()->where('parent_id', '=', $category->entityId())->update([
            'parent_id' => $newCategory->entityId(),
            'level'     => $newCategory->level + 1,
        ]);

        $this->incrementTotalItemCategories($newCategory, $totalItem);
    }

    public function deleteCategory(User $context, int $id, int $newCategoryId): bool
    {
        $category = $this->find($id);

        DeleteGenreJob::dispatch($category, $newCategoryId);

        $this->clearCache();

        return true;
    }

    public function deleteAllBelongTo(Genre $genre): bool
    {
        $genre->songs()->each(function (Song $song) {
            $song->delete();
        });

        $genre->albums()->each(function (Album $album) {
            $album->delete();
        });

        $genre->subCategories()->each(function (Genre $item) {
            DeleteGenreJob::dispatch($item, 0);
        });

        $this->clearCache();

        return true;
    }
}
