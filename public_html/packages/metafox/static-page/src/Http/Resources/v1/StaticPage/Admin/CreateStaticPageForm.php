<?php

namespace MetaFox\StaticPage\Http\Resources\v1\StaticPage\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\StaticPage\Models\StaticPage as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateStaticPageForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class CreateStaticPageForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('static-page::phrase.create_page'))
            ->action(apiUrl('admin.static-page.page.store'))
            ->navigationConfirmation()
            ->asPost()
            ->setValue([
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('slug')
                    ->required()
                    ->label(__p('static-page::phrase.slug'))
                    ->yup(Yup::string()->required()),
                Builder::text('title')
                    ->required()
                    ->label(__p('core::phrase.title'))
                    ->yup(Yup::string()->required()),
                Builder::richTextEditor('text')
                    ->required()
                    ->label(__p('static-page::phrase.content'))
                    ->yup(Yup::string()->required()),
            );

        $this->addDefaultFooter();
    }
}
