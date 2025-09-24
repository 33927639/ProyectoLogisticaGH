<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<h2>📋 Listado de Productos</h2>

<table>
    <thead>
    <tr>
        <th>Nombre</th>
        <th>SKU</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Descripción</th>
        <th>Expira</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku }}</td>
            <td>Q {{ number_format($product->price, 2) }}</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->description }}</td>
            <td>{{ $product->expires_at ? $product->expires_at->format('d/m/Y') : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer no-print">
    <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    <button onclick="window.print()">🖨️ Imprimir</button>
</div>
</body>
</html>
