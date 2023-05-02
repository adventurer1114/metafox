<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Category\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Marketplace\Models\Category as Model;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
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
        $this->asPost()->title('Create Category')
            ->action(url_utility()->makeApiUrl('admincp/marketplace/category'));
    }

    /**
     * @return array<string,mixed>
     */
    private function getParentCategoryOptions(): array
    {
        return resolve(CategoryRepositoryInterface::class)->getCategoriesForStoreForm($this->resource);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic([]);

        $basic->addFields(
            Builder::text('name')
                ->label(__p('core::phrase.name'))
                ->required(true)
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_required'))
                        ->maxLength(
                            MetaFoxConstant::DEFAULT_MAX_CATEGORY_TITLE_LENGTH,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.name'),
                                'maxLength' => MetaFoxConstant::DEFAULT_MAX_CATEGORY_TITLE_LENGTH,
                            ])
                        )
                ),
            Builder::slug('name_url')
                ->label('Slug')
                ->mappingField('name')
                ->yup(
                    Yup::string()
                        ->nullable()
                        ->label('Slug')
                ),
            Builder::choice('parent_id')
                ->label('Parent Category')
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
