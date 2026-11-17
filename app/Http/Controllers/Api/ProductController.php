<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(path: '/api/products', tags: ['Productos'], summary: 'Listar catálogo público', responses: [
        new OA\Response(response: 200, description: 'Catálogo de productos'),
    ])]
    public function index()
    {
        $products = Product::latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    #[OA\Post(path: '/api/products', tags: ['Productos'], summary: 'Crear producto', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 201, description: 'Producto creado'),
        new OA\Response(response: 401, description: 'No autenticado'),
        new OA\Response(response: 422, description: 'Datos inválidos'),
    ])]
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente.',
            'data'    => $product,
        ], 201);
    }

    #[OA\Get(path: '/api/products/{product}', tags: ['Productos'], summary: 'Consultar producto', parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], responses: [
        new OA\Response(response: 200, description: 'Producto encontrado'),
        new OA\Response(response: 404, description: 'Producto no encontrado'),
    ])]
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    #[OA\Put(path: '/api/products/{product}', tags: ['Productos'], summary: 'Actualizar producto', security: [['bearerAuth' => []]], parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], responses: [
        new OA\Response(response: 200, description: 'Producto actualizado'),
        new OA\Response(response: 401, description: 'No autenticado'),
        new OA\Response(response: 422, description: 'Datos inválidos'),
    ])]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente.',
            'data'    => $product,
        ]);
    }

    #[OA\Delete(path: '/api/products/{product}', tags: ['Productos'], summary: 'Eliminar producto', security: [['bearerAuth' => []]], parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], responses: [
        new OA\Response(response: 200, description: 'Producto eliminado'),
        new OA\Response(response: 401, description: 'No autenticado'),
    ])]
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }
}
