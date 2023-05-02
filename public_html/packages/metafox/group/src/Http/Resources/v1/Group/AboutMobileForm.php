<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Repositories\GroupRepositoryInterface;

/**
 * Class AboutForm.
 * @property Model $resource
 */
class AboutMobileForm extends AbstractForm
{
    public function boot(GroupRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('group::phrase.about_group'))
            ->action("group/{$this->resource->entityId()}");
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::richTextEditor('text')
                ->label(__p('core::phrase.description')),
            Builder::location('location')
                ->label(__p('core::phrase.location'))
                ->placeholder(__p('group::phrase.this_group_location')),
        );

        $values = [
            'text' => $this->resource->groupText ? $this->resource->groupText->text_parsed : '',
        ];

        if (null !== $this->resource->location_name) {
            Arr::set($values, 'location', [
                'address' => $this->resource->location_name,
                'lat'     => $this->resource->location_latitude,
                'lng'     => $this->resource->location_longitude,
            ]);
        }

        $this->setValue($values);
    }
}
