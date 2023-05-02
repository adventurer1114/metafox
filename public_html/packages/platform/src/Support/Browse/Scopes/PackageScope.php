<?php

namespace MetaFox\Platform\Support\Browse\Scopes;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Class PackageScope.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PackageScope extends BaseScope
{
    /** @var string */
    private string $table;

    /** @var array<string> */
    private array $packages;

    /**
     * __construct.
     *
     * @param string        $table
     * @param array<string> $packages
     */
    public function __construct(string $table, array $packages = [])
    {
        $this->setTable($table);
        $this->setPackages($packages);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     *
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * getPackages.
     *
     * @return array<string>
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * setPackages.
     *
     * @param  array<string> $packages
     * @return self
     */
    public function setPackages(array $packages = []): self
    {
        if (empty($packages)) {
            $packages = resolve('core.packages')->getActivePackageAliases();
        }

        $this->packages = $packages;

        return $this;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $this->getTable();
        if (!$table || !Schema::hasColumn($table, 'module_id')) {
            return;
        }

        $builder->whereIn("$table.module_id", $this->getPackages());
    }

    public function applyQueryBuilder(QueryBuilder $builder): void
    {
        $table = $this->getTable();
        if (!$table || !Schema::hasColumn($table, 'module_id')) {
            return;
        }

        $builder->whereIn("$table.module_id", $this->getPackages());
    }
}
