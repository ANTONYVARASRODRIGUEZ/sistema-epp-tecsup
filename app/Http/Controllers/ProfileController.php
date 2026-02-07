<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\AuditLog;
use App\Models\Epp;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario
     */
    public function show()
    {
        $usuario = auth()->user();

        if (Schema::hasTable('login_attempts')) {
            // Intentos de acceso recientes
            $accesosRecientes = LoginAttempt::where('user_id', $usuario->id)
                ->where('exitoso', true)
                ->latest()
                ->take(10)
                ->get();
            
            // Último acceso exitoso
            $ultimoAcceso = LoginAttempt::where('user_id', $usuario->id)
                ->where('exitoso', true)
                ->latest()
                ->first();
            
            // Intentos fallidos recientes (últimos 7 días)
            $intentosFallidos = LoginAttempt::where('user_id', $usuario->id)
                ->where('exitoso', false)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        } else {
            $accesosRecientes = collect();
            $ultimoAcceso = null;
            $intentosFallidos = 0;
        }
        
        // INDICADORES PARA EL RESUMEN DE ACTIVIDAD
        $totalEppRegistrados = Epp::count(); // Total de tipos de EPP en catálogo
        $totalEppAsignados = Asignacion::count(); // Historial total de asignaciones
        $totalEppBaja = Epp::sum('deteriorado'); // Total de unidades dadas de baja/deterioradas
        $inventariadosRealizados = AuditLog::count(); // Total de movimientos registrados en el sistema

        return view('profile.show', compact(
            'usuario',
            'ultimoAcceso',
            'totalEppRegistrados',
            'totalEppAsignados',
            'totalEppBaja',
            'inventariadosRealizados'
        ));
    }

    /**
     * Actualizar datos personales del administrador
     */
    public function actualizarDatosPersonales(Request $request)
    {
        $usuario = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'workshop' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|max:2048', // Máx 2MB
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('perfiles', 'public');
            $usuario->imagen_url = '/storage/' . $path;
        }

        $usuario->fill($request->only(['name', 'dni', 'department', 'workshop']));
        $usuario->save();

        AuditLog::registrar(
            'perfil_actualizado',
            'User',
            $usuario->id,
            'Actualizó su información personal'
        );

        return redirect()->route('perfil.show')->with('success', 'Información personal actualizada correctamente');
    }

    /**
     * Actualizar email del usuario
     */
    public function actualizarEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        $usuario = auth()->user();
        $emailAnterior = $usuario->email;
        $usuario->update(['email' => $request->email]);

        AuditLog::registrar('email_actualizado', 'User', $usuario->id, 'Email actualizado de ' . $emailAnterior . ' a ' . $request->email);

        return redirect()->route('perfil.show')->with('success', 'Correo electrónico actualizado correctamente');
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => 'required|min:6|confirmed',
        ]);

        $usuario = auth()->user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->password_actual, $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        $usuario->update(['password' => Hash::make($request->password_nueva)]);

        AuditLog::registrar('contrasena_actualizada', 'User', $usuario->id, 'Contraseña actualizada');

        return redirect()->route('perfil.show')->with('success', 'Contraseña actualizada correctamente');
    }

    /**
     * Actualizar contraseña obligatoria al primer inicio
     */
    public function actualizarPasswordInicial(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $usuario = auth()->user();
        $usuario->password = Hash::make($request->password);
        // Al guardar, updated_at cambiará automáticamente, liberando al usuario del bloqueo
        $usuario->save(); 

        AuditLog::registrar('primer_ingreso', 'User', $usuario->id, 'Usuario activó su cuenta y cambió contraseña inicial');

        return redirect()->route('dashboard')->with('success', '¡Bienvenido! Tu cuenta ha sido activada.');
    }
}
