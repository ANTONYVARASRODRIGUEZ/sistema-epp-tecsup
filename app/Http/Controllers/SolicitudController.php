<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Datos ficticios para solicitudes
        $solicitudes = [
            [
                'id' => 1,
                'tipo' => 'Nuevo',
                'usuario' => 'Prof. Martínez',
                'epp' => 'Casco de Seguridad',
                'cantidad' => 5,
                'fecha' => '07/01/2026',
                'estado' => 'Aprobado'
            ],
            [
                'id' => 2,
                'tipo' => 'Renovación',
                'usuario' => 'Prof. Sánchez',
                'epp' => 'Guantes Nitrilo',
                'cantidad' => 10,
                'fecha' => '06/01/2026',
                'estado' => 'Pendiente'
            ],
            [
                'id' => 3,
                'tipo' => 'Devolución',
                'usuario' => 'Prof. Delgado',
                'epp' => 'Lentes de Seguridad',
                'cantidad' => 3,
                'fecha' => '05/01/2026',
                'estado' => 'Aprobado'
            ],
        ];

        // Contar por estado
        $pendientes = collect($solicitudes)->where('estado', 'Pendiente')->count();
        $aprobadas = collect($solicitudes)->where('estado', 'Aprobado')->count();
        $rechazadas = collect($solicitudes)->where('estado', 'Rechazado')->count();

        return view('solicitudes.index', compact('solicitudes', 'pendientes', 'aprobadas', 'rechazadas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('solicitudes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:Nuevo,Renovación,Devolución',
            'usuario' => 'required|string',
            'epp' => 'required|string',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Lógica de almacenamiento
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('solicitudes.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('solicitudes.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Lógica de actualización
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Lógica de eliminación
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud eliminada correctamente');
    }

    /**
     * Aprobar una solicitud
     */
    public function aprobar(string $id)
    {
        // Lógica para aprobar
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud aprobada');
    }

    /**
     * Rechazar una solicitud
     */
    public function rechazar(string $id)
    {
        // Lógica para rechazar
        return redirect()->route('solicitudes.index')->with('success', 'Solicitud rechazada');
    }
}
