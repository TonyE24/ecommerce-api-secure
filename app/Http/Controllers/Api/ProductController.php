<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente.',
            'data'    => $product,
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente.',
            'data'    => $product,
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }
}
