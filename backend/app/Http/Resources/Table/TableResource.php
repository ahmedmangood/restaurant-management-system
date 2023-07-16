<?php

namespace App\Http\Resources\Table;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,
            'number'=> $this->number,
            'number'=> $this->number,
            'guest_numbers'=>$this->guest_numbers,
            'status'=>$this->status,
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
