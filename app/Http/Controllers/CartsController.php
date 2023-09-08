<?php

namespace App\Http\Controllers;

use App\Http\Resources\carts\CartCollection;
use App\Models\Cart;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $carts = Cart::paginate(5);

        return $this->trueResponse('Cart list', CartCollection::collection($carts), $this->metaPagination($carts));
    }

    /**
     * Add a product to the cart
     */
    public function addToCart(Request $request, string $productId)
    {
        $this->validate($request, [
            'quantity' => 'required|numeric',
        ]);

        if (!$product = Product::find($productId)) {
            return $this->falseResponse('Product not found');
        }

        $existingCart = Cart::where('users_id', Auth::guard('api')->user()->id)
                    ->where('products_id', $productId)
                    ->first();
        
        if ($existingCart) 
        {
            if (($existingCart->quantity + $request->quantity) > $product->quantity) {
                return $this->falseResponse('Quantity is out of product stocks!');
            }

            $existingCart->quantity = $existingCart->quantity + $request->quantity;
            $existingCart->save();
        } else {
            Cart::create([
                'users_id' => Auth::guard('api')->user()->id,
                'products_id' => $product->id,
                'products_name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
            ]);
        }

        return $this->trueResponse('Add to cart success');
    }
    
    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(string $id)
    {
        if (!$cart = Cart::find($id)) {
            return $this->falseResponse('Cart not found');
        }

        $cart->delete();

        return $this->trueResponse('Remove from cart success');
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateCart(Request $request, string $id)
    {
        $this->validate($request, [
            'quantity' => 'required|numeric',
        ]);

        if (!$cart = Cart::find($id)) {
            return $this->falseResponse('Cart not found');
        }

        $cart->load('Product');

        if (!$cart->Product) {
            return $this->falseResponse('Product not found');
        }

        if ($request->quantity > $cart->Product->quantity) {
            return $this->falseResponse('Quantity is out of product stocks!');
        }

        $cart->update([
            'quantity' => $request->quantity,
        ]);

        return $this->trueResponse('Update cart success');
    }

    /**
     * Checkout the cart proceed to order.
     */
    public function checkout(Request $request)
    {
        $this->validate($request, [
            'address' => 'required|min:30'
        ]);
        
        $carts = Cart::where('users_id', Auth::guard('api')->user()->id)->get();

        if (!$carts) {
            return $this->falseResponse('Cart is empty');
        }

        $checkout = (new OrderService())->checkoutFromCart($carts, Auth::guard('api')->user()->id, $request->address);

        if (!$checkout['status']) {
            return $this->falseResponse($checkout['message']);
        }

        return $this->trueResponse('Checkout success');
    }
}
