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
            --sidebar-w: 270px;
            --tecsup-blue: #002855;
            --bg-main: #f4f7fa;
            --topbar-h: 64px;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-main);
            font-family: 'Inter', sans-serif;
            color: #334155;
            overflow-x: hidden;
            margin: 0;
        }

        /* ══════════════════════════════
           TOPBAR MOBILE
        ══════════════════════════════ */
        .topbar-mobile {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--topbar-h);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1040;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .topbar-mobile .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 1rem;
            color: var(--tecsup-blue);
            text-decoration: none;
        }

        .topbar-mobile .brand i {
            font-size: 1.3rem;
        }

        .btn-hamburger {
            background: none;
            border: none;
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            color: var(--tecsup-blue);
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-hamburger:hover { background: #f1f5f9; }
        .btn-hamburger i { font-size: 1.6rem; }

        /* ══════════════════════════════
           OVERLAY
        ══════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1045;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
        }

        .sidebar-overlay.active { display: block; }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: var(--transition);
            box-shadow: none;
        }

        .sidebar-header {
            padding: 22px 22px 16px;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }

        .sidebar-header .brand-title {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--tecsup-blue);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            margin-bottom: 2px;
        }

        .sidebar-header .brand-title i { font-size: 1.3rem; }

        .sidebar-header small { color: #94a3b8; font-size: .75rem; }

        .btn-close-sidebar {
            position: absolute;
            top: 14px;
            right: 14px;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            padding: 6px 8px;
            cursor: pointer;
            color: #64748b;
            font-size: 1rem;
            line-height: 1;
            display: none;
            transition: background 0.2s;
        }

        .btn-close-sidebar:hover { background: #e2e8f0; color: #334155; }

        /* ── NAV LINKS ── */
        .sidebar nav {
            padding: 12px 12px;
            overflow-y: auto;
            flex: 1;
        }

        .nav-section-title {
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #cbd5e1;
            padding: 10px 10px 4px;
            margin-top: 4px;
        }

        .nav-link {
            color: #64748b;
            padding: 11px 14px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            font-weight: 500;
            font-size: .875rem;
            border-radius: var(--radius);
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--tecsup-blue);
            transform: translateX(4px);
        }

        .nav-link.active {
            background-color: var(--tecsup-blue);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(0, 40, 85, 0.22);
        }

        .nav-link.active:hover { transform: none; }

        /* ── USER SECTION ── */
        .user-section {
            margin: 12px;
            padding: 16px;
            background: #f8fafc;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .user-name-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            margin-bottom: 10px;
            padding: 4px 0;
        }

        .user-name-link i { font-size: 1.2rem; color: var(--tecsup-blue); }
        .user-name-link:hover { color: var(--tecsup-blue); }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: .85rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-logout:hover { background: #fecaca; }

        /* ══════════════════════════════
           MAIN CONTENT
        ══════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-w);
            padding: 36px;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* ── ALERTS ── */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 420px;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* ══════════════════════════════
           RESPONSIVE — TABLET & MOBILE
        ══════════════════════════════ */
        @media (max-width: 991.98px) {

            /* Mostrar topbar */
            .topbar-mobile { display: flex; }

            /* Ocultar botón cerrar por defecto y mostrarlo en mobile */
            .btn-close-sidebar { display: block; }

            /* Sidebar fuera de pantalla */
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            /* Sidebar abierto */
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 30px rgba(0,0,0,0.12);
            }

            /* Content sin margen lateral, con padding top para el topbar */
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
                padding-top: calc(var(--topbar-h) + 20px);
            }

            /* Alerts en mobile */
            .alert-floating {
                top: calc(var(--topbar-h) + 10px);
                right: 10px;
                left: 10px;
                min-width: unset;
                max-width: unset;
            }
        }

        @media (max-width: 575.98px) {
            .main-content { padding: 16px 12px; padding-top: calc(var(--topbar-h) + 16px); }
        }
    </style>
</head>
<body>

    {{-- ══════════════════════════════
         TOPBAR MOBILE
    ══════════════════════════════ --}}
    <header class="topbar-mobile">
        <a href="{{ route('dashboard') }}" class="brand">
            <i class="bi bi-shield-lock-fill"></i>
            EPP Sistema
        </a>
        <button class="btn-hamburger" id="btnOpen" aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>
    </header>

    {{-- OVERLAY --}}
    <div class="sidebar-overlay" id="overlay"></div>

    {{-- ══════════════════════════════
         SIDEBAR
    ══════════════════════════════ --}}
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">
            <div class="brand-title">
                <i class="bi bi-shield-lock-fill"></i> EPP Sistema
            </div>
            <small>Tecsup Norte</small>
            <button class="btn-close-sidebar" id="btnClose" aria-label="Cerrar menú">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="flex-column">

            <div class="nav-section-title">Principal</div>

            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="bi bi-bar-chart-line"></i> Dashboard
            </a>

            <div class="nav-section-title">Equipos</div>

            <a class="nav-link {{ request()->routeIs('epps.index') ? 'active' : '' }}"
               href="{{ route('epps.index') }}">
                <i class="bi bi-book"></i> Catálogo de EPP
            </a>

            <a class="nav-link {{ request()->is('inventario*') ? 'active' : '' }}"
               href="{{ route('inventario.index') }}">
                <i class="bi bi-box-seam"></i> Inventario / Stock
            </a>

            <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}"
               href="{{ route('categorias.index') }}">
                <i class="bi bi-tags"></i> Categorías
            </a>

            <div class="nav-section-title">Personal</div>

            <a class="nav-link {{ request()->routeIs('personals.index') ? 'active' : '' }}"
               href="{{ route('personals.index') }}">
                <i class="bi bi-person-badge"></i> Base de Datos Personal
            </a>

            <a class="nav-link {{ request()->routeIs('departamentos.index') ? 'active' : '' }}"
               href="{{ route('departamentos.index') }}">
                <i class="bi bi-building"></i> Departamentos
            </a>

            <div class="nav-section-title">Gestión</div>

            <a class="nav-link {{ request()->is('entregas*') ? 'active' : '' }}"
               href="{{ route('entregas.index') }}">
                <i class="bi bi-box-arrow-in-down"></i> Entrega de EPP
            </a>

            <a class="nav-link {{ request()->is('asignaciones*') ? 'active' : '' }}"
               href="{{ route('asignaciones.index') }}">
                <i class="bi bi-clock-history"></i> Historial de Entregas
            </a>

            <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active' : '' }}"
               href="{{ route('reportes.index') }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
            </a>

        </nav>

        <div class="user-section">
            <a href="{{ route('perfil.show') }}" class="user-name-link">
                <i class="bi bi-person-circle"></i>
                <span class="text-truncate">{{ Auth::user()->name }}</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </button>
            </form>
        </div>

    </aside>

    {{-- ══════════════════════════════
         MAIN CONTENT
    ══════════════════════════════ --}}
    <main class="main-content">

        {{-- Alertas flotantes --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow alert-floating" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow alert-floating" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
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

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // evitar scroll del fondo
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        btnOpen?.addEventListener('click', openSidebar);
        btnClose?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        // Cerrar con Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
        });

        // Auto-cerrar si se redimensiona a desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) closeSidebar();
        });

        // Auto-dismiss alerts después de 4s
        document.querySelectorAll('.alert-floating').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert?.close();
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>