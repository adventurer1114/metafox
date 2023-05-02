<?php

namespace MetaFox\Page\Http\Resources\v1\PageInvite;

use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('page-invite')
            ->apiParams(['page_id' => ':id']);

        $this->add('addItem')
            ->apiUrl('core/mobile/form/page.invite.store/:id');

        $this->add('acceptInvite')
            ->apiUrl('page-invite')
            ->asPut()
            ->apiParams(['page_id' => ':id', 'accept' => 1]);

        $this->add('declineInvite')
            ->apiUrl('page-invite')
            ->asPut()
            ->apiParams(['page_id' => ':id', 'accept' => 0]);

        $this->add('cancelInvite')
            ->apiUrl('page-invite')
            ->asDelete()
            ->apiParams(['page_id' => ':page_id', 'user_id' => ':user_id']);

    }
}
