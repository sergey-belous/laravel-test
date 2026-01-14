#!/usr/bin/env bash
set -euo pipefail

# Simple DB dump script.
# Supports PostgreSQL (default) and SQLite fallback.
# Reads env from app/.env

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="$ROOT_DIR/app/.env"

if [[ -f "$ENV_FILE" ]]; then
  # shellcheck disable=SC2046
  export $(grep -vE '^\s*#' "$ENV_FILE" | grep -E '^\s*[A-Z0-9_]+=.*' | xargs -d '\n')
fi

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-laravel}"
DB_USERNAME="${DB_USERNAME:-laravel}"
DB_PASSWORD="${DB_PASSWORD:-secret}"

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
BACKUP_DIR="$ROOT_DIR/backups"
mkdir -p "$BACKUP_DIR"

if [[ "$DB_CONNECTION" == "sqlite" ]]; then
  DB_FILE="${DB_DATABASE:-$ROOT_DIR/app/database/database.sqlite}"
  if [[ ! -f "$DB_FILE" ]]; then
    echo "SQLite file not found: $DB_FILE" >&2
    exit 1
  fi
  OUT_FILE="$BACKUP_DIR/sqlite-${TIMESTAMP}.db"
  cp "$DB_FILE" "$OUT_FILE"
  echo "SQLite dump saved to $OUT_FILE"
  exit 0
fi

OUT_FILE="$BACKUP_DIR/pg-${DB_DATABASE}-${TIMESTAMP}.sql"
echo "Dumping PostgreSQL database '$DB_DATABASE' to $OUT_FILE"
PGPASSWORD="$DB_PASSWORD" pg_dump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --username="$DB_USERNAME" \
  --format=plain \
  --no-owner \
  "$DB_DATABASE" > "$OUT_FILE"

echo "Done."

