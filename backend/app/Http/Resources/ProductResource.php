<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->total_price,
            'image' => $this->image,
            'status' => $this->status,
            'description' => $this->description,
            'discount' => $this->discount,
            'extra' => $this->extra,
            'category' => new CategoryResource($this->category),
            'ingredients' => new IngredientResource($this->ingredients),

        ];
    }
}
