<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // REGISTRO DE USUARIO NUEVO
    public function register(Request $request)
    {
        // 1. Validar datos
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users', // Email único
            'password' => 'required|string|min:6'
        ]);

        // 2. Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password) // NUNCA guardar contraseñas en texto plano
        ]);

        // 3. Crear el primer token (la llave)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Responder
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // INICIAR SESIÓN
    public function login(Request $request)
    {
        // 1. Validar que vengan los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Intentar autenticar (Auth::attempt hace el hash check automático)
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas (Email o contraseña erróneos)'
            ], 401); // 401 = Unauthorized
        }

        // 3. Si pasa, buscamos al usuario
        $user = User::where('email', $request->email)->firstOrFail();

        // 4. Generamos una nueva llave para esta sesión
        // Borramos tokens anteriores si queremos sesión única (opcional, aquí no lo hacemos para permitir login en celular y pc a la vez)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hola de nuevo, ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // CERRAR SESIÓN (Destruir la llave)
    public function logout(Request $request)
    {
        // Borra el token que se usó para esta petición específica
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}