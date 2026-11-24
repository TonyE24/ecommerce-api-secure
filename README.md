# API para tienda online

API para gestionar productos, órdenes y pagos de una tienda online. Está hecha
con Laravel 12 y usa JWT para iniciar sesión, Stripe en modo de prueba y Swagger
para consultar sus endpoints.

## Qué necesitas

- PHP 8.2 o superior con `pdo_mysql`, `openssl` y `mbstring`.
- Composer.
- MySQL 8 o superior.
- Node.js y npm para compilar los assets de Laravel.
- Una cuenta de Stripe. Para probar, usa sus claves de test.

## Instalación

```bash
composer install
```

Copia `.env.example` y nómbralo `.env`. En PowerShell puedes hacerlo así:

```powershell
Copy-Item .env.example .env
```

En `.env`, agrega los datos de conexión a MySQL y las claves de prueba de
Stripe. Después corre:

```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

La API queda en `http://127.0.0.1:8000`. Puedes probar los endpoints desde
Swagger en `http://127.0.0.1:8000/api/documentation`.

## Iniciar sesión

Regístrate o inicia sesión en `/api/auth/register` o `/api/auth/login`. La
respuesta incluye un token JWT. En las rutas privadas, envíalo en el header:

```text
Authorization: Bearer <token>
```

## Rutas principales

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

Para crear una orden, manda una lista de productos con su cantidad:

```json
{
  "items": [
    { "product_id": 1, "quantity": 2 }
  ]
}
```

Después puedes pagarla con su `order_id` y un `payment_method_id`. Para probar
con Stripe usa `pm_card_visa`. No envíes números de tarjeta a esta API.

## Algunas notas

- Laravel guarda las contraseñas con hashing. Los tokens JWT caducan y se
  invalidan al cerrar sesión.
- El servidor calcula el total y actualiza el stock dentro de una transacción.
- Los errores de la API se devuelven en JSON. Los de validación incluyen los
  campos que hay que corregir.
- No subas tu `.env` a GitHub. Ahí van tus claves; `.env.example` solo sirve
  como plantilla.

## Pruebas

```bash
php artisan test
```

Para cargar los productos de ejemplo, ejecuta `php artisan db:seed`.
