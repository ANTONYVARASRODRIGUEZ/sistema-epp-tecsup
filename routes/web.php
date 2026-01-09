<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UsuarioController;

// --- SECCIÓN PÚBLICA (LOGIN) ---

// 1. Mostrar el Login (GET en la raíz)
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// 2. Procesar el Login (POST en la raíz)
Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        // Al loguearse, lo enviamos directo al dashboard
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'El correo o la contraseña no coinciden con nuestros registros.',
    ])->onlyInput('email');
})->name('login.post');


// --- SECCIÓN PROTEGIDA (REQUIERE LOGIN) ---

Route::middleware(['auth'])->group(function () {

    // 3. Salir del sistema
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // 4. Ruta del Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 4.5. Mi Perfil
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil.show');
    Route::post('/perfil/email', [ProfileController::class, 'actualizarEmail'])->name('perfil.actualizar-email');
    Route::post('/perfil/contrasena', [ProfileController::class, 'cambiarContrasena'])->name('perfil.cambiar-contrasena');

    // 5. Rutas de Solicitudes
    Route::resource('solicitudes', SolicitudController::class);
    Route::post('/solicitudes/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');

    // 6. Rutas de EPPs (Inventario)
    Route::resource('epps', EppController::class);

    // 7. Departamentos
    Route::resource('departamentos', DepartamentoController::class);
    Route::get('/departamentos/create', [DepartamentoController::class, 'create']);

    // 8. Usuarios
    Route::resource('usuarios', UsuarioController::class);

    //catalogo de epps
    Route::get('/catalogo', [App\Http\Controllers\EppController::class, 'catalogo'])->name('epps.catalogo');
    
    //usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    //Agregar usuario
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');

    // 9. Configuración (solo Admin)
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
});