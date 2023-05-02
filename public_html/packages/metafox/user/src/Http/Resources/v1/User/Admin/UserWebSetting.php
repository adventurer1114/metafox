<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use MetaFox\Platform\Resource\WebSetting;

/**
 *--------------------------------------------------------------------------
 * User Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class UserWebSetting
 * Inject this class into property $resources.
 * @link \MetaFox\User\Http\Resources\v1\WebAppSetting::$resources;
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UserWebSetting extends WebSetting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('admincp/user');

        $this->add('addItem')
            ->apiUrl('admincp/user');

        $this->add('editItem')
            ->apiUrl('admincp/user/:id');

        $this->add('featureItem')
            ->apiUrl('admincp/user/feature/:id');

        $this->add('banItem')
            ->apiUrl('admincp/user/ban/:id');

        $this->add('deleteItem')
            ->apiUrl('admincp/user/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('user::phrase.delete_confirm'),
                ]
            );

        /*$this->add('searchForm')
            ->apiUrl('admincp/core/form/user.search-form');*/
    }
}
