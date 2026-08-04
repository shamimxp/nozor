<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManufactureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_no' => 'nullable|string',
            'reff_invoice' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'dealer_id' => 'required|exists:dealers,id',
            'worker_id' => 'required|exists:workers,id',
            'manufacture_qty' => 'required|numeric|min:1',
            'parts' => 'required|array|min:1',
            'note' => 'nullable|string',
            'is_confirm' => 'nullable|boolean'
        ];
    }
}
