<?php

namespace MetaFox\User\Support\Browse\Scopes\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;
use MetaFox\Profile\Repositories\FieldRepositoryInterface;

class CustomFieldScope extends BaseScope
{
    /**
     * @return Collection
     */
    public static function getAllowCustomFields(): Collection
    {
        return resolve(FieldRepositoryInterface::class)
            ->getActiveFields();
    }

    private function getAllowCustomFieldIds(): array
    {
        return Arr::pluck(self::getAllowCustomFields(), 'id');
    }

    /**
     * @var array
     */
    private array $customFields = [];

    /**
     * @param  array            $customFields
     * @return CustomFieldScope
     */
    public function setCustomFields(array $customFields): self
    {
        $this->customFields = $customFields;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    /**
     * @param Builder $builder
     * @param Model   $model
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $customFields        = $this->getCustomFields();
        $allowCustomFieldIds = $this->getAllowCustomFieldIds();

        foreach ($customFields as $field) {
            $fieldId    = Arr::get($field, 'id');
            $fieldValue = Arr::get($field, 'value');

            if (!in_array($fieldId, $allowCustomFieldIds)) {
                continue;
            }

            $this->handleSearchCustomFields($builder, $fieldId, $fieldValue);
        }
    }

    private function handleSearchCustomFields(Builder $builder, int $fieldId, string $fieldValue): void
    {
        $customValueAlias = 'ucv_' . $fieldId;
        $customFieldAlias = 'ufv_' . $fieldId;

        $builder->join('user_custom_value as ' . $customValueAlias, function (JoinClause $join) use ($customValueAlias) {
            $join->on($customValueAlias . '.user_id', '=', 'users.id');
        });

        $builder->where($customValueAlias . '.field_id', $fieldId);
        $builder->where($customValueAlias . '.field_value_text', $this->likeOperator(), '%' . $fieldValue . '%');

        $builder->join('user_custom_fields as ' . $customFieldAlias, function (JoinClause $join) use ($customFieldAlias, $customValueAlias) {
            $join->on($customFieldAlias . '.id', '=', $customValueAlias . '.field_id');
        });
    }
}
