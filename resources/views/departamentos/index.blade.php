+<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Departamentos</title>
</head>
<body>

<h1>Listado de Departamentos</h1>

@if($departamentos->isEmpty())
    <p>No hay departamentos registrados.</p>
@else
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
        </tr>

        @foreach($departamentos as $dep)
        <tr>
            <td>{{ $dep->id }}</td>
            <td>{{ $dep->nombre }}</td>
            <td>{{ $dep->descripcion }}</td>
        </tr>
        @endforeach
    </table>
@endif

</body>
</html>
