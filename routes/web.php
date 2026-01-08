<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



// 1. Mostrar el Login (GET en la raíz)
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// 2. Procesar el Login (POST en la raíz)
// Cambiamos '/login' por '/' para que coincida con donde está el formulario
Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('epps');
    }

    return back()->withErrors([
        'email' => 'El correo o la contraseña no coinciden con nuestros registros.',
    ])->onlyInput('email');
})->name('login.post');

// 3. Salir del sistema (POST)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');



//Ruta de listado de los epps
Route::resource('epps', EppController::class);

//listado de los departamentos
Route::resource('departamentos', DepartamentoController::class);

//crear nuevos departamentos
Route::get('/departamentos/create', [DepartamentoController::class, 'create']);

