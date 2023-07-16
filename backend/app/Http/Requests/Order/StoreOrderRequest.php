<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
                
                'total_price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric',
                'tax'=> 'sometimes|numeric|max:1',
                'payment_method' => 'nullable|in:CASH,VISA',
                'service_fee' => 'sometimes|numeric|max:1',
                // 'status' => 'required|in:Pending,Accepted,Prepare,Complete,Served,Canceled,Paid',
                'table_id' => 'required|exists:tables,id',
                'user_id' => 'nullable|exists:users,id',
                'customer_id' => 'nullable|exists:customers,id',
                // 'reservation_id' => 'nullable|exists:reservations,id',

                
            ];       
    }
}
