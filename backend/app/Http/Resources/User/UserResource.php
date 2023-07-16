<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image,
            'role' => $this->role,
        
        ];    
    
    }
    public function with($request)
    {

        if ($request->route()->getName() === 'users.show') {
            return [];
        }
        return [
            'meta' => [
                'pagination' => [
                    'total' => $this->resource->total(),
                    'per_page' => $this->resource->perPage(),
                    'current_page' => $this->resource->currentPage(),
                    'last_page' => $this->resource->lastPage(),
                    'from' => $this->resource->firstItem(),
                    'to' => $this->resource->lastItem(),
                ]
            ],
        ];
    }





}
