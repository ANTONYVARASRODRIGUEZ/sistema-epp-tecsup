<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ExcelImageExtractor
{
    /**
     * Extrae todas las imágenes del archivo Excel y las asigna a los EPPs por nombre
     * Devuelve un array con el mapeo: nombreEpp => rutaGuardada
     */
    public static function extraerImagenesConNombres($archivoPath)
    {
        $imagenesPorEpp = [];
        
        try {
            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($archivoPath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Obtener todas las imágenes
            $drawings = $worksheet->getDrawingCollection();
            
            Log::info('Extrayendo imágenes. Total encontrado: ' . count($drawings));
            
            // Crear un ZipArchive para acceder a las imágenes internas
            $zip = new ZipArchive();
            if ($zip->open($archivoPath) !== true) {
                Log::error('No se pudo abrir el archivo Excel como ZIP');
                return $imagenesPorEpp;
            }
            
            foreach ($drawings as $drawing) {
                $coordinate = $drawing->getCoordinates();
                
                // Extraer número de fila de la coordenada (ej: "D3" -> 3)
                preg_match('/(\d+)$/', $coordinate, $matches);
                
                if (!isset($matches[1])) {
                    continue;
                }
                
                $numeroFila = (int)$matches[1];
                
                // Obtener el nombre del EPP en la fila (columna B = índice 1)
                $nombreEpp = self::obtenerNombreEppDeFila($worksheet, $numeroFila);
                
                if (!$nombreEpp) {
                    Log::warning("No se pudo obtener nombre de EPP para fila {$numeroFila}");
                    continue;
                }
                
                // Guardar la imagen desde el ZIP
                $imagenPath = self::guardarImagenDesdeZip($zip, $drawing, $nombreEpp);
                
                if ($imagenPath) {
                    // Usar el nombre completo como clave (más único)
                    $clave = trim($nombreEpp);
                    $imagenesPorEpp[$clave] = $imagenPath;
                    Log::info("✓ Imagen guardada para EPP '{$clave}': {$imagenPath}");
                }
            }
            
            $zip->close();
            
            Log::info('Extracción completada. Total guardadas: ' . count($imagenesPorEpp));
            Log::info('Mapeo de imágenes: ' . json_encode(array_keys($imagenesPorEpp)));
            
        } catch (\Exception $e) {
            Log::error('Error extrayendo imágenes: ' . $e->getMessage());
        }
        
        return $imagenesPorEpp;
    }
    
    private static function obtenerNombreEppDeFila($worksheet, $numeroFila)
    {
        try {
            // La columna B tiene índice 2 (A=1, B=2)
            $celda = $worksheet->getCellByColumnAndRow(2, $numeroFila);
            $valor = $celda->getValue();
            return $valor ? trim((string)$valor) : null;
        } catch (\Exception $e) {
            Log::warning("Error leyendo nombre de EPP en fila {$numeroFila}: " . $e->getMessage());
            return null;
        }
    }
    
    private static function guardarImagen($drawing, $nombreEpp)
    {
        try {
            // La ruta es una URL ZIP, necesitamos obtener la imagen de otra forma
            // PhpOffice guarda las imágenes internamente en el objeto Drawing
            
            $image = $drawing->getImage();
            
            if (!$image) {
                Log::warning("No se pudo obtener imagen para EPP '{$nombreEpp}'");
                return null;
            }
            
            // Obtener el stream/contenido de la imagen
            $imagenStream = $image->getStream();
            $imagenMimeType = $image->getMimeType();
            
            // Determinar extensión por MIME type
            $extensiones = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            
            $ext = $extensiones[$imagenMimeType] ?? 'jpg';
            
            // Crear nombre final - sanitizar el nombre del EPP
            $nombreSanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($nombreEpp, 0, 30));
            $nombreFinal = 'epps/epp_' . $nombreSanitizado . '_' . time() . '.' . $ext;
            
            // Guardar la imagen usando Storage
            Storage::disk('public')->put($nombreFinal, $imagenStream);
            
            Log::info("Imagen guardada correctamente: {$nombreFinal}");
            
            return $nombreFinal;
            
        } catch (\Throwable $e) {
            Log::error("Error guardando imagen para EPP '{$nombreEpp}': " . $e->getMessage());
            Log::error("Stack: " . $e->getTraceAsString());
            return null;
        }
    }

    private static function guardarImagenDesdeZip($zip, $drawing, $nombreEpp)
    {
        try {
            // Obtener la ruta de la imagen dentro del ZIP (ej: xl/media/image1.png)
            $rutaEnZip = $drawing->getPath();
            
            // Extraer solo la parte después de # (xl/media/image1.png)
            if (strpos($rutaEnZip, '#') !== false) {
                $rutaEnZip = substr($rutaEnZip, strpos($rutaEnZip, '#') + 1);
            }
            
            Log::info("Intentando extraer imagen de ZIP: {$rutaEnZip}");
            
            // Leer el contenido de la imagen del ZIP
            $imagenStream = $zip->getStream($rutaEnZip);
            
            if (!$imagenStream) {
                Log::warning("No se pudo leer imagen del ZIP: {$rutaEnZip}");
                return null;
            }
            
            // Leer el contenido completo
            $imagenContenido = stream_get_contents($imagenStream);
            fclose($imagenStream);
            
            if (empty($imagenContenido)) {
                Log::warning("El contenido de la imagen está vacío para: {$rutaEnZip}");
                return null;
            }
            
            // Detectar tipo de imagen
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imagenContenido);
            finfo_close($finfo);
            
            // Mapear MIME type a extensión
            $extensiones = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            
            $ext = $extensiones[$mimeType] ?? 'jpg';
            
            Log::info("MIME type detectado: {$mimeType}, extensión: {$ext}");
            
            // Crear nombre final - sanitizar el nombre del EPP
            $nombreSanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($nombreEpp, 0, 30));
            $nombreFinal = 'epps/epp_' . $nombreSanitizado . '_' . time() . '.' . $ext;
            
            // Guardar la imagen usando Storage
            Storage::disk('public')->put($nombreFinal, $imagenContenido);
            
            Log::info("✓ Imagen guardada correctamente: {$nombreFinal}");
            
            return $nombreFinal;
            
        } catch (\Throwable $e) {
            Log::error("Error extrayendo imagen del ZIP para EPP '{$nombreEpp}': " . $e->getMessage());
            return null;
        }
    }
}
