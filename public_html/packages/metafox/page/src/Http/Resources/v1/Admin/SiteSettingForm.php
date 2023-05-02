<?php

namespace MetaFox\Page\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\UserRole;
use MetaFox\User\Models\User;
use MetaFox\User\Support\Facades\User as UserSupport;
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
        $module = 'page';
        $vars   = [
            'page.default_item_privacy',
            'page.admin_in_charge_of_page_claims',
            'page.display_profile_photo_within_gallery',
            'page.display_cover_photo_within_gallery',
            'page.default_category',
            'page.minimum_name_length',
            'page.maximum_name_length',
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
        $basic             = $this->addBasic();
        $maximumNameLength = MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH;
        $users             = UserSupport::getUsersByRoleId(UserRole::ADMIN_USER);
        $categories        = $this->getCategoryRepository()->getCategoriesForForm();

        $admins = [['label' => __p('core::phrase.none'), 'value' => 0]];
        if (null != $users) {
            $users = $users->map(function (User $user) {
                return [
                    'label' => $user->full_name,
                    'value' => $user->entityId(),
                ];
            })->toArray();
            $admins = array_merge($admins, $users);
        }

        $basic->addFields(
            Builder::choice('page.admin_in_charge_of_page_claims')
                ->label(__p('page::phrase.site_settings.admin_in_charge_of_page_claims'))
                ->description(__p('page::phrase.site_settings.admin_in_charge_of_page_claims_description'))
                ->options($admins),
            Builder::text('page.minimum_name_length')
                ->required()
                ->label(__p('page::phrase.minimum_name_length'))
                ->description(__p('page::phrase.minimum_name_length'))
                ->yup(
                    Yup::number()
                        ->required(__p('page::validation.minimum_name_length_description_required', ['min' => 1]))
                        ->unint()
                        ->min(1)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::text('page.maximum_name_length')
                ->required()
                ->label(__p('page::phrase.maximum_name_length'))
                ->description(__p('page::phrase.maximum_name_length'))
                ->maxLength($maximumNameLength)
                ->yup(
                    Yup::number()
                        ->required(__p(
                            'page::validation.maximum_name_length_description_required',
                            ['max' => $maximumNameLength]
                        ))
                        ->unint()
                        ->when(
                            Yup::when('minimum_name_length')
                                ->is('$exists')
                                ->then(Yup::number()->min(['ref' => 'minimum_name_length']))
                        )
                        ->max($maximumNameLength)
                        ->setError('typeError', __p('core::validation.numeric', ['attribute' => '${path}']))
                ),
            Builder::switch('page.display_profile_photo_within_gallery')
                ->label(__p('page::phrase.site_settings.display_profile_photo_within_gallery'))
                ->description(__p('page::phrase.site_settings.display_profile_photo_within_gallery_description')),
            Builder::switch('page.display_cover_photo_within_gallery')
                ->label(__p('page::phrase.site_settings.display_cover_photo_within_gallery'))
                ->description(__p('page::phrase.site_settings.display_cover_photo_within_gallery_description')),
            Builder::choice('page.default_item_privacy')
                ->label(__p('page::phrase.site_settings.default_item_privacy'))
                ->description(__p('page::phrase.site_settings.default_item_privacy_description'))
                ->required()
                ->options([
                    [
                        'label' => __p('phrase.user_privacy.anyone'),
                        'value' => MetaFoxPrivacy::EVERYONE,
                    ],
                    [
                        'label' => __p('phrase.user_privacy.members_only'),
                        'value' => MetaFoxPrivacy::FRIENDS,
                    ],
                    [
                        'label' => __p('phrase.user_privacy.admins_only'),
                        'value' => MetaFoxPrivacy::CUSTOM,
                    ],
                ]),
            Builder::choice('page.default_category')
                ->label(__p('page::phrase.site_settings.page_default_category'))
                ->description(__p('page::phrase.site_settings.page_default_category_description'))
                ->disableClearable()
                ->required()
                ->options($categories),
        );

        $this->addDefaultFooter(true);
    }

    protected function getCategoryRepository(): PageCategoryRepositoryInterface
    {
        return resolve(PageCategoryRepositoryInterface::class);
    }
}
