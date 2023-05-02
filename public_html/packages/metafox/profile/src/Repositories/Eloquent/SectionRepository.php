<?php

namespace MetaFox\Profile\Repositories\Eloquent;

use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Models\Section;
use MetaFox\Profile\Repositories\SectionRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class SectionRepository.
 */
class SectionRepository extends AbstractRepository implements SectionRepositoryInterface
{
    public function model()
    {
        return Section::class;
    }

    /**
     * @inheritDoc
     */
    public function getSectionForForm(): array
    {
        $data     = [];
        $sections = $this->getModel()->newQuery()->get();
        foreach ($sections as $section) {
            /* @var Section $section */
            $data[] = [
                'value' => $section->entityId(),
                'label' => $section->label,
            ];
        }

        return $data;
    }

    public function deleteOrMoveToNewSection(User $user, array $attribute): bool
    {
        $sectionId    = $attribute['section_id'];
        $newSectionId = $attribute['new_section_id'] ?? 0;
        $section      = $this->find($sectionId);

        if ($newSectionId > 0) {
            //move to new section
            $section->fields()->update(['section_id' => $newSectionId]);

            return (bool) $section->delete();
        }

        $section->fields()->delete();

        return (bool) $section->delete();
    }
}
