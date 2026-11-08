#!/bin/bash
set -euo pipefail

echo "🔁 Devcontainer: préparation d'OGSpy"

# Default envs
DB_HOST=${DB_HOST:-db}
DB_USER=${DB_USER:-ogspy}
DB_PASSWORD=${DB_PASSWORD:-password}
DB_NAME=${DB_NAME:-ogspy}

echo "⏳ Attente de MariaDB..."
for attempt in $(seq 1 30); do
  if php -r '$m = @new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME")); exit($m->connect_errno ? 1 : 0);' >/dev/null 2>&1; then
    echo "✅ MariaDB est prête."
    break
  fi

  if [ "$attempt" -eq 30 ]; then
    echo "❌ MariaDB reste inaccessible après 60 secondes."
    exit 1
  fi

  sleep 2
done

echo "🚀 Vérification de l'installation OGSpy..."
su -s /bin/bash www-data -c "/usr/local/bin/setup-ogspy.sh"

echo "✅ OGSpy est prêt. Démarrage d'Apache."

# Exec the standard apache foreground command explicitly to keep the container running
exec docker-php-entrypoint apache2-foreground
