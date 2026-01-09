<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\AuditLog;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario
     */
    public function show()
    {
        $usuario = auth()->user();
        
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
        
        // Actividad del admin (últimas acciones registradas en auditoría)
        $actividadAdmin = AuditLog::where('user_id', $usuario->id)
            ->latest()
            ->take(20)
            ->get();
        
        // Entregas registradas por este admin
        $entregasRegistradas = Entrega::count();
        $aprobacionesRealizadas = AuditLog::where('user_id', $usuario->id)
            ->where('evento', 'aprobacion_solicitud')
            ->count();
        $bajaEpp = AuditLog::where('user_id', $usuario->id)
            ->where('evento', 'epp_eliminado')
            ->count();
        $modificacionesInventario = AuditLog::where('user_id', $usuario->id)
            ->where('evento', 'like', '%actualizado%')
            ->count();

        return view('profile.show', compact(
            'usuario',
            'accesosRecientes',
            'ultimoAcceso',
            'intentosFallidos',
            'actividadAdmin',
            'entregasRegistradas',
            'aprobacionesRealizadas',
            'bajaEpp',
            'modificacionesInventario'
        ));
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
}
