#!/usr/bin/env bash
# ============================================================
#  IncluKids · respaldo de la base de datos (Linux / macOS)
# ------------------------------------------------------------
#  Genera  backups/inclukids_AAAA-MM-DD_HHMM.sql  y conserva
#  solo los ÚLTIMOS 14. Para cron, p. ej. a las 2:00:
#     0 2 * * *  /ruta/IncluKids/scripts/respaldo.sh
#
#  Variables de entorno opcionales: DB, DBUSER, DBPASS, MYSQLDUMP.
# ============================================================
set -euo pipefail

DB="${DB:-inclukids}"
DBUSER="${DBUSER:-root}"
DBPASS="${DBPASS:-}"
MYSQLDUMP="${MYSQLDUMP:-mysqldump}"

PROY="$(cd "$(dirname "$0")/.." && pwd)"
DEST="$PROY/backups"
mkdir -p "$DEST"

STAMP="$(date +%Y-%m-%d_%H%M)"
ARCHIVO="$DEST/inclukids_${STAMP}.sql"

args=(-u "$DBUSER" --single-transaction --routines --events)
[ -n "$DBPASS" ] && args+=("-p$DBPASS")

if "$MYSQLDUMP" "${args[@]}" "$DB" > "$ARCHIVO"; then
    echo "[$(date '+%F %T')] OK $ARCHIVO" >> "$DEST/respaldo.log"
else
    echo "[$(date '+%F %T')] ERROR al respaldar" >> "$DEST/respaldo.log"
    rm -f "$ARCHIVO"
    exit 1
fi

# Conservar solo los 14 más recientes
ls -1t "$DEST"/inclukids_*.sql 2>/dev/null | tail -n +15 | xargs -r rm -f
