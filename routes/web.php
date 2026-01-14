<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
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
        $user = Auth::user();

        // REDIRECCIÓN POR ROL (Súper seguro)
        if ($user->role === 'Docente') {
            return redirect()->route('docente.dashboard');
        }

        // Si es Admin o Coordinador, va al dashboard principal
        return redirect()->route('dashboard'); 
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

    // 4. Ruta del Dashboard (La que usa el diseño de la imagen)
    // Usamos el método index del EppController para que cargue los datos de la tabla
    Route::get('/dashboard', [EppController::class, 'index'])->name('dashboard');

    // 5. Rutas de EPPs (Inventario)
    Route::resource('epps', EppController::class);

    // 6. Departamentos
    Route::resource('departamentos', DepartamentoController::class);
    Route::get('/departamentos/create', [DepartamentoController::class, 'create']);

    // 7. Usuarios
    Route::resource('usuarios', UsuarioController::class);

    //catalogo de epps
    Route::get('/catalogo', [App\Http\Controllers\EppController::class, 'catalogo'])->name('epps.catalogo');
    
    //usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    //Agregar usuario
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');



    //solicitudes
    Route::post('/solicitudes', [App\Http\Controllers\SolicitudController::class, 'store'])->name('solicitudes.store');

    //dashboard docente
    Route::get('/docente/dashboard', function () {
        return view('docente.dashboard');
    })->name('docente.dashboard');
});