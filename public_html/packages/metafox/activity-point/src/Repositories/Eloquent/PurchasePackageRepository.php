<?php

namespace MetaFox\ActivityPoint\Repositories\Eloquent;

use MetaFox\ActivityPoint\Models\PackagePurchase as Model;
use MetaFox\ActivityPoint\Repositories\PurchasePackageRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class PurchasePackageRepository.
 *
 * @method Model find($id, $columns = ['*'])
 * @method Model getModel()
 */
class PurchasePackageRepository extends AbstractRepository implements PurchasePackageRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }
}
