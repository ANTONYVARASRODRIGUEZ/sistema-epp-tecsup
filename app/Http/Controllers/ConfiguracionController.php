<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use App\Models\Departamento;
use App\Models\MatrizHomologacion;
use App\Models\AuditLog;
use App\Models\Epp;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar el panel de configuración
     */
    public function index()
    {
        $configuracion = ConfiguracionSistema::obtener();
        $departamentos = Departamento::all();
        $epps = Epp::all();
        $matrizHomologacion = MatrizHomologacion::with('departamento', 'epp')->get();
        $logs = AuditLog::with('usuario')->latest()->take(50)->get();

        return view('configuracion.index', compact(
            'configuracion',
            'departamentos',
            'epps',
            'matrizHomologacion',
            'logs'
        ));
    }

    /**
     * Actualizar configuración general
     */
    public function actualizarGeneral(Request $request)
    {

        $request->validate([
            'nombre_sistema' => 'required|string|max:255',
            'sede' => 'required|string|max:255',
            'anio_academico' => 'required|string|max:4',
        ]);

        $config = ConfiguracionSistema::obtener();
        $config->update($request->only([
            'nombre_sistema',
            'sede',
            'anio_academico',
        ]));

        AuditLog::registrar('config_actualizada', 'ConfiguracionSistema', $config->id, 'Configuración general actualizada');

        return redirect()->route('configuracion.index')->with('success', 'Configuración general actualizada');
    }

    /**
     * Actualizar parámetros de EPP
     */
    public function actualizarParametrosEpp(Request $request)
    {
        $this->authorize('isAdmin');

        $request->validate([
            'tiempo_renovacion_dias' => 'required|integer|min:1',
            'umbral_stock_bajo' => 'required|integer|min:1',
        ]);

        $config = ConfiguracionSistema::obtener();
        $config->update($request->only([
            'tiempo_renovacion_dias',
            'umbral_stock_bajo',
        ]));

        AuditLog::registrar('parametros_epp_actualizados', 'ConfiguracionSistema', $config->id, 'Parámetros de EPP actualizados');

        return redirect()->route('configuracion.index')->with('success', 'Parámetros de EPP actualizados');
    }

    /**
     * Actualizar configuración de notificaciones
     */
    public function actualizarNotificaciones(Request $request)
    {
        $this->authorize('isAdmin');

        $config = ConfiguracionSistema::obtener();
        $config->update([
            'alertas_vencimiento' => $request->has('alertas_vencimiento'),
            'alertas_stock_bajo' => $request->has('alertas_stock_bajo'),
            'alertas_solicitudes_pendientes' => $request->has('alertas_solicitudes_pendientes'),
        ]);

        AuditLog::registrar('notificaciones_actualizadas', 'ConfiguracionSistema', $config->id, 'Configuración de notificaciones actualizada');

        return redirect()->route('configuracion.index')->with('success', 'Notificaciones configuradas');
    }

    /**
     * Actualizar configuración de auditoría
     */
    public function actualizarAuditoria(Request $request)
    {
        $this->authorize('isAdmin');

        $request->validate([
            'dias_retencion_logs' => 'required|integer|min:30|max:1825',
        ]);

        $config = ConfiguracionSistema::obtener();
        $config->update([
            'auditoria_activa' => $request->has('auditoria_activa'),
            'dias_retencion_logs' => $request->dias_retencion_logs,
        ]);

        AuditLog::registrar('auditoria_actualizada', 'ConfiguracionSistema', $config->id, 'Configuración de auditoría actualizada');

        return redirect()->route('configuracion.index')->with('success', 'Auditoría configurada');
    }

    /**
     * Crear departamento
     */
    public function crearDepartamento(Request $request)
    {
        $this->authorize('isAdmin');

        $request->validate([
            'nombre' => 'required|string|max:255|unique:departamentos,nombre',
        ]);

        $depto = Departamento::create($request->only('nombre'));

        AuditLog::registrar('departamento_creado', 'Departamento', $depto->id, 'Departamento creado: ' . $depto->nombre);

        return redirect()->route('configuracion.index')->with('success', 'Departamento creado');
    }

    /**
     * Actualizar departamento
     */
    public function actualizarDepartamento(Request $request, $id)
    {
        $this->authorize('isAdmin');

        $depto = Departamento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:departamentos,nombre,' . $id,
        ]);

        $depto->update($request->only('nombre'));

        AuditLog::registrar('departamento_actualizado', 'Departamento', $depto->id, 'Departamento actualizado: ' . $depto->nombre);

        return redirect()->route('configuracion.index')->with('success', 'Departamento actualizado');
    }

    /**
     * Desactivar departamento
     */
    public function desactivarDepartamento($id)
    {
        $this->authorize('isAdmin');

        $depto = Departamento::findOrFail($id);
        $depto->update(['activo' => false]);

        AuditLog::registrar('departamento_desactivado', 'Departamento', $depto->id, 'Departamento desactivado: ' . $depto->nombre);

        return redirect()->route('configuracion.index')->with('success', 'Departamento desactivado');
    }

    /**
     * Activar departamento
     */
    public function activarDepartamento($id)
    {
        $this->authorize('isAdmin');

        $depto = Departamento::findOrFail($id);
        $depto->update(['activo' => true]);

        AuditLog::registrar('departamento_activado', 'Departamento', $depto->id, 'Departamento activado: ' . $depto->nombre);

        return redirect()->route('configuracion.index')->with('success', 'Departamento activado');
    }

    /**
     * Agregar a matriz de homologación
     */
    public function agregarMatriz(Request $request)
    {
        $this->authorize('isAdmin');

        $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'epp_id' => 'required|exists:epps,id',
            'tipo_requerimiento' => 'required|in:obligatorio,especifico,opcional',
            'puesto' => 'nullable|string|max:255',
            'taller' => 'nullable|string|max:255',
        ]);

        $matriz = MatrizHomologacion::create($request->all());

        AuditLog::registrar('matriz_homologacion_creada', 'MatrizHomologacion', $matriz->id, 'Matriz de homologación creada');

        return redirect()->route('configuracion.index')->with('success', 'EPP agregado a matriz de homologación');
    }

    /**
     * Eliminar de matriz
     */
    public function eliminarMatriz($id)
    {
        $this->authorize('isAdmin');

        $matriz = MatrizHomologacion::findOrFail($id);
        $matriz->delete();

        AuditLog::registrar('matriz_homologacion_eliminada', 'MatrizHomologacion', $id, 'Matriz de homologación eliminada');

        return redirect()->route('configuracion.index')->with('success', 'EPP removido de matriz de homologación');
    }
}
