<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Reporte del Mes</h1>
    <div class="card">
        <div class="card-body">
            <p>El total de ventas para el mes actual es:
                <strong>Q {{ number_format($totalVentas, 2) }}</strong>
            </p>
            <p>Mes: {{ now()->month }} / Año: {{ now()->year }}</p>
        </div>
    </div>
</div>
</body>
</html>
