<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\BgsCollection\Admin;

use Illuminate\Support\Arr;
use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\BackgroundStatus\Models\BgsCollection as Model;
use MetaFox\BackgroundStatus\Repositories\BgsCollectionRepositoryInterface;
use MetaFox\Platform\Facades\ResourceGate;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateCollectionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateCollectionForm extends StoreCollectionForm
{
    public function boot(BgsCollectionRepositoryInterface $repository, ?int $id = null)
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $values = [
            'title'     => $this->resource->title,
            'is_active' => $this->resource->is_active,
        ];
        $values = $this->prepareAttachedPhotos($values);
        $this->action("admincp/bgs/collection/{$this->resource->entityId()}")
            ->asPut()
            ->setValue($values);
    }

    protected function prepareAttachedPhotos(array $values): array
    {
        $items = [];

        $backgrounds = $this->resource->backgrounds
            ->where('is_deleted', '!=', BgsCollection::IS_DELETED);
        if ($backgrounds->count()) {
            $items = $backgrounds->map(function ($background) {
                return ResourceGate::asItem($background, null);
            });
        }

        Arr::set($values, 'background_temp_file', !empty($items) ? $items->values() : []);

        return $values;
    }
}
