<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // 4. Ruta del Dashboard (La que usa el diseño de la imagen)
    // Usamos el método index del EppController para que cargue los datos de la tabla
    Route::get('/dashboard', [EppController::class, 'index'])->name('dashboard');

    // 5. Rutas de EPPs (Inventario)
    Route::resource('epps', EppController::class);

    // 6. Departamentos
    Route::resource('departamentos', DepartamentoController::class);
    Route::get('/departamentos/create', [DepartamentoController::class, 'create']);


    //catalogo de epps
    Route::get('/catalogo', [App\Http\Controllers\EppController::class, 'catalogo'])->name('epps.catalogo');
    
});