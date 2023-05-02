<?php

namespace MetaFox\SEO\Http\Resources\v1\Meta\Admin;

use MetaFox\Form\Builder;
use MetaFox\SEO\Models\Meta as Model;
use MetaFox\SEO\Repositories\MetaRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateMetaForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateMetaForm extends StoreMetaForm
{
    public function boot($id, MetaRepositoryInterface $repository)
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit') . ' "' . $this->resource->name . '"')
            ->action(apiUrl('admin.seo.metum.update', ['metum' => $this->resource->id]))
            ->asPut()
            ->setValue([
                'title'        => $this->resource->title,
                'description'  => $this->resource->description,
                'keywords'     => $this->resource->keywords,
                'heading'      => $this->resource->heading,
                'resolution'   => $this->resource->resolution,
                'phrase_title' => $this->resource->phrase_title,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('title')
                ->required()
                ->label(__p('core::phrase.title'))
                ->description($this->resource->phrase_title)
                ->yup(Yup::string()->required()),
            Builder::text('heading')
                ->optional()
                ->yup(Yup::string()->optional()->nullable())
                ->showWhen(['notEquals', 'resolution', 'admin'])
                ->label(__p('seo::phrase.page_heading')),
            Builder::text('keywords')
                ->optional()
                ->showWhen(['notEquals', 'resolution', 'admin'])
                ->yup(Yup::string()->optional()->nullable())
                ->label(__p('seo::phrase.page_keywords')),
            Builder::textArea('description')
                ->optional()
                ->label(__p('seo::phrase.page_description'))
                ->description(__p('seo::phrase.page_description_desc'))
                ->showWhen(['notEquals', 'resolution', 'admin'])
                ->yup(Yup::string()->optional()->maxLength(128))
        );

        $this->addDefaultFooter($this->resource?->id > 0);
    }
}
