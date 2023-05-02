<?php

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Sticker\Models\StickerSet as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreStickerSetForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class StoreStickerSetForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('sticker::phrase.new_sticker_set'))
            ->action(apiUrl('admin.sticker.sticker-set.store'))
            ->asMultipart()
            ->setValue([
                'is_active' => 0,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('title')
                ->required()
                ->label(__p('core::phrase.title'))
                ->placeholder(__p('core::phrase.fill_in_a_title'))
                ->yup(
                    Yup::string()
                        ->minLength(MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH)
                        ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                        ->required(__p('validation.this_field_is_a_required_field'))
                ),
            Builder::rawFile('file')
                ->label(__p('sticker::phrase.sticker_list'))
                ->accept('.zip, .gif')
                ->maxUploadSize(Settings::get('sticker.sticker_package_upload_limit'))
                ->placeholder(__p('sticker::phrase.select_sticker_images_to_upload'))
                ->itemType('sticker')
                ->required(),
            Builder::checkbox('is_active')
                ->fullWidth()
                ->marginNormal()
                ->label(__p('core::phrase.is_active')),
        );

        $this->addDefaultFooter(false);
    }
}
