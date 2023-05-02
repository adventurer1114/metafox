<?php

namespace MetaFox\Activity\Http\Resources\v1\Type\Admin;

use MetaFox\Activity\Models\Type as Model;
use MetaFox\Activity\Repositories\TypeRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateTypeForm.
 * @property Model $resource
 */
class UpdateTypeForm extends AbstractForm
{
    public function boot(int $id): void
    {
        $this->resource = resolve(TypeRepositoryInterface::class)->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('activity::phrase.edit_activity_type', ['title' => __p($this->resource->title)]))
            ->action('admincp/feed/type/' . $this->resource->id)
            ->setValue(new TypeItem($this->resource));
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::checkbox('is_active')
                ->label(__p('activity::phrase.enable_this_activity_type')),
            Builder::checkbox('is_system')
                ->label(__p('activity::phrase.is_system_activity_type')),
            Builder::checkbox('can_comment')
                ->label(__p('activity::phrase.activity_type_can_comment')),
            Builder::checkbox('can_like')
                ->label(__p('activity::phrase.activity_type_can_like')),
            Builder::checkbox('can_share')
                ->label(__p('activity::phrase.activity_type_can_share')),
            Builder::checkbox('can_edit')
                ->label(__p('activity::phrase.activity_type_can_edit')),
            Builder::checkbox('can_create_feed')
                ->label(__p('activity::phrase.activity_type_can_create_feed')),
            Builder::checkbox('can_put_stream')
                ->label(__p('activity::phrase.activity_type_can_put_stream')),
            Builder::checkbox('can_change_privacy_from_feed')
                ->label(__p('activity::admin.can_change_privacy_from_feed')),
            Builder::checkbox('can_redirect_to_detail')
                ->label(__p('activity::phrase.activity_type_can_redirect_to_detail')),
            Builder::checkbox('prevent_from_edit_feed_item')
                ->label(__p('activity::admin.prevent_from_edit_feed_item')),
        );

        $this->addDefaultFooter(true);
    }
}
