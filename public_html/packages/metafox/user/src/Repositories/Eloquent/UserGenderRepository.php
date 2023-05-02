<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Support\Facades\Language;
use MetaFox\Localize\Models\Phrase;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\User\Models\UserGender as Model;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;

/**
 * Class UserGenderRepository.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserGenderRepository extends AbstractRepository implements UserGenderRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }

    /**
     * @param  string $phrase
     * @return Model
     */
    public function findGenderByPhrase(string $phrase): ?Model
    {
        return $this->getModel()->newModelQuery()->where('phrase', $phrase)->first();
    }

    /**
     * @inheritDoc
     */
    public function viewGenders(User $context, array $attributes): Paginator
    {
        return $this->getModel()->newModelQuery()->simplePaginate();
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function createGender(User $context, array $attributes): Model
    {
        $phraseParams = [
            'locale'     => Arr::get($attributes, 'locale', 'en'),
            'package_id' => Arr::get($attributes, 'package_id'),
            'group'      => Arr::get($attributes, 'group'),
            'name'       => Arr::get($attributes, 'name'),
            'text'       => Arr::get($attributes, 'text'),
        ];

        $phrase = $this->getPhraseRepository()->createPhrase($phraseParams);

        $genderParams = [
            'phrase'    => $phrase->key,
            'is_custom' => Arr::get($attributes, 'is_custom', 1),
        ];

        $gender = new Model();
        $gender->fill($genderParams);
        $gender->save();

        Artisan::call('cache:reset');

        return $gender;
    }

    /**
     * @inheritDoc
     * @throws ValidationException
     */
    public function updateGender(User $context, int $id, array $attributes): Model
    {
        /** @var Model $gender */
        $gender = $this->find($id);
        $text   = Arr::get($attributes, 'text', '');

        $phrase = $this->getPhraseRepository()->findWhere([
            ['locale', '=', 'en'],
            ['key', '=', $gender->phrase],
        ])->first();

        if (!$phrase instanceof Phrase) {
            throw (new ModelNotFoundException())->setModel(Phrase::class);
        }

        $this->getPhraseRepository()->updatePhrase($phrase->entityId(), ['text' => $text]);

        Artisan::call('cache:reset');

        return $gender;
    }

    /**
     * @inheritDoc
     */
    public function deleteGender(User $context, int $id): bool
    {
        $gender = $this->find($id);

        if (!$gender instanceof Model || !$gender->is_custom) {
            abort(401, __p('phrase.permission_deny'));
        }

        $this->getPhraseRepository()->deleteWhere(['key' => $gender->phrase]);

        return (bool) $gender->delete();
    }

    /**
     * @inheritDoc
     */
    public function getForForms(User $context, ?array $where = null): array
    {
        $query = $this->getModel()->newModelQuery();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->get()
            ->collect()
            ->map(function (Model $gender, $key) {
                return [
                    'label' => $gender->name,
                    'value' => $gender->entityId(),
                ];
            })
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getSuggestion(array $params): array
    {
        $search   = Arr::get($params, 'q', null);
        $isCustom = Arr::get($params, 'is_custom', null);
        $query    = $this->getModel()->newModelQuery();

        $searchScope = new SearchScope($search);
        $searchScope->setFields(['name'])->setSearchText($search);
        $query = $query->addScope($searchScope);

        if (null !== $isCustom) {
            $query->where('is_custom', $isCustom);
        }

        return $query->orderBy('id')
            ->limit($params['limit'])
            ->get()
            ->collect()
            ->map(function (Model $gender, int $key) {
                return [
                    'label' => $gender->name,
                    'value' => $gender->entityId(),
                ];
            })
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function viewGendersForAdmin(User $context, array $attributes): LengthAwarePaginator
    {
        $query = $this->buildQueryViewGendersAdmin($attributes);

        return $query->paginate($attributes['limit']);
    }

    private function buildQueryViewGendersAdmin(array $attributes)
    {
        $search        = Arr::get($attributes, 'q');
        $defaultLocale = Language::getDefaultLocaleId();

        $query = $this->getModel()->newModelQuery()
            ->select(['user_gender.*']);

        if ($search) {
            $searchScope = new SearchScope(
                $search,
                ['user_gender.phrase', 'ps.text']
            );
            $searchScope->setTableField('phrase');
            $searchScope->setJoinedTable('phrases');
            $searchScope->setAliasJoinedTable('ps');
            $searchScope->setJoinedField('key');
            $query->where('ps.locale', '=', $defaultLocale);
            $query = $query->addScope($searchScope);
        }

        return $query;
    }

    public function getPhraseRepository(): PhraseRepositoryInterface
    {
        return resolve(PhraseRepositoryInterface::class);
    }

    public function getGenderOptions(): array
    {
        return Model::query()
            ->get()
            ->map(function ($gender) {
                return ['value' => $gender->entityId(), 'label' => $gender->name];
            })
            ->toArray();
    }

    public function viewAllGenders(array $ids = []): Collection
    {
        $query = Model::query();

        if (count($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->get()
            ->map(function ($gender) {
                $gender->phrase = __p($gender->phrase);

                return $gender;
            });
    }
}
