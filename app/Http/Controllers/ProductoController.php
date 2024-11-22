<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Producto;
use App\Models\Inventario;

class ProductoController extends Controller
{
    // Método para obtener todos los productos
    public function index()
    {
        try {
            // Cargar productos con sus categorías
            $productos = Producto::with('categoria')->get();
            return response()->json($productos, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener los productos: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al obtener los productos',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para crear un nuevo producto
    public function store(Request $request)
    {
        try {
            $request->validate([
                'Nombre' => 'nullable|string',
                'Descripcion' => 'nullable|string',
                'Precio' => 'nullable|numeric',
                'IdCategoria' => 'nullable|exists:categorias,IdCategoria',
            ]);

            if ($request->hasFile('NombreImagen')) {
                $request->validate([
                    'NombreImagen' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
            } elseif ($request->filled('NombreImagen')) {
                $request->validate([
                    'NombreImagen' => 'string|max:65535',
                ]);
            }

            $imageBase64 = null;
            if ($request->hasFile('NombreImagen')) {
                $image = $request->file('NombreImagen');
                $imageBase64 = base64_encode(file_get_contents($image->path()));
            } elseif ($request->filled('NombreImagen')) {
                $imageBase64 = $request->input('NombreImagen');
            }

            $producto = Producto::create([
                'Nombre' => $request->input('Nombre'),
                'Descripcion' => $request->input('Descripcion'),
                'Precio' => $request->input('Precio'),
                'IdCategoria' => $request->input('IdCategoria'),
                'NombreImagen' => $imageBase64,
            ]);

            return response()->json(['mensaje' => 'Producto creado con éxito'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear el producto: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear el producto',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para actualizar un producto
    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $request->validate([
                'Nombre' => 'required|string',
                'Descripcion' => 'required|string',
                'Precio' => 'required|numeric',
                'IdCategoria' => 'required|exists:categorias,IdCategoria',
            ]);

            if ($request->hasFile('NombreImagen')) {
                $request->validate([
                    'NombreImagen' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
            } elseif ($request->filled('NombreImagen')) {
                $request->validate([
                    'NombreImagen' => 'string|max:65535',
                ]);
            }

            $producto->Nombre = $request->input('Nombre');
            $producto->Descripcion = $request->input('Descripcion');
            $producto->Precio = $request->input('Precio');
            $producto->IdCategoria = $request->input('IdCategoria');

            if ($request->hasFile('NombreImagen')) {
                $image = $request->file('NombreImagen');
                $producto->NombreImagen = base64_encode(file_get_contents($image->path()));
            } elseif ($request->filled('NombreImagen')) {
                $producto->NombreImagen = $request->input('NombreImagen');
            }

            $producto->save();

            return response()->json(['mensaje' => 'Producto actualizado con éxito'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', ['errores' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el producto'], 500);
        }
    }

    // Método para obtener productos por categoría con cantidad total de inventario
    public function getProductosPorCategoria($idCategoria)
{
    try {
        $productos = Producto::where('IdCategoria', $idCategoria)
            ->with('categoria', 'inventarios.tienda') // Relación con inventarios y tienda
            ->get();

        if ($productos->isEmpty()) {
            return response()->json(['mensaje' => 'No hay productos en esta categoría'], 404);
        }

        $productosConCantidadTotal = $productos->map(function ($producto) {
            return [
                'IdProducto' => $producto->IdProducto,
                'Nombre' => $producto->Nombre,
                'Descripcion' => $producto->Descripcion,
                'Precio' => $producto->Precio,
                'IdCategoria' => $producto->IdCategoria,
                'NombreImagen' => $producto->NombreImagen,
                'categoria' => $producto->categoria,
                'cantidad_total_inventarios' => $producto->inventarios->sum('Cantidad'), // Total de inventarios
                'tiendas' => $producto->inventarios->map(function ($inventario) {
                    return [
                        'IdTienda' => $inventario->IdTienda,
                        'NombreTienda' => $inventario->tienda->Nombre, // Nombre de la tienda
                        'Cantidad' => $inventario->Cantidad, // Cantidad disponible
                    ];
                }),
            ];
        });

        return response()->json($productosConCantidadTotal, 200);
    } catch (\Exception $e) {
        Log::error('Error al obtener los productos por categoría: ' . $e->getMessage());
        return response()->json([
            'error' => 'Error al obtener los productos por categoría',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    // Método para eliminar un producto
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            Inventario::where('IdProducto', $id)->delete();
            $producto->delete();

            return response()->json(['mensaje' => 'Producto y su inventario eliminado con éxito'], 200);
        } catch (\ModelNotFoundException $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el producto: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al eliminar el producto',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
