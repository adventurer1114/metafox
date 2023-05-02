<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Http\Resources\v1\Request;

use MetaFox\Platform\Resource\WebSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * GroupRequest Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class WebSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('group-request')
            ->apiParams(['group_id' => ':id']);

        $this->add('acceptMemberRequest')
            ->apiUrl('group-request/accept-request')
            ->asPut()
            ->apiParams(['group_id' => ':group_id', 'user_id' => ':user_id']);

        $this->add('denyMemberRequest')
            ->apiUrl('group-request/deny-request')
            ->asDelete()
            ->apiParams(['group_id' => ':group_id', 'user_id' => ':user_id']);

        $this->add('cancelRequest')
            ->apiUrl('group-request/cancel-request/:id')
            ->asDelete()
            ->confirm(['title' => __p('core::phrase.confirm'), 'message' => __p('group::phrase.are_you_sure_you_want_to_cancel_this_request')]);
    }
}
