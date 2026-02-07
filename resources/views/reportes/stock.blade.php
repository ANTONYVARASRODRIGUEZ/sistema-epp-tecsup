@extends('layouts.app')

@section('content')
<!-- Librería para generar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="container py-4" id="reporte-completo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Reporte de Stock Actual</h2>
            <p class="text-muted mb-0">Inventario general de EPPs al {{ date('d/m/Y') }}</p>
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
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre del EPP</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock Físico</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Deteriorados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($epps as $epp)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $epp->nombre }}</td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $epp->categoria->nombre ?? 'General' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $epp->stock < 10 ? 'bg-danger' : 'bg-success' }} fs-6">
                                    {{ $epp->stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($epp->stock == 0)
                                    <span class="text-danger fw-bold small">AGOTADO</span>
                                @elseif($epp->stock < 10)
                                    <span class="text-warning fw-bold small">CRÍTICO</span>
                                @else
                                    <span class="text-success fw-bold small">DISPONIBLE</span>
                                @endif
                            </td>
                            <td class="text-center text-muted">{{ $epp->deteriorado ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            filename:     'Reporte_Stock_EPP_{{ date("d-m-Y") }}.pdf',
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