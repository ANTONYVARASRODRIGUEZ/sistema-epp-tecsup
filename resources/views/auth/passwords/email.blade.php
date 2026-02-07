<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - EPP Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f4f8; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { width: 400px; border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white; }
        .btn-primary { background-color: #003366; border: none; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { background-color: #002244; transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark">Recuperar Contraseña</h4>
            <p class="text-muted small">Ingresa tu correo para recibir el enlace</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success small border-0 shadow-sm" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Correo Electrónico</label>
                <input type="email" name="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm mb-3">
                Enviar Enlace de Recuperación
            </button>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Login
                </a>
            </div>
        </form>
    </div>
</body>
</html>
