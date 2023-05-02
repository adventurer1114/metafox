<?php

namespace MetaFox\Profile\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Profile\Repositories\Eloquent\SectionRepository;
use MetaFox\Profile\Repositories\FieldRepositoryInterface;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\Profile\Repositories\SectionRepositoryInterface;
use MetaFox\Profile\Repositories\StructureRepositoryInterface;
use MetaFox\Profile\Repositories\Eloquent\FieldRepository;
use MetaFox\Profile\Repositories\Eloquent\ProfileRepository;
use MetaFox\Profile\Repositories\Eloquent\StructureRepository;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Providers/PackageServiceProvider.stub.
 */

/**
 * Class PackageServiceProvider.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    public array $bindings = [
        FieldRepositoryInterface::class     => FieldRepository::class,
        ProfileRepositoryInterface::class   => ProfileRepository::class,
        StructureRepositoryInterface::class => StructureRepository::class,
        SectionRepositoryInterface::class   => SectionRepository::class,
    ];
}
