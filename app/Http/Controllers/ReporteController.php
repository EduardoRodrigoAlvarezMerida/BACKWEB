<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto; 

class ReporteController extends Controller
{
    public function topProductosVendidos(Request $request)
{
    try {
       
        $productosGenerales = \DB::table('detalle_ventas')
            ->join('inventarios', 'detalle_ventas.IdInventario', '=', 'inventarios.IdInventario')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->selectRaw('productos.Nombre as NombreProducto, SUM(detalle_ventas.Cantidad) as TotalVendido')
            ->groupBy('productos.IdProducto', 'productos.Nombre')
            ->orderByDesc('TotalVendido')
            ->limit(100)
            ->get();

    
        $productosPorTienda = \DB::table('detalle_ventas')
            ->join('inventarios', 'detalle_ventas.IdInventario', '=', 'inventarios.IdInventario')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->join('tiendas', 'inventarios.IdTienda', '=', 'tiendas.IdTienda')
            ->selectRaw('productos.Nombre as NombreProducto, tiendas.Nombre as NombreTienda, SUM(detalle_ventas.Cantidad) as TotalVendidoPorTienda')
            ->groupBy('productos.IdProducto', 'productos.Nombre', 'tiendas.IdTienda', 'tiendas.Nombre')
            ->orderBy('productos.Nombre')
            ->orderByDesc('TotalVendidoPorTienda')
            ->get();

    
        return response()->json([
            'mas_vendidos_general' => $productosGenerales,
            'mas_vendidos_por_tienda' => $productosPorTienda,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}


public function topProductosBajoStock()
{
    try {
    
        $productosBajoStock = \DB::table('inventarios')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->join('tiendas', 'inventarios.IdTienda', '=', 'tiendas.IdTienda')
            ->select('tiendas.Nombre as NombreTienda', 'productos.Nombre as NombreProducto', 'inventarios.Cantidad as Existencia')
            ->where('inventarios.Cantidad', '<', 10)
            ->orderBy('tiendas.Nombre')
            ->orderBy('inventarios.Cantidad', 'ASC')
            ->limit(20)
            ->get();

        return response()->json($productosBajoStock, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}

   
public function productosMasVendidosPorMes()
{
    try {
        
        $productosPorMes = \DB::table('detalle_ventas')
            ->join('inventarios', 'detalle_ventas.IdInventario', '=', 'inventarios.IdInventario')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->join('ventas', 'detalle_ventas.IdVenta', '=', 'ventas.IdVenta')
            ->selectRaw("DATE_FORMAT(ventas.Fecha, '%Y-%m') as Mes, productos.Nombre as NombreProducto, SUM(detalle_ventas.Cantidad) as TotalVendido")
            ->groupBy('Mes', 'productos.IdProducto', 'productos.Nombre')
            ->orderBy('Mes', 'ASC')
            ->orderBy('TotalVendido', 'DESC')
            ->get();

        return response()->json($productosPorMes, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}


public function clientesQueCompranMas()
{
    try {
        // Consulta para obtener los clientes que compran más
        $clientes = \DB::table('ventas')
            ->join('clientes', 'ventas.IdCliente', '=', 'clientes.IdCliente')
            ->join('detalle_ventas', 'ventas.IdVenta', '=', 'detalle_ventas.IdVenta')
            ->selectRaw('
                clientes.Nombre AS NombreCliente,
                clientes.Apellido AS ApellidoCliente,
                COUNT(ventas.IdVenta) AS TotalCompras,
                SUM(detalle_ventas.Cantidad * detalle_ventas.Precio) AS TotalGastado
            ')
            ->groupBy('clientes.IdCliente', 'clientes.Nombre', 'clientes.Apellido')
            ->orderBy('TotalGastado', 'DESC')
            ->limit(10)
            ->get();

        return response()->json($clientes, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}

public function comprasPorRangoFecha(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    try {
        // Consulta para obtener las compras agrupadas con listado de productos
        $compras = \DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.IdVenta', '=', 'ventas.IdVenta')
            ->join('clientes', 'ventas.IdCliente', '=', 'clientes.IdCliente')
            ->join('inventarios', 'detalle_ventas.IdInventario', '=', 'inventarios.IdInventario')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->selectRaw('
                ventas.Fecha AS FechaCompra,
                clientes.Nombre AS NombreCliente,
                clientes.Apellido AS ApellidoCliente,
                GROUP_CONCAT(CONCAT(productos.Nombre, " (", detalle_ventas.Cantidad, ")") SEPARATOR ", ") AS DetalleProductos,
                SUM(detalle_ventas.Cantidad * detalle_ventas.Precio) AS TotalGastado
            ')
            ->whereBetween('ventas.Fecha', [$request->start_date, $request->end_date])
            ->groupBy('ventas.Fecha', 'clientes.IdCliente', 'clientes.Nombre', 'clientes.Apellido')
            ->orderBy('ventas.Fecha', 'ASC')
            ->orderBy('TotalGastado', 'DESC')
            ->get();

        return response()->json($compras, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}

public function productosMasVendidosPorTienda(Request $request)
{
    $request->validate([
        'nombre_tienda' => 'required|string|max:255', // Validar que el nombre de la tienda sea obligatorio y un string
    ]);

    try {
        // Obtener los productos más vendidos en una tienda específica
        $productosVendidos = \DB::table('detalle_ventas')
            ->join('inventarios', 'detalle_ventas.IdInventario', '=', 'inventarios.IdInventario')
            ->join('productos', 'inventarios.IdProducto', '=', 'productos.IdProducto')
            ->join('tiendas', 'inventarios.IdTienda', '=', 'tiendas.IdTienda')
            ->selectRaw('
                productos.Nombre AS NombreProducto,
                SUM(detalle_ventas.Cantidad) AS TotalVendido
            ')
            ->where('tiendas.Nombre', $request->nombre_tienda) // Filtrar por el nombre de la tienda
            ->groupBy('productos.IdProducto', 'productos.Nombre')
            ->orderByDesc('TotalVendido')
            ->limit(10) // Limitar a los 10 más vendidos
            ->get();

        return response()->json($productosVendidos, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al generar el reporte', 'message' => $e->getMessage()], 500);
    }
}


}
