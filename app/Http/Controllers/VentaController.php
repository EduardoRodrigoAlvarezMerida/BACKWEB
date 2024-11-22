<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Crear la venta
            $venta = Venta::create([
                'IdCliente' => $request->IdCliente,
                'Fecha' => now(),
                'Total' => $request->amount,
                'Destino' => $request->address
            ]);

            // Procesar cada producto en el carrito
            foreach ($request->items as $item) {
                $inventario = Inventario::where('IdProducto', $item['id'])
                    ->where('Cantidad', '>=', $item['quantity'])
                    ->first();

                if (!$inventario) {
                    throw new \Exception("No hay suficiente inventario para el producto ID: {$item['id']}");
                }

                // Crear el detalle de la venta
                DetalleVenta::create([
                    'IdVenta' => $venta->IdVenta,
                    'Cantidad' => $item['quantity'],
                    'IdInventario' => $inventario->IdInventario,
                    'Precio' => $inventario->producto->Precio,
                ]);

                // Reducir la cantidad en el inventario
                $inventario->Cantidad -= $item['quantity'];
                $inventario->save();
            }

            DB::commit();

            // Generar y guardar el PDF de la factura
            $pdfPath = $this->generarFactura($venta);

            // Retornar el PDF como respuesta
            return response()->file(storage_path("app/public/{$pdfPath}"));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function generarFactura($venta)
    {
        $detallesVenta = DetalleVenta::where('IdVenta', $venta->IdVenta)
            ->with('inventario.producto')
            ->get();
        
        $cliente = $venta->cliente;

        // Preparar los datos para la factura
        $datos = [
            'venta' => $venta,
            'detalles' => $detallesVenta,
            'fecha' => now()->format('d/m/Y'),
            'hora' => now()->format('H:i:s'),
            'cliente' => [
                'nombre' => $cliente->Nombre,
                'apellido' => $cliente->Apellido,
                'nit' => $cliente->NIT ?: 'CF',
            ],
        ];

        // Generar el PDF para la factura
        $pdf = Pdf::loadView('factura', $datos);

        // Ruta donde se guardará el PDF
        $pdfPath = "facturas/Factura_{$venta->IdVenta}.pdf";

        // Guardar el archivo en la carpeta storage/app/public/facturas/
        \Storage::disk('public')->put($pdfPath, $pdf->output());

        // Retornar la ruta relativa del archivo
        return $pdfPath;
    }
}
