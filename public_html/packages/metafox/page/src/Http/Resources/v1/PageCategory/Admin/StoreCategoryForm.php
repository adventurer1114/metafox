<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory\Admin;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Page\Models\Category as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class StoreCategoryForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class StoreCategoryForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('core::phrase.create_category'))
            ->action(url_utility()->makeApiUrl('admincp/page/category'));
    }

    /**
     * @return array<string,mixed>
     */
    private function getParentCategoryOptions(): array
    {
        return resolve(PageCategoryRepositoryInterface::class)->getCategoriesForStoreForm($this->resource);
    }

    /**
     * @throws AuthenticationException
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic([]);

        $basic->addFields(
            Builder::text('name')
                ->label(__p('core::phrase.name'))
                ->required(true)
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            MetaFoxConstant::DEFAULT_MAX_CATEGORY_TITLE_LENGTH,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.name'),
                                'maxLength' => MetaFoxConstant::DEFAULT_MAX_CATEGORY_TITLE_LENGTH,
                            ])
                        )
                ),
            Builder::slug('name_url')
                ->label(__p('core::phrase.slug'))
                ->mappingField('name')
                ->yup(
                    Yup::string()
                        ->nullable()
                        ->label(__p('core::phrase.slug'))
                ),
            Builder::choice('parent_id')
                ->label(__p('core::phrase.parent_category'))
                ->required(false)
                ->options($this->getParentCategoryOptions()),
            Builder::checkbox('is_active')
                ->disabled($this->isDisabled())
                ->label(__p('core::phrase.is_active'))
                ->required(true),
        );

        $this->addDefaultFooter();
    }

    protected function isDisabled(): bool
    {
        return false;
    }
}
