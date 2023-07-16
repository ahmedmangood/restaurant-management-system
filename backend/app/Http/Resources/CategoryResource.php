<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'image' => $this->image,
            'status' => $this->status,
        ];    
    }

    public function with($request)
    {

        if ($request->route()->getName() === 'category.index') {
            return [];
        }
        return [
            'meta' => [
                'pagination' => [
                    'total' => $this->resource->total(),
                    'per_page' => $this->resource->per_page(),
                    'current_page' => $this->resource->currentPage(),
                    'last_page' => $this->resource->lastPage(),
                    'from' => $this->resource->firstItem(),
                    'to' => $this->resource->lastItem(),
                ]
            ],
        ];
    }
}
