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
        .sidebar { width: 250px; height: 100vh; position: fixed; background: white; border-right: 1px solid #e9ecef; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; color: #003366; }
        .nav-link { color: #333; padding: 12px 25px; display: flex; align-items: center; font-weight: 500; transition: 0.3s; }
        .nav-link i { margin-right: 15px; font-size: 1.2rem; }
        .nav-link:hover, .nav-link.active { background-color: #f0f4f8; color: #003366; }
        .nav-link.logout { color: #e74c3c; background: #fff5f5; border-radius: 8px; margin: 10px 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .user-section { padding: 20px 25px; border-top: 1px solid #eee; margin-top: auto; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="fw-bold mb-0">EPP Sistema</h4>
            <small class="text-muted">Tecsup Norte</small>
        </div>

        <nav class="nav flex-column mt-3">
            {{-- VERIFICACIÓN DE ROL --}}
            @if(Auth::user()->role === 'Docente' || str_contains(Auth::user()->email, 'docente'))
                {{-- MENU DOCENTE (Tus cambios) --}}
                <a class="nav-link {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}" href="{{ route('docente.dashboard') }}">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
                <a class="nav-link {{ request()->routeIs('epps.catalogo') ? 'active' : '' }}" href="{{ route('epps.catalogo') }}">
                    <i class="bi bi-file-earmark-text"></i> Catálogo de EPP
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-box-seam"></i> Mis EPP
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-journal-text"></i> Mis Solicitudes
                </a>
            @else
                {{-- MENU ADMINISTRADOR (Cambios de tu compañera integrados) --}}
                <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-bar-chart-line"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->is('solicitudes*') ? 'active' : '' }}" href="{{ route('solicitudes.index') }}">
                    <i class="bi bi-file-earmark-check"></i> Solicitudes
                </a>
                <a class="nav-link {{ request()->is('epps*') ? 'active' : '' }}" href="{{ route('epps.index') }}">
                    <i class="bi bi-box-seam"></i> Inventario
                </a>
                <a class="nav-link {{ request()->routeIs('epps.catalogo') ? 'active' : '' }}" href="{{ route('epps.catalogo') }}">
                    <i class="bi bi-file-earmark-text"></i> Catálogo EPP
                </a>
                <a class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                <a class="nav-link {{ request()->routeIs('matriz-epp.index') ? 'active' : '' }}" href="{{ route('matriz-epp.index') }}">
                    <i class="bi bi-grid-3x3"></i> Matriz EPP
                </a>
                <a class="nav-link {{ request()->routeIs('departamentos.index') ? 'active' : '' }}" href="{{ route('departamentos.index') }}">
                    <i class="bi bi-building"></i> Departamentos
                </a>
                <a class="nav-link {{ request()->routeIs('configuracion.index') ? 'active' : '' }}" href="{{ route('configuracion.index') }}">
                    <i class="bi bi-gear"></i> Configuración
                </a>
            @endif
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
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>