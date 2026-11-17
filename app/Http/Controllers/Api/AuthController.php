<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(path: '/api/auth/register', tags: ['Autenticación'], summary: 'Registrar un cliente', responses: [
        new OA\Response(response: 201, description: 'Usuario registrado'),
        new OA\Response(response: 422, description: 'Datos inválidos'),
    ])]
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente.',
            'data'    => [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'bearer',
            ],
        ], 201);
    }

    #[OA\Post(path: '/api/auth/login', tags: ['Autenticación'], summary: 'Iniciar sesión', responses: [
        new OA\Response(response: 200, description: 'Token JWT generado'),
        new OA\Response(response: 401, description: 'Credenciales inválidas'),
    ])]
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'data'    => [
                'user'         => auth('api')->user(),
                'access_token' => $token,
                'token_type'   => 'bearer',
            ],
        ]);
    }

    #[OA\Get(path: '/api/auth/me', tags: ['Autenticación'], summary: 'Consultar usuario autenticado', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 200, description: 'Usuario autenticado'),
        new OA\Response(response: 401, description: 'No autenticado'),
    ])]
    public function me()
    {
        return response()->json([
            'success' => true,
            'data'    => auth('api')->user(),
        ]);
    }

    #[OA\Post(path: '/api/auth/logout', tags: ['Autenticación'], summary: 'Invalidar token JWT', security: [['bearerAuth' => []]], responses: [
        new OA\Response(response: 200, description: 'Sesión cerrada'),
        new OA\Response(response: 401, description: 'No autenticado'),
    ])]
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
