<?php

namespace MetaFox\Form;

use MetaFox\Platform\Contracts\User;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;

/**
 * Trait GenderTrait.
 */
trait GenderTrait
{
    /**
     * @param  User                             $context
     * @param  array<string, mixed>|null        $extra
     * @return array<int, array<string, mixed>>
     */
    public function getGenders(User $context, ?array $extra = null): array
    {
        return resolve(UserGenderRepositoryInterface::class)->getForForms($context, $extra);
    }

    /**
     * @param  User                             $context
     * @return array<int, array<string, mixed>>
     */
    public function getDefaultGenders(User $context): array
    {
        $customGenders = $this->getCustomGenders($context);
        $customOption = [
            [
                'label' => __p('user::phrase.custom'),
                'value' => 0,
            ],
        ];

        $genders = $this->getGenders($context, [
            ['is_custom', '=', 0],
        ]);

        if (count($customGenders) > 0) {
            return array_merge($genders, $customOption);
        }

        return $genders;
    }

    /**
     * @param  User                             $context
     * @return array<int, array<string, mixed>>
     */
    public function getCustomGenders(User $context): array
    {
        return $this->getGenders($context, [
            ['is_custom', '=', 1],
        ]);
    }
}
