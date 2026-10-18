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
            'dealer_id' => 'required|exists:dealers,id',
            'worker_id' => 'required|exists:workers,id',
            'note' => 'nullable|string',
            'is_confirm' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.cost_setting_id' => 'nullable|exists:cost_settings,id',
            'items.*.cost_title' => 'nullable|string',
            'items.*.part_type' => 'required|in:body,finishing,both',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.reff_invoice' => 'nullable|string',
            'items.*.note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'dealer_id.required' => 'Please select a dealer.',
            'worker_id.required' => 'Please select a worker.',
            'items.required' => 'Please add at least one manufacture product.',
            'items.min' => 'Please add at least one manufacture product.',
            'items.*.product_id.required' => 'Product is required for all items.',
            'items.*.qty.required' => 'Quantity is required for all items.',
            'items.*.qty.min' => 'Quantity must be at least 1.',
            'items.*.part_type.required' => 'Part type is required.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            if (is_array($items)) {
                foreach ($items as $index => $item) {
                    if (!empty($item['product_id'])) {
                        $product = \App\Models\Product::with('recipe')->find($item['product_id']);
                        if (!$product) {
                            $validator->errors()->add("items.{$index}.product_id", "The selected product does not exist.");
                        } elseif (!$product->is_manufacturer) {
                            $validator->errors()->add("items.{$index}.product_id", "Product '{$product->name}' is not a manufacture product.");
                        } elseif (!$product->recipe || !$product->recipe->cost_setting_id) {
                            $validator->errors()->add("items.{$index}.product_id", "Product '{$product->name}' does not have an assigned Product Recipe / Cost Setting. Please configure its recipe first.");
                        }
                    }
                }
            }
        });
    }
}
