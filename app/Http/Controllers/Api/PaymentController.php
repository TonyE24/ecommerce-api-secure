<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    #[OA\Post(path: '/api/payments/process', tags: ['Pagos'], summary: 'Procesar pago con Stripe', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 200, description: 'Pago procesado'),
        new OA\Response(response: 401, description: 'No autenticado'),
        new OA\Response(response: 402, description: 'Stripe rechazó el pago'),
        new OA\Response(response: 422, description: 'Datos inválidos'),
    ])]
    public function pay(ProcessPaymentRequest $request)
    {
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth('api')->id())
            ->firstOrFail();

        if ($order->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Esta orden ya fue pagada.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));

        try {
            // Stripe maneja los montos en centavos (ej. $10.50 -> 1050)
            $amountInCents = (int) round($order->total * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'payment_method' => $request->validated('payment_method_id'),
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id'  => auth('api')->id(),
                ],
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total,
                'currency' => 'usd',
                'status' => $paymentIntent->status,
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $order->update(['status' => 'paid']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado correctamente.',
                'data'    => $payment,
            ]);

        } catch (ApiErrorException $e) {
            $order->update(['status' => 'failed']);
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error en la pasarela de pago Stripe.',
            ], 402);
        }
    }
}