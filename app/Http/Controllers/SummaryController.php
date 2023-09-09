<?php

namespace App\Http\Controllers;

use App\Http\Resources\summary\OrderCollection;
use App\Http\Resources\summary\ProductCollection;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SummaryController extends Controller
{
    /**
     * Show order summary.
     */
    public function order(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'required_with:start_date|date_format:Y-m-d|after_or_equal:start_date',

        ]);

        $orderSummary = Order::where('users_id', Auth::guard('api')->user()->id)
                    ->where(function ($q) use ($request) {
                        if ($request->start_date && $request->end_date) {
                            $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
                        }
                    })
                    ->paginate(5);

        $orderSummary->load('OrderItem.Product');

        return $this->trueResponse('Order summary', OrderCollection::collection($orderSummary), $this->metaPagination($orderSummary));
    }

    /**
     * Show product summary.
     */
    public function product(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'required_with:start_date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $productSummary = Product::select(DB::raw(
                    'products.id AS product_id,'. 
                    'products.name AS product_name,'.
                    'products.price AS product_price,'.
                    'products.description AS product_description,'.
                    'order_items.id AS order_items_id,'. 
                    'order_items.quantity AS order_items_quantity,'. 
                    'orders.id AS orders_id,'. 
                    'orders.total_amount AS total_amount,'. 
                    'orders.created_at AS orders_created_at'
                ))
                ->join('order_items', function ($q) {
                    $q->on('order_items.products_id', '=', 'products.id');
                })
                ->join('orders', function ($q) use ($request) {
                    $q->on('orders.id', '=', 'order_items.orders_id')
                        ->where('orders.users_id', Auth::guard('api')->user()->id)
                        ->where(function ($q) use ($request) {
                            if ($request->start_date && $request->end_date) {
                                $q->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
                            }
                        });
                })->paginate(5);

        return $this->trueResponse('Product summary', $productSummary->items(), $this->metaPagination($productSummary));
    }
}
