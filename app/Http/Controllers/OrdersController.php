<?php

namespace App\Http\Controllers;

use App\Enums\OrderEnum;
use App\Http\Resources\orders\OrderCollection;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list($status = '')
    {
        $orders = Order::where('users_id', Auth::guard('api')->user()->id)
                ->where(function ($q) use ($status) {
                    if ($status && in_array($status, array_column(OrderEnum::cases(), 'value'))) {
                        $q->where('status', $status);
                    }
                })->paginate(5);
        
        return $this->trueResponse('Order list', OrderCollection::collection($orders), $this->metaPagination($orders));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$order = Order::find($id)) {
            return $this->falseResponse('Order not found');
        }

        $order->load('OrderItem');

        return $this->trueResponse('Show order', new OrderCollection($order));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 
    }
}
