<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExcelImageExtractor;
use Illuminate\Support\Facades\Log;

class TestExcelImageExtraction extends Command
{
    protected $signature = 'test:extract-images {file : Ruta del archivo Excel}';
    protected $description = 'Prueba la extracción de imágenes de un archivo Excel';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Archivo no encontrado: {$file}");
            return 1;
        }

        $this->info("Iniciando extracción de imágenes desde: {$file}");
        $this->info("");

        try {
            $imagenes = ExcelImageExtractor::extraerImagenesConNombres($file);

            if (empty($imagenes)) {
                $this->warn("⚠ No se encontraron imágenes o no se pudieron extraer");
                return 0;
            }

            $this->info("✓ Total de imágenes extraídas: " . count($imagenes));
            $this->info("");
            $this->info("Mapeo de imágenes:");
            
            foreach ($imagenes as $nombre => $ruta) {
                $this->line("  • {$nombre}");
                $this->line("    → {$ruta}");
            }

            $this->info("");
            $this->info("✓ Las imágenes se guardaron en: storage/app/public/epps/");
            $this->info("  Verifica que existan en: storage/app/public/epps/");

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error("Test extraction error: " . $e->getMessage());
            return 1;
        }
    }
}
