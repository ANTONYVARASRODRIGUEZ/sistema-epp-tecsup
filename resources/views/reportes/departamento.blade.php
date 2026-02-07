@extends('layouts.app')

@section('content')
<!-- Librería para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container py-4" id="reporte-completo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Asignaciones por Área</h2>
            <p class="text-muted mb-0">Consulte los equipos entregados al personal de cada departamento.</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill no-print" data-html2canvas-ignore="true">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- Filtro -->
    <div class="card border-0 shadow-sm mb-4 no-export" style="border-radius: 15px;" data-html2canvas-ignore="true">
        <div class="card-body p-4">
            <form action="{{ route('reportes.departamento') }}" method="GET" class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Seleccione Departamento</label>
                    <select name="departamento_id" class="form-select form-select-lg bg-light border-0" onchange="this.form.submit()">
                        <option value="">-- Seleccionar --</option>
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto->id }}" {{ (isset($departamentoSeleccionado) && $departamentoSeleccionado->id == $depto->id) ? 'selected' : '' }}>
                                {{ $depto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end d-flex gap-2 justify-content-end">
                    @if(isset($departamentoSeleccionado))
                        <button id="btnDescargarPdf" type="button" class="btn btn-danger rounded-pill px-4 shadow-sm">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Descargar PDF
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm">
                            <i class="bi bi-printer me-2"></i> Imprimir
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($departamentoSeleccionado))
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-building me-2"></i> {{ $departamentoSeleccionado->nombre }}
                </h5>
            </div>
            <div class="card-body p-0">
                @if($personal->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted fs-1"></i>
                        <p class="text-muted mt-2">No hay personal registrado en este departamento.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Docente / Personal</th>
                                    <th>DNI</th>
                                    <th>Equipos Asignados (EPP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personal as $persona)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $persona->nombre_completo }}</div>
                                        <small class="text-muted">{{ $persona->carrera }}</small>
                                    </td>
                                    <td>{{ $persona->dni }}</td>
                                    <td>
                                        @if($persona->asignaciones->where('estado', 'Entregado')->isEmpty())
                                            <span class="badge bg-light text-muted border">Sin asignaciones activas</span>
                                        @else
                                            <ul class="list-unstyled mb-0">
                                                @foreach($persona->asignaciones->where('estado', 'Entregado') as $asignacion)
                                                    <li class="mb-1">
                                                        <i class="bi bi-check2-circle text-success me-1"></i>
                                                        {{ $asignacion->epp->nombre }} 
                                                        <span class="fw-bold">x{{ $asignacion->cantidad }}</span>
                                                        <small class="text-muted ms-1">({{ $asignacion->fecha_entrega ? \Carbon\Carbon::parse($asignacion->fecha_entrega)->format('d/m/Y') : 'S/F' }})</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    const btnPdf = document.getElementById('btnDescargarPdf');
    if (btnPdf) {
        btnPdf.addEventListener('click', function() {
            const element = document.getElementById('reporte-completo');
            
            // Añadimos clase para reducir tamaño de letra temporalmente
            element.classList.add('modo-pdf');

            const opt = {
                margin:       0.5,
                filename:     'Reporte_Asignaciones_{{ date("d-m-Y") }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save().then(() => {
                element.classList.remove('modo-pdf');
            });
        });
    }
</script>

<style>
    /* Estilos específicos para reducir tamaño en PDF generado */
    .modo-pdf {
        font-size: 10px !important;
        background: white;
    }
    .modo-pdf h2 {
        font-size: 16px !important;
        margin-bottom: 5px !important;
    }
    .modo-pdf p {
        font-size: 11px !important;
        margin-bottom: 10px !important;
    }
    .modo-pdf .table th, 
    .modo-pdf .table td {
        padding: 4px 6px !important;
        font-size: 10px !important;
    }
    .modo-pdf .badge {
        font-size: 9px !important;
        padding: 3px 6px !important;
    }
    .modo-pdf .no-export {
        display: none !important;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #reporte-completo, #reporte-completo * {
            visibility: visible;
        }
        #reporte-completo {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
        }
        .no-print, .no-export {
            display: none !important;
        }
        
        /* Ajustes de tamaño para Impresión física (Ctrl+P) */
        body {
            font-size: 10pt;
        }
        h2 {
            font-size: 14pt !important;
        }
        .table th, .table td {
            padding: 4px !important;
            font-size: 9pt !important;
        }
    }
</style>
@endsection