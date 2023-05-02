<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\ResourceText;

/**
 * Class AboutMobileForm.
 * @property Model $resource
 */
class AboutMobileForm extends AbstractForm
{
    public function boot(PageRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $resource = $this->resource;

        $location = null;
        $text     = '';

        if ($this->resource instanceof HasLocationCheckin) {
            $location = $this->resource->location_name;
        }

        if ($this->resource->pageText instanceof ResourceText) {
            $text = $this->resource->pageText->text_parsed;
        }
        if (null !== $location) {
            $location = [
                'address' => $this->resource->location_name,
                'lat'     => $this->resource->location_latitude,
                'lng'     => $this->resource->location_longitude,
            ];
        }
        $this->title(__p('page::phrase.page_about'))
            ->action(url_utility()->makeApiUrl("page/{$resource->entityId()}"))
            ->secondAction('updatePageAbout')
            ->asPut()
            ->setValue([
                'text'     => $text,
                'location' => $location,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::richTextEditor('text')
                ->label(__p('core::phrase.description')),
            Builder::location('location')
                ->label(__p('core::phrase.location'))
                ->placeholder(__p('core::phrase.location'))
        );
    }
}
