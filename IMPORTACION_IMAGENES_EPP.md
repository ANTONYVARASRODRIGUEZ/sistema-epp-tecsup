# SOLUCIÓN: Importación de Imágenes en EPPs desde Excel

## Problema Identificado
El sistema estaba importando todos los datos del Excel EXCEPTO las imágenes. En su lugar, se asignaban URLs automáticas de Unsplash.

## Causa Raíz
La clase `EppImport.php` estaba usando URLs de Unsplash en lugar de procesar las imágenes embebidas en el archivo Excel.

## Solución Implementada

Se ha creado un sistema de **dos pasos** para la importación:

### 1. **Nuevo Servicio: ExcelImageExtractor**
   - **Archivo**: `app/Services/ExcelImageExtractor.php`
   - **Función**: Extrae todas las imágenes del Excel ANTES de procesar los datos
   - **Proceso**:
     - Lee el archivo Excel
     - Obtiene todas las imágenes (drawings) embebidas
     - Identifica el número de fila de cada imagen
     - Guarda cada imagen en `storage/app/public/epps/`
     - Retorna un mapeo: `numeroFila => rutaGuardada`

### 2. **EppImport Actualizado**
   - **Archivo**: `app/Imports/EppImport.php`
   - **Cambios**:
     - Ahora acepta el mapeo de imágenes en el constructor
     - Rastrea el número de fila actual durante la importación
     - Busca si hay imagen para cada fila
     - Si encontrada: usa la imagen del Excel
     - Si no: genera URL de Unsplash (fallback)

### 3. **EppController Actualizado**
   - **Archivo**: `app/Http/Controllers/EppController.php`
   - **Método `import()`** modificado para:
     1. Llamar primero a `ExcelImageExtractor::extraerImagenes()`
     2. Obtener el mapeo de imágenes
     3. Pasar el mapeo al constructor de `EppImport`
     4. Ejecutar la importación normalmente

## Flujo de Importación (Nuevo)

```
1. Usuario sube archivo Excel
   ↓
2. ExcelImageExtractor extrae imágenes
   ├─ Lee drawing collection del Excel
   ├─ Guarda cada imagen como archivo físico
   └─ Retorna mapeo: fila → imagen
   ↓
3. EppImport procesa datos
   ├─ Para cada fila:
   │  ├─ Obtiene datos del Excel
   │  ├─ Busca imagen en el mapeo
   │  ├─ Crea registro Epp con imagen
   └─ Continúa
   ↓
4. Importación completada
   └─ Todos los EPPs tienen sus imágenes!
```

## Estructura del Excel Esperada

El Excel debe tener:
- **Fila 1**: Título/Encabezado general
- **Fila 2**: Títulos de columnas
- **Fila 3+**: Datos del EPP + Imagen en columna D (o la columna donde esté)

**Columnas esperadas**:
- B: Nombre EPP
- C: Descripción
- D: Imagen (embebida)
- E: Frecuencia de entrega
- F: Código de logística
- G: Marca/Modelo
- I: Precio
- J: Cantidad

## Cómo Usar

1. **Preparar el Excel**:
   - Asegúrate que las imágenes estén embebidas en las celdas del Excel (Copy/Paste directo)
   - Las imágenes deben estar en la misma fila que el EPP

2. **Importar**:
   - Ve a: Panel Gestión EPP → Importar Matriz
   - Carga el Excel
   - Ahora se importarán DATOS + IMÁGENES

## Logs para Debugging

Si algo no funciona, revisa `storage/logs/laravel.log`:

```
✓ Iniciando extracción de imágenes. Total de dibujos encontrados: 5
✓ Imagen guardada para fila 3: epps/epp_3_1708872345.jpg
✓ EppImport inicializado con 5 imágenes
```

## Ubicación de Archivos Modificados

```
app/
├── Services/
│   └── ExcelImageExtractor.php (NUEVO)
├── Imports/
│   └── EppImport.php (ACTUALIZADO)
└── Http/Controllers/
    └── EppController.php (ACTUALIZADO)
```

## Resolución de Problemas

### Las imágenes no se importan
1. Verifica que estén embebidas en el Excel (no solo rutas)
2. Revisa los logs en `storage/logs/laravel.log`
3. Asegúrate que `storage/app/public/` es escribible

### Error: "No se puede leer el archivo"
1. Asegúrate que el archivo sea .xlsx o .xls válido
2. Prueba abriendo el archivo en Excel y guardándolo nuevamente

### Las imágenes se guardan pero con nombre raño
- Esto es normal, se crean con timestamp para evitar conflictos
- Ejemplo: `epp_3_1708872345.jpg` es la imagen de la fila 3

## Ventajas de la Nueva Solución

✅ Las imágenes se extraen del Excel  
✅ Se guardan en el servidor local (no depende de Unsplash)  
✅ Control total sobre las imágenes  
✅ Mejor velocidad (no requiere conexión a internet)  
✅ Fallback a Unsplash si no hay imagen en Excel  
✅ Logs detallados para debugging  

---

**Fecha**: 25 de febrero de 2026  
**Estado**: Implementado y listo para usar
