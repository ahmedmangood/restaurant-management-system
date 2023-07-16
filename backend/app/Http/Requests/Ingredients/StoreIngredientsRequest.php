<?php

namespace App\Http\Requests\Ingredients;

use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientsRequest extends FormRequest
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
            "profit"=>"required|numeric|min:0.1|max:0.99",
            "name"=>"required|string|unique:ingredients",
            "quntity"=>"required|numeric|min:0.01",
            'price'=>'required|numeric|min:1'
        ];
    }
}
