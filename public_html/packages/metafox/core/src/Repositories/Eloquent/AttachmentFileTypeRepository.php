<?php

namespace MetaFox\Core\Repositories\Eloquent;

use MetaFox\Core\Models\AttachmentFileType;
use MetaFox\Core\Repositories\AttachmentFileTypeRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

class AttachmentFileTypeRepository extends AbstractRepository implements AttachmentFileTypeRepositoryInterface
{
    public function model()
    {
        return AttachmentFileType::class;
    }
}
