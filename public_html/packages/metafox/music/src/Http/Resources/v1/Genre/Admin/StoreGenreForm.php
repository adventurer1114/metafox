<?php

namespace MetaFox\Music\Http\Resources\v1\Genre\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Music\Models\Genre as Model;
use MetaFox\Music\Repositories\GenreRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * Class StoreGenreForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class StoreGenreForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()->title(__p('music::phrase.create_genre'))
            ->action(url_utility()->makeApiUrl('admincp/music/genre'));
    }

    /**
     * @return array<string,mixed>
     */
    protected function getParentCategoryOptions(): array
    {
        return resolve(GenreRepositoryInterface::class)->getCategoriesForStoreForm($this->resource);
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
                ->label(__p('music::phrase.parent_genre'))
                ->required(false)
                ->options($this->getParentCategoryOptions()),
            Builder::checkbox('is_active')
                ->label(__p('core::phrase.is_active'))
                ->required(true)
                ->disabled($this->isDisabled()),
        );

        $this->addDefaultFooter();
    }

    protected function isDisabled(): bool
    {
        return false;
    }
}
