<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API de E-commerce Segura",
    description: "Documentación oficial de la API RESTful con JWT y Stripe"
)]
#[OA\Server(url: "http://127.0.0.1:8000", description: "Servidor Local")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Tag(name: "Autenticación", description: "Registro y sesiones JWT")]
#[OA\Tag(name: "Productos", description: "Catálogo de productos")]
#[OA\Tag(name: "Órdenes", description: "Compras e historial")]
#[OA\Tag(name: "Pagos", description: "Procesamiento de pagos con Stripe")]
abstract class Controller
{
    //
}