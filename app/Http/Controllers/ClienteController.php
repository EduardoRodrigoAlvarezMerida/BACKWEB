<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClienteController extends Controller
{
    // Mostrar todos los clientes
    public function index()
{
    try {
        $clientes = Cliente::with('rol')->get(); // Cargar también la relación 'rol'
        return response()->json($clientes, 200);
    } catch (\Exception $e) {
        Log::error('Error al obtener los clientes: ' . $e->getMessage());
        return response()->json([
            'error' => 'Error al obtener los clientes',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    

    // Mostrar un cliente por ID
    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id); // Busca el cliente o lanza una excepción si no lo encuentra
            return response()->json($cliente, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error al mostrar el cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar el cliente'], 500);
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'Nombre' => 'required|string|max:255',
                'Apellido' => 'required|string|max:255',
                'Correo' => 'required|string|email|max:255|unique:clientes,correo',
                'Contraseña' => 'required|string|min:5',
                'Nit' => 'nullable|string|max:20',
                'IdRol' => 'nullable|exists:rols,IdRol', // Validar que el IdRol exista en la tabla roles si se proporciona
            ]);
    
            // Asignar valor por defecto a IdRol si no se envía
            $validatedData['IdRol'] = $validatedData['IdRol'] ?? 4;
    
            // Encriptar la contraseña
            $validatedData['Contraseña'] = Hash::make($validatedData['Contraseña']);
    
            // Crear el cliente en la base de datos
            $cliente = Cliente::create($validatedData);
    
            return response()->json([
                'message' => 'Cliente creado con éxito',
                'cliente' => $cliente,
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Error al crear el cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el cliente'], 500);
        }
    }
    
    

    // Actualizar un cliente
    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
    
            $validatedData = $request->validate([
                'Nombre' => 'required|string|max:255',
                'Apellido' => 'required|string|max:255',
                'Correo' => 'required|string|email|max:255',
                'NIT' => 'nullable|string|max:255',
                'Contraseña' => 'nullable|string|min:5',
                'IdRol' => 'nullable|exists:rols,IdRol', // Validar que el IdRol exista si se envía
            ]);
    
            if ($request->filled('Contraseña')) {
                $validatedData['Contraseña'] = bcrypt($request->Contraseña);
            } else {
                unset($validatedData['Contraseña']);
            }
    
            // Actualizar el cliente con los datos validados
            $cliente->update($validatedData);
    
            return response()->json([
                'message' => 'Cliente actualizado con éxito',
                'cliente' => $cliente,
            ], 200);
    
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el cliente'], 500);
        }
    }



    // Eliminar un cliente
    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id); // Busca el cliente o lanza una excepción si no lo encuentra

            $cliente->delete();

            return response()->json(['message' => 'Cliente eliminado con éxito'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el cliente'], 500);
        }
    }
}
