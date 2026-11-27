# OGSpy Development Container

Ce devcontainer configure automatiquement un environnement de développement complet pour OGSpy avec PHP 8.5, Apache, MariaDB et toutes les extensions nécessaires.

## 🚀 Démarrage rapide

### Avec GitHub Codespaces
1. Cliquez sur le bouton "Code" > "Codespaces" > "Create codespace"
2. Attendez l'installation automatique (2-3 minutes)
3. OGSpy s'ouvrira automatiquement dans votre navigateur

### Avec VS Code local
1. Installez et démarrez Docker Desktop.
2. Installez l'extension "Dev Containers" dans VS Code.
3. Ouvrez ce projet dans VS Code.
4. Cliquez sur "Reopen in Container" quand la notification apparaît.
5. Attendez l'installation automatique.

### Avec Docker Compose sous Windows
Depuis PowerShell, à la racine du dépôt :

```powershell
Copy-Item .devcontainer/.env.example .devcontainer/.env
docker compose --env-file .devcontainer/.env -f .devcontainer/docker-compose.yml up --build -d
docker compose -f .devcontainer/docker-compose.yml logs -f
```

Une fois l'environnement créé, le script développeur raccourcit les commandes
courantes :

```powershell
.\dev.cmd help               # Afficher l'aide Windows
.\dev.cmd shell              # Ouvrir directement le shell PHP
.\tools\dev.ps1 up          # Démarrer
.\tools\dev.ps1 build       # Reconstruire puis démarrer
.\tools\dev.ps1 logs        # Suivre les logs web
.\tools\dev.ps1 test        # Lancer PHPUnit
.\tools\dev.ps1 coverage    # Générer coverage/index.html
.\tools\dev.ps1 shell       # Ouvrir un shell dans le conteneur PHP
.\tools\dev.ps1 db-shell    # Ouvrir le client MariaDB
.\tools\dev.ps1 doctor      # Vérifier l'environnement
.\tools\dev.ps1 open        # Ouvrir OGSpy dans le navigateur
```

Un double-clic sur `dev.cmd` ouvre également une fenêtre avec la liste des
commandes disponibles et la conserve ouverte jusqu'à confirmation.

La commande `.\tools\dev.ps1 db-reset` supprime la base locale et demande une
confirmation explicite avant de recréer OGSpy.

### Avec PhpStorm
1. Ouvrez le projet dans PhpStorm
2. PhpStorm détectera automatiquement la configuration devcontainer
3. Acceptez d'ouvrir le projet dans le conteneur

## 🔧 Configuration

### Variables d'environnement
Vous pouvez personnaliser l'installation en définissant ces variables :

```bash
DB_HOST=db                    # Serveur de base de données
DB_ROOT_PASSWORD=rootpassword # Mot de passe root MariaDB (local uniquement)
DB_USER=ogspy                 # Utilisateur MySQL
DB_PASSWORD=password          # Mot de passe MySQL
DB_NAME=ogspy                 # Nom de la base de données
DB_PORT=3306                  # Port MariaDB exposé sur le PC
ADMIN_USER=admin              # Nom d'utilisateur admin
ADMIN_PASSWORD=admin123       # Mot de passe admin
ADMIN_EMAIL=admin@example.com # Email admin
DB_PREFIX=ogspy_              # Préfixe des tables
OGSPY_HTTP_PORT=8080          # Port HTTP exposé sur le PC
USER_UID=1000                 # UID du compte hôte sous Linux (`id -u`)
USER_GID=1000                 # GID du compte hôte sous Linux (`id -g`)
```

Sous Linux, renseignez `USER_UID` et `USER_GID` avec les résultats de
`id -u` et `id -g`. Si le checkout reste non accessible, l'entrypoint ajuste
uniquement les dossiers d'écriture d'OGSpy. Sous Docker Desktop, les valeurs par
défaut conviennent généralement.

### Services inclus
- **Apache/PHP 8.5** : serveur web sur le port 8080
- **MariaDB** : base de données accessible depuis le PC sur le port 3306
- **Xdebug** : Debugger PHP configuré
- **Extensions VS Code** : Intelephense, Copilot, etc.

## 🌐 Accès

Une fois l'installation terminée :
- **Interface web** : http://localhost:8080
- **Connexion** : admin / admin123
- **Base de données** : localhost:3306

## 🛠️ Développement

Le conteneur est configuré pour :
- Synchronisation en temps réel du code
- Debug PHP avec Xdebug
- Extensions VS Code optimisées pour PHP
- GitHub Copilot activé
- Oh My Zsh avec thème personnalisé

Les tâches `OGSpy: tests complets`, `OGSpy: couverture HTML` et
`OGSpy: vérifier le fichier PHP courant` sont également disponibles dans la
palette **Tasks: Run Task** de VS Code. Pour une requête web, lancez
`OGSpy: écouter Xdebug (Web)` puis activez un déclencheur Xdebug dans le
navigateur. Le mapping entre `/var/www/html` et le dossier de travail est déjà
configuré.

## 📝 Logs

Les logs sont disponibles dans le dossier `logs/` :
- `OGSpy-*.log` : Logs applicatifs
- `OGSpy-sql-*.log` : Logs SQL

## 🔄 Réinstallation

Pour réinstaller OGSpy :
```powershell
docker compose -f .devcontainer/docker-compose.yml down -v
Remove-Item install/install.lock -ErrorAction SilentlyContinue
docker compose -f .devcontainer/docker-compose.yml up --build -d
```

## 🆘 Dépannage

### La base de données ne se connecte pas
```powershell
docker compose logs db
```

### Réinitialiser l'environnement
Cette commande supprime toutes les données de la base locale :

```powershell
docker compose down -v
docker compose up --build
```
