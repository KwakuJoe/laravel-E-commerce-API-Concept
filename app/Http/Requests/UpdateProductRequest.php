<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'store_id' => 'sometimes|exists:stores,id',
            'category_id' => 'sometimes|exists:categories,id',
            'user_id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            // 'images.*' => 'sometimes|image|mimes:jpeg,png,gif|max:5120', we woud update image sepratedly
        ];
    }
}
