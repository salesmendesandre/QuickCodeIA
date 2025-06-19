#!/bin/bash

# Definir la URL y archivos
URL="https://download.moodle.org/download.php/direct/stable405/moodle-latest-405.zip"
DEST_DIR="moodle"
ZIP_FILE="moodle-latest-405.zip"

# Descargar el archivo
echo "Descargando Moodle desde $URL..."
curl -L -o "$ZIP_FILE" "$URL"

# Verificar si la descarga fue exitosa
if [ $? -ne 0 ]; then
    echo "Error al descargar Moodle."
    exit 1
fi

# Eliminar la carpeta previa si existe
if [ -d "$DEST_DIR" ]; then
    echo "Eliminando carpeta existente: $DEST_DIR"
    rm -rf "$DEST_DIR"
fi

# Crear la carpeta destino
mkdir -p "$DEST_DIR"

# Descomprimir el archivo en la carpeta destino
echo "Descomprimiendo Moodle en $DEST_DIR..."
unzip -q "$ZIP_FILE" -d "$DEST_DIR"

# Eliminar el archivo ZIP
rm "$ZIP_FILE"

echo "Moodle ha sido descargado y extraído en la carpeta '$DEST_DIR'."
