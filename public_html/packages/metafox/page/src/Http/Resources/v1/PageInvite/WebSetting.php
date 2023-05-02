<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Http\Resources\v1\PageInvite;

use MetaFox\Platform\Resource\WebSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * GroupMember Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class WebSetting extends Setting
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
            ->apiParams(['page_id' => ':page_id', 'user_id' => ':user_id'])
            ->confirm([
                'title'        => __p('page::phrase.confirm_cancel_invite_title'),
                'message'      => 'confirm_cancel_invite_desc',
                'phraseParams' => [
                    'userName' => ':user.full_name',
                ],
            ]);
    }
}
