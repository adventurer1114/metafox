<?php

namespace MetaFox\User\Http\Resources\v1\UserGender\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\User\Models\UserGender as Model;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreUserGenderForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName user.user_gender.store
 * @driverType form
 * @
 */
class StoreUserGenderForm extends AbstractForm
{
    public function boot(UserGenderRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = new Model();
    }

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.add_new_gender'))
            ->action('/admincp/user/user-gender')
            ->asPost()
            ->setValue([
                'locale'     => 'en',
                'package_id' => 'user',
                'group'      => 'phrase',
                'text'       => '',
                'is_custom'  => 1,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::choice('locale')
                ->required()
                ->label(__p('localize::phrase.language'))
                ->options([['label' => 'English', 'value' => 'en']]) //@todo: need implement this on locale package
                ->yup(Yup::string()->required()),
            Builder::selectPackageAlias('package_id')
                ->label(__p('core::phrase.package_name'))
                ->required(),
            Builder::choice('group')
                ->options(resolve(PhraseRepositoryInterface::class)->getGroupOptions())
                ->freeSolo()
                ->label(__p('localize::phrase.group'))
                ->required()
                ->yup(Yup::string()->required()->maxLength(32)),
            Builder::text('name')
                ->label(__p('localize::phrase.phrase_name'))
                ->required()
                ->yup(Yup::string()->required()->maxLength(64)),
            Builder::textArea('text')
                ->required()
                ->label(__p('user::phrase.gender_name'))
                ->yup(Yup::string()->required()),
            Builder::hidden('is_custom'),
        );

        $this->addDefaultFooter();
    }
}
