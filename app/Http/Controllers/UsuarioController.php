<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MatrizHomologacion;
use App\Models\Departamento;
use App\Models\Epp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        // Agregamos with('departamento') para que la lista principal también los cargue
        $usuarios = User::with('departamento')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
{
    // 1. Construimos el correo completo antes de validar
    $emailCompleto = $request->email_prefix . '@tecsup.edu.pe';
    
    // Agregamos el email completo al request para que la validación 'unique' funcione
    $request->merge(['email' => $emailCompleto]);

    // 2. Validación adaptada a la nueva lógica
    $request->validate([
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email', // Valida el correo construido
        'password'     => 'required|min:6',
        'role'         => 'required|in:Admin,Coordinador,Docente,Usuario',
    ]);

    // 3. Creación del usuario con valores por defecto para el docente
    User::create([
        'name'            => $request->name,
        'email'           => $emailCompleto,
        'password'        => Hash::make($request->password), // Tecsup2026
        'role'            => $request->role,
        
        // Estos campos quedan NULL porque el docente los llenará al unirse
        'dni'             => null, 
        'departamento_id' => null,
        'talla_zapatos'   => null,
        'talla_mandil'    => null,
    ]);

    return redirect()->route('usuarios.index')
        ->with('success', 'Cuenta creada. El usuario deberá completar su perfil al iniciar sesión.');
}
    /**
     * Ver detalles de un usuario específico.
     * ESTA FUNCIÓN ES LA QUE ARREGLA TU FICHA TÉCNICA
     */
    public function show($id)
    {
        // Cargamos la relación 'departamento' para que no salga "No asignado"
        $usuario = User::with('departamento')->findOrFail($id);
        
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $departamentos = Departamento::all(); // Necesario para el select de edición
        return view('usuarios.edit', compact('usuario', 'departamentos'));
    }

    /**
     * Actualizar un usuario.
     */
   public function update(Request $request, $id)
{
    $usuario = User::findOrFail($id);

    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'dni'   => 'nullable|string|max:20|unique:users,dni,' . $id, // Permite que sea nulo
        'role'  => 'required',
    ]);

    $usuario->name = $request->name;
    $usuario->email = $request->email;
    $usuario->role = $request->role;
    $usuario->dni = $request->dni; // Se guardará null si el campo llega vacío

    if ($request->filled('password')) {
        $usuario->password = Hash::make($request->password);
    }

    $usuario->save();

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
}
    /**
     * Eliminar un usuario.
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}