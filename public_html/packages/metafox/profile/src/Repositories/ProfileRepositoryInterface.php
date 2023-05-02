<?php

namespace MetaFox\Profile\Repositories;

use MetaFox\Form\AbstractForm;
use MetaFox\Platform\MetaFoxConstant;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Profile.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface ProfileRepositoryInterface
{
    public function loadEditFields(AbstractForm $form, $user, ?string $resolution = null): void;

    public function loadEditRules(\ArrayObject $rules);

    public function saveValues($user, array $input): void;

    public function getFieldNames(): array;

    /**
     * @return array
     *               denormalize to array
     */
    public function denormalize($user): array;

    public function viewSections($user, \ArrayObject $output): void;
}
