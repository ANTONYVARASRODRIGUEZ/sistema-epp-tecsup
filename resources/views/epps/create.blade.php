@extends('layouts.app')

@section('content')

<h2>Registrar Nuevo EPP</h2>

<a href="{{ route('epps.index') }}" class="btn btn-secondary mb-3">← Volver</a>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<div class="card">
<div class="card-body">

<form action="{{ route('epps.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label class="form-label">Nombre del EPP</label>
    <input type="text" name="nombre" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Tipo</label>
    <input type="text" name="tipo" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Vida útil (meses)</label>
    <input type="number" name="vida_util_meses" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Ficha Técnica (PDF opcional)</label>
    <input type="file" name="ficha_tecnica" class="form-control">
</div>

<button type="submit" class="btn btn-primary">
    Guardar EPP
</button>

</form>

</div>
</div>

@endsection
