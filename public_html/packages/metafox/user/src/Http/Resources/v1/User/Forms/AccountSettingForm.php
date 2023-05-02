<?php

namespace MetaFox\User\Http\Resources\v1\User\Forms;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\User;

class AccountSettingForm extends AbstractForm
{
    protected function prepare(): void
    {
        /** @var User $user */
        $user = $this->resource;

        $this->setValue([
            'profile' => [
                'language_id' => $user->profile->language_id,
                'currency_id' => $user->profile->currency_id,
            ],
            'full_name' => $user->full_name,
            'user_name' => $user->user_name,
            'email'     => $user->email,
        ]);
    }

    public function bootstrap()
    {
        $this->resource = \user();
    }

    public function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('full_name')
                    ->required()
                    ->label(__p('core::phrase.full_name')),
                Builder::text('user_name')
                    ->required()
                    ->label(__p('core::phrase.username')),
                Builder::text('email')
                    ->required()
                    ->label(__p('core::phrase.email_address')),
                Builder::password('password')
                    ->label(__p('core::phrase.password'))
                    ->placeholder('**************'),
                Builder::choice('profile.language_id')
                    ->label(__p('core::phrase.primary_language'))
                    ->options([])// //Todo get options)
                ,
                Builder::choice('profile.currency_id')
                    ->label(__p('core::phrase.preferred_currency'))
                    ->options([])//, //Todo get options)
            );
    }
}
