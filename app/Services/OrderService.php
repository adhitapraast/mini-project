<?php

namespace App\Services;

use App\Enums\OrderEnum;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderService 
{
    public function checkoutFromCart(Collection $carts, string|int $userId, string $address): array|bool
    {
        DB::transaction(function () use ($carts, $userId, $address) {
            $totalAmount = 0;
            $orderItem = [];
            
            $order = new Order();
            $order->users_id = $userId;
            $order->total_amount = $totalAmount;
            $order->status = OrderEnum::PENDING;
            $order->address = $address;
            $order->save();

            foreach ($carts as $cart) {
                $totalAmount = $totalAmount + ($cart->price * $cart->quantity);
                $orderItem[] = [
                    'orders_id' => $order->id,
                    'products_id' => $cart->products_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price,
                ];
            }
    
            OrderItem::insert($orderItem);
            
            $order->total_amount = $totalAmount;
            $order->save();

            Cart::whereIn('id', $carts->pluck('id'))->delete();
        }, 3);

        return [
            'status' => true,
            'message' => 'Checkout success'
        ];
    }
}
