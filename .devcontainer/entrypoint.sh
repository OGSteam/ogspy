#!/bin/bash
set -euo pipefail

echo "🔁 devcontainer entrypoint: ensuring OGSpy is installed before starting Apache"

# Default envs
DB_HOST=${DB_HOST:-db}
DB_USER=${DB_USER:-ogspy}
DB_PASSWORD=${DB_PASSWORD:-password}
DB_NAME=${DB_NAME:-ogspy}

# Helper to check if ogspy_config table exists
function db_has_config_table() {
  php -r "\$m = new mysqli('${DB_HOST}','${DB_USER}','${DB_PASSWORD}','${DB_NAME}'); if (\$m->connect_errno) exit(2); \$r = \$m->query('SHOW TABLES LIKE \'ogspy_config\''); if (\$r && \$r->num_rows>0) exit(0); exit(1);" >/dev/null 2>&1
  return $?
}

RETRY=0
MAX_RETRIES=30
until db_has_config_table; do
  STATUS=$?
  if [ "$STATUS" -eq 0 ]; then
    break
  fi
  if [ "$RETRY" -ge "$MAX_RETRIES" ]; then
    echo "❌ Timeout waiting for DB to have ogspy_config table"
    exit 1
  fi
  echo "⏳ ogspy not installed yet (attempt $((RETRY+1))/$MAX_RETRIES)..."
  # Try running setup if DB is reachable but table missing
  php -r "\$m = @new mysqli('${DB_HOST}','${DB_USER}','${DB_PASSWORD}','${DB_NAME}'); if (!\$m || \$m->connect_errno) exit;" >/dev/null 2>&1 && {
    echo "➡️ Running setup script to initialize OGSpy..."
    /usr/local/bin/setup-ogspy.sh || true
  }
  RETRY=$((RETRY+1))
  sleep 2
done

echo "✅ OGSpy database ready (ogspy_config found). Starting Apache"

# Exec the standard apache foreground command explicitly to keep the container running
exec docker-php-entrypoint apache2-foreground
