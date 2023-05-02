<?php

namespace MetaFox\Platform\Contracts;

/**
 * Interface HasSavedItem.
 * @package MetaFox\Platform\Contracts
 */
interface HasSavedItem extends Entity
{
    /**
     * [title, image, item_type_name, total_photo, user(UserEntity), link].
     * @return array<string, mixed>
     */
    public function toSavedItem(): array;
}
