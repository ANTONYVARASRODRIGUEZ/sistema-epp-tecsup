@extends('layouts.app')

@section('content')
<!-- Librería para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container py-4" id="reporte-completo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Incidencias y Bajas</h2>
            <p class="text-muted mb-0">Listado de EPPs reportados como dañados, perdidos o dados de baja.</p>
        </div>
        <div class="d-flex gap-2 no-print" data-html2canvas-ignore="true">
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <button id="btnDescargarPdf" class="btn btn-danger rounded-pill shadow-sm">
                <i class="bi bi-file-earmark-pdf me-2"></i> Descargar PDF
            </button>
            <button onclick="window.print()" class="btn btn-dark rounded-pill shadow-sm">
                <i class="bi bi-printer me-2"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-0">
            @if($incidencias->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <p class="text-muted mt-2">No se han reportado incidencias hasta la fecha.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">EPP</th>
                                <th>Personal Asignado</th>
                                <th>Departamento</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Estado Incidencia</th>
                                <th>Fecha Reporte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incidencias as $incidencia)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $incidencia->epp->nombre ?? 'N/A' }}</td>
                                <td>{{ $incidencia->personal->nombre_completo ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        {{ $incidencia->personal->departamento->nombre ?? 'Sin Depto.' }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $incidencia->cantidad }}</span></td>
                                <td class="text-center"><span class="badge bg-{{ $incidencia->estado == 'Dañado' ? 'warning text-dark' : 'danger' }}">{{ $incidencia->estado }}</span></td>
                                <td>{{ $incidencia->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('btnDescargarPdf').addEventListener('click', function() {
        const element = document.getElementById('reporte-completo');
        
        // Añadimos clase para reducir tamaño de letra temporalmente
        element.classList.add('modo-pdf');

        const opt = {
            margin:       0.5,
            filename:     'Reporte_Incidencias_{{ date("d-m-Y") }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(() => {
            element.classList.remove('modo-pdf');
        });
    });
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
        .no-print {
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
        .badge {
            font-size: 8pt !important;
            border: 1px solid #000;
        }
    }
</style>
@endsection