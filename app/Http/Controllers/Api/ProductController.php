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
        new OA\Response(response: 200, description: 'Catálogo de productos', content: new OA\JsonContent(ref: '#/components/schemas/ProductListResponse')),
        new OA\Response(response: 500, description: 'Error interno', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
    ])]
    public function index()
    {
        $products = Product::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    #[OA\Post(path: '/api/products', tags: ['Productos'], summary: 'Crear producto', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'price', 'stock'], properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Teclado mecánico'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Switches táctiles'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.5),
        new OA\Property(property: 'stock', type: 'integer', example: 25),
    ])), responses: [
        new OA\Response(response: 201, description: 'Producto creado', content: new OA\JsonContent(ref: '#/components/schemas/ProductMutationResponse')),
        new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 422, description: 'Datos inválidos', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 500, description: 'Error interno', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
    ])]
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente.',
            'data' => $product,
        ], 201);
    }

    #[OA\Get(path: '/api/products/{product}', tags: ['Productos'], summary: 'Consultar producto', parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], responses: [
        new OA\Response(response: 200, description: 'Producto encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ProductResponse')),
        new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 500, description: 'Error interno', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
    ])]
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    #[OA\Put(path: '/api/products/{product}', tags: ['Productos'], summary: 'Actualizar producto', security: [['bearerAuth' => []]], parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Teclado mecánico'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Switches táctiles'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.5),
        new OA\Property(property: 'stock', type: 'integer', example: 25),
    ])), responses: [
        new OA\Response(response: 200, description: 'Producto actualizado', content: new OA\JsonContent(ref: '#/components/schemas/ProductMutationResponse')),
        new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 422, description: 'Datos inválidos', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 500, description: 'Error interno', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
    ])]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente.',
            'data' => $product,
        ]);
    }

    #[OA\Delete(path: '/api/products/{product}', tags: ['Productos'], summary: 'Eliminar producto', security: [['bearerAuth' => []]], parameters: [
        new OA\Parameter(name: 'product', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ], responses: [
        new OA\Response(response: 200, description: 'Producto eliminado', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
        new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 404, description: 'Producto no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        new OA\Response(response: 500, description: 'Error interno', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
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
