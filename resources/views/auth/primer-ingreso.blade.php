<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activar Cuenta - EPP Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #e9ecef; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .card-setup { width: 450px; border: none; border-radius: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); background: white; overflow: hidden; }
        .header-setup { background: #003366; padding: 30px; text-align: center; color: white; }
    </style>
</head>
<body>
    <div class="card card-setup">
        <div class="header-setup">
            <i class="bi bi-shield-lock-fill display-4 mb-2"></i>
            <h4 class="fw-bold mb-0">Activación de Cuenta</h4>
            <p class="small opacity-75 mb-0">Hola, {{ Auth::user()->name }}</p>
        </div>
        <div class="p-4">
            <div class="alert alert-warning border-0 d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2 fs-4"></i>
                <div class="small">
                    Por seguridad, detectamos que es tu <strong>primer ingreso</strong>. Debes cambiar tu contraseña temporal para continuar.
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger small border-0 shadow-sm mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('primer.ingreso.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nueva Contraseña</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Mínimo 6 caracteres" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Repite la contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold" style="background-color: #003366;">
                    Guardar y Acceder al Sistema
                </button>
            </form>
        </div>
    </div>
</body>
</html>