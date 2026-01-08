<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Departamento</title>
</head>
<body>

<h2>Registrar Departamento</h2>

<a href="{{ route('departamentos.index') }}">← Volver al listado</a>
<br><br>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('departamentos.store') }}" method="POST">
    @csrf

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="{{ old('nombre') }}"><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion">{{ old('descripcion') }}</textarea><br><br>

    <button type="submit">Guardar</button>
</form>

</body>
</html>
