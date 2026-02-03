<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PersonalController; 
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\OrganizadorController;
use App\Http\Controllers\ReporteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// --- SECCIÓN PÚBLICA (LOGIN ÚNICO) ---
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Capturamos si el usuario marcó "Recordarme"
    $remember = $request->has('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return redirect()->route('dashboard'); 
    }

    return back()->withErrors([
        'email' => 'Acceso denegado. Verifique sus credenciales.',
    ])->onlyInput('email');
})->name('login.post');

// --- RECUPERACIÓN DE CONTRASEÑA (Manual) ---
Route::get('password/reset', [App\Http\Controllers\PasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\PasswordController::class, 'reset'])->name('password.update');


// --- SECCIÓN PROTEGIDA (SOLO PARA ADMIN) ---
Route::middleware(['auth', 'isAdmin'])->group(function () {

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

    // --- MANTENEDORES (CATÁLOGO E INVENTARIO) ---
    Route::post('/epps/importar', [EppController::class, 'import'])->name('epps.import');
    Route::delete('/epps-truncate', [EppController::class, 'clearAll'])->name('epps.clearAll');
    Route::resource('epps', EppController::class);
    Route::resource('categorias', CategoriaController::class);

    // --- GESTIÓN DE PERSONAL (LISTA MAESTRA DE DOCENTES) ---
    // Jiancarlo registra aquí a los docentes que no tienen cuenta
    Route::post('/personals/importar', [PersonalController::class, 'import'])->name('personals.import');
    Route::resource('personals', PersonalController::class);

    // --- DEPARTAMENTOS Y ORGANIZADOR ---
    // Borrado masivo
    Route::delete('/departamentos-destroy-all', [DepartamentoController::class, 'destroyAll'])->name('departamentos.destroy_all');
    Route::delete('/departamentos-destroy-selected', [DepartamentoController::class, 'destroySelected'])->name('departamentos.destroy_selected');
    
    // Rutas de Departamentos
    Route::resource('departamentos', DepartamentoController::class);
    Route::post('/departamentos/{id}/asignar-masivo', [DepartamentoController::class, 'asignarMasivo'])->name('departamentos.asignar_masivo');
    
    // Organizador Visual (Mover docentes a departamentos)
    Route::get('/organizador', [OrganizadorController::class, 'index'])->name('organizador.index');
    Route::post('/organizador/asignar', [OrganizadorController::class, 'asignarMasivo'])->name('organizador.asignar');

    // --- MÓDULO DE ASIGNACIÓN (ENTREGA DE EPP) ---
    Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
    Route::post('/asignaciones/entregar', [AsignacionController::class, 'store'])->name('asignaciones.store');
    Route::delete('/asignaciones/{id}', [AsignacionController::class, 'destroy'])->name('asignaciones.destroy');
    Route::put('/asignaciones/{id}/devolver', [AsignacionController::class, 'devolver'])->name('asignaciones.devolver');
    Route::put('/asignaciones/{id}/incidencia', [AsignacionController::class, 'reportarIncidencia'])->name('asignaciones.incidencia');

    // --- OTROS (ADMINISTRACIÓN DE USUARIOS DEL SISTEMA Y CONFIG) ---
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    // --- REPORTES ---
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/stock', [ReporteController::class, 'stock'])->name('reportes.stock');
    Route::get('/reportes/departamento', [ReporteController::class, 'porDepartamento'])->name('reportes.departamento');
    Route::get('/reportes/incidencias', [ReporteController::class, 'incidencias'])->name('reportes.incidencias');
});