<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header, .footer {
            text-align: center;
            border-bottom: 4px solid #FFD700; 
            padding: 10px 0;
        }
        .footer {
            border-top: 4px solid #FFD700; 
            border-bottom: none;
            margin-top: 30px;
            padding-top: 20px;
        }
        h1, h2 {
            color: #000000; 
        }
        .details {
            margin: 20px 0;
        }
        .details p {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background-color: #FFFACD; 
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factura #: 000{{ $venta->IdVenta }}</h1>
        <p>Generada el {{ $fecha }} a las {{ $hora }}</p>
        <p><strong>Tienda Los Tilines</strong></p>
        <p>Dirección: 9na calle 15 avenida 15-61 zona 3 , Quetzaltenango</p>
        <p>Teléfono: +502 3481-2347</p>
    </div>

    <div class="details">
        <h2>Detalles del Cliente</h2>
        <p><strong>Nombre:</strong> {{ $cliente['nombre'] }} {{ $cliente['apellido'] }}</p>
        <p><strong>NIT:</strong> {{ $cliente['nit'] }}</p>
        <p><strong>Dirección de Entrega:</strong> {{ $venta->Destino }}</p>
    </div>

    <h2>Detalles de la Compra</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $detalle)
                <tr>
                    <td>{{ $detalle->inventario->producto->Nombre }}</td>
                    <td>{{ $detalle->Cantidad }}</td>
                    <td>Q{{ number_format($detalle->Precio, 2) }}</td>
                    <td>Q{{ number_format($detalle->Cantidad * $detalle->Precio, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total: Q{{ number_format($venta->Total, 2) }}</p>

    <div class="footer">
        <p style="color: #FFD700;">Gracias por su compra</p>
        <p>Por favor conserve esta factura como comprobante.</p>
    </div>
</body>
</html>
