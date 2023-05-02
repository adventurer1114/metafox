<?php

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Platform\Resource\MobileSetting as ResourceSetting;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class MobileSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->apiUrl('user')
            ->apiParams([
                'q'         => ':q',
                'sort'      => ':sort',
                'country'   => ':country',
                'gender'    => ':gender',
                'city_code' => ':city_code',
            ])
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
                'gender'           => ['includes', 'gender', ['1', '2']],
                'view'             => ['includes', 'view', ['recommend', 'featured', 'recent']],
                'country'          => ['truthy', 'country'],
                'city'             => ['truthy', 'city'],
                'city_code'        => ['truthy', 'city_code'],
                'country_state_id' => ['truthy', 'country_state_id'],
            ]);

        $this->add('editItem')
            ->apiUrl('core/mobile/form/user.profile/:id');

        $this->add('viewItem')
            ->apiUrl('user/:id')
            ->pageUrl('user/:id');

        $this->add('deleteItem')
            ->apiUrl('user/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('user::phrase.delete_confirm'),
                ]
            );

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

        $this->add('editAccountInfo')
            ->asGet()
            ->apiUrl('core/mobile/form/user.account.info');

        $this->add('getGatewaySettings')
            ->asGet()
            ->apiUrl('core/mobile/form/payment.account.setting');

        $this->add('editAccountPassword')
            ->asGet()
            ->apiUrl('core/mobile/form/user.account.password');

        $this->add('editAccountLanguage')
            ->asGet()
            ->apiUrl('core/mobile/form/user.account.language');

        $this->add('viewRecommendUsers')
            ->apiUrl('user')
            ->apiParams([
                'view' => 'recommend',
            ]);
        $this->add('viewRecentUsers')
            ->apiUrl('user')
            ->apiParams([
                'view' => 'recent',
            ]);
        $this->add('viewFeaturedUsers')
            ->apiUrl('user')
            ->apiParams([
                'view' => 'featured',
            ]);

        $this->add('filterMember')
            ->asGet()
            ->apiUrl('core/mobile/form/user.search');

        $this->add('searchGlobalUser')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'       => 'user',
                'q'          => ':q',
                'is_hashtag' => ':is_hashtag',
            ]);

        $this->add('getReviewTagForm')
            ->apiUrl('core/mobile/form/user.account.review_tag');

        $this->add('follow')
            ->apiUrl('follow')
            ->asPost()
            ->apiParams([
                'user_id' => ':user_id',
            ]);

        $this->add('unfollow')
            ->apiUrl('follow/:id')
            ->asDelete();

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

        $this->add('updateSmsNotificationSettings')
            ->apiUrl('account/notification')
            ->asPut()
            ->apiParams([
                'channel' => 'sms',
            ]);

        $this->add('getCancelAccountForm')
            ->apiUrl('core/mobile/form/user.account.cancel/:id');
    }
}
