<?php

namespace App\Http\Resources\summary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'address' => $this->address,
            'created_at' => $this->created_at,
        ];

        if ($this->relationLoaded('OrderItem')) {
            $data['order_items'] = $this->OrderItem->toArray();

            if ($this->relationLoaded('OrderItem.Product')) {
                $data['order_items']['product'] = $this->Product->toArray();
            }
        }

        return $data;
    }
}
