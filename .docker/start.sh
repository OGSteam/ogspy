#!/bin/sh

set -e

APP_DIR="/var/www/html"
DB_HOST="${OGSPY_DB_HOST:-db}"
DB_NAME="${MARIADB_DATABASE:-ogspy}"
DB_USER="${MARIADB_USER:-ogspy}"
DB_PASSWORD="${MARIADB_PASSWORD:-}"
TABLE_PREFIX="${OGSPY_TABLE_PREFIX:-ogspy_}"
LOCK_ON_START="${OGSPY_LOCK_INSTALL_ON_START:-false}"

if [ "$LOCK_ON_START" = "true" ] && [ "${OGSPY_AUTO_INSTALL_ON_START:-false}" != "true" ]; then
  touch "$APP_DIR/install/install.lock"
fi

# Optional non-interactive first-run installation.
if [ "${OGSPY_AUTO_INSTALL_ON_START:-false}" = "true" ]; then
  if [ -f "$APP_DIR/install/install.lock" ]; then
    echo "[ogspy] install.lock exists, skipping auto-install"
  elif [ -z "${OGSPY_ADMIN_USER:-}" ] || [ -z "${OGSPY_ADMIN_PASSWORD:-}" ]; then
    echo "[ogspy] OGSPY_AUTO_INSTALL_ON_START=true but missing OGSPY_ADMIN_USER or OGSPY_ADMIN_PASSWORD"
    exit 1
  else
    echo "[ogspy] waiting for database at ${DB_HOST}:3306"
    i=0
    until env DB_HOST="$DB_HOST" DB_USER="$DB_USER" DB_PASSWORD="$DB_PASSWORD" DB_NAME="$DB_NAME" \
      php -r '$h=getenv("DB_HOST"); $c=@mysqli_connect($h, getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME")); if ($c) { mysqli_close($c); exit(0); } exit(1);'
    do
      i=$((i + 1))
      if [ "$i" -ge 30 ]; then
        echo "[ogspy] database is not reachable after 60s"
        exit 1
      fi
      sleep 2
    done

    echo "[ogspy] checking existing installation"
    if env DB_HOST="$DB_HOST" DB_NAME="$DB_NAME" DB_USER="$DB_USER" DB_PASSWORD="$DB_PASSWORD" TABLE="${TABLE_PREFIX}config" \
      php -r '$h=getenv("DB_HOST"); $db=getenv("DB_NAME"); $u=getenv("DB_USER"); $p=getenv("DB_PASSWORD"); $t=getenv("TABLE"); $c=@mysqli_connect($h,$u,$p,$db); if(!$c){exit(2);} $r=@mysqli_query($c, "SHOW TABLES LIKE \"" . mysqli_real_escape_string($c, $t) . "\""); if($r && mysqli_num_rows($r) > 0){exit(0);} exit(1);'
    then
      echo "[ogspy] existing installation detected, skipping auto-install"
    else
      echo "[ogspy] running non-interactive install"
      php "$APP_DIR/install/upgrade_cli.php" install \
        "$DB_HOST" \
        "$DB_USER" \
        "$DB_PASSWORD" \
        "$DB_NAME" \
        "$OGSPY_ADMIN_USER" \
        "$OGSPY_ADMIN_PASSWORD" \
        "${OGSPY_ADMIN_EMAIL:-}" \
        "$TABLE_PREFIX"

      if [ "$LOCK_ON_START" = "true" ]; then
        touch "$APP_DIR/install/install.lock"
      fi
    fi
  fi
fi

chown -R ogspy:ogspy "$APP_DIR/cache" "$APP_DIR/logs" "$APP_DIR/config"

exec php-fpm