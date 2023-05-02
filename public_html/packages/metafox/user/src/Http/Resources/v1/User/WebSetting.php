<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Platform\Resource\WebSetting as ResourceSetting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;

/**
 *--------------------------------------------------------------------------
 * User Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class UserWebSetting.
 * @@SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->pageUrl('user/search')
            ->placeholder(__p('user::phrase.search_users'));

        $this->add('unblockItem')
            ->apiUrl('account/blocked-user/:id')
            ->asDelete();

        $this->add('blockItem')
            ->apiUrl('account/blocked-user')
            ->asPost()
            ->apiParams(['user_id' => ':id'])
            ->confirm([
                'title'   => __p('core::phrase.are_you_sure'),
                'message' => __p('user::phrase.block_user_confirm'),
            ]);

        $this->add('viewAll')
            ->apiUrl('user')
            ->apiRules([
                'q'                => ['truthy', 'q'],
                'sort'             => ['includes', 'sort', ['full_name', 'last_login', 'last_activity']],
                'gender'           => ['truthy', 'gender'],
                'view'             => ['includes', 'view', ['recommend', 'featured', 'recent']],
                'country'          => ['truthy', 'country'],
                'city'             => ['truthy', 'city'],
                'city_code'        => ['truthy', 'city_code'],
                'country_state_id' => ['truthy', 'country_state_id'],
            ]);

        $this->add('editItem')
            ->pageUrl('user/profile')
            ->apiUrl('user/profile/form');

        $this->add('viewItem')
            ->apiUrl('user/:id')
            ->urlParams([':id' => 'id'])
            ->pageUrl('user/:id');

        $this->add('deleteItem')
            ->apiUrl('user/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('user::phrase.delete_confirm'),
                ]
            );

        $this->add('updateAccountSettings')
            ->apiUrl('user/:id')
            ->urlParams([':id' => 'id'])
            ->asPut();

        $this->add('featureItem')
            ->apiUrl('user/feature/:id');

        $this->add('updateAvatar')
            ->apiUrl('user/avatar/:id');

        $this->add('updateProfileCover')
            ->apiUrl('user/cover/:id')
            ->asPost();

        $this->add('removeProfileCover')
            ->apiUrl('user/remove-cover/:id')
            ->asPut()
            ->confirm(['message' => 'Are you sure you want to delete this photo?']);

        $this->add('sendRequest')
            ->asPost()
            ->apiUrl('friend/request?friend_user_id=:id');

        $this->add('viewFriends')
            ->asGet()
            ->apiUrl('friend/?user_id=:id')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes', 'sort', [Browse::SORT_RECENT, SortScope::SORT_FULL_NAME],
                ],
            ]);

        $this->add('cancelRequest')
            ->asDelete()
            ->apiUrl('friend/request/:id');

        $this->add('unfriend')
            ->asDelete()
            ->apiUrl('friend/:id')
            ->confirm(['message' => 'Are you sure you want to remove this person as your friend?']);

        $this->add('acceptFriendRequest')
            ->asPut()
            ->apiUrl('friend/request/:id');

        $this->add('denyFriendRequest')
            ->asPut()
            ->apiUrl('friend/request/:id');

        $this->add('getInvisibleSettings')
            ->asGet()
            ->apiUrl('account/invisible');

        $this->add('updateInvisibleSettings')
            ->asPut()
            ->apiUrl('account/invisible');

        $this->add('getProfileSettings')
            ->asGet()
            ->apiUrl('account/profile-privacy/:id');

        $this->add('updateProfileSettings')
            ->asPut()
            ->apiUrl('account/profile-privacy');

        $this->add('getProfileMenuSettings')
            ->asGet()
            ->apiUrl('account/profile-menu/:id');

        $this->add('updateProfileMenuSettings')
            ->asPut()
            ->apiUrl('account/profile-menu');

        $this->add('getItemPrivacySettings')
            ->asGet()
            ->apiUrl('account/item-privacy/:id');

        $this->add('updateItemPrivacySettings')
            ->asPut()
            ->apiUrl('account/item-privacy');

        $this->add('getRegisterForm')
            ->apiUrl('core/form/user.register');

        $this->add('getGatewaySettings')
            ->apiUrl('payment-gateway/configuration')
            ->asGet();

        $this->add('getEmailNotificationSettings')
            ->apiUrl('account/notification')
            ->asGet()
            ->apiParams([
                'channel' => 'mail',
            ]);

        $this->add('getNotificationSettings')
            ->apiUrl('account/notification')
            ->asGet()
            ->apiParams([
                'channel' => 'database',
            ]);

        $this->add('getSmsNotificationSettings')
            ->apiUrl('account/notification')
            ->asGet()
            ->apiParams([
                'channel' => 'sms',
            ]);

        $this->add('updateEmailNotificationSettings')
            ->apiUrl('account/notification')
            ->asPut()
            ->apiParams([
                'channel' => ':channel',
            ]);

        $this->add('updateNotificationSettings')
            ->apiUrl('account/notification')
            ->asPut()
            ->apiParams([
                'channel' => ':channel',
            ]);

        $this->add('follow')
            ->apiUrl('follow')
            ->asPost()
            ->apiParams([
                'user_id' => ':user_id',
            ]);

        $this->add('unfollow')
            ->apiUrl('follow/:id')
            ->asDelete();

        $this->add('updateSmsNotificationSettings')
            ->apiUrl('account/notification')
            ->asPut()
            ->apiParams([
                'channel' => 'sms',
            ]);

        $this->add('getCancelAccountForm')
            ->apiUrl('core/form/user.account.cancel/:id')
            ->asGet();

        $this->add('getPasswordRequestForm')
            ->apiUrl('core/form/user.forgot_password')
            ->asGet();

        $this->add('getPasswordRequestMethodForm')
            ->apiUrl('core/form/user.password.request_method')
            ->apiParams([
                'email' => ':email',
            ])
            ->asGet();

        $this->add('getPasswordVerifyRequestForm')
            ->apiUrl('core/form/user.password.verify_request')
            ->apiParams([
                'user_id'        => ':user_id',
                'request_method' => ':request_method',
            ])
            ->asGet();

        $this->add('getPasswordResetForm')
            ->apiUrl('core/form/user.password.edit')
            ->apiParams([
                'user_id' => ':user_id',
                'token'   => ':token',
            ])
            ->asGet();

        $this->add('getPhoneNumberSettingForm')
            ->asGet()
            ->apiUrl('core/form/user.account.edit_phone_number');
    }
}
