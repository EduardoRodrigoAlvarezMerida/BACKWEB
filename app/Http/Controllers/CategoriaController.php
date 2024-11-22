<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    
    // Mostrar todas las categorías
    public function index()
    {
        try {
            $categorias = Categoria::all();
            return response()->json($categorias, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener las categorías: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener las categorías',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Mostrar una categoría por ID
    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id); // Busca la categoría o lanza una excepción si no lo encuentra
            return response()->json($categoria, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Error al mostrar la categoría: ' . $e->getMessage());
            return response()->json(['error' => 'Error al mostrar la categoría'], 500);
        }
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'Categoria' => 'required|string|max:255',
            ]);

            $categoria = Categoria::create($validatedData);

            return response()->json([
                'message' => 'Categoría creada con éxito',
                'categoria' => $categoria,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear la categoría: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear la categoría'], 500);
        }
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        try {
            // Buscar la categoría por su ID o lanzar excepción si no se encuentra
            $categoria = Categoria::findOrFail($id);

            // Validar los datos proporcionados
            $validatedData = $request->validate([
                'Categoria' => 'required|string|max:255',
            ]);

            // Actualizar la categoría con los datos validados
            $categoria->update($validatedData);

            return response()->json([
                'message' => 'Categoría actualizada con éxito',
                'categoria' => $categoria,
            ], 200);

        } catch (ModelNotFoundException $e) {
     
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
          
            Log::error('Error al actualizar la categoría: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la categoría'], 500);
        }
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id); 

            $categoria->delete();

            return response()->json(['message' => 'Categoría eliminada con éxito'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar la categoría: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la categoría'], 500);
        }
    }
}
