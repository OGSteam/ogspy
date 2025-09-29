# OGSpy - Environnement Docker de Développement

Ce dossier contient tout le nécessaire pour lancer OGSpy en environnement de développement via Docker.

## Prérequis

- Docker
- Docker Compose

## Installation

1. Cloner le dépôt OGSpy :
   ```bash
   git clone <url-du-repo>
   cd ogspy/.docker-dev
   ```

2. Lancer le déploiement :
   ```bash
   ./deploy.sh
   ```

Ce script va :
- Nettoyer les anciens containers, volumes et réseaux Docker
- Corriger les permissions des dossiers nécessaires
- Construire et démarrer les containers
- Attendre la disponibilité de la base de données
- Installer OGSpy en CLI

## Accès

- OGSpy : http://localhost:8080
- Base de données : MySQL (db:3306, user: ogspy, password: password)

## Structure du dossier

- `docker-compose.yml` : Définition des services Docker
- `deploy.sh` : Script d'installation et de déploiement automatisé

## Dépannage

- Pour relancer l'environnement, exécuter à nouveau `./deploy.sh`.
- Les logs sont disponibles dans le dossier `../logs`.

## Auteur
## Compatibilité Windows

Le script `deploy.sh` n'est pas compatible nativement avec Windows. Pour utiliser l'environnement Docker sous Windows, il est recommandé d'utiliser WSL (Windows Subsystem for Linux) ou d'adapter le script en batch ou PowerShell.


DarkNoon

