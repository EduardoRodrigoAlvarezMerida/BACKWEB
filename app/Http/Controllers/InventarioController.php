<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\LogInventario;


class InventarioController extends Controller
{

    public function show($id)
{
    try {
        $inventario = Inventario::find($id);

        if (!$inventario) {
            return response()->json(['error' => 'Producto no encontrado en el inventario'], 404);
        }

        return response()->json($inventario, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al obtener el producto'], 500);
    }
}


public function getProductos()
{
    $productos = Producto::all();
    return response()->json($productos,200);
}

public function getTiendas()
{
    $tiendas = Tienda::all();   
    return response()->json($tiendas, 200);
}

public function index($idTienda)
{
    try {
        // Cargar inventario con sus productos
        $inventario = Inventario::where('IdTienda', $idTienda)
            ->with('producto')
            ->get();

        return response()->json($inventario, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Ocurrió un error al obtener el inventario'], 500);
    }
}


public function trasladarProducto(Request $request)
{
    try {
        // Validar la entrada
        $request->validate([
            'IdProducto' => 'required|exists:productos,IdProducto',
            'IdTiendaOrigen' => 'required|exists:tiendas,IdTienda',
            'IdTiendaDestino' => 'required|exists:tiendas,IdTienda|different:IdTiendaOrigen',
            'Cantidad' => 'required|numeric|min:1',
        ]);

        // Verificar el inventario en la tienda de origen
        $inventarioOrigen = Inventario::where('IdTienda', $request->input('IdTiendaOrigen'))
            ->where('IdProducto', $request->input('IdProducto'))
            ->first();

        // Si el producto no está en el inventario de la tienda de origen, mostrar mensaje
        if (!$inventarioOrigen) {
            return response()->json(['error' => 'El producto no está disponible en el inventario de la tienda de origen'], 404);
        }

        // Verificar si hay suficiente cantidad en la tienda de origen
        if ($inventarioOrigen->Cantidad < $request->input('Cantidad')) {
            return response()->json(['error' => 'No hay suficiente cantidad en el inventario de origen'], 409);
        }

        // Verificar o crear el inventario en la tienda de destino
        $inventarioDestino = Inventario::firstOrNew([
            'IdTienda' => $request->input('IdTiendaDestino'),
            'IdProducto' => $request->input('IdProducto')
        ]);

        // Realizar el traslado
        $cantidadTrasladar = $request->input('Cantidad');
        $inventarioOrigen->Cantidad -= $cantidadTrasladar;
        $inventarioOrigen->save();

        $inventarioDestino->Cantidad += $cantidadTrasladar;
        $inventarioDestino->save();

        return response()->json(['mensaje' => 'Producto trasladado con éxito'], 200);
    } catch (\Exception $e) {
        Log::error('Error al trasladar el producto: ' . $e->getMessage());
        return response()->json(['error' => 'Ocurrió un error al trasladar el producto'], 500);
    }
}



public function store(Request $request)
{
    try {
        $request->validate([
            'IdTienda' => 'required|exists:tiendas,IdTienda',
            'IdProducto' => 'required|exists:productos,IdProducto',
            'Cantidad' => 'required|numeric',
            'LogDescripcion' => 'required|string' // Validar que se envíe la descripción del log
        ]);

        // Verificar si el producto ya existe en el inventario de la tienda
        $existeProducto = Inventario::where('IdTienda', $request->input('IdTienda'))
            ->where('IdProducto', $request->input('IdProducto'))
            ->first();

        if ($existeProducto) {
            return response()->json(['error' => 'El producto ya existe en el inventario de esta tienda'], 409);
        }

        // Crear el producto en el inventario
        Inventario::create([
            'IdTienda' => $request->input('IdTienda'),
            'IdProducto' => $request->input('IdProducto'),
            'Cantidad' => $request->input('Cantidad'),
        ]);

        // Guardar el log
        LogInventario::create([
            'Descripcion' => $request->input('LogDescripcion') // Usar la descripción enviada desde el frontend
        ]);

        return response()->json(['mensaje' => 'Producto agregado al inventario con éxito'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function update(Request $request, $id)
{
    try {
        $inventario = Inventario::find($id);

        if (!$inventario) {
            return response()->json(['error' => 'Producto no encontrado en el inventario'], 404);
        }

        $request->validate([
            'Cantidad' => 'required|numeric',
            'LogDescripcion' => 'required|string' // Validar que se envíe la descripción del log
        ]);

        // Obtener la cantidad anterior
        $cantidadAnterior = $inventario->Cantidad;

        // Actualizar la cantidad
        $inventario->Cantidad = $request->input('Cantidad');
        $inventario->save();

        // Guardar el log
        LogInventario::create([
            'Descripcion' => $request->input('LogDescripcion') // Adjuntar cantidad anterior
        ]);

        return response()->json(['mensaje' => 'Producto actualizado en el inventario con éxito'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function destroy(Request $request, $id)
{
    try {
        $inventario = Inventario::find($id);

        if (!$inventario) {
            return response()->json(['error' => 'Producto no encontrado en el inventario'], 404);
        }

        // Eliminar el producto del inventario
        $inventario->delete();

        // Guardar el log en la tabla logs
        if ($request->has('LogDescripcion')) {
            LogInventario::create([
                'Descripcion' => $request->input('LogDescripcion') // Guardar la descripción enviada desde el frontend
            ]);
        }

        return response()->json(['mensaje' => 'Producto eliminado del inventario con éxito'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    
}
