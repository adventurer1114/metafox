<?php

namespace MetaFox\Form;

use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Form\Constants as MetaFoxForm;

/**
 * Trait RelationTrait.
 */
trait RelationTrait
{
    /**
     * @return array
     */
    public function getRelations(): array
    {
        $phpfoxRelations = MetaFoxForm::getRelations();

        $data            = [];

        foreach ($phpfoxRelations as $key => $name) {
            $data[] = [
                'value' => $key,
                'label' => $name,
            ];
        }

        return $data;
    }

    /**
     * @return array<int>
     */
    public function getWithRelations(): array
    {
        return [
            MetaFoxConstant::RELATION_ENGAGED,
            MetaFoxConstant::RELATION_MARRIED,
            MetaFoxConstant::RELATION_IN_A_OPEN_RELATIONSHIP,
            MetaFoxConstant::RELATION_IN_A_RELATIONSHIP,
            MetaFoxConstant::RELATION_IN_A_RELATIONSHIP,
        ];
    }
}
