<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
     *'status' =>
     * @return array<string, \Illuminate\\Contracts\\Validationz\ValidationRule|array|string>
     */
    public function rules(): array
    {
        //validations
        return [
            'name'   => 'string|required|max:50|unique:categories',
            'image'  => 'required|max:1000|mimes:jpg,png,jpeg,gif'
        ]; 
    }
}
