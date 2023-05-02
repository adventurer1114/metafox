<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Traits\Eloquent\Model;

/**
 * Trait HasEntity.
 *
 * @property string $primaryKey
 */
trait HasEntity
{
    public function entityId(): int
    {
        return $this->{$this->primaryKey}??0;
    }

    public function entityType(): string
    {
        return self::ENTITY_TYPE ?? '';
    }

    public function moduleName(): string
    {
        $moduleName = getFrontendAliasByEntityType($this->entityType());

        if (null !== $moduleName) {
            return $moduleName;
        }

        return $this->entityType();
    }
}
