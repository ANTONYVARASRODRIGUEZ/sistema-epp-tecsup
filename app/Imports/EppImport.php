<?php

namespace App\Imports;

use App\Models\Epp;
use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class EppImport implements ToModel, WithStartRow, SkipsEmptyRows
{
    private $imagenesPorNombre = [];
    private $fechaRegistro = null;

    // IMPORTANTE: Variable para recordar el departamento de la fila de arriba
    private $ultimoDepartamento = null;

    public function __construct($imagenesPorNombre = [], $fechaRegistro = null)
    {
        $this->imagenesPorNombre = $imagenesPorNombre;
        $this->fechaRegistro = $fechaRegistro;
        Log::info('EppImport inicializado con ' . count($imagenesPorNombre) . ' imágenes');
        if (count($imagenesPorNombre) > 0) {
            Log::info('EPPs con imagen: ' . implode(', ', array_slice(array_keys($imagenesPorNombre), 0, 5)));
        }
    }

    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
{
    // 1. Validar nombre del EPP (Columna B / índice 1)
    if (!isset($row[1]) || empty(trim($row[1]))) {
        return null;
    }

    $nombreEpp = trim($row[1]);

    // 2. Saltar encabezados (Mantenemos tu filtro original)
    if (str_contains(strtoupper($nombreEpp), 'EQUIPOS DE PROTECCIÓN')) {
        return null;
    }
    

    // --- REINCORPORADO: Tus Logs de seguimiento de imágenes ---
    $imagenPath = $this->imagenesPorNombre[$nombreEpp] ?? null;

    if ($imagenPath) {
        Log::info("✓ Imagen encontrada para: {$nombreEpp} -> {$imagenPath}");
    } else {
        Log::info("✗ Sin imagen en mapeo para: {$nombreEpp}");
    }
    // --------------------------------------------------------

    // 3. Categoría y Vida Útil (Columna E / índice 4)
    $categoriaId = $this->obtenerCategoriaPorNombre($nombreEpp);
    $frecuenciaTexto = strtolower($row[4] ?? '');
    $vidaUtilMeses = 12;

    if (preg_match('/\d+/', $frecuenciaTexto, $matches)) {
        $numero = (int)$matches[0];
        if (str_contains($frecuenciaTexto, 'año') || str_contains($frecuenciaTexto, 'ano')) {
            $vidaUtilMeses = $numero * 12;
        } else {
            $vidaUtilMeses = $numero;
        }
    }

    // 4. Gestión de Fechas
    $fechaDetec = $this->detectarFechaIngreso($row);
    $fechaBase = $fechaDetec ?: ($this->fechaRegistro ? Carbon::parse($this->fechaRegistro) : now());
    $fechaVencimiento = $fechaBase->copy()->addMonths($vidaUtilMeses);

    // 5. Imagen Final
    $imagenFinal = $imagenPath ?? $this->generarImagenAutomatica($nombreEpp);

    // 6. CREACIÓN DEL EPP (Aquí eliminamos row[9] y row[11] que dan error)
    $epp = new Epp([
        'nombre'             => $nombreEpp,
        'imagen'             => $imagenFinal,
        'descripcion'        => $row[2] ?? null,           // Columna C
        'frecuencia_entrega' => $row[4] ?? null,           // Columna E
        'codigo_logistica'   => $row[5] ?? null,           // Columna F
        'marca_modelo'       => $row[6] ?? null,           // Columna G
        
        // Guardamos los departamentos de la Columna H (índice 7)
        'departamento_texto' => $row[7] ?? null,

        // Forzamos 0 porque estas columnas ya no existen en tu Excel
        // --- SECCIÓN DE INVENTARIO (DATOS DEL EXCEL) ---
    // Columna J (Precio) - Limpiamos posibles espacios o caracteres extra
    'precio'             => isset($row[9]) ? (float) str_replace(',', '', $row[9]) : 0,
    
    // Columna L (Cantidad / Stock Inicial)
    'cantidad'           => isset($row[11]) ? (int) $row[11] : 0,
    
    // Mantenemos estos en 0 porque son contadores operativos del sistema
    'stock'              => isset($row[11]) ? (int) $row[11] : 0, // Inicialmente el stock es igual a la cantidad
        'entregado'          => 0,
        'deteriorado'        => 0,
        
        'tipo'               => 'Protección de seguridad',
        'vida_util_meses'    => $vidaUtilMeses,
        'fecha_vencimiento'  => $fechaVencimiento,
        'categoria_id'       => $categoriaId,
        'estado'             => 'disponible',
    ]);

    $epp->created_at = $fechaBase;

    return $epp;
}

    private function generarImagenAutomatica($nombreEpp)
    {
        $terminoBusqueda = urlencode($nombreEpp . ' safety equipment');
        return "https://source.unsplash.com/featured/400x400?{$terminoBusqueda}";
    }

    private function obtenerCategoriaPorNombre($nombre)
    {
        $nombreLower = strtolower($nombre);
        $nombreCategoria = 'Otros';

        if (str_contains($nombreLower, 'casco') || str_contains($nombreLower, 'craneal')) {
            $nombreCategoria = 'Protección Craneal';
        } elseif (str_contains($nombreLower, 'lente') || str_contains($nombreLower, 'gafa') || str_contains($nombreLower, 'careta')) {
            $nombreCategoria = 'Protección Visual';
        } elseif (str_contains($nombreLower, 'guante')) {
            $nombreCategoria = 'Protección Manual';
        } elseif (str_contains($nombreLower, 'bota') || str_contains($nombreLower, 'zapato') || str_contains($nombreLower, 'calzado')) {
            $nombreCategoria = 'Protección de Pies';
        } elseif (str_contains($nombreLower, 'tapon') || str_contains($nombreLower, 'orejera') || str_contains($nombreLower, 'auditivo')) {
            $nombreCategoria = 'Protección Auditiva';
        } elseif (str_contains($nombreLower, 'chaleco') || str_contains($nombreLower, 'mameluco') || str_contains($nombreLower, 'ropa')) {
            $nombreCategoria = 'Ropa de Trabajo';
        }

        $categoria = Categoria::firstOrCreate(
            ['nombre' => $nombreCategoria],
            ['descripcion' => 'Categoría generada automáticamente por importación']
        );

        return $categoria->id;
    }

    /**
     * Intenta detectar una fecha de ingreso en la fila.
     * Busca celdas con formatos comunes de fecha (dd/mm/yyyy, yyyy-mm-dd, etc.).
     */
    private function detectarFechaIngreso(array $row): ?Carbon
    {
        foreach ($row as $cell) {
            if (!is_string($cell) && !is_numeric($cell)) continue;

            $value = trim((string)$cell);
            if ($value === '') continue;

            // ✅ CORRECCIÓN: regex corregido — barras invertidas bien escapadas dentro de clase de caracteres
            if (preg_match('/\d{1,4}[\/\-]\d{1,2}[\/\-]\d{1,4}/', $value)) {
                try {
                    return Carbon::parse($value);
                } catch (\Throwable $e) {
                    // ignorar y seguir
                }
            }

            // Si es número serial de Excel (base 1899-12-30), intentar convertir
            if (is_numeric($cell) && $cell > 30000 && $cell < 60000) {
                try {
                    return Carbon::create(1899, 12, 30)->addDays((int)$cell);
                } catch (\Throwable $e) {
                    // ignorar
                }
            }
        }
        return null;
    }
}