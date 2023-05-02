<?php

namespace MetaFox\Localize\Http\Resources\v1\Currency\Admin;

use MetaFox\Localize\Repositories\CurrencyRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * EditCurrencyForm
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditCurrencyForm.
 * @driverType form
 * @driverName core.currency.update
 */
class UpdateCurrencyForm extends StoreCurrencyForm
{
    public function boot(?int $id = null): void
    {
        $this->resource = resolve(CurrencyRepositoryInterface::class)->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('localize::currency.edit_currency'))
            ->action(url_utility()->makeApiUrl('/admincp/localize/currency/' . $this->resource->entityId()))
            ->setValue($this->resource->toArray());
    }
}
