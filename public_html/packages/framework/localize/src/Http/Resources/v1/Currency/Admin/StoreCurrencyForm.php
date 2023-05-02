<?php

namespace MetaFox\Localize\Http\Resources\v1\Currency\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Currency as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * CurrencyEditForm
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreCurrencyForm.
 * @property ?Model $resource
 * @driverType form
 * @driverName core.currency.store
 */
class StoreCurrencyForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('localize::currency.add_new_currency'))
            ->action(apiUrl('admin.localize.currency.store'))
            ->secondAction('@redirectTo')
            ->setValue([
                'is_active'  => 1,
                'is_default' => 0,
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('name')
                    ->required()
                    ->label(__p('core::phrase.name'))
                    ->yup(Yup::string()->required()->minLength(3)->uppercase()),
                Builder::text('code')
                    ->required()
                    ->label(__p('localize::currency.code'))
                    ->yup(Yup::string()->required()->maxLength(3)),
                Builder::text('symbol')
                    ->required()
                    ->label(__p('localize::currency.symbol'))
                    ->yup(Yup::string()->required()->maxLength(3)),
                Builder::text('format')
                    ->required()
                    ->label(__p('localize::currency.format'))
                    ->placeholder('{0} #,###.00 {1}')
                    ->description(__p('localize::currency.currency_format_description'))
                    ->setAttributes([
                        'alwayShowDescription' => true,
                    ])
                    ->yup(Yup::string()->required()->minLength(8)),
                Builder::switch('is_default')
                    ->label(__p('core::web.default_ucfirst')),
                Builder::switch('is_active')
                    ->label(__p('localize::currency.is_active'))
                    ->showWhen([
                        'eq',
                        'is_default',
                        0,
                    ]),
            );
        $this->addDefaultFooter();
    }
}
