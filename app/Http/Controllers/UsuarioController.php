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
        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'departamento_id' => 'nullable|exists:departamentos,id', // Validamos contra ID real
        ]);

        User::create([
            'name' => $request->name,
            'dni' => $request->dni,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'departamento_id' => $request->departamento_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
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
            'name' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:users,dni,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'dni' => $request->dni,
            'email' => $request->email,
            'role' => $request->role,
            'departamento_id' => $request->departamento_id,
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