#!/bin/bash

set -e

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

echo "📁 Correction des permissions des dossiers nécessaires..."
chown -R root:root /var/www/html/config /var/www/html/install /var/www/html/cache /var/www/html/logs /var/www/html/mod 2>/dev/null || true
chmod -R 777 /var/www/html/config /var/www/html/install /var/www/html/cache /var/www/html/logs /var/www/html/mod 2>/dev/null || true

echo "⏳ Attente de la base de données ($DB_HOST)..."
RETRY_COUNT=0
MAX_RETRIES=30
until php -r "mysqli_connect('$DB_HOST','$DB_USER','$DB_PASSWORD','$DB_NAME') or exit(1);" > /dev/null 2>&1; do
  RETRY_COUNT=$((RETRY_COUNT + 1))
  if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
    echo "❌ Erreur: Impossible de se connecter à la base de données après $MAX_RETRIES tentatives"
    exit 1
  fi
  echo "⏳ MySQL non disponible, tentative $RETRY_COUNT/$MAX_RETRIES..."
  sleep 2
done

echo "✅ Base de données prête."

# Vérification que le fichier d'installation existe
if [ ! -f "/var/www/html/install/upgrade_cli.php" ]; then
    echo "❌ Erreur: Fichier d'installation introuvable"
    exit 1
fi

# Nettoyage préventif du cache
echo "🧹 Nettoyage du cache..."
rm -f /var/www/html/cache/cache_*.php || true

echo "🚀 Installation d'OGSpy..."
cd /var/www/html
php install/upgrade_cli.php install "$DB_HOST" "$DB_USER" "$DB_PASSWORD" "$DB_NAME" "$ADMIN_USER" "$ADMIN_PASSWORD" "$ADMIN_EMAIL" "$DB_PREFIX"

echo "✅ Installation OGSpy terminée."
echo "🌐 OGSpy est accessible sur http://localhost:8080"
echo "👤 Connexion: $ADMIN_USER / $ADMIN_PASSWORD"

# Ouverture automatique du navigateur (uniquement en environnement de développement local)
if [ "$OPEN_BROWSER" = "true" ] && [ -n "$DISPLAY" ] && command -v xdg-open >/dev/null 2>&1; then
    echo "🌐 Ouverture du navigateur..."
    sleep 3  # Attendre que le serveur soit complètement prêt
    xdg-open http://localhost:8080 2>/dev/null &
fi
