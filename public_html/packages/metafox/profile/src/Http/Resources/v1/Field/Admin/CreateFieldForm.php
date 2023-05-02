<?php

namespace MetaFox\Profile\Http\Resources\v1\Field\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Profile\Models\Field;
use MetaFox\Profile\Models\Field as Model;
use MetaFox\Profile\Models\Section;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateFieldForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class CreateFieldForm extends AbstractForm
{
    public const MAX_NAME_LENGTH = 32;

    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action(apiUrl('admin.profile.field.store'))
            ->asPost()
            ->setValue([
                'section_id'      => 1,
                'type_id'         => 'main',
                'edit_type'       => 'text',
                'view_type'       => 'text',
                'var_type'        => 'string',
                'has_label'       => 1,
                'has_description' => 1,
                'is_active'       => 1,
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('field_name')
                    ->maxLength(self::MAX_NAME_LENGTH)
                    ->label(__p('core::phrase.name'))
                    ->description(__p('profile::phrase.name_desc'))
                    ->required()
                    ->yup(
                        Yup::string()
                            ->required()
                            ->matches(MetaFoxConstant::RESOURCE_IDENTIFIER_REGEX, __p('validation.alpha_underscore', [
                                'attribute' => __p('core::phrase.name'),
                            ]))
                    ),
                Builder::text('label')
                    ->label(__p('core::phrase.label'))
                    ->required()
                    ->yup(Yup::string()->required()),
                Builder::textArea('description')
                    ->label(__p('core::phrase.description'))
                    ->description(__p('profile::phrase.description_desc'))
                    ->optional(),
                Builder::dropdown('section_id')
                    ->required()
                    ->label(__p('profile::phrase.group'))
                    ->options($this->getLocationOptions())
                    ->yup(Yup::number()->required()),
                Builder::dropdown('edit_type')
                    ->label(__p('profile::phrase.edit_type_label'))
                    ->options($this->getEditTypeOptions()),
                Builder::checkbox('has_label')
                    ->label(__p('profile::phrase.has_label')),
                Builder::checkbox('has_description')
                    ->label(__p('profile::phrase.has_description')),
                Builder::checkbox('is_active')
                    ->label(__p('profile::phrase.is_active')),
                Builder::checkbox('is_required')
                    ->label(__p('profile::phrase.is_required')),
//                Builder::checkbox('is_register')
//                    ->label(__p('profile::phrase.is_register')),
//                Builder::checkbox('is_search')
//                    ->label(__p('profile::phrase.is_search')),
//                Builder::checkbox('is_feed')
//                    ->label(__p('profile::phrase.is_feed')),
            );

        $this->addDefaultFooter();
    }

    /**
     * getLocationOptions.
     *
     * @return array<mixed>
     */
    protected function getLocationOptions(): array
    {
        $response = [];

        /** @var Section[] $fields */
        $fields = Section::query()->orderBy('name')->get();

        foreach ($fields as $field) {
            $response[] = ['label' => $field->label, 'value' => $field->id];
        }

        return $response;
    }

    /**
     * getEditTypeOptions.
     *
     * @return array<mixed>
     */
    protected function getEditTypeOptions(): array
    {
        $response = [];

        $response[] = ['value' => 'text', 'label' => 'Text'];
        $response[] = ['value' => 'textArea', 'label' => 'Textarea'];
        $response[] = ['value' => 'richTextEditor', 'label' => 'Rich Text Editor'];
//        $response[] = ['value' => 'dropdown', 'label' => 'Selection'];
//        $response[] = ['value' => 'checkbox', 'label' => 'Checkbox'];
//        $response[] = ['value' => 'switch', 'label' => 'Switch'];
//        $response[] = ['value' => 'radio', 'label' => 'Radio'];

        return $response;
    }
}
