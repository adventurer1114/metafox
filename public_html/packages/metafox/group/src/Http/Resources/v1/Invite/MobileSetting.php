<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Http\Resources\v1\Invite;

use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * GroupMember Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class MobileSetting.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('group-invite')
            ->apiParams(['group_id' => ':id']);

        $this->add('cancelInvite')
            ->apiUrl('group-invite')
            ->asDelete()
            ->apiParams(['group_id' => ':group_id', 'user_id' => ':user_id']);

        $this->add('addItem')
            ->apiUrl('core/mobile/form/group.invite.store/:id');
    }
}
