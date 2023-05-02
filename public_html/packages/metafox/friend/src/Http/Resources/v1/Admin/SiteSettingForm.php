<?php

namespace MetaFox\Friend\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Friend\Models\Friend as Model;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SiteSettingForm.
 * @property Model $resource
 */
class SiteSettingForm extends Form
{
    protected function prepare(): void
    {
        $module = 'friend';
        $vars   = [
            'friend.friend_request_total',
            /*
            'friend.enable_birthday_notices',
            'friend.days_to_check_for_birthday',
            'friend.enable_friend_suggestion',
            'friend.friend_suggestion_timeout',
            'friend.friend_suggestion_user_based',
            'friend.friend_suggestion_friend_check_count',
            'friend.friend_cache_limit',
            'friend.friends_only_profile',
            'friend.cache_rand_list_of_friends',
            'friend.friendship_direction',
            */
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this
            ->title(__p('core::phrase.settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/' . $module))
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('friend.friend_request_total')
                ->label(__p('friend::phrase.site_settings.friend_request_total_label'))
                ->description(__p('friend::phrase.site_settings.friend_request_total_desc'))
                ->yup(
                    Yup::number()
                        ->int()
                        ->min(0)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            /*
                Builder::switch('friend.enable_birthday_notices')
                    ->label(__p('friend::phrase.site_settings.enable_birthday_notices'))
                    ->description(__p('friend::phrase.site_settings.enable_birthday_notices_description')),

                Builder::text('friend.days_to_check_for_birthday')
                    ->label(__p('friend::phrase.site_settings.days_to_check_for_birthday'))
                    ->description(__p('friend::phrase.site_settings.days_to_check_for_birthday_description'))
                    ->showWhen(['truthy', 'friend.enable_birthday_notices']),
                Builder::divider(),

                Builder::text('friend.friend_suggestion_friend_check_count')
                    ->label(__p('friend::phrase.site_settings.friend_suggestion_friend_check_count'))
                    ->description(__p('friend::phrase.site_settings.friend_suggestion_friend_check_count_desc')),

                Builder::divider(),

                Builder::switch('friend.enable_friend_suggestion')
                    ->label(__p('friend::phrase.site_settings.enable_friend_suggestion'))
                    ->description(__p('friend::phrase.site_settings.enable_friend_suggestion_description')),

                Builder::text('friend.friend_suggestion_timeout')
                    ->label(__p('friend::phrase.site_settings.friend_suggestion_timeout'))
                    ->description(__p('friend::phrase.site_settings.friend_suggestion_timeout_description'))
                    ->showWhen(['truthy', 'friend.enable_friend_suggestion']),

                Builder::switch('friend.friend_suggestion_user_based')
                    ->label(__p('friend::phrase.site_settings.friend_suggestion_user_based'))
                    ->description(__p('friend::phrase.site_settings.friend_suggestion_user_based_description'))
                    ->showWhen(['truthy', 'friend.enable_friend_suggestion']),
                Builder::divider(),

                Builder::text('friend.friend_cache_limit')
                    ->label(__p('friend::phrase.site_settings.friend_cache_limit'))
                    ->description(__p('friend::phrase.site_settings.friend_cache_limit_description')),
                Builder::divider(),

                Builder::switch('friend.friends_only_profile')
                    ->label(__p('friend::phrase.site_settings.friends_only_profile'))
                    ->description(__p('friend::phrase.site_settings.friends_only_profile_description')),
                Builder::divider(),

                Builder::text('friend.cache_rand_list_of_friends')
                    ->label(__p('friend::phrase.site_settings.cache_rand_list_of_friends'))
                    ->description(__p('friend::phrase.site_settings.cache_rand_list_of_friends_description')),
                Builder::divider(),

                Builder::choice('friend.friendship_direction')
                    ->label(__p('friend::phrase.site_settings.friendship_direction'))
                    ->description(__p('friend::phrase.site_settings.friendship_direction_desc'))
                    ->options([
                        [
                            'label' => __p('friend::phrase.site_settings.two_way_friendships'),
                            'value' => MetaFoxConstant::TWO_WAY_FRIENDSHIPS,
                        ],
                        [
                            'label' => __p('friend::phrase.site_settings.one_way_friendships'),
                            'value' => MetaFoxConstant::ONE_WAY_FRIENDSHIPS,
                        ],
                    ]),
             */
        );

        $this->addDefaultFooter(true);
    }
}
