<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\BgsCollection\Admin;

use MetaFox\BackgroundStatus\Models\BgsCollection as Model;
use MetaFox\BackgroundStatus\Repositories\BgsCollectionRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreCollectionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class StoreCollectionForm extends AbstractForm
{
    protected const MAX_LENGTH_TITLE = 100;
    public const PHOTO_MINE_TYPES    = ['image/jpg', 'image/jpeg', 'image/png'];

    public function boot(BgsCollectionRepositoryInterface $repository, ?int $id = null)
    {
    }

    protected function prepare(): void
    {
        $this->action(apiUrl('admin.bgs.collection.store'))
            ->asPost()
            ->setValue([
                'is_active' => 0,
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('title')
                    ->required()
                    ->maxLength(self::MAX_LENGTH_TITLE)
                    ->label(__p('backgroundstatus::phrase.collection_name'))
                    ->yup(Yup::string()
                        ->maxLength(self::MAX_LENGTH_TITLE)),
                Builder::uploadMultiMedia('background_temp_file')
                    ->required()
                    ->label(__p('backgroundstatus::phrase.add_photos'))
                    ->accepts(implode(',', self::PHOTO_MINE_TYPES))
                    ->acceptFail(__p('backgroundstatus::phrase.photo_accept_type_fail'))
                    ->itemType('backgroundstatus')
                    ->uploadUrl('file'),
                Builder::checkbox('is_active')
                    ->label(__p('core::phrase.is_active')),
            );

        $this->addDefaultFooter();
    }
}
