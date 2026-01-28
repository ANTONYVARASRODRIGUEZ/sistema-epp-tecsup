<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPP Sistema - Tecsup Norte</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background: white; border-right: 1px solid #e9ecef; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-header { padding: 20px; color: #003366; }
        .nav-link { color: #333; padding: 12px 25px; display: flex; align-items: center; font-weight: 500; transition: 0.3s; text-decoration: none; }
        .nav-link i { margin-right: 15px; font-size: 1.2rem; }
        .nav-link:hover, .nav-link.active { background-color: #f0f4f8; color: #003366; }
        .nav-link.logout { color: #e74c3c; background: #fff5f5; border-radius: 8px; margin: 10px 20px; }
        .main-content { margin-left: 250px; padding: 30px; min-height: 100vh; }
        .user-section { padding: 20px 25px; border-top: 1px solid #eee; margin-top: auto; }
        
        /* Estilos para alertas flotantes */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: 350px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="fw-bold mb-0">EPP Sistema</h4>
            <small class="text-muted">Tecsup Norte</small>
        </div>

        <nav class="nav flex-column mt-3">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-bar-chart-line"></i> Dashboard
    </a>

    <a class="nav-link {{ request()->is('asignaciones*') ? 'active' : '' }}" href="{{ route('asignaciones.index') }}">
        <i class="bi bi-file-earmark-check"></i> Asignaciones de EPP
    </a>

    <a class="nav-link {{ request()->routeIs('personals.index') ? 'active' : '' }}" href="{{ route('personals.index') }}">
        <i class="bi bi-person-badge"></i> Base de Datos Personal
    </a>

    <hr class="mx-3 my-2 text-secondary opacity-25">

    <a class="nav-link {{ request()->is('epps*') ? 'active' : '' }}" href="{{ route('epps.index') }}">
        <i class="bi bi-box-seam"></i> Inventario / Catálogo
    </a>

    <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
        <i class="bi bi-tags"></i> Categorías
    </a>

    <a class="nav-link {{ request()->routeIs('departamentos.index') ? 'active' : '' }}" href="{{ route('departamentos.index') }}">
        <i class="bi bi-building"></i> Departamentos
    </a>

    <hr class="mx-3 my-2 text-secondary opacity-25">

    <a class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
        <i class="bi bi-shield-lock"></i> Usuarios Sistema
    </a>
</nav>

        <div class="user-section">
            <a class="nav-link p-0 mb-3" href="{{ route('perfil.show') }}">
                <i class="bi bi-person-circle"></i> Mi Perfil ({{ Auth::user()->name }})
            </a>
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="submit" class="nav-link logout border-0 w-100 text-start" style="cursor: pointer;">
                    <i class="bi bi-box-arrow-left"></i> Salir
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        
        <div class="alert-container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')

</body>
</html>