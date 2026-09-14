#!/bin/sh
set -e

UPLOADS_DIR="/var/www/html/public/uploads"

echo "[SGPRO] Preparando almacenamiento persistente..."

mkdir -p "$UPLOADS_DIR"

# Directorios utilizados actualmente por SGPRO
mkdir -p \
    "$UPLOADS_DIR/proofs" \
    "$UPLOADS_DIR/comprobantes" \
    "$UPLOADS_DIR/contracts" \
    "$UPLOADS_DIR/evaluations" \
    "$UPLOADS_DIR/photos" \
    "$UPLOADS_DIR/portfolios"

# Apache/PHP debe poder crear, reemplazar y eliminar archivos.
# Los documentos ya existentes se mantienen intactos.
find "$UPLOADS_DIR" -type d -exec chown www-data:www-data {} \;
find "$UPLOADS_DIR" -type d -exec chmod 775 {} \;

echo "[SGPRO] Permisos de uploads preparados."

exec apache2-foreground
