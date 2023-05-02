<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\Account;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\User;
use MetaFox\Yup\Yup;

/**
 * Class EditCurrencyForm.
 * @property ?User $resource
 */
class EditCurrencyForm extends AbstractForm
{
    protected function prepare(): void
    {
        $value = $this->resource ? [
            'currency_id' => $this->resource->profile->currency_id,
        ] : null;

        $this
            ->asPut()
            ->action('/account/setting')
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addField(
            Builder::choice('currency_id')
                ->marginNormal()
                ->label(__p('core::phrase.preferred_currency'))
                ->placeholder(__p('user::phrase.preferred_currency'))
                ->options(app('currency')->getActiveOptions())
                ->required()
                ->yup(Yup::string()->required()),
        );

        $this->addDefaultFooter();
    }
}
