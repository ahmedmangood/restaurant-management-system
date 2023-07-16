<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')->id;
        
        return [
            'name'  => 'string|required|max:50|unique:categories,name,'.$categoryId,
            'image' => 'nullable|max:1000|mimes:jpg,png,jpeg,gif'
        ];
    }
}
