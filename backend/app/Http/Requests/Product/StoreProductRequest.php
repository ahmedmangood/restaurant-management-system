<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name"=>"required|string|unique:products,name",
            "total_price"=>"required|numeric",
            "description"=>"nullable|string",
            "category_id"=>"required|exists:categories,id",
            "ingredients"=>"array|required",
            "ingredients.*.id"=>"required|exists:ingredients",
            "ingredients.*.quantity"=>"required",
            "ingredients.*.total"=>"required",
            "extra"=>"sometimes|array",
            "extra.*"=>"exists:ingredients,id",
            'image'  => 'required|max:2500|mimes:jpg,png,jpeg,gif|image',
            'discount'=>'sometimes|numeric|min:0.01|max:0.99'
                ];
    }
}
