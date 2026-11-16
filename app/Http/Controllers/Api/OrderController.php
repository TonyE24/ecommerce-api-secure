<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
   public function index()
{
    $orders = auth('api')->user()->orders()->with(['items.product', 'payment'])->latest()->get();
    return response()->json(['success' => true, 'data' => $orders]);
}

public function store(StoreOrderRequest $request)
{
    return DB::transaction(function () use ($request) {
        $total = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::lockForUpdate()->findOrFail($item['product_id']);
            
            if ($product->stock < $item['quantity']) {
                abort(400, "Stock insuficiente para el producto: {$product->name}");
            }

            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];

            $product->decrement('stock', $item['quantity']);
        }

        $order = Order::create([
            'user_id' => auth('api')->id(),
            'total'   => $total,
            'status'  => 'pending',
        ]);

        $order->items()->createMany($orderItemsData);

        return response()->json([
            'success' => true,
            'message' => 'Orden creada exitosamente. Proceda al pago.',
            'data'    => $order->load('items.product'),
        ], 201);
    });
}
}
