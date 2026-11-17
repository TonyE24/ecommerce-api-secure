<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    //
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

    #[OA\Post(path: '/api/orders', tags: ['Órdenes'], summary: 'Crear una orden', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 201, description: 'Orden creada'),
        new OA\Response(response: 401, description: 'No autenticado'),
        new OA\Response(response: 422, description: 'Stock insuficiente o datos inválidos'),
    ])]
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $total = 0;
            $orderItemsData = [];

            foreach ($request->validated('items') as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto: {$product->name}.",
                    ], 422);
                }

                $total += $product->price * $item['quantity'];
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id' => auth('api')->id(),
                'total' => $total,
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
