<?php

namespace App\Http\Controllers;

use App\Http\Resources\products\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $products = Product::paginate(5);

        return $this->trueResponse('Product list', ProductCollection::collection($products), $this->metaPagination($products));
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
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required',
            'quantity' => 'required|numeric|max:999',
            'price' => 'required|decimal:2',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return $this->trueResponse('Create product success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$product = Product::find($id)) {
            return $this->falseResponse('Product not found');
        }

        return $this->trueResponse('Show product', new ProductCollection($product));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!$product = Product::find($id)) {
            return $this->falseResponse('Product not found');
        }

        return $this->trueResponse('Edit product', new ProductCollection($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!$product = Product::find($id)) {
            return $this->falseResponse('Product not found');
        }
        
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required',
            'quantity' => 'required|numeric|max:999',
            'price' => 'required|decimal:2',
        ]);
        
        $product->name = $request->name;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->price = $request->price;
        $product->save();

        return $this->trueResponse('Update product success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$product = Product::find($id)) {
            return $this->falseResponse('Product not found');
        }

        $product->delete();

        return $this->trueResponse('Delete product success');
    }
}
