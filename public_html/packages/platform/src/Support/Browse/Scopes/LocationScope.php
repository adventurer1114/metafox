<?php

namespace MetaFox\Platform\Support\Browse\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LocationScope.
 */
class LocationScope extends BaseScope
{
    public const DEFAULT_COUNTRY_FIELD = 'country_iso';

    protected ?string $country = null;

    protected ?string $countryField = null;

    protected ?string $table = null;

    /**
     * @param string|null $country
     * @return $this
     */
    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setCountryField(string $field): static
    {
        $this->countryField = $field;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryField(): ?string
    {
        return $this->countryField ?? self::DEFAULT_COUNTRY_FIELD;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $this->getTable() ?? $model->getTable();

        $this->buildCountryCondition($builder, $table);
    }

    protected function buildCountryCondition(Builder $builder, string $table)
    {
        $country = $this->getCountry();

        if (null !== $country) {
            $builder->where($this->alias($table, $this->getCountryField()), $country);
        }
    }
}
