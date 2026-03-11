<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPP Sistema - Tecsup Norte</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-w: 280px;
            --tecsup-blue: #002855; 
            --bg-main: #f4f7fa;
            --topbar-h: 70px;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--bg-main);
            font-family: 'Inter', sans-serif;
            color: #334155;
            overflow-x: hidden;
        }

        /* ── SIDEBAR PRO ── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 25px;
            color: var(--tecsup-blue);
        }

        .sidebar-header h4 {
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ── NAVEGACIÓN (Orden Original) ── */
        .sidebar nav {
            padding: 10px 15px;
            overflow-y: auto;
            flex: 1;
        }

        .nav-link {
            color: #64748b;
            padding: 12px 18px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            font-weight: 500;
            border-radius: var(--radius);
            transition: var(--transition);
            text-decoration: none;
        }

        .nav-link i { 
            margin-right: 14px; 
            font-size: 1.2rem; 
        }

        .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--tecsup-blue);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--tecsup-blue);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 40, 85, 0.2);
        }

        /* ── SECCIÓN DE USUARIO ── */
        .user-section {
            margin: 15px;
            padding: 20px;
            background: #f8fafc;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
        }

        .nav-link.logout {
            color: #ef4444;
            background: #fee2e2;
            margin-top: 10px;
            justify-content: center;
        }

        .nav-link.logout:hover { background: #fecaca; transform: none; }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: var(--sidebar-w);
            padding: 40px;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Responsive Mobile */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 15px 0 40px rgba(0,0,0,0.1); }
            .main-content { margin-left: 0; padding: 25px 15px; padding-top: 90px; }
            .topbar-mobile {
                display: flex; position: fixed; top: 0; width: 100%; height: 70px;
                background: #fff; z-index: 1040; align-items: center; padding: 0 20px;
                border-bottom: 1px solid #eee; justify-content: space-between;
            }
        }
        
        .topbar-mobile { display: none; }
    </style>
</head>
<body>

    <header class="topbar-mobile">
        <span class="fw-bold text-primary">EPP Sistema</span>
        <button class="btn btn-light shadow-sm" id="btnOpen"><i class="bi bi-list fs-3"></i></button>
    </header>

    <div class="sidebar-overlay" id="overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); z-index:1045; backdrop-filter: blur(4px);"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-shield-lock-fill"></i> EPP Sistema</h4>
            <small class="text-muted d-block ps-5">Tecsup Norte</small>
            <button class="btn d-lg-none position-absolute top-0 end-0 mt-3 me-3" id="btnClose"><i class="bi bi-x-lg"></i></button>
        </div>

        <nav class="flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-bar-chart-line"></i> Dashboard
            </a>

            <a class="nav-link {{ request()->routeIs('epps.index') ? 'active' : '' }}" href="{{ route('epps.index') }}">
                <i class="bi bi-book"></i> Catálogo de EPP
            </a>

            <a class="nav-link {{ request()->is('inventario*') ? 'active' : '' }}" href="{{ route('inventario.index') }}">
                <i class="bi bi-box-seam"></i> Inventario / Stock
            </a>

            <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                <i class="bi bi-tags"></i> Categorías
            </a>

            <a class="nav-link {{ request()->routeIs('personals.index') ? 'active' : '' }}" href="{{ route('personals.index') }}">
                <i class="bi bi-person-badge"></i> Base de Datos Personal
            </a>

            <a class="nav-link {{ request()->routeIs('departamentos.index') ? 'active' : '' }}" href="{{ route('departamentos.index') }}">
                <i class="bi bi-building"></i> Departamentos
            </a>

            <a class="nav-link {{ request()->is('entregas*') ? 'active' : '' }}" href="{{ route('entregas.index') }}">
                <i class="bi bi-box-arrow-in-down"></i> Entrega de EPPS
            </a>

            <a class="nav-link {{ request()->is('asignaciones*') ? 'active' : '' }}" href="{{ route('asignaciones.index') }}">
                <i class="bi bi-clock-history"></i> Historial de Entregas
            </a>

            <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
            </a>
        </nav>

        <div class="user-section">
            <a class="nav-link p-0 mb-3 text-dark border-0 fw-bold" href="{{ route('perfil.show') }}">
                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link logout border-0 w-100 fw-bold">
                    <i class="bi bi-box-arrow-left"></i> SALIR
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const btnOpen = document.getElementById('btnOpen');
        const btnClose = document.getElementById('btnClose');

        const toggleMenu = () => {
            sidebar.classList.toggle('open');
            overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
        };

        btnOpen?.addEventListener('click', toggleMenu);
        btnClose?.addEventListener('click', toggleMenu);
        overlay?.addEventListener('click', toggleMenu);
    </script>
    @stack('scripts')
</body>
</html>