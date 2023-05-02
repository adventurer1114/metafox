<?php

namespace MetaFox\Advertise\Http\Requests\v1\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'invoice_id'      => ['nullable', 'numeric', 'exists:advertise_invoices,id'],
            'item_id'         => ['required', 'numeric', 'min:1'],
            'item_type'       => ['required', 'string'],
            'payment_gateway' => ['nullable', 'numeric', 'exists:payment_gateway,id'],
        ];
    }
}
