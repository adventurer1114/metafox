<?php

namespace MetaFox\Layout\Http\Resources\v1\Theme\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Layout\Models\Theme as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub
 */

/**
 * Class CreateThemeForm
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class CreateThemeForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action(apiUrl('admin.layout.theme.store'))
            ->description('layout::phrase.create_theme_description_guide')
            ->asPost()
            ->setValue([
                'theme_id' => uniqid('t'),
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('theme_id')
                    ->required()
                    ->label(__p('core::phrase.id'))
                    ->description(__p('layout::phrase.create_theme_id_guide'))
                    ->yup(Yup::string()->required()),
                Builder::text('title')
                    ->required()
                    ->label(__p('core::phrase.title'))
                    ->yup(Yup::string()->required()),
            );

        $this->addDefaultFooter();
    }
}
