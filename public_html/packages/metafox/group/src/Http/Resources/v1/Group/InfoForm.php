<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Form\Section;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class InfoForm.
 * @property Model $resource
 */
class InfoForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('group::phrase.group_info'))
            ->action("group/{$this->resource->entityId()}")
            ->secondAction('updateGroupInfo')
            ->setValue([
                'name'         => $this->resource->name,
                'category_id'  => $this->resource->category_id,
                'landing_page' => $this->resource->landing_page ?? 'home',
                'vanity_url'   => $this->resource->profile_name ?? '',
                'reg_method'   => $this->resource->privacy_type,
            ]);
    }

    protected function initialize(): void
    {
        $basic              = $this->addBasic();
        $categoryRepository = resolve(CategoryRepositoryInterface::class);
        $options            = $categoryRepository->getCategoriesForForm();

        $minGroupNameLength = Settings::get('group.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxGroupNameLength = Settings::get('group.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic->addFields(
            Builder::singleUpdateInput('name')
                ->required()
                ->editComponent(MetaFoxForm::COMPONENT_TEXT)
                ->label(__p('group::phrase.group_name'))
                ->placeholder(__p('group::phrase.fill_in_a_name_for_your_group'))
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxGroupNameLength,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('group::phrase.group_name'),
                                'maxLength' => $maxGroupNameLength,
                            ])
                        )
                        ->minLength(
                            $minGroupNameLength,
                            __p('validation.field_must_be_at_least_min_length_characters', [
                                'field'     => __p('group::phrase.group_name'),
                                'minLength' => $minGroupNameLength,
                            ])
                        )
                ),
            Builder::singleUpdateInput('category_id')
                ->required()
                ->label(__p('core::phrase.category'))
                ->editComponent(MetaFoxForm::COMPONENT_SELECT)
                ->options($options)
                ->yup(Yup::number()->required()),
            Builder::singleUpdateInput('landing_page')
                ->required()
                ->label(__p('core::phrase.landing_page'))
                ->placeholder(__p('core::phrase.landing_page'))
                ->editComponent(MetaFoxForm::COMPONENT_SELECT)
                ->options($this->getProfileMenus()),
            Builder::singleUpdateInput('vanity_url')
                ->label(__p('core::phrase.url'))
                ->placeholder(__p('core::phrase.url'))
                ->editComponent(MetaFoxForm::COMPONENT_TEXT)
                ->description(__p('group::phrase.description_edit_group_url'))
                ->setAttribute('contextualDescription', url_utility()->makeApiFullUrl(''))
                ->findReplace([
                    'find'    => MetaFoxConstant::SLUGIFY_REGEX,
                    'replace' => '-',
                ]),
        );
        $this->handleFieldPrivacy($basic);
    }

    /**
     * @return array<int, mixed>
     */
    protected function getProfileMenus(): array
    {
        $menuItemRepository = resolve(MenuItemRepositoryInterface::class);
        $menus              = $menuItemRepository->loadItems('group.group.profileMenu', 'web');

        return collect($menus)->map(function ($menu) {
            return [
                'label' => $menu['label'],
                'value' => $menu['tab'],
            ];
        })->toArray();
    }

    protected function handleFieldPrivacy(Section $basic): AbstractField
    {
        $isPendingChangePrivacy = resolve(GroupChangePrivacyRepositoryInterface::class)
            ->isPendingChangePrivacy($this->resource);
        if ($isPendingChangePrivacy) {
            return $basic->addFields(
                Builder::description('typography')
                    ->label(__p('group::phrase.waiting_for_changes_privacy_label'))
                    ->description(__p('group::phrase.waiting_for_changes_privacy_desc'))
                    ->color('text.secondary'),
                Builder::customButton('cancel')->label(__p('core::phrase.cancel'))->customAction([
                    'type'    => 'group/cancelChangePrivacy',
                    'payload' => [
                        'id' => $this->resource->entityId(),
                    ],
                ])
            );
        }

        $builder = Builder::singleUpdateInput('reg_method')
            ->required()
            ->label(__p('core::phrase.privacy'))
            ->placeholder(__p('core::phrase.privacy'))
            ->editComponent(MetaFoxForm::RADIO_GROUP)
            ->options($this->getRegOptions())
            ->setAttributes([
                'descriptionSingleInput' => __p('group::phrase.group_privacy_description'),
                'reloadOnSubmit'         => true,
            ]);

        if ($this->resource->isSecretPrivacy()) {
            $builder->setAttributes([
                'isCanEdit'        => false,
                'descriptionValue' => __p('group::phrase.change_privacy_group_secret_description'),
            ]);
        }

        return $basic->addFields($builder);
    }

    /**
     * @return array<int, mixed>
     */
    protected function getRegOptions(): array
    {
        $currentPrivacy = $this->resource->privacy_type;

        return [
            [
                'value'       => PrivacyTypeHandler::PUBLIC,
                'label'       => __p('group::phrase.public'),
                'description' => __p('group::phrase.anyone_can_see_the_group_its_members_and_their_posts'),
                'disabled'    => $this->checkDisabledPublicPrivacy($currentPrivacy),
            ], [
                'value'       => PrivacyTypeHandler::CLOSED,
                'label'       => __p('group::phrase.closed'),
                'description' => __p('group::phrase.anyone_can_find_the_group_and_see_who_s_in_it_only_members_can_see_posts'),
                'disabled'    => $this->checkDisabledClosedPrivacy($currentPrivacy),
            ], [
                'value'       => PrivacyTypeHandler::SECRET,
                'label'       => __p('group::phrase.secret'),
                'description' => __p('group::phrase.only_members_can_find_the_group_and_see_posts'),
            ],
        ];
    }

    /**
     * @param int $privacy
     *
     * @return bool
     */
    protected function checkDisabledPublicPrivacy(int $privacy): bool
    {
        return $privacy != PrivacyTypeHandler::PUBLIC;
    }

    /**
     * @param int $privacy
     *
     * @return bool
     */
    protected function checkDisabledClosedPrivacy(int $privacy): bool
    {
        return $privacy == PrivacyTypeHandler::SECRET;
    }
}
