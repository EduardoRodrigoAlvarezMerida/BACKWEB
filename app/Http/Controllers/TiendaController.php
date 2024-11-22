<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tienda;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TiendaController extends Controller
{
    public function index()
    {
        try {
            $tienda = Tienda::all();
            return response()->json($tienda, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener las tiendas: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener las tiendas',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'direccion' => 'required',
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
            ]);

            $tienda = new Tienda();
            $tienda->nombre = $request->input('nombre');
            $tienda->direccion = $request->input('direccion');
            $tienda->latitud = $request->input('latitud');
            $tienda->longitud = $request->input('longitud');
            $tienda->save();

            return response()->json(['mensaje' => 'Tienda creada con éxito'], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear la tienda: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear la tienda',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $tienda = Tienda::find($id);

            if (!$tienda) {
                return response()->json(['error' => 'Tienda no encontrada'], 404);
            }

            $request->validate([
                'nombre' => 'required',
                'direccion' => 'required',
                'latitud' => 'required|numeric|between:-90,90',
                'longitud' => 'required|numeric|between:-180,180',
            ]);

            $tienda->nombre = $request->input('nombre');
            $tienda->direccion = $request->input('direccion');
            $tienda->latitud = $request->input('latitud');
            $tienda->longitud = $request->input('longitud');
            $tienda->save();

            return response()->json(['mensaje' => 'Tienda actualizada con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la tienda: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar la tienda',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $tienda = Tienda::find($id);

            if (!$tienda) {
                return response()->json(['error' => 'Tienda no encontrada'], 404);
            }

            $tienda->delete();

            return response()->json(['mensaje' => 'Tienda eliminada con éxito'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la tienda: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al eliminar la tienda',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
