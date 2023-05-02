<?php

namespace MetaFox\Saved\Repositories;

use MetaFox\Platform\Contracts\HasSavedItem;

interface SavedSearchRepositoryInterface
{
    /**
     * @param  HasSavedItem $item
     * @return void
     */
    public function createdBy(HasSavedItem $item): void;

    /**
     * @param  HasSavedItem $item
     * @return void
     */
    public function updatedBy(HasSavedItem $item): void;
}
