<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocenteDashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatrizEppController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// --- SECCIÓN PÚBLICA (LOGIN) ---
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->role === 'Docente') {
            return redirect()->route('docente.dashboard');
        }
        return redirect()->route('dashboard'); 
    }

    return back()->withErrors([
        'email' => 'El correo o la contraseña no coinciden con nuestros registros.',
    ])->onlyInput('email');
})->name('login.post');


// --- SECCIÓN PROTEGIDA (REQUIERE LOGIN) ---
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // Dashboard y Perfil
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::post('/perfil/datos', [ProfileController::class, 'actualizarDatosPersonales'])->name('perfil.actualizar-datos');
    Route::post('/perfil/email', [ProfileController::class, 'actualizarEmail'])->name('perfil.actualizar-email');
    Route::post('/perfil/contrasena', [ProfileController::class, 'cambiarContrasena'])->name('perfil.cambiar-contrasena');

    // Recursos Principales (Epps, Departamentos, Usuarios, Solicitudes)
    Route::resource('epps', EppController::class);
    Route::resource('departamentos', DepartamentoController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('solicitudes', SolicitudController::class);
    
    // Acciones de Solicitudes
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');

    // Catálogo y Dashboard Docente
    Route::get('/catalogo', [EppController::class, 'catalogo'])->name('epps.catalogo');
    Route::get('/docente/dashboard', DocenteDashboardController::class)->name('docente.dashboard');

    Route::get('/docente/mis-epp', [SolicitudController::class, 'misEpps'])->name('docente.mis-epp');

    Route::get('/docente/mis-solicitudes', [SolicitudController::class, 'misSolicitudes'])->name('docente.mis-solicitudes');

    // Matriz EPP
    Route::resource('matriz-epp', MatrizEppController::class);

    // 9. Configuración (Solo Admin) - Cambios de tu compañera
    Route::middleware('isAdmin')->group(function () {
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion/general', [ConfiguracionController::class, 'actualizarGeneral'])->name('configuracion.actualizar-general');
        Route::post('/configuracion/parametros-epp', [ConfiguracionController::class, 'actualizarParametrosEpp'])->name('configuracion.actualizar-parametros-epp');
        Route::post('/configuracion/notificaciones', [ConfiguracionController::class, 'actualizarNotificaciones'])->name('configuracion.actualizar-notificaciones');
        Route::post('/configuracion/auditoria', [ConfiguracionController::class, 'actualizarAuditoria'])->name('configuracion.actualizar-auditoria');
        Route::post('/configuracion/departamentos', [ConfiguracionController::class, 'crearDepartamento'])->name('configuracion.crear-departamento');
        Route::put('/configuracion/departamentos/{id}', [ConfiguracionController::class, 'actualizarDepartamento'])->name('configuracion.actualizar-departamento');
        Route::put('/configuracion/departamentos/{id}/desactivar', [ConfiguracionController::class, 'desactivarDepartamento'])->name('configuracion.desactivar-departamento');
        Route::put('/configuracion/departamentos/{id}/activar', [ConfiguracionController::class, 'activarDepartamento'])->name('configuracion.activar-departamento');
        Route::post('/configuracion/matriz', [ConfiguracionController::class, 'agregarMatriz'])->name('configuracion.agregar-matriz');
        Route::delete('/configuracion/matriz/{id}', [ConfiguracionController::class, 'eliminarMatriz'])->name('configuracion.eliminar-matriz');
    });

    // Importación General (Desde el Index de Departamentos)
Route::post('/departamentos/importar-general', [DepartamentoController::class, 'importarGeneral'])->name('departamentos.importar_general');


   
    Route::post('/departamentos/{id}/importar', [DepartamentoController::class, 'importar'])->name('departamentos.importar');
});