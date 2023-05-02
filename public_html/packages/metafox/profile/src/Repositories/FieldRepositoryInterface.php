<?php

namespace MetaFox\Profile\Repositories;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Form\AbstractForm;
use MetaFox\Profile\Models\Field;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Contracts\Pagination\Paginator;

/**
 * Interface Field.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface FieldRepositoryInterface
{
    /**
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     */
    public function viewFields(array $attributes): Paginator;

    /**
     * @return Collection
     */
    public function getActiveFields(): Collection;

    /**
     * @param  array<int> $orderIds
     * @return bool
     */
    public function orderFields(array $orderIds): bool;

    /**
     * @param  array $attributes
     * @return Field
     */
    public function createField(array $attributes): Field;
}
