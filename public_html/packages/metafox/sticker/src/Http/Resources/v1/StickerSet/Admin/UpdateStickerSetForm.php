<?php

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Models\StickerSet as Model;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateStickerSetForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateStickerSetForm extends AbstractForm
{
    public function boot(StickerSetRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $items = $this->resource->stickers->map(function (Sticker $item) {
            return ResourceGate::asResource($item, 'item', false);
        });

        $this->title(__p('sticker::phrase.update_sticker_set'))
            ->action('admincp/sticker/sticker-set/' . $this->resource->id)
            ->asPut()
            ->setValue([
                'title'     => $this->resource->title,
                'is_active' => $this->resource->is_active,
                'stickers'  => $items,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('title')
                ->fullWidth()
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('core::phrase.fill_in_a_title'))
                ->yup(Yup::string()
                    ->minLength(MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH)
                    ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)),
            Builder::switch('is_active')
                ->label(__p('core::phrase.is_active')),
        );

        $this->addDefaultFooter(true);
    }
}
