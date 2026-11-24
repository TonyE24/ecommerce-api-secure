# E-commerce Secure API

API RESTful para un e-commerce desarrollada con Laravel 12, PHP 8.2+, MySQL,
JWT, Stripe y Swagger/OpenAPI.

## Requisitos

- PHP 8.2 o superior con extensiones `pdo_mysql`, `openssl` y `mbstring`.
- Composer.
- MySQL 8+.
- Node.js y npm (solo para los assets de Laravel).
- Una cuenta de Stripe en modo prueba.

## Instalación

```bash
composer install
```

Copia `.env.example` como `.env` (en PowerShell: `Copy-Item .env.example .env`) y configura las credenciales de MySQL y las claves de prueba de Stripe. Luego ejecuta:

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

La documentación interactiva está disponible en
`http://127.0.0.1:8000/api/documentation`.

## Autenticación

`POST /api/auth/register` y `POST /api/auth/login` devuelven un JWT. En las
rutas protegidas envía:

```text
Authorization: Bearer <token>
```

## Endpoints

| Método | Endpoint | Acceso |
| --- | --- | --- |
| POST | `/api/auth/register` | Público |
| POST | `/api/auth/login` | Público |
| GET | `/api/auth/me` | JWT |
| POST | `/api/auth/logout` | JWT |
| GET | `/api/products` | Público |
| GET | `/api/products/{id}` | Público |
| POST | `/api/products` | JWT |
| PUT, DELETE | `/api/products/{id}` | JWT |
| GET/POST | `/api/orders` | JWT |
| POST | `/api/payments/process` | JWT |

Para crear una orden envía `items` como una lista de objetos:

```json
{
  "items": [
    { "product_id": 1, "quantity": 2 }
  ]
}
```

Para pagarla, envía `order_id` y `payment_method_id`. En modo de pruebas de Stripe puede usarse `pm_card_visa`; no envíes datos reales de tarjetas a esta API.

## Seguridad y errores

- Las contraseñas se almacenan con hashing de Laravel.
- Los JWT expiran y se invalidan al cerrar sesión.
- El total de la orden se calcula en servidor y el stock se bloquea dentro de
  una transacción.
- Las respuestas de validación, autenticación y recursos inexistentes usan JSON
  con `success`, `message` y, cuando aplica, `errors`.
- No se guardan claves secretas en el repositorio; usa `.env`.

## Pruebas

```bash
php artisan test
```

Los productos de ejemplo se cargan mediante `ProductSeeder`.
