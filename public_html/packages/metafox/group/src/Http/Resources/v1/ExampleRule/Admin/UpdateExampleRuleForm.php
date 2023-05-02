<?php

namespace MetaFox\Group\Http\Resources\v1\ExampleRule\Admin;

use MetaFox\Form\Builder;
use MetaFox\Group\Models\ExampleRule as Model;
use MetaFox\Group\Repositories\ExampleRuleRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateExampleRuleForm.
 * @property ?Model $resource
 */
class UpdateExampleRuleForm extends StoreExampleRuleForm
{
    /** @var bool */
    protected $isEdit = false;

    public function boot(ExampleRuleRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('group::phrase.edit_example_rule'))
            ->action(url_utility()->makeApiUrl('admincp/group/example-rule/' . $this->resource->id))
            ->setValue([
                'title_phrase'       => $this->resource->title,
                'description_phrase' => $this->resource->description,
                'title'              => __p($this->resource->title),
                'description'        => __p($this->resource->description),
                'is_active'          => $this->resource->is_active,
            ]);
    }

    protected function initialize(): void
    {
        $basic          = $this->addBasic([]);
        $maxLengthTitle = MetaFoxConstant::CHARACTER_LIMIT;

        $basic->addFields(
            Builder::text('title_phrase')
                ->label(__p('group::phrase.title_phrase'))
                ->disabled(),
            Builder::text('title')
                ->label(__p('core::phrase.title'))
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxLengthTitle,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.title'),
                                'maxLength' => $maxLengthTitle,
                            ])
                        )
                ),
            Builder::text('description_phrase')
                ->label(__p('group::phrase.description_phrase'))
                ->disabled(),
            Builder::textArea('description')
                ->label(__p('core::phrase.description'))
                ->maxLength(self::MAX_LENGTH_DESCRIPTION)
                ->yup(
                    Yup::string()
                        ->nullable()
                        ->maxLength(
                            self::MAX_LENGTH_DESCRIPTION,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.description'),
                                'maxLength' => self::MAX_LENGTH_DESCRIPTION,
                            ])
                        )
                        ->label(__p('core::phrase.description'))
                ),
            Builder::checkbox('is_active')
                ->label(__p('core::phrase.is_active')),
            Builder::hidden('is_custom'),
        );

        $this->addDefaultFooter();
    }
}
