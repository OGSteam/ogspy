#!/bin/bash

set -e

echo "Avant suppression..."
docker ps -a

# Nettoyage des containers et volumes via Docker Compose...
echo "Nettoyage des containers et volumes via Docker Compose..."
docker compose -f docker-compose.yml down --volumes --remove-orphans

# Purge des images inutilisées, suppression du volume et du réseau
docker image prune -af
docker volume rm docker-dev_db_data || true
docker network rm docker-dev_ogspy-net || true

echo "Après suppression :"
docker ps -a

# Correction des permissions des dossiers nécessaires
sudo chown -R anthony:anthony ../config ../install ../cache ../logs
sudo chmod -R 755 ../config ../install ../cache ../logs

echo "Déploiement des containers..."
docker compose -f docker-compose.yml up -d --build

echo "Containers démarrés."

# Attente de la base de données (via ogspy-web)...
echo "Attente de la base de données (via ogspy-web)..."
until docker exec ogspy-web php -r 'mysqli_connect("db","ogspy","password","ogspy") or exit(1);' > /dev/null 2>&1; do
  echo "MySQL non disponible, attente..."
  sleep 2
done

echo "Base de données prête."

# Nettoyage préventif du cache
rm -f ../cache/cache_*.php || true

echo "Installation d'OGSpy..."
docker exec ogspy-web php ./install/upgrade_cli.php install db ogspy password ogspy admin admin123 admin@example.com ogspy_

echo "Installation OGSpy terminée."

