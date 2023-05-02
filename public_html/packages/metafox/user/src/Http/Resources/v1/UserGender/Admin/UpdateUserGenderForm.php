<?php

namespace MetaFox\User\Http\Resources\v1\UserGender\Admin;

use MetaFox\Form\Builder as Builder;
use MetaFox\User\Models\UserGender as Model;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateUserGenderForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName user.user_gender.update
 * @driverType form
 */
class UpdateUserGenderForm extends StoreUserGenderForm
{
    public function boot(UserGenderRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.update_gender'))
            ->action('/admincp/user/user-gender/' . $this->resource->entityId())
            ->asPut()
            ->setValue([
                'key'  => $this->resource->phrase,
                'text' => $this->resource->name,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('key')
                ->required()
                ->disabled()
                ->label(__p('localize::phrase.translation_key'))
                ->variant('outlined'),
            Builder::textArea('text')
                ->variant('outlined')
                ->required()
                ->label(__p('localize::phrase.text_value'))
                ->disableEditor()
                ->placeholder(__p('localize::phrase.text_value')),
        );

        $this->addDefaultFooter();
    }
}
