<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use App\Models\User; // Descomenta esto cuando vayas a traer datos reales

class UsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index()
    {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
{
    // 1. Validar los datos
    $request->validate([
        'name' => 'required|string|max:255',
        'dni' => 'required|string|max:20|unique:users,dni',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required',
        'department' => 'nullable|string|max:255',
        'workshop' => 'nullable|string|max:255'
    ]);

    // 2. Crear el usuario
    User::create([
        'name' => $request->name,
        'dni' => $request->dni,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'department' => $request->department,
        'workshop' => $request->workshop,
    ]);

    // 3. Redirigir con mensaje de éxito
    return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
}

    /**
     * Ver detalles de un usuario específico.
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualizar un usuario.
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'department' => 'nullable|string|max:255',
            'workshop' => 'nullable|string|max:255',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'dni' => $request->dni,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department,
            'workshop' => $request->workshop,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

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