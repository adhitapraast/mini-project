<?php

namespace App\Http\Resources\summary;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        dd($request->toArray());
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product_name,
            'product_price' => $this->product_price,
            'product_description' => $this->product_description,
            'order_items_id' => $this->order_items_id,
            'order_items_quantity' => $this->order_items_quantity,
            'orders_id' => $this->orders_id,
            'total_amount' => $this->total_amount,
            'orders_created_at' => $this->orders_created_at,
        ];
    }
}
