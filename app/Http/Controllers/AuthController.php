<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Cliente;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'Correo' => 'required|email',
            'Contraseña' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Buscar al cliente por correo
        $cliente = Cliente::where('Correo', $request->Correo)->first();

        if ($cliente && Hash::check($request->Contraseña, $cliente->Contraseña)) {
            // Iniciar sesión
            Auth::login($cliente);

            // Login exitoso: Aquí puedes devolver solo el nombre, apellido y otros datos si es necesario
            return response()->json([
                'success' => true,
                'Cliente' => [
                    'IdCliente' => $cliente->IdCliente,
                    'nombre' => $cliente->Nombre,
                    'apellido' => $cliente->Apellido,
                    'IdRol' => $cliente->IdRol,
                ]
            ]);
        } else {
            // Login fallido
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }
    }
}
