<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class InfoPageForm.
 * @property Model $resource
 */
class InfoPageForm extends AbstractForm
{
    protected function prepare(): void
    {
        $resource = $this->resource;

        $landingPage = 'home';

        if (is_string($this->resource->landing_page)) {
            $landingPage = $this->resource->landing_page;
        }

        $this->asPut()
            ->title(__p('page::phrase.page_info'))
            ->action("page/{$resource->entityId()}")
            ->secondAction('updatePageInfo')
            ->setValue([
                'name'          => $this->resource->name,
                'category_id'   => $this->resource->category_id,
                'landing_page'  => $landingPage,
                'vanity_url'    => $this->resource->profile_name,
                'external_link' => $this->resource->external_link,
            ]);
    }

    protected function initialize(): void
    {
        $options           = resolve(PageCategoryRepositoryInterface::class)->getCategoriesForForm();
        $minPageNameLength = Settings::get('page.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxPageNameLength = Settings::get('page.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::singleUpdateInput('name')
                ->required()
                ->editComponent(MetaFoxForm::COMPONENT_TEXT)
                ->minLength($minPageNameLength)
                ->maxLength($maxPageNameLength)
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('page::phrase.fill_in_a_name_for_your_page'))
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxPageNameLength,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('page::phrase.page_name'),
                                'maxLength' => $maxPageNameLength,
                            ])
                        )
                        ->minLength(
                            $minPageNameLength,
                            __p('validation.field_must_be_at_least_min_length_characters', [
                                'field'     => __p('page::phrase.page_name'),
                                'minLength' => $minPageNameLength,
                            ])
                        )
                ),
            Builder::singleUpdateInput('category_id')
                ->required()
                ->label(__p('core::phrase.category'))
                ->editComponent(MetaFoxForm::COMPONENT_SELECT)
                ->setAttribute('disableClearable', true)
                ->yup(Yup::number()->required())
                ->options($options),
            Builder::singleUpdateInput('landing_page')
                ->required()
                ->yup(Yup::string()->required())
                ->editComponent(MetaFoxForm::COMPONENT_SELECT)
                ->label(__p('core::phrase.landing_page'))
                ->placeholder(__p('core::phrase.landing_page'))
                ->options($this->getProfileMenus()),
            Builder::singleUpdateInput('external_link')
                ->editComponent(MetaFoxForm::COMPONENT_TEXT)
                ->label(__p('core::phrase.external_link'))
                ->placeholder(__p('core::phrase.external_link')),
            Builder::singleUpdateInput('vanity_url')
                ->editComponent(MetaFoxForm::COMPONENT_TEXT)
                ->label(__p('core::phrase.url'))
                ->placeholder(__p('core::phrase.url'))
                ->description(__p('page::phrase.description_edit_page_url'))
                ->setAttribute('contextualDescription', url_utility()->makeApiFullUrl(''))
                ->findReplace([
                    'find'    => MetaFoxConstant::SLUGIFY_REGEX,
                    'replace' => '-',
                ]),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getProfileMenus(): array
    {
        $menuItemRepository = $this->getMenuItemRepository();
        $menus              = $menuItemRepository->loadItems('page.page.profileMenu', 'web');

        return collect($menus)->map(function ($menu) {
            return [
                'label' => $menu['label'],
                'value' => $menu['tab'],
            ];
        })->toArray();
    }

    public function getMenuItemRepository(): MenuItemRepositoryInterface
    {
        return resolve(MenuItemRepositoryInterface::class);
    }
}
