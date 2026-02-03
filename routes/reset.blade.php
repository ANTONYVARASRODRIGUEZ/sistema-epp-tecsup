<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - EPP Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <h4 class="fw-bold text-dark">Nueva Contraseña</h4>
            <p class="text-muted small">Crea una contraseña segura</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Correo Electrónico</label>
                <input type="email" name="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" 
                       value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Nueva Contraseña</label>
                <input type="password" name="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" required>
                @error('password')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary small text-uppercase">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" class="form-control bg-light border-0" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                Restablecer Contraseña
            </button>
        </form>
    </div>
</body>
</html>
