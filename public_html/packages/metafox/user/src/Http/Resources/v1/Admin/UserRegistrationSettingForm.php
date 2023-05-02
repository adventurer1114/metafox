<?php

namespace MetaFox\User\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\AdminSettingForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\UserRole;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */
class UserRegistrationSettingForm extends AdminSettingForm
{
    protected function prepare(): void
    {
        $vars   = [
            'user.allow_user_registration',
            'user.signup_repeat_password',
            // 'user.multi_step_registration_form',
            // 'user.profile_use_id',
            'user.enable_date_of_birth',
            'user.enable_gender',
            'user.enable_location',
            'user.enable_city',
            'user.verify_email_at_signup',
            'user.verify_email_timeout',
            'user.resend_verification_email_delay_time',
            'user.days_for_delete_pending_user_verification',
            'user.approve_users',
            'user.send_welcome_email',
            'user.force_user_to_upload_on_sign_up',
            'user.on_register_privacy_setting',
            'user.on_signup_new_friend',
            'user.redirect_after_signup',
            // 'user.disable_username_on_sign_up',
            // 'user.invite_only_community',
            'user.new_user_terms_confirmation',
            'user.require_basic_field',
            'user.on_register_user_group',
            'user.required_strong_password',
            // 'user.redirect_after_signup',
            'user.force_user_to_reenter_email',
            'user.shorter_reset_password_routine',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this
            ->title(__p('user::phrase.registration_settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/user'))
            ->asPost()
            ->setValue($value);
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $phrase = MetaFoxPrivacy::getUserPrivacy();

        $registerPrivacyOptions = [
            [
                'value' => MetaFoxPrivacy::EVERYONE,
                'label' => __p($phrase[MetaFoxPrivacy::EVERYONE]),
            ], [
                'value' => MetaFoxPrivacy::MEMBERS,
                'label' => __p($phrase[MetaFoxPrivacy::MEMBERS]),
            ], [
                'value' => MetaFoxPrivacy::FRIENDS,
                'label' => __p($phrase[MetaFoxPrivacy::FRIENDS]),
            ], [
                'value' => MetaFoxPrivacy::ONLY_ME,
                'label' => __p($phrase[MetaFoxPrivacy::ONLY_ME]),
            ],
        ];

        $basic->addFields(
            Builder::switch('user.allow_user_registration')
                ->label(__p('user::admin.allow_user_registration_label'))
                ->description(__p('user::admin.allow_user_registration_desc')),
            Builder::switch('user.signup_repeat_password')
                ->label(__p('user::admin.signup_repeat_password_label'))
                ->description(__p('user::admin.signup_repeat_password_desc')),

            // Builder::switch('user.multi_step_registration_form')
            //     ->label('Multi-step Registration Form')
            //     ->description('Enabling this option will turn the registration process into multiple steps and using as few fields as we can on the first step to entice users to register..'),
            // Builder::switch('user.profile_use_id')
            //     ->label('Profile User ID Connection')
            //     ->description('Set to Yes if you would like to have user profiles connected via their user ID#. Set to No if you would like to have user profiles connected via their user name. Note if you connect via their user ID# you will allow your members the ability to use non-supported characters which are not allowed if connecting a profile with their user name. Warning: This action cannot be reversed.This setting may lock users out if you force log in by their user names'),
            // Builder::text('user.redirect_after_signup')
            //     ->label('Redirect After SignUp')
            //     ->description('Add the full path you want to send users right after they register. If you want to use our default routine just leave this blank.'),
            Builder::switch('user.verify_email_at_signup')
                ->label(__p('user::admin.verify_email_at_signup_label'))
                ->description(__p('user::admin.verify_email_at_signup_desc')),
            Builder::text('user.verify_email_timeout')
                ->label(__p('user::admin.verify_email_timeout_label'))
                ->description(__p('user::admin.verify_email_timeout_desc'))
                ->required()
                ->yup(Yup::number()->positive()->min(0)),
            Builder::text('user.redirect_after_signup')
                ->label(__p('user::admin.redirect_after_signup_label'))
                ->description(__p('user::admin.redirect_after_signup_desc')),
            Builder::text('user.resend_verification_email_delay_time')
                ->label(__p('user::admin.resend_verification_email_delay_time_label'))
                ->description(__p('user::admin.resend_verification_email_delay_time_desc'))
                ->required()
                ->yup(Yup::number()->required()->int()->min(1)),
            Builder::text('user.days_for_delete_pending_user_verification')
                ->label(__p('user::admin.days_for_delete_pending_user_verification_label'))
                ->description(__p('user::admin.days_for_delete_pending_user_verification_desc'))
                ->required()
                ->yup(Yup::number()->required()->positive()->min(0)),
            Builder::switch('user.send_welcome_email')
                ->label(__p('user::admin.send_welcome_email_label'))
                ->description(__p('user::admin.send_welcome_email_desc')),
            Builder::switch('user.approve_users')
                ->label(__p('user::admin.approve_users_label'))
                ->description(__p('user::admin.approve_users_desc')),
            Builder::switch('user.force_user_to_upload_on_sign_up')
                ->label(__p('user::admin.force_user_to_upload_on_sign_up_label'))
                ->description(__p('user::admin.force_user_to_upload_on_sign_up_desc')),
            Builder::switch('user.force_user_to_reenter_email')
                ->label(__p('user::admin.force_user_to_reenter_email_label'))
                ->description(__p('user::admin.force_user_to_reenter_email_desc')),
            Builder::choice('user.on_signup_new_friend')
                ->label(__p('user::admin.on_signup_new_friend_label'))
                ->description(__p('user::admin.on_signup_new_friend_desc'))
                ->options($this->getAdminAndStaffOptions()),
            Builder::choice('user.on_register_privacy_setting')
                ->label(__p('user::admin.on_register_privacy_setting_label'))
                ->description(__p('user::admin.on_register_privacy_setting_desc'))
                ->options($registerPrivacyOptions),
            // Builder::choice('user.disable_username_on_sign_up')
            //     ->label('Display Full Name (Display Name) and Username on Sign Up')
            //     ->description('The Username is used to create a vanity URL of users (eg. http://www.site.com/username). If Username field isn\'t displayed on registration form, we will use their unique ID number to create their vanity URL. You can then enable a user group setting that can allow users to edit their username at a later time. Otherwise, if Full Name (Display Name) field is hidden, we will use Username as Full Name (Display Name) and user can change it when access to Account Settings page. ')
            //     ->options([['value' => 'full_name', 'label' => 'Full Name'], ['value' => 'username', 'label' => 'Username'], ['value' => 'both', 'label' => 'Both']]),
            // Builder::switch('user.invite_only_community')
            //     ->label('Invite Only')
            //     ->description('Enable this option if your community is an "invite only" community.'),
            Builder::switch('user.new_user_terms_confirmation')
                ->label(__p('user::admin.new_user_terms_confirmation_label'))
                ->description(__p('user::admin.new_user_terms_confirmation_desc')),
            Builder::switch('user.required_strong_password')
                ->label(__p('user::admin.required_strong_password_label'))
                ->description(__p('user::admin.required_strong_password_desc')),
            Builder::switch('user.require_basic_field')
                ->label(__p('user::admin.require_basic_field_label'))
                ->description(__p('user::admin.require_basic_field_desc')),
            Builder::choice('user.on_register_user_group')
                ->label(__p('user::admin.on_register_user_group_label'))
                ->description(__p('user::admin.on_register_user_group_desc'))
                ->required()
                ->options($this->getRoleOptions())
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::switch('user.enable_date_of_birth')
                ->label(__p('user::admin.enable_date_of_birth_label'))
                ->description(__p('user::admin.enable_date_of_birth_desc')),
            Builder::switch('user.enable_gender')
                ->label(__p('user::admin.enable_gender_label'))
                ->description(__p('user::admin.enable_gender_desc')),
            Builder::switch('user.enable_location')
                ->label(__p('user::admin.enable_location_label'))
                ->description(__p('user::admin.enable_location_desc')),
            Builder::switch('user.enable_city')
                ->label(__p('user::admin.enable_city_label'))
                ->description(__p('user::admin.enable_city_desc')),
            Builder::switch('user.shorter_reset_password_routine', )
                ->label(__p('user::admin.shorter_reset_password_routine_label'))
                ->description(__p('user::admin.shorter_reset_password_routine_desc')),
        );

        $this->addDefaultFooter(true);
    }

    protected function getRoleOptions(): array
    {
        $roles = resolve(RoleRepositoryInterface::class)->getRoleOptions();

        $disallowedRoleIds = [
            UserRole::SUPER_ADMIN_USER,
            UserRole::PAGE_USER,
            UserRole::GUEST_USER,
            UserRole::BANNED_USER,
        ];

        $roles = array_filter($roles, function ($role) use ($disallowedRoleIds) {
            return !in_array($role['value'], $disallowedRoleIds);
        });

        return $roles;
    }

    private function getAdminAndStaffOptions(): array
    {
        $listAdminStaff = resolve(UserRepositoryInterface::class)->getAdminAndStaffOptions();

        if (empty($listAdminStaff)) {
            return [
                ['label' => __p('core::phrase.none'), 'value' => 0],
            ];
        }

        return $listAdminStaff;
    }

    public function validated(Request $request): array
    {
        $data = $request->all();

        $roleOptions = array_column($this->getRoleOptions(), 'value');

        $rules = [
            'user.on_register_user_group' => ['required', 'numeric', 'in:' . implode(',', $roleOptions)],
        ];

        $validator = Validator::make($data, $rules);

        $validator->validate();

        return $data;
    }
}
