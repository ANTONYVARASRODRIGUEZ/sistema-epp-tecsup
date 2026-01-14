<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'epp_id' => 'required|exists:epps,id',
        'motivo' => 'required|string|max:500'
    ]);

    \App\Models\Solicitud::create([
        'user_id' => auth()->id(),
        'epp_id' => $request->epp_id,
        'motivo' => $request->motivo,
    ]);

    return back()->with('success', '¡Solicitud enviada correctamente!');
}
}
