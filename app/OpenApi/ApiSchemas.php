<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'User', type: 'object', required: ['id', 'name', 'email'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Ana Pérez'),
    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ana@example.com'),
    new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'Product', type: 'object', required: ['id', 'name', 'price', 'stock'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'name', type: 'string', example: 'Teclado mecánico'),
    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Switches táctiles'),
    new OA\Property(property: 'price', type: 'string', example: '89.50'),
    new OA\Property(property: 'stock', type: 'integer', example: 25),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'OrderItem', type: 'object', required: ['id', 'product_id', 'quantity', 'unit_price', 'product'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'product_id', type: 'integer', example: 1),
    new OA\Property(property: 'quantity', type: 'integer', example: 2),
    new OA\Property(property: 'unit_price', type: 'string', example: '89.50'),
    new OA\Property(property: 'product', ref: '#/components/schemas/Product'),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'Payment', type: 'object', required: ['id', 'order_id', 'stripe_payment_intent_id', 'amount', 'currency', 'status'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'order_id', type: 'integer', example: 1),
    new OA\Property(property: 'stripe_payment_intent_id', type: 'string', example: 'pi_3Example'),
    new OA\Property(property: 'amount', type: 'string', example: '179.00'),
    new OA\Property(property: 'currency', type: 'string', example: 'usd'),
    new OA\Property(property: 'status', type: 'string', example: 'succeeded'),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'Order', type: 'object', required: ['id', 'user_id', 'total', 'status', 'items'], properties: [
    new OA\Property(property: 'id', type: 'integer', example: 1),
    new OA\Property(property: 'user_id', type: 'integer', example: 1),
    new OA\Property(property: 'total', type: 'string', example: '179.00'),
    new OA\Property(property: 'status', type: 'string', enum: ['pending', 'paid', 'failed', 'canceled'], example: 'pending'),
    new OA\Property(property: 'items', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderItem')),
    new OA\Property(property: 'payment', ref: '#/components/schemas/Payment', nullable: true),
    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
])]
#[OA\Schema(schema: 'ErrorResponse', type: 'object', required: ['success', 'message'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: false),
    new OA\Property(property: 'message', type: 'string', example: 'Error de validación en los datos enviados.'),
    new OA\Property(property: 'errors', type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')), example: ['email' => ['The email field is required.']]),
])]
#[OA\Schema(schema: 'MessageResponse', type: 'object', required: ['success', 'message'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'message', type: 'string', example: 'Operación completada correctamente.'),
])]
#[OA\Schema(schema: 'AuthTokenResponse', type: 'object', required: ['success', 'message', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'message', type: 'string', example: 'Inicio de sesión exitoso.'),
    new OA\Property(property: 'data', type: 'object', required: ['user', 'access_token', 'token_type'], properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'access_token', type: 'string', example: 'eyJhbGciOiJIUzI1NiJ9...'),
        new OA\Property(property: 'token_type', type: 'string', enum: ['bearer'], example: 'bearer'),
    ]),
])]
#[OA\Schema(schema: 'CurrentUserResponse', type: 'object', required: ['success', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'data', ref: '#/components/schemas/User'),
])]
#[OA\Schema(schema: 'ProductListResponse', type: 'object', required: ['success', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
])]
#[OA\Schema(schema: 'ProductResponse', type: 'object', required: ['success', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
])]
#[OA\Schema(schema: 'ProductMutationResponse', type: 'object', required: ['success', 'message', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'message', type: 'string', example: 'Producto creado exitosamente.'),
    new OA\Property(property: 'data', ref: '#/components/schemas/Product'),
])]
#[OA\Schema(schema: 'OrderListResponse', type: 'object', required: ['success', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Order')),
])]
#[OA\Schema(schema: 'OrderResponse', type: 'object', required: ['success', 'message', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'message', type: 'string', example: 'Orden creada exitosamente. Proceda al pago.'),
    new OA\Property(property: 'data', ref: '#/components/schemas/Order'),
])]
#[OA\Schema(schema: 'PaymentResponse', type: 'object', required: ['success', 'message', 'data'], properties: [
    new OA\Property(property: 'success', type: 'boolean', example: true),
    new OA\Property(property: 'message', type: 'string', example: 'Pago procesado correctamente.'),
    new OA\Property(property: 'data', ref: '#/components/schemas/Payment'),
])]
class ApiSchemas {}
