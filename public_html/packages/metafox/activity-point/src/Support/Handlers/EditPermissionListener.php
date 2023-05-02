<?php

namespace MetaFox\ActivityPoint\Support\Handlers;

use MetaFox\Form\Builder;
use MetaFox\Form\FormField;
use MetaFox\Yup\Yup;

class EditPermissionListener
{
    public static function maximumActivityPointsAdminCanAdjust(string $name, string $label, string $description): FormField
    {
        return Builder::text($name)
            ->required(false)
            ->label(__p($label))
            ->description(__p($description))
            ->yup(
                Yup::number()->min(1)
            );
    }

    public static function periodTimeAdminAdjustActivityPoints(string $name, string $label, string $description): FormField
    {
        return Builder::choice($name)
            ->required(false)
            ->label(__p($label))
            ->description(__p($description))
            ->options([
                [
                    'label' => __p('activitypoint::phrase.per_day'),
                    'value' => 1,
                ],
                [
                    'label' => __p('activitypoint::phrase.per_week'),
                    'value' => 2,
                ],
                [
                    'label' => __p('activitypoint::phrase.per_month'),
                    'value' => 3,
                ],
                [
                    'label' => __p('activitypoint::phrase.per_year'),
                    'value' => 4,
                ],
            ]);
    }
}
