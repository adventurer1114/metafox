<?php

namespace MetaFox\User\Http\Resources\v1\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MetaFox\Form\AdminSettingForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Rules\FullBirthdayFormatRule;
use MetaFox\User\Rules\MonthDayBirthdayFormatRule;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\Facades\User;
use MetaFox\Yup\Yup;

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */
class UserSettingForm extends AdminSettingForm
{
    protected function prepare(): void
    {
        $module = 'user';
        $vars   = [
            // 'user.logout_after_change_phone_number',
            // 'user.logout_after_change_email_if_verify',
            // 'user.display_user_online_status',
            // 'user.login_type',
            'user.date_of_birth_start',
            'user.date_of_birth_end',
            'user.default_birthday_privacy',
            'user.browse_user_default_order',
            // 'user.display_or_full_name',
            // 'user.check_promotion_system',
            // 'user.enable_user_tooltip',
            // 'user.brute.force_attempts_count',
            // 'user.brute.force_time_check',
            // 'user.brute.force_cool_down',
            'user.enable_relationship_status',
            // 'user.password_reset_routine',
            'user.maximum_length_for_full_name',
            'user.minimum_length_for_password',
            'user.maximum_length_for_password',
            // 'user.split_full_name',
            'user.redirect_after_login',
            'user.redirect_after_logout',
            // 'user.disable_store_last_user',
            'user.enable_feed_user_update_relationship',
            'user.user_dob_month_day_year',
            'user.user_dob_month_day',
            // 'user.cache_recent_logged_in',
            'user.min_length_for_username',
            'user.max_length_for_username',
            'user.enable_feed_user_update_profile',
            // 'user.validate_full_name',
            // 'user.hide_main_menu',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->action(url_utility()->makeApiUrl('admincp/setting/' . $module))
            ->title(__p('user::phrase.user_settings'))
            ->asPost()
            ->setValue($value);
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            // Builder::switch('user.logout_after_change_phone_number')
        //     ->label(__p('user::phrase.logout_after_change_phone_number'))
        //     ->description(__p('user::phrase.logout_after_change_phone_number_desc')),
            // Builder::switch('user.logout_after_change_email_if_verify')
        //     ->label(__p('user::phrase.logout_after_change_email_if_verify'))
        //     ->description(__p('user::phrase.logout_after_change_email_if_verify_desc')),
            // Builder::switch('user.display_user_online_status')
        //     ->label(__p('user::phrase.display_user_online_status'))
        //     ->description(__p('user::phrase.display_user_online_status_desc')),
            // Builder::radioGroup('user.login_type')
        //     ->label(__p(('user::phrase.login_type_label'))
        //     ->description(__p(('user::phrase.login_type_desc'))
        //     ->required()
        //     ->options([
        //         ['value' => 'email', 'label' => 'Must use their email.'], ['value' => 'user_name', 'label' => 'Must use their user name.'], ['value' => 'both', 'label' => 'Can use either email or user name.'],
        //     ]),
            Builder::text('user.date_of_birth_start')
                ->label(__p('user::admin.date_of_birth_start'))
                ->description(__p('user::admin.date_of_birth_start_description'))
                ->yup(Yup::number()
                    ->min(1900)
                    ->max([
                        'ref' => 'date_of_birth_end',
                    ])),
            Builder::text('user.date_of_birth_end')
                ->label(__p('user::admin.date_of_birth_end'))
                ->description(__p('user::admin.date_of_birth_end_description'))
                ->yup(Yup::number()
                    ->max(9999)
                    ->min([
                        'ref' => 'date_of_birth_start',
                    ])),
            // Builder::choice('user.display_or_full_name')
            //     ->label(__p(('user::phrase.display_or_full_name_label'))
            //     ->description(__p(('user::phrase.display_or_full_name_desc'))
            //     ->options([
            //         ['label' => 'Full Name', 'value' => 'full_name'], ['label' => 'Display Name', 'value' => 'display_name'],
            //     ]),
            // Builder::switch('user.check_promotion_system')
            //     ->label(__p(('user::phrase.check_promotion_system_label'))
            //     ->description(__p(('user::phrase.check_promotion_system_desc')),
            // Builder::switch('user.enable_user_tooltip')
            //     ->label(__p(('user::phrase.enable_user_tooltip_label'))
            //     ->description(__p(('user::phrase.enable_user_tooltip_desc')),
            // Builder::text('user.brute.force_attempts_count')
            //     ->label(__p(('user::phrase.force_attempts_count_label'))
            //     ->description(__p(('user::phrase.force_attempts_count_desc'))->required(),
            // Builder::text('user.brute.force_time_check')
            //     ->label(__p(('user::phrase.force_time_check_label'))
            //     ->description(__p(('user::phrase.force_time_check_desc'))->required(),
            // Builder::text('user.brute.force_cool_down')
            //     ->label(__p(('user::phrase.force_cool_down_label'))
            //     ->description(__p(('user::phrase.force_cool_down_desc'))->required(),

            // Builder::radioGroup('user.password_reset_routine')
            //     ->label(__p(('user::phrase.password_reset_routine_label'))
            //     ->options([
            //         [
            //             'value' => 'login_url',
            //             'label' => __p(('user::phrase.password_reset_routine_options_label'),
            //         ],
            //     ]),
            Builder::text('user.maximum_length_for_full_name')
                ->label(__p('user::admin.maximum_length_for_full_name_label'))
                ->description(__p('user::admin.maximum_length_for_full_name_desc'))
                ->required()
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->min(1)
                ),
            // Builder::switch('user.split_full_name')
            //     ->label(__p(('user::phrase.split_full_name_label'))
            //     ->description(__p(('user::phrase.split_full_name_desc')),
            // Builder::switch('user.validate_full_name')
            //     ->label(__p(('user::phrase.validate_full_name_label'))
            //     ->description(__p(('user::phrase.validate_full_name_desc')),
            // Builder::switch('user.disable_store_last_user')
            //     ->label(__p(('user::phrase.disable_store_last_user_label'))
            //     ->description(__p(('user::phrase.disable_store_last_user_desc')),
            Builder::switch('user.enable_feed_user_update_relationship')
                ->label(__p('user::admin.enable_feed_user_update_relationship_label'))
                ->description(__p('user::admin.enable_feed_user_update_relationship_desc')),
            // Builder::text('user.cache_recent_logged_in')
            //     ->label(__p(('user::phrase.cache_recent_logged_in_label'))
            //     ->description(__p(('user::phrase.cache_recent_logged_in_desc')),
            Builder::text('user.min_length_for_username')
                ->label(__p('user::admin.min_length_for_username_label'))
                ->description(__p('user::admin.min_length_for_username_desc'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->min(1)
                        ->setError('typeError', __p('validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('user.max_length_for_username')
                ->label(__p('user::admin.max_length_for_username_label'))
                ->description(__p('user::admin.max_length_for_username_desc'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->when(
                            Yup::when('min_length_for_username')
                                ->is('$exists')
                                ->then(Yup::number()->min(['ref' => 'min_length_for_username']))
                        )
                        ->setError('typeError', __p('validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('user.minimum_length_for_password')
                ->label(__p('user::admin.minimum_length_for_password_label'))
                ->description(__p('user::admin.minimum_length_for_password_desc'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->min(4)
                        ->setError('typeError', __p('validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('user.maximum_length_for_password')
                ->label(__p('user::admin.maximum_length_for_password_label'))
                ->description(__p('user::admin.maximum_length_for_password_desc'))
                ->yup(
                    Yup::number()
                        ->required()
                        ->unint()
                        ->when(
                            Yup::when('minimum_length_for_password')
                                ->is('$exists')
                                ->then(Yup::number()->min(['ref' => 'minimum_length_for_password']))
                        )
                        ->setError('typeError', __p('validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::switch('user.enable_feed_user_update_profile')
                ->label(__p('user::admin.enable_feed_user_update_profile_label'))
                ->description(__p('user::admin.enable_feed_user_update_profile_desc')),
            Builder::switch('user.enable_relationship_status')
                ->label(__p('user::admin.enable_relationship_status_label'))
                ->description(__p('user::admin.enable_relationship_status_desc')),
            // Builder::switch('user.hide_main_menu')
            //     ->label(__p(('user::phrase.chide_main_menu_label'))
            //     ->description(__p(('user::phrase.chide_main_menu_desc')),
            Builder::choice('user.default_birthday_privacy')
                ->label(__p('user::admin.default_birthday_privacy_label'))
                ->description(__p('user::admin.default_birthday_privacy_desc'))
                ->options($this->getBirthdayOptions()),
            Builder::choice('user.browse_user_default_order')
                ->label(__p('user::admin.browse_user_default_order_label'))
                ->description(__p('user::admin.browse_user_default_order_desc'))
                ->options($this->getOptionsDefaultOrder()),
            Builder::choice('user.user_dob_month_day_year')
                ->label(__p('user::admin.user_dob_month_day_year_label'))
                ->description(__p('user::admin.user_dob_month_day_year_desc'))
                ->options($this->getFullBirthdayFormatOptions()),
            Builder::choice('user.user_dob_month_day')
                ->label(__p('user::admin.user_dob_month_day_label'))
                ->description(__p('user::admin.user_dob_month_day_desc'))
                ->options($this->getMonthDayBirthdayFormatOptions()),
            Builder::text('user.redirect_after_login')
                ->label(__p('user::admin.redirect_after_login_label'))
                ->description(__p('user::admin.redirect_after_login_desc')),
            Builder::text('user.redirect_after_logout')
                ->label(__p('user::admin.redirect_after_logout_label'))
                ->description(__p('user::admin.redirect_after_logout_desc')),
        );

        $this->addDefaultFooter(true);
    }

    protected function getBirthdayOptions(): array
    {
        return resolve(UserPrivacyRepositoryInterface::class)->getBirthdayOptionsForForm(true);
    }

    protected function getOptionsDefaultOrder(): array
    {
        return [
            [
                'label' => __p('user::phrase.full_name'),
                'value' => SortScope::SORT_FULL_NAME,
            ],
            [
                'label' => __p('user::phrase.last_login'),
                'value' => SortScope::SORT_LAST_LOGIN,
            ],
        ];
    }

    protected function getFullBirthdayFormatOptions(): array
    {
        return $this->generateBirthdayOptions(User::getFullBirthdayFormat());
    }

    protected function getMonthDayBirthdayFormatOptions(): array
    {
        return $this->generateBirthdayOptions(User::getMonthDayBirthdayFormat());
    }

    protected function generateBirthdayOptions($formats): array
    {
        $options = [];

        foreach ($formats as $format) {
            $options[] = [
                'label' => sprintf('%s - %s', $format, Carbon::now()->format($format)),
                'value' => $format,
            ];
        }

        return $options;
    }

    /**
     * @param  Request                    $request
     * @return array<string,       mixed>
     * @throws ValidationException
     */
    public function validated(Request $request): array
    {
        $data = $request->all();

        $rules = [
            'user.user_dob_month_day_year' => ['sometimes', new FullBirthdayFormatRule()],
            'user.user_dob_month_day'      => ['sometimes', new MonthDayBirthdayFormatRule()],
        ];

        $validator = Validator::make($data, $rules);

        $validator->validate();

        return $data;
    }
}
