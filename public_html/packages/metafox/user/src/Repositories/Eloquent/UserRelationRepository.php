<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use MetaFox\Localize\Models\Phrase;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Models\UserRelation;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;

class UserRelationRepository extends AbstractRepository implements UserRelationRepositoryInterface
{
    public function model()
    {
        return UserRelation::class;
    }

    public function getPhraseRepository(): PhraseRepositoryInterface
    {
        return resolve(PhraseRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function viewRelationShips(User $user, array $attributes): Paginator
    {
        $limit = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        return $this->getModel()->newQuery()->simplePaginate($limit);
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function createRelationShip(User $user, array $attributes): UserRelation
    {
        $fileId = null;
        if ($attributes['temp_file'] > 0) {
            $tempFile = upload()->getFile($attributes['temp_file']);
            $fileId   = $tempFile->id;

            // Delete temp file after done
            upload()->rollUp($attributes['temp_file']);
        }

        $phraseParams = [
            'locale'     => Arr::get($attributes, 'locale', 'en'),
            'package_id' => Arr::get($attributes, 'package_id'),
            'group'      => Arr::get($attributes, 'group'),
        ];

        $phraseTitle = $this->getPhraseRepository()->createPhrase(array_merge($phraseParams, [
            'name' => Arr::get($attributes, 'phrase_var'),
            'text' => Arr::get($attributes, 'title'),
        ]));

        $relationParams = [
            'phrase_var'    => $phraseTitle->key,
            'is_active'     => Arr::get($attributes, 'is_active'),
            'is_custom'     => Arr::get($attributes, 'is_custom', 1),
            'image_file_id' => $fileId,
        ];

        $relation = new UserRelation();
        $relation->fill($relationParams)->save();

        return $relation;
    }

    /**
     * @inheritDoc
     */
    public function activeRelation(int $id): UserRelation
    {
        $item = $this->find($id);

        $item->update(['is_active' => $item->is_active ? 0 : 1]);

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getRelations(): Collection
    {
        return $this->getModel()->newQuery()
            ->where('is_active', 1)->get();
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function updateRelationShip(User $user, array $attributes): UserRelation
    {
        $id       = Arr::get($attributes, 'id');
        $locale   = Arr::get($attributes, 'locale', 'en');
        $text     = Arr::get($attributes, 'title', '');
        $isActive = Arr::get($attributes, 'is_active');
        /** @var UserRelation $relation */
        $relation = $this->find($id);
        $phrase   = $this->getPhraseRepository()->findWhere([
            ['locale', '=', $locale],
            ['key', '=', $relation->phrase_var],
        ])->first();

        if (!$phrase instanceof Phrase) {
            throw (new ModelNotFoundException())->setModel(Phrase::class);
        }

        $fileId = null;
        if ($attributes['temp_file'] > 0) {
            $tempFile = upload()->getFile($attributes['temp_file']);
            $fileId   = $tempFile->id;

            // Delete temp file after done
            upload()->rollUp($attributes['temp_file']);
        }

        $this->getPhraseRepository()->updatePhrase($phrase->entityId(), ['text' => $text]);
        $relationParams = [
            'is_active'     => $isActive,
            'image_file_id' => $fileId,
        ];

        $relation->update($relationParams);

        return $relation->refresh();
    }

    /**
     * @inheritDoc
     */
    public function deleteRelation(User $context, int $id): bool
    {
        $relation = $this->find($id);

        if (!$relation instanceof UserRelation || !$relation->is_custom) {
            abort(401, __p('phrase.permission_deny'));
        }

        $this->getPhraseRepository()->deleteWhere(['key' => $relation->phrase_var]);

        return (bool) $relation->delete();
    }
}
