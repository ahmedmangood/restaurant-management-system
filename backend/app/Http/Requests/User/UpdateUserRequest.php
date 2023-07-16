<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'string|max:255',
            'email' => [
                'string',
                'email',
                'max:255',
                'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                'unique:users,email,'.$this->id,

            ],
            'phone' => [
            'string',
            'size:11',
            'regex:/^[0-9]+$/',
           'unique:users,phone,'.$this->id,


         ],

            'image'=>'nullable|image|max:2048',
            'password'=>[
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        ],
            // 'role' => 'in:' . implode(',', array_keys(User::$roleOptions)),
        ];
        $user = $this->route('user');
        $loggedInUser = $this->user();
        if ($loggedInUser->role != 'Admin' && $loggedInUser->id == $user->id) {
            unset($rules['role']);
        } else {
          $rules ['role'] = 'in:' . implode(',', array_keys(User::$roleOptions));
        }
        return $rules;



    }
}
