<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use MetaFox\Platform\Resource\WebSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * SavedList Web Resource Setting
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
        $this->add('addItem')
            ->pageUrl('saved/list/add')
            ->apiUrl('saveditems-collection/form');

        $this->add('editItem')
            ->pageUrl('saved/list/edit/:id')
            ->apiUrl('saveditems-collection/form/:id');

        $this->add('deleteItem')
            ->apiUrl('saveditems-collection/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('saved::phrase.delete_confirm_saved_list', ),
                ]
            );

        $this->add('viewItems')
            ->asGet()
            ->apiUrl('saveditems-collection')
            ->apiParams([
                'saved_id' => ':saved_id',
            ]);

        $this->add('addFriend')
            ->asGet()
            ->apiUrl('core/form/saved.saved_list.add_friend/:id');

        $this->add('viewFriend')
            ->asGet()
            ->apiUrl('saveditems-collection/view-friend/:id');

        $this->add('leaveCollection')
            ->asDelete()
            ->apiUrl('saveditems-collection/leave-collection/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('saved::phrase.leave_confirm_saved_list', ),
                ]
            );
    }
}
