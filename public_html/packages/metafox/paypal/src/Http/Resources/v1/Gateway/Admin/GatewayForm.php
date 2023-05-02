<?php

namespace MetaFox\Paypal\Http\Resources\v1\Gateway\Admin;

use MetaFox\Form\Builder;
use MetaFox\Form\FormField;
use MetaFox\Payment\Http\Resources\v1\Gateway\Admin\GatewayForm as AdminGatewayForm;
use MetaFox\Payment\Models\Gateway as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class GatewayForm.
 * @property Model $resource
 */
class GatewayForm extends AdminGatewayForm
{
    public function prepare(): void
    {
        // TODO: for security reason, should prevent client_secret from populating
        parent::prepare();
    }
    /**
     * getGatewayConfigFields.
     *
     * @return array<FormField>
     */
    protected function getGatewayConfigFields(): array
    {
        return [
            Builder::text('client_id')
                ->required()
                ->label(__p(('paypal::admin.client_id')))
                ->yup(
                    Yup::string()->required(__p('validation.this_field_is_a_required_field'))
                ),
            Builder::text('client_secret')
                ->required()
                ->label(__p(('paypal::admin.client_secret')))
                ->yup(
                    Yup::string()->required(__p('validation.this_field_is_a_required_field'))
                ),
        ];
    }

    protected function getValidationRules(): array
    {
        return array_merge(parent::getValidationRules(), [
            'client_id'     => ['sometimes', 'string'],
            'client_secret' => ['sometimes', 'string'],
        ]);
    }
}
