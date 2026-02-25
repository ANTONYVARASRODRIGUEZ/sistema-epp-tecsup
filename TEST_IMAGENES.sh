#!/bin/bash
# Test script para verificar extracción de imágenes

echo "=== PRUEBA DE EXTRACCIÓN DE IMÁGENES ==="
echo ""

# Buscar si hay archivos Excel recientes
echo "Buscando archivos Excel..."
find /c/Users -name "*.xlsx" -mtime -7 2>/dev/null | head -10

echo ""
echo "Si encuentras un archivo, cópialo a la carpeta del proyecto"
echo "y luego sube la importación desde la interfaz web."
echo ""
echo "Verifica los logs en: storage/logs/laravel.log"
echo "Busca mensajes como:"
echo '  [INFO] Imagen guardada para EPP'
echo '  [WARNING] Sin imagen en mapeo para:'
