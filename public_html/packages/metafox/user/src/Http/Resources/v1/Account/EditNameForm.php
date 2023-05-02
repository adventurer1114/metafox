<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\Account;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;
use MetaFox\Yup\Yup;

/**
 * Class EditNameForm.
 * @property ?User $resource
 */
class EditNameForm extends AbstractForm
{
    protected function prepare(): void
    {
        $value = $this->resource ? [
            'first_name' => $this->resource->first_name,
            'last_name'  => $this->resource->last_name,
            'full_name'  => $this->resource->full_name,
        ] : null;
        $this
            ->action(url_utility()->makeApiUrl('/account/setting'))
            ->asPut()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('first_name')
                ->label(__p('user::phrase.first_name'))
                ->placeholder(__p('user::phrase.first_name'))
                ->marginNormal()
                ->variant('outlined')
                ->required()
                ->yup(Yup::string()->required()),
        );
        $basic->addFields(
            Builder::text('last_name')
                ->label(__p('user::phrase.last_name'))
                ->placeholder(__p('user::phrase.last_name'))
                ->marginNormal()
                ->variant('outlined')
                ->required()
                ->yup(Yup::string()->required()),
            Builder::text('full_name')
                ->label(__p('user::phrase.full_name'))
                ->placeholder(__p('user::phrase.full_name'))
                ->marginNormal()
                ->variant('outlined')
                ->required()
                ->yup(
                    Yup::string()->required()
                        ->maxLength(Settings::get('user.maximum_length_for_full_name')),
                ),
        );

        $footer = $this->addFooter(['separator' => false]);

        $footer->addFields(
            Builder::submit()->label(__p('core::phrase.save'))->variant('contained'),
            Builder::cancelButton()->label(__p('core::phrase.cancel'))->variant('outlined'),
        );
    }
}
