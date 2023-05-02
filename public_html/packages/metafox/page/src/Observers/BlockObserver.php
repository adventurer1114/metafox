<?php

namespace MetaFox\Page\Observers;

use MetaFox\Page\Models\Block as Model;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;

class BlockObserver
{
    public function created(Model $model): void
    {
        $service = resolve(PageMemberRepositoryInterface::class);
        $service->deletePageMember($model->owner, $model->page->entityId(), $model->userId());
    }
}
