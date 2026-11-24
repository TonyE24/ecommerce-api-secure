<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(path: '/api/orders', tags: ['Órdenes'], summary: 'Consultar historial del cliente', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 200, description: 'Historial de órdenes'),
        new OA\Response(response: 401, description: 'No autenticado'),
    ])]
    public function index()
    {
        $orders = auth('api')->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    #[OA\Post(path: '/api/orders', tags: ['Órdenes'], summary: 'Crear una orden', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['items'], properties: [
        new OA\Property(property: 'items', type: 'array', minItems: 1, items: new OA\Items(type: 'object', required: ['product_id', 'quantity'], properties: [
            new OA\Property(property: 'product_id', type: 'integer', example: 1),
            new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
        ])),
    ])), responses: [
        new OA\Response(response: 201, description: 'Orden creada'),
        new OA\Response(response: 401, description: 'No autenticado'),
        new OA\Response(response: 422, description: 'Stock insuficiente o datos inválidos'),
    ])]
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $items = $request->validated('items');
            $products = [];
            $quantities = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $products[$productId] ??= Product::lockForUpdate()->findOrFail($productId);
                $quantities[$productId] = ($quantities[$productId] ?? 0) + $item['quantity'];
            }

            foreach ($quantities as $productId => $quantity) {
                $product = $products[$productId];

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para el producto: {$product->name}."],
                    ]);
                }
            }

            $totalInCents = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $product = $products[$item['product_id']];
                $unitPriceInCents = (int) round((float) $product->price * 100);
                $totalInCents += $unitPriceInCents * $item['quantity'];
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            foreach ($quantities as $productId => $quantity) {
                $products[$productId]->decrement('stock', $quantity);
            }

            $order = Order::create([
                'user_id' => auth('api')->id(),
                'total' => $totalInCents / 100,
                'status' => 'pending',
            ]);
            $order->items()->createMany($orderItemsData);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente. Proceda al pago.',
                'data' => $order->load('items.product'),
            ], 201);
        });
    }
}
