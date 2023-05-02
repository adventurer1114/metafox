<?php

namespace MetaFox\Payment\Http\Resources\v1\Gateway\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AdminSettingForm;
use MetaFox\Form\Builder;
use MetaFox\Form\FormField;
use MetaFox\Form\Section;
use MetaFox\Payment\Contracts\GatewayManagerInterface;
use MetaFox\Payment\Models\Gateway as Model;
use MetaFox\Payment\Repositories\GatewayRepositoryInterface;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class GatewayForm.
 * @property ?Model $resource
 */
class GatewayForm extends AdminSettingForm
{
    protected function serviceManager(): GatewayManagerInterface
    {
        return resolve(GatewayManagerInterface::class);
    }

    public function boot(?int $id = null): void
    {
        if ($id) {
            $this->resource = resolve(GatewayRepositoryInterface::class)->find($id);
        }
    }

    protected function prepare(): void
    {
        if (empty($this->resource)) {
            return;
        }

        $this->title(__p('payment::phrase.edit_payment_gateway'))
            ->action(apiUrl('admin.payment.gateway.update', ['gateway' => $this->resource->id]))
            ->asPut();

        if ($this->resource instanceof Model) {
            $this->setValue(array_merge([
                'title'       => $this->resource->title,
                'description' => $this->resource->description,
                'is_test'     => $this->resource->is_test,
                'is_active'   => $this->resource->is_active,
            ], $this->resource->config));
        }
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('title')
                ->required()
                ->label(__p(('core::phrase.title')))
                ->yup(
                    Yup::string()->required(__p('validation.this_field_is_required'))
                ),
            Builder::richTextEditor('description')
                ->required(false)
                ->returnKeyType('default')
                ->label(__p('core::phrase.description')),
        );

        $fields = $this->getGatewayConfigFields();
        if (!empty($fields)) {
            $basic->addFields(...$fields);
        }

        $basic->addField(
            Builder::switch('is_active')
                ->label(__p('core::phrase.is_active')),
        );

        $this->handleFieldIsTest($basic);
        $this->addDefaultFooter();
    }

    /**
     * validated.
     *
     * @param  Request             $request
     * @return array<mixed>
     * @throws ValidationException
     */
    public function validated(Request $request): array
    {
        $data  = $request->all();
        $rules = $this->getValidationRules();

        $validator = Validator::make($data, $rules);
        $validator->validate();

        return $data;
    }

    /**
     * getGatewayConfigFields.
     *
     * @return array<FormField>
     */
    protected function getGatewayConfigFields(): array
    {
        return [];
    }

    /**
     * getValidationRules.
     *
     * @return array<string, array<mixed>>
     */
    protected function getValidationRules(): array
    {
        return [
            'title'       => ['required', 'string', 'between:2,255'],
            'description' => ['sometimes', 'string'],
            'is_active'   => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'is_test'     => ['sometimes', 'numeric', new AllowInRule([0, 1])],
        ];
    }

    protected function handleFieldIsTest(Section $basic): AbstractField
    {
        return $basic->addField(
            Builder::switch('is_test')
                ->label(__p('payment::phrase.is_test'))
        );
    }
}
