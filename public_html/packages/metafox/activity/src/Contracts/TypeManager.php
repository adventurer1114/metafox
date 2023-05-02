<?php

namespace MetaFox\Activity\Contracts;

use MetaFox\Activity\Models\Feed;
use MetaFox\Platform\Contracts\TypeManagerInterface;

/**
 * Interface TypeManager.
 */
interface TypeManager extends TypeManagerInterface
{
    /**
     * @param  Feed        $feed
     * @return string|null
     */
    public function getTypePhraseWithContext(Feed $feed): ?string;

    /**
     * @return array
     */
    public function getTypes(): array;

    /**
     * @return array
     */
    public function getAbilities(): array;

    /**
     * @return array
     */
    public function getTypeSettings(): array;

    /**
     * @return void
     */
    public function cleanData(): void;
}
