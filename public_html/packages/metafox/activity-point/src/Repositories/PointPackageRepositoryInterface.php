<?php

namespace MetaFox\ActivityPoint\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\ActivityPoint\Models\PackagePurchase;
use MetaFox\ActivityPoint\Models\PointPackage as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Interface PointPackageRepositoryInterface.
 * @method Model find($id, $columns = ['*'])
 * @method Model getModel()
 */
interface PointPackageRepositoryInterface
{
    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Paginator
     */
    public function viewPackages(User $context, array $attributes): Paginator;

    /**
     * @param  User  $context
     * @param  int   $id
     * @return Model
     */
    public function viewPackage(User $context, int $id): Model;

    /**
     * @param  User                 $context
     * @param  int                  $id
     * @param  array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public function purchasePackage(User $context, int $id, array $attributes): array;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Paginator
     */
    public function viewPackagesAdmin(User $context, array $attributes): Paginator;

    /**
     * @param  User                 $context
     * @param  array<string, mixed> $attributes
     * @return Model
     */
    public function createPackage(User $context, array $attributes): Model;

    /**
     * @param  User                 $context
     * @param  int                  $id
     * @param  array<string, mixed> $attributes
     * @return Model
     */
    public function updatePackage(User $context, int $id, array $attributes): Model;

    /**
     * @param  User $context
     * @param  int  $id
     * @return bool
     */
    public function deletePackage(User $context, int $id): bool;

    /**
     * @param  User  $context
     * @param  int   $id
     * @return Model
     */
    public function activatePackage(User $context, int $id): Model;

    /**
     * @param  User  $context
     * @param  int   $id
     * @return Model
     */
    public function deactivatePackage(User $context, int $id): Model;

    /**
     * @param  User                 $context
     * @param  Model                $package
     * @param  array<string, mixed> $attributes
     * @return PackagePurchase
     */
    public function initPurchase(User $context, Model $package, array $attributes): PackagePurchase;

    /**
     * @param  PackagePurchase $purchase
     * @return void
     */
    public function onSuccessPurchasePackage(PackagePurchase $purchase): void;

    /**
     * @param  PackagePurchase $purchase
     * @return void
     */
    public function onFailedPurchasePackage(PackagePurchase $purchase): void;

    /**
     * @param  int               $id
     * @return array<int, mixed>
     */
    public function getPurchasePackageMessage(int $id): array;
}
