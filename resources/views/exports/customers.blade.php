<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Lista de Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>NIT</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Dirección</th>
                <th>Activo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->nit }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->tbl_municipality->tbl_department->name_department ?? '' }}</td>
                <td>{{ $customer->tbl_municipality->name_municipality ?? '' }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->status ? 'Sí' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
