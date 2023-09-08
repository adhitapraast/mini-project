<?php

namespace App\Http\Resources\orders;

use App\Http\Resources\orderItems\OrderItemCollection;
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
        ];

        if ($this->relationLoaded('OrderItem')) {
            $data['order_items'] = $this->OrderItem->toArray();
        }

        return $data;
    }
}
