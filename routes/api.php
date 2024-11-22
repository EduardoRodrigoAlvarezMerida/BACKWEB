<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\LogController;

Route::apiResource('cliente', ClienteController::class);
Route::apiResource('categoria', CategoriaController::class);
Route::apiResource('tienda', TiendaController::class);
Route::apiResource('rol', RolController::class);
Route::apiResource('producto', ProductoController::class);
Route::get('inventario/{idTienda}', [InventarioController::class, 'index']);       //filtrar los inventarios por el id de la tienda
Route::apiResource('inventario', InventarioController::class)->except(['index']);  
Route::post('login', [AuthController::class, 'login']);
Route::get('productos/categoria/{idCategoria}', [ProductoController::class, 'getProductosPorCategoria']);
Route::post('traslado', [InventarioController::class, 'trasladarProducto']);
Route::post('ventas', [VentaController::class,'store']);
Route::get('reporte/top-productos', [ReporteController::class, 'topProductosVendidos']);
Route::get('reporte/productos-bajo-stock', [ReporteController::class, 'topProductosBajoStock']);
Route::get('reporte/productos-mas-vendidos-mes', [ReporteController::class, 'productosMasVendidosPorMes']);
Route::get('reporte/clientes-que-compran-mas', [ReporteController::class, 'clientesQueCompranMas']);
Route::post('reporte/compras-por-rango-fecha', [ReporteController::class, 'comprasPorRangoFecha']);
Route::post('cotizaciones', [CotizacionController::class,'store']);
Route::get('logs', [LogController::class, 'index']);
Route::post('reporte/toptienda', [ReporteController::class, 'productosMasVendidosPorTienda']);






Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


