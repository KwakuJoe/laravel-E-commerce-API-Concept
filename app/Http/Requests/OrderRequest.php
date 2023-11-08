<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            // 'order_id' => 'required|string|unique:orders,order_id,',
            'user_id' => 'required|exists:users,id',
            // 'order_date' => 'required|date',
            'location' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required|string',
            'alternate_phone' => 'sometimes|string' ,
            // 'total_amount' => 'required|numeric',
            'additional_information' => 'sometimes|string',
            // 'status' => 'required|string',
            'order_items' => 'required|array',
            // 'order_items.*.order_id' => 'required|unique:order_items,order_id',
            'order_items.*.product_id' => 'required|numeric|exists:products,id',
            'order_items.*.quantity' => 'required|numeric',
            'order_items.*.price' => 'required|numeric'
        ];
    }
}
