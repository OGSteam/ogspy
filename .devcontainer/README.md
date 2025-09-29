# OGSpy Development Container

Ce devcontainer configure automatiquement un environnement de développement complet pour OGSpy avec PHP 8.4, Apache, MariaDB et toutes les extensions nécessaires.

## 🚀 Démarrage rapide

### Avec GitHub Codespaces
1. Cliquez sur le bouton "Code" > "Codespaces" > "Create codespace"
2. Attendez l'installation automatique (2-3 minutes)
3. OGSpy s'ouvrira automatiquement dans votre navigateur

### Avec VS Code local
1. Installez l'extension "Dev Containers" dans VS Code
2. Ouvrez ce projet dans VS Code
3. Cliquez sur "Reopen in Container" quand la notification apparaît
4. Attendez l'installation automatique

### Avec PhpStorm
1. Ouvrez le projet dans PhpStorm
2. PhpStorm détectera automatiquement la configuration devcontainer
3. Acceptez d'ouvrir le projet dans le conteneur

## 🔧 Configuration

### Variables d'environnement
Vous pouvez personnaliser l'installation en définissant ces variables :

```bash
DB_HOST=db                    # Serveur de base de données
DB_USER=ogspy                 # Utilisateur MySQL
DB_PASSWORD=password          # Mot de passe MySQL
DB_NAME=ogspy                 # Nom de la base de données
ADMIN_USER=admin              # Nom d'utilisateur admin
ADMIN_PASSWORD=admin123       # Mot de passe admin
ADMIN_EMAIL=admin@example.com # Email admin
DB_PREFIX=ogspy_              # Préfixe des tables
OPEN_BROWSER=true             # Ouverture auto du navigateur
```

### Services inclus
- **Apache/PHP 8.4** : Serveur web sur le port 8080
- **MariaDB** : Base de données sur le port 3306
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

## 📝 Logs

Les logs sont disponibles dans le dossier `logs/` :
- `OGSpy-*.log` : Logs applicatifs
- `OGSpy-sql-*.log` : Logs SQL

## 🔄 Réinstallation

Pour réinstaller OGSpy :
```bash
rm install/install.lock
bash .devcontainer/setup-ogspy.sh
```

## 🆘 Dépannage

### La base de données ne se connecte pas
```bash
docker compose logs db
```

### Permissions insuffisantes
```bash
chmod -R 777 cache logs config
```

### Réinitialiser l'environnement
```bash
docker compose down -v
docker compose up --build
```
