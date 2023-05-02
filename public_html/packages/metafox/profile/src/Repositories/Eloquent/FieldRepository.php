<?php

namespace MetaFox\Profile\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Models\Field;
use Illuminate\Support\Arr;
use MetaFox\Profile\Repositories\FieldRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * class FieldRepository.
 */
class FieldRepository extends AbstractRepository implements FieldRepositoryInterface
{
    public function model()
    {
        return Field::class;
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     */
    public function viewFields(array $attributes): Paginator
    {
        $query = $this->buildQueryViewField($attributes);

        return $query
            ->orderBy('id', 'desc')
            ->paginate($attributes['limit'] ?? 100);
    }

    private function buildQueryViewField(array $attributes)
    {
        $name     = Arr::get($attributes, 'name');
        $required = Arr::get($attributes, 'required');
        $active   = Arr::get($attributes, 'active');

        $query = $this->getModel()->newModelQuery();

        if ($name) {
            $searchScope = new SearchScope($name, ['field_name']);
            $query       = $query->addScope($searchScope);
        }

        if (null !== $active) {
            $query->where('is_active', $active);
        }

        if (null !== $required) {
            $query->where('is_required', $required);
        }

        return $query;
    }

    public function getActiveFields(): Collection
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('is_active', 1)
            ->get();
    }
}
