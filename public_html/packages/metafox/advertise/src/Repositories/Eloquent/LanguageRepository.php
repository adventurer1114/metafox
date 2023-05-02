<?php

namespace MetaFox\Advertise\Repositories\Eloquent;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Advertise\Repositories\LanguageRepositoryInterface;
use MetaFox\Advertise\Models\Language;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class LanguageRepository.
 */
class LanguageRepository extends AbstractRepository implements LanguageRepositoryInterface
{
    public function model()
    {
        return Language::class;
    }

    public function addLanguages(Advertise $advertise, ?array $languages): void
    {
        if (null === $languages) {
            $languages = [];
        }

        $advertise->languages()->syncWithPivotValues($languages, ['item_type' => $advertise->entityType()]);
    }

    public function deleteLanguages(Advertise $advertise): void
    {
        $advertise->languages()->sync([]);
    }
}
