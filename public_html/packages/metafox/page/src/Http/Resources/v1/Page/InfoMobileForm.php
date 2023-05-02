<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class InfoMobileForm.
 * @property Model $resource
 */
class InfoMobileForm extends AbstractForm
{
    public function boot(PageRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $landingPage = 'home';
        if (is_string($this->resource->landing_page)) {
            $landingPage = $this->resource->landing_page;
        }

        $this->asPut()
            ->title(__p('page::phrase.page_info'))
            ->action("page/{$this->resource->entityId()}")
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
        $minPageNameLength = Settings::get('page.minimum_name_length', MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH);
        $maxPageNameLength = Settings::get('page.maximum_name_length', MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH);

        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('name')
                ->required()
                ->label(__p('core::phrase.title'))
                ->minLength($minPageNameLength)
                ->maxLength($maxPageNameLength)
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
            Builder::category('category_id')
                ->required()
                ->label(__p('core::phrase.category'))
                ->yup(Yup::string()->required())
                ->setRepository(PageCategoryRepositoryInterface::class)
                ->multiple(false)
                ->valueType('number'),
            Builder::text('external_link')
                ->label(__p('core::phrase.external_link'))
                ->placeholder(__p('core::phrase.external_link')),
            Builder::text('vanity_url')
                ->label(__p('core::phrase.url'))
                ->placeholder(__p('core::phrase.url'))
                ->description(__p('page::phrase.description_edit_page_url'))
                ->setAttribute('contextualDescription', url_utility()->makeApiFullUrl(''))
                ->findReplace([
                    'find'    => MetaFoxConstant::SLUGIFY_REGEX,
                    'replace' => '-',
                ]),
            Builder::Hidden('category_id'),
        );
    }

    public function getMenuItemRepository(): MenuItemRepositoryInterface
    {
        return resolve(MenuItemRepositoryInterface::class);
    }
}
