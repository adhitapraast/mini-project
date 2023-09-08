<?php

namespace App\Http\Resources\orderItems;

use App\Http\Resources\products\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderItemCollection extends ResourceCollection
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
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];

        if ($this->relationLoaded('Product')) {
            $data['products_name'] = $this->Product->name;
        }

        return $data;
    }
}
