#!/bin/bash

set -euo pipefail

echo "🔧 Configuration OGSpy pour devcontainer..."

# Variables d'environnement avec valeurs par défaut
DB_HOST=${DB_HOST:-"db"}
DB_USER=${DB_USER:-"ogspy"}
DB_PASSWORD=${DB_PASSWORD:-"password"}
DB_NAME=${DB_NAME:-"ogspy"}
ADMIN_USER=${ADMIN_USER:-"admin"}
ADMIN_PASSWORD=${ADMIN_PASSWORD:-"admin123"}
ADMIN_EMAIL=${ADMIN_EMAIL:-"admin@example.com"}
DB_PREFIX=${DB_PREFIX:-"ogspy_"}

if [[ ! "$DB_PREFIX" =~ ^[A-Za-z0-9_]+$ ]]; then
  echo "❌ Le préfixe de tables doit contenir uniquement des lettres, chiffres et underscores."
  exit 1
fi

echo "📁 Vérification des dossiers accessibles en écriture..."
for d in /var/www/html/config /var/www/html/install /var/www/html/cache /var/www/html/logs /var/www/html/mod; do
  [ -d "$d" ] || continue
  if [ ! -w "$d" ]; then
    echo "❌ Le dossier $d n'est pas accessible en écriture par www-data."
    exit 1
  fi
done

echo "📦 Installation des dépendances Composer..."
cd /var/www/html
## Fix Git 'dubious ownership' when repo is mounted from host (common with Docker Desktop on Windows)
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  git config --global --add safe.directory /var/www/html || true
fi

# Run composer with appropriate user
composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader

echo "✅ Dépendances Composer installées (dev + prod)"

RETRY_COUNT=0
MAX_RETRIES=30
until php -r '$m = @new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME")); exit($m->connect_errno ? 1 : 0);' >/dev/null 2>&1; do
  RETRY_COUNT=$((RETRY_COUNT + 1))
  if [ "$RETRY_COUNT" -ge "$MAX_RETRIES" ]; then
    echo "❌ Erreur: Impossible de se connecter à la base de données après $MAX_RETRIES tentatives"
    exit 1
  fi
  echo "⏳ MySQL non disponible, tentative $RETRY_COUNT/$MAX_RETRIES..."
  sleep 2
done

echo "✅ Base de données prête."

if php -r '$m = new mysqli(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASSWORD"), getenv("DB_NAME")); $table = getenv("DB_PREFIX") . "config"; $result = $m->query("SHOW TABLES LIKE \"$table\""); exit($result && $result->num_rows > 0 ? 0 : 1);' >/dev/null 2>&1; then
  echo "✅ OGSpy est déjà installé, aucune réinstallation n'est nécessaire."
  exit 0
fi

# Vérification que le fichier d'installation existe
if [ ! -f "/var/www/html/install/upgrade_cli.php" ]; then
  echo "❌ Erreur: Fichier d'installation introuvable"
  exit 1
fi

# Nettoyage préventif du cache
echo "🧹 Nettoyage du cache..."
rm -f /var/www/html/cache/cache_*.php || true

echo "🚀 Installation d'OGSpy..."
php install/upgrade_cli.php install "$DB_HOST" "$DB_USER" "$DB_PASSWORD" "$DB_NAME" "$ADMIN_USER" "$ADMIN_PASSWORD" "$ADMIN_EMAIL" "$DB_PREFIX"

echo "✅ Installation OGSpy terminée."
echo "🌐 OGSpy est accessible sur http://localhost:8080"
echo "👤 Connexion: $ADMIN_USER / $ADMIN_PASSWORD"
