# Documentation du Système d'Installation OGSpy 4.0

## Vue d'ensemble

Le système d'installation d'OGSpy 4.0 introduit un mécanisme d'auto-upgrade moderne qui permet des mises à jour transparentes et automatiques de la base de données sans intervention manuelle. Le système utilise le logger Monolog intégré au projet et est optimisé pour les hébergements mutualisés.

## Architecture

### Composants principaux

1. **AutoUpgradeManager.php** - Gestionnaire d'auto-upgrade silencieux avec logging Monolog
2. **MigrationManager.php** - Gestionnaire de migrations avec versioning horodaté et transactions
3. **ConfigGenerator.php** - Générateur automatisé du fichier `id.php` avec MySQLi
4. **upgrade_cli.php** - Outil en ligne de commande pour diagnostic et maintenance

### Structure des dossiers

```
install/
├── AutoUpgradeManager.php      # Gestionnaire auto-upgrade
├── ConfigGenerator.php         # Générateur de configuration
├── MigrationManager.php        # Gestionnaire de migrations
├── upgrade_cli.php             # Outil CLI
├── version.php                 # Versions logiciel/DB
├── index.php                   # Interface d'installation
├── config/
│   └── database_config.php     # Configuration centralisée
└── migrations/
    └── [timestamp]_[description].php  # Classes de migration
```

## Versioning

### Système de versions de base de données

- **Version base de données** : Format horodaté (ex: `20250713001`)

Le versioning de la base de données utilise le format `YYYYMMDDNNN` où :
- `YYYY` = Année
- `MM` = Mois  
- `DD` = Jour
- `NNN` = Numéro incrémental dans la journée (001, 002, etc.)

### Avantages

- Évite les conflits lors de développements parallèles
- Ordre chronologique garanti
- Traçabilité temporelle des modifications
- Système autonome sans dépendance à l'ancien versioning

## Migrations

### Format des fichiers de migration

Les migrations suivent la convention de nommage : `[timestamp]_[description].php`

Exemple : `20250713001_upgrade_to_4_0_0.php`

### Structure d'une migration (Classes)

```php
<?php

class Migration_20250713001_UpgradeTo400
{
    public function getVersion(): string
    {
        return '20250713001';
    }

    public function getDescription(): string
    {
        return 'Mise à jour vers OGSpy 4.0.0';
    }

    public function up($db): void
    {
        global $table_prefix;
        
        // Modifications de base de données
        $db->sql_query("ALTER TABLE {$table_prefix}user ADD new_column VARCHAR(255)");
        $db->sql_query("CREATE TABLE {$table_prefix}new_table (id INT PRIMARY KEY)");
    }

    public function down($db): void
    {
        global $table_prefix;
        
        // Rollback des modifications
        $db->sql_query("ALTER TABLE {$table_prefix}user DROP COLUMN new_column");
        $db->sql_query("DROP TABLE {$table_prefix}new_table");
    }
}
```

### Convention de nommage automatique

- **Fichier :** `20250713001_upgrade_to_4_0_0.php`
- **Classe :** `Migration_20250713001_UpgradeTo400`

Le système convertit automatiquement le nom de fichier en nom de classe CamelCase.

### Bonnes pratiques

1. **Une migration = une fonctionnalité**
2. **Toujours tester en local d'abord** 
3. **Code SQL direct dans up() et down()** (pas de sous-méthodes)
4. **Utiliser les transactions automatiques pour la cohérence**
5. **Éviter les modifications de limites PHP** (incompatibles hébergement mutualisé)
6. **Ajouter des commentaires** pour clarifier les sections SQL

### Contrat de Rollback (Annulation de migration)

Le système de rollback garantit que l'historique des migrations reste strictement cohérent avec l'état réel de la base de données :

1. **Déclaration de support (`canRollback(): bool`)** :
   - Une migration indique si elle prend en charge l'inversion en implémentant `canRollback(): bool`.
   - Si `canRollback()` retourne `false`, ou si la méthode `down()` est absente, le rollback échoue explicitement avec une exception.
   - L'enregistrement d'historique dans `ogspy_migrations` est **conservé** en cas d'échec ou d'opération non supportée.
   - Les migrations irréversibles (telle que `00000000000_Initial`) déclarent `canRollback(): bool { return false; }` et lèvent une exception dans `down()`.

2. **Exécution de l'inversion (`down($db)`)** :
   - La méthode `down($db)` implémente l'inversion exacte des modifications apportées par `up()`.
   - Elle peut retourner une chaîne de requêtes SQL (qui sera exécutée par `MigrationManager` avec substitution du préfixe de table) ou exécuter directement les requêtes via l'objet `$db`.
   - Si `down()` lève une exception ou retourne `false`, la transaction est annulée et l'enregistrement d'historique est conservé.

3. **Suppression de l'historique conditionnelle au succès** :
   - L'enregistrement correspondant dans la table `ogspy_migrations` n'est supprimé qu'après l'exécution réussie de l'inversion.
   - Si le rollback échoue ou n'est pas supporté, la migration reste marquée comme appliquée dans la base de données.

## Auto-Upgrade

### Fonctionnement

Le système d'auto-upgrade s'intègre dans le flux principal de l'application :

1. Vérification des versions via le MigrationManager
2. Détection des migrations en attente
3. Exécution silencieuse avec logging Monolog
4. Nettoyage automatique du cache
5. Continuation normale de l'application

### Conditions d'activation

- Permissions d'écriture sur `/cache`
- Site non en mode maintenance  
- Logger Monolog disponible (obligatoire)
- Aucun autre upgrade en cours (système de verrous)

### Sécurités

- **Verrous temporels** : Évite les exécutions simultanées (timeout 5 minutes)
- **Transactions automatiques** : Rollback automatique en cas d'erreur
- **Logging complet** : Intégration avec le système Monolog du projet
- **Compatible hébergement mutualisé** : Pas de modification des limites PHP

### Limitations volontaires

- Pas de `set_time_limit()` ou `ini_set()` (incompatibles mutualisé)
- Logger obligatoire avec erreur explicite si absent
- Limite à 10 migrations simultanées avec avertissement

## Configuration

### Fichier de configuration principal

`config/database_config.php` contient :

```php
return [
    'migrations_table' => 'ogspy_migrations',
    'migrations_path' => __DIR__ . '/../migrations/',
    'db_version_format' => 'YmdHis',
    'max_execution_time' => 300,
    'transaction_enabled' => true,
    'log_migrations' => true,
    'log_failed_migrations' => true,
];
```

### Générateur de configuration

Le `ConfigGenerator` crée uniquement les paramètres de connexion dans `id.php` :

```php
// Configuration de la base de données uniquement
define("DB_HOST", "localhost");
define("DB_USER", "username");
define("DB_PASSWORD", "password"); 
define("DB_NAME", "database");
$table_prefix = "ogspy_";
define("DB_CHARSET", "utf8");
define("DB_COLLATE", "utf8_general_ci");
```

**Note importante** : Les constantes de tables (`TABLE_*`) restent dans `includes/config.php` pour une meilleure séparation des responsabilités.

### Table de suivi des migrations

La table `ogspy_migrations` stocke l'historique :

| Colonne | Type | Description |
|---------|------|-------------|
| id | INT AUTO_INCREMENT | Identifiant unique |
| migration | VARCHAR(255) | Version de la migration |
| executed_at | TIMESTAMP | Date d'exécution |
| execution_time | DECIMAL(10,3) | Temps d'exécution en secondes |
| success | BOOLEAN | Statut de réussite |
| error_message | TEXT | Message d'erreur éventuel |

## Sécurité de l'installation

### Système de verrouillage

Après installation, l'interface web peut être verrouillée pour la sécurité :

- **Verrouillage automatique** : Bouton dans l'interface d'installation
- **Fichier de verrouillage** : `install/install.lock`
- **Accès bloqué** : HTTP 403 pour l'interface web
- **Fonctionnalités préservées** : Auto-upgrade et CLI continuent de fonctionner

### Déverrouillage

```bash
# Via CLI (recommandé)
php upgrade_cli.php unlock-install

# Ou manuellement
rm install/install.lock
```

### Recommandation

⚠️ **Ne pas supprimer le dossier install/** car il contient :
- Classes nécessaires à l'auto-upgrade
- Outils CLI de diagnostic
- Migrations futures

## Outils CLI

### Utilisation du script CLI

```bash
# Vérifier le statut
php install/upgrade_cli.php check

# Lancer une mise à jour
php install/upgrade_cli.php upgrade

# Annuler une migration spécifique
php install/upgrade_cli.php rollback 20251227001

# Forcer en cas de problème
php install/upgrade_cli.php force

# Voir le statut complet
php install/upgrade_cli.php status

# Consulter les logs
php install/upgrade_cli.php logs

# Débloquer les verrous
php install/upgrade_cli.php unlock

# Débloquer l'installation
php install/upgrade_cli.php unlock-install
```

## 🛠️ Commandes CLI disponibles

### Installation automatisée
```bash
# Installation basique
php upgrade_cli.php install localhost root mypass ogspy admin admin123

# Installation complète avec options
php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --lock
```

**Paramètres :**
- `db_host` : Serveur de base de données
- `db_user` : Utilisateur DB
- `db_password` : Mot de passe DB
- `db_name` : Nom de la base de données
- `admin_user` : Nom d'utilisateur administrateur
- `admin_password` : Mot de passe administrateur
- `admin_email` : Email administrateur (optionnel)
- `table_prefix` : Préfixe des tables (défaut: `ogspy_`)
- `--lock` : Verrouille l'installation après succès

### Gestion des mises à jour
```bash
# Vérifier le statut des migrations
php upgrade_cli.php check

# Lancer une mise à jour automatique
php upgrade_cli.php upgrade

# Forcer une mise à jour (supprime les verrous)
php upgrade_cli.php force

# Afficher le statut complet du système
php upgrade_cli.php status
```

### Gestion des verrous
```bash
# Supprimer les verrous de mise à jour
php upgrade_cli.php unlock

# Déverrouiller l'interface d'installation web
php upgrade_cli.php unlock-install
```

### Tests et diagnostics
```bash
# Lancer tous les tests (installation + mise à niveau)
php upgrade_cli.php test

# Test d'installation uniquement
php upgrade_cli.php test-install

# Test de mise à niveau uniquement
php upgrade_cli.php test-upgrade

# Test de performance des migrations
php upgrade_cli.php test-performance
```

### Logs et maintenance
```bash
# Consulter les logs de mise à jour
php upgrade_cli.php logs

# Désinstaller complètement OGSpy (ATTENTION: supprime tout)
php upgrade_cli.php uninstall

# Afficher l'aide complète
php upgrade_cli.php help
```

### Exemples d'utilisation

#### Installation rapide en local
```bash
php upgrade_cli.php install localhost root password ogspy admin admin123 --lock
```

#### Installation complète avec configuration
```bash
php upgrade_cli.php install db.example.com ogspy_user mypassword ogspy_prod admin secretpass admin@domain.com ogspy_ --lock
```

#### Diagnostic et maintenance
```bash
# Vérifier l'état du système
php upgrade_cli.php status

# En cas de problème avec les verrous
php upgrade_cli.php unlock
php upgrade_cli.php force

# Consulter les derniers logs
php upgrade_cli.php logs
```

#### Tests de validation
```bash
# Test complet avant mise en production
php upgrade_cli.php test

# Test uniquement les performances
php upgrade_cli.php test-performance
```

## 📊 Détails des commandes

### `install` - Installation automatisée
- Vérifie les prérequis PHP et extensions
- Teste la connexion à la base de données
- Génère le fichier de configuration `id.php`
- Exécute toutes les migrations nécessaires
- Crée le compte administrateur
- Option de verrouillage de sécurité

### `check` - Vérification du statut
- Affiche la version actuelle de la base de données
- Liste les migrations en attente
- Indique si l'auto-upgrade est possible

### `upgrade` - Mise à jour standard
- Lance l'auto-upgrade en respectant les verrous
- Exécute les migrations en attente
- Affiche le résultat détaillé

### `force` - Mise à jour forcée
- Supprime tous les verrous existants
- Force l'exécution des migrations
- À utiliser uniquement en cas de blocage

### `status` - Statut complet du système
- Version du logiciel et de la base de données
- État des verrous
- Configuration de l'auto-upgrade

### `unlock` - Gestion des verrous
- Supprime les verrous de mise à jour bloquants
- Permet de débloquer un processus interrompu

### `unlock-install` - Déverrouillage installation
- Supprime le fichier `install.lock`
- Rend l'interface web d'installation accessible

### `test-*` - Suite de tests
- **`test`** : Tests complets (installation + upgrade)
- **`test-install`** : Test d'installation fraîche uniquement
- **`test-upgrade`** : Test de mise à niveau uniquement  
- **`test-performance`** : Mesure les performances des migrations

### `logs` - Consultation des logs
- Affiche les logs du jour courant
- Consulte les derniers logs disponibles
- Utile pour diagnostiquer les problèmes

### `uninstall` - Désinstallation complète
- ⚠️ **ATTENTION** : Opération destructive
- Supprime toutes les tables de la base de données
- Efface les fichiers générés (cache, logs, config)
- Demande confirmation avant exécution

## 🔧 Actions disponibles (résumé)

| Action | Description |
|--------|-------------|
| `install` | Installation automatisée complète |
| `check` | Vérifie les migrations en attente |
| `upgrade` | Lance la mise à jour automatique |
| `force` | Force la mise à jour (supprime verrous) |
| `status` | Affiche le statut complet du système |
| `logs` | Consulte les logs de mise à jour |
| `unlock` | Supprime les verrous de mise à jour |
| `unlock-install` | Déverrouille l'interface d'installation |
| `uninstall` | Désinstalle complètement le système |
| `test` | Lance tous les tests |
| `test-install` | Test d'installation uniquement |
| `test-upgrade` | Test de mise à niveau uniquement |
| `test-performance` | Test de performance des migrations |
| `help` | Affiche l'aide complète |

## Logs et Diagnostic

### Intégration Monolog

Le système utilise le logger Monolog configuré dans `common.php` :

- **Logger principal** : `$log` - Messages généraux dans `/logs/OGSpy.log`
- **Logger SQL** : `$logSQL` - Requêtes dans `/logs/OGSpy-sql.log`
- **Logger SQL lent** : `$logSlowSQL` - Requêtes lentes dans `/logs/OGSpy-sql-slow.log`

### Format des logs migrations

```
[2025-07-19 14:30:15] OGSpy.INFO: === DÉBUT AUTO-UPGRADE ===
[2025-07-19 14:30:15] OGSpy.INFO: Migrations à exécuter: 2
[2025-07-19 14:30:15] OGSpy.INFO: Exécution de la migration: 20250713001 - upgrade to 4 0 0
[2025-07-19 14:30:16] OGSpy.INFO: Migration 20250713001 exécutée avec succès en 0.543s
[2025-07-19 14:30:16] OGSpy.INFO: SUCCÈS: 2 migration(s) réussie(s)
[2025-07-19 14:30:16] OGSpy.INFO: Nouvelle version DB: 20250713002
[2025-07-19 14:30:16] OGSpy.INFO: Temps d'exécution: 1s
[2025-07-19 14:30:16] OGSpy.INFO: Cache nettoyé: 5 fichier(s) supprimé(s)
[2025-07-19 14:30:16] OGSpy.INFO: === FIN AUTO-UPGRADE ===
```

## Tests de connexion

### ConfigGenerator avec MySQLi

Le générateur de configuration utilise MySQLi au lieu de PDO pour une meilleure compatibilité :

```php
// Test de connexion automatique
$mysqli = new mysqli($host, $user, $password, $database);
if ($mysqli->connect_error) {
    throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
}
```

### Avantages MySQLi

- Activé par défaut sur la plupart des hébergements
- Pas besoin d'extension PDO supplémentaire
- Cohérence avec le reste du projet OGSpy
- Compatible avec tous les hébergements mutualisés

## Création d'une nouvelle migration

### Étapes

1. **Créer le fichier** : `migrations/YYYYMMDDNNN_description.php`
2. **Définir la classe** : `Migration_YYYYMMDDNNN_Description`
3. **Implémenter up()** : Code de migration
4. **Implémenter down()** : Code de rollback (optionnel)

### Exemple pratique

```bash
# Fichier : migrations/20250720001_add_user_preferences.php
```

```php
<?php

class Migration_20250720001_AddUserPreferences
{
    public function getVersion(): string
    {
        return '20250720001';
    }

    public function getDescription(): string
    {
        return 'Ajout table préférences utilisateur';
    }

    public function up($db): void
    {
        global $table_prefix;
        
        $db->sql_query("CREATE TABLE {$table_prefix}user_preferences (
            user_id INT NOT NULL,
            pref_key VARCHAR(50) NOT NULL,
            pref_value TEXT,
            PRIMARY KEY (user_id, pref_key),
            FOREIGN KEY (user_id) REFERENCES {$table_prefix}user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down($db): void
    {
        global $table_prefix;
        $db->sql_query("DROP TABLE {$table_prefix}user_preferences");
    }
}
```

## Troubleshooting

### Erreurs communes

**Logger manquant :**
```
InvalidArgumentException: Logger requis pour AutoUpgradeManager
```
→ Solution : Vérifier que `common.php` est inclus et `$log` disponible

**Classe de migration introuvable :**
```
Classe de migration 'Migration_20250713001_UpgradeTo400' introuvable
```
→ Solution : Vérifier le nom de classe et la convention de nommage

**Permissions insuffisantes :**
```
Impossible d'écrire le fichier de configuration
```
→ Solution : Vérifier les permissions sur `/cache` et `/config`

**Verrous bloqués :**
```
Mise à jour en cours...
```
→ Solution : `php upgrade_cli.php unlock` ou attendre 5 minutes

### Commandes de diagnostic

```bash
# Vérification complète
php upgrade_cli.php status

# Forçage en cas de blocage
php upgrade_cli.php force

# Consultation des logs détaillées
php upgrade_cli.php logs
```

## Compatibilité

### Hébergements supportés

✅ **Hébergements mutualisés** : Optimisé pour les restrictions courantes  
✅ **Serveurs dédiés** : Fonctionnalités complètes  
✅ **VPS** : Performance optimale  

### Prérequis

- PHP 7.4+ avec MySQLi
- MySQL 5.7+ ou MariaDB 10.2+
- Permissions d'écriture sur `/cache`, `/logs`, `/config`
- Monolog disponible (inclus dans `common.php`)

### Limitations connues

- Pas de modification des limites PHP (`set_time_limit`, `ini_set`)
- Logger obligatoire (erreur explicite si absent)
- Maximum 10 migrations simultanées recommandé
- Format de migration basé sur des classes PHP

# Installation OGSpy 4.0

Ce répertoire contient le système d'installation et de mise à jour d'OGSpy 4.0, incluant une interface web moderne et des outils CLI pour l'automatisation.

## 🚀 Installation rapide

### Interface Web
Accédez à `http://votre-serveur/ogspy/install/` pour utiliser l'interface d'installation graphique avec le thème spatial.

### Installation CLI (recommandée pour l'automatisation)
```bash
php upgrade_cli.php install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--lock]
```

## 📋 Prérequis

- PHP 7.4 ou supérieur
- Extensions PHP : mysqli, json, mbstring
- Base de données MySQL/MariaDB
- Permissions d'écriture sur les dossiers `cache/`, `logs/`, `config/`

## 🛠️ Commandes CLI disponibles

### Installation automatisée
```bash
# Installation basique
php upgrade_cli.php install localhost root mypass ogspy admin admin123

# Installation complète avec options
php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --lock
```

**Paramètres :**
- `db_host` : Serveur de base de données
- `db_user` : Utilisateur DB
- `db_password` : Mot de passe DB
- `db_name` : Nom de la base de données
- `admin_user` : Nom d'utilisateur administrateur
- `admin_password` : Mot de passe administrateur
- `admin_email` : Email administrateur (optionnel)
- `table_prefix` : Préfixe des tables (défaut: `ogspy_`)
- `--lock` : Verrouille l'installation après succès

### Gestion des mises à jour
```bash
# Vérifier le statut des migrations
php upgrade_cli.php check

# Lancer une mise à jour automatique
php upgrade_cli.php upgrade

# Forcer une mise à jour (supprime les verrous)
php upgrade_cli.php force

# Afficher le statut complet du système
php upgrade_cli.php status
```

### Gestion des verrous
```bash
# Supprimer les verrous de mise à jour
php upgrade_cli.php unlock

# Déverrouiller l'interface d'installation web
php upgrade_cli.php unlock-install
```

### Tests et diagnostics
```bash
# Lancer tous les tests (installation + mise à niveau)
php upgrade_cli.php test

# Test d'installation uniquement
php upgrade_cli.php test-install

# Test de mise à niveau uniquement
php upgrade_cli.php test-upgrade

# Test de performance des migrations
php upgrade_cli.php test-performance
```

### Logs et maintenance
```bash
# Consulter les logs de mise à jour
php upgrade_cli.php logs

# Désinstaller complètement OGSpy (ATTENTION: supprime tout)
php upgrade_cli.php uninstall

# Afficher l'aide complète
php upgrade_cli.php help
```

### Exemples d'utilisation

#### Installation rapide en local
```bash
php upgrade_cli.php install localhost root password ogspy admin admin123 --lock
```

#### Installation complète avec configuration
```bash
php upgrade_cli.php install db.example.com ogspy_user mypassword ogspy_prod admin secretpass admin@domain.com ogspy_ --lock
```

#### Diagnostic et maintenance
```bash
# Vérifier l'état du système
php upgrade_cli.php status

# En cas de problème avec les verrous
php upgrade_cli.php unlock
php upgrade_cli.php force

# Consulter les derniers logs
php upgrade_cli.php logs
```

#### Tests de validation
```bash
# Test complet avant mise en production
php upgrade_cli.php test

# Test uniquement les performances
php upgrade_cli.php test-performance
```

## 📊 Détails des commandes

### `install` - Installation automatisée
- Vérifie les prérequis PHP et extensions
- Teste la connexion à la base de données
- Génère le fichier de configuration `id.php`
- Exécute toutes les migrations nécessaires
- Crée le compte administrateur
- Option de verrouillage de sécurité

### `check` - Vérification du statut
- Affiche la version actuelle de la base de données
- Liste les migrations en attente
- Indique si l'auto-upgrade est possible

### `upgrade` - Mise à jour standard
- Lance l'auto-upgrade en respectant les verrous
- Exécute les migrations en attente
- Affiche le résultat détaillé

### `force` - Mise à jour forcée
- Supprime tous les verrous existants
- Force l'exécution des migrations
- À utiliser uniquement en cas de blocage

### `status` - Statut complet du système
- Version du logiciel et de la base de données
- État des verrous
- Configuration de l'auto-upgrade

### `unlock` - Gestion des verrous
- Supprime les verrous de mise à jour bloquants
- Permet de débloquer un processus interrompu

### `unlock-install` - Déverrouillage installation
- Supprime le fichier `install.lock`
- Rend l'interface web d'installation accessible

### `test-*` - Suite de tests
- **`test`** : Tests complets (installation + upgrade)
- **`test-install`** : Test d'installation fraîche uniquement
- **`test-upgrade`** : Test de mise à niveau uniquement  
- **`test-performance`** : Mesure les performances des migrations

### `logs` - Consultation des logs
- Affiche les logs du jour courant
- Consulte les derniers logs disponibles
- Utile pour diagnostiquer les problèmes

### `uninstall` - Désinstallation complète
- ⚠️ **ATTENTION** : Opération destructive
- Supprime toutes les tables de la base de données
- Efface les fichiers générés (cache, logs, config)
- Demande confirmation avant exécution

## 🔧 Actions disponibles (résumé)

| Action | Description |
|--------|-------------|
| `install` | Installation automatisée complète |
| `check` | Vérifie les migrations en attente |
| `upgrade` | Lance la mise à jour automatique |
| `force` | Force la mise à jour (supprime verrous) |
| `status` | Affiche le statut complet du système |
| `logs` | Consulte les logs de mise à jour |
| `unlock` | Supprime les verrous de mise à jour |
| `unlock-install` | Déverrouille l'interface d'installation |
| `uninstall` | Désinstalle complètement le système |
| `test` | Lance tous les tests |
| `test-install` | Test d'installation uniquement |
| `test-upgrade` | Test de mise à niveau uniquement |
| `test-performance` | Test de performance des migrations |
| `help` | Affiche l'aide complète |

## Logs et Diagnostic

### Intégration Monolog

Le système utilise le logger Monolog configuré dans `common.php` :

- **Logger principal** : `$log` - Messages généraux dans `/logs/OGSpy.log`
- **Logger SQL** : `$logSQL` - Requêtes dans `/logs/OGSpy-sql.log`
- **Logger SQL lent** : `$logSlowSQL` - Requêtes lentes dans `/logs/OGSpy-sql-slow.log`

### Format des logs migrations

```
[2025-07-19 14:30:15] OGSpy.INFO: === DÉBUT AUTO-UPGRADE ===
[2025-07-19 14:30:15] OGSpy.INFO: Migrations à exécuter: 2
[2025-07-19 14:30:15] OGSpy.INFO: Exécution de la migration: 20250713001 - upgrade to 4 0 0
[2025-07-19 14:30:16] OGSpy.INFO: Migration 20250713001 exécutée avec succès en 0.543s
[2025-07-19 14:30:16] OGSpy.INFO: SUCCÈS: 2 migration(s) réussie(s)
[2025-07-19 14:30:16] OGSpy.INFO: Nouvelle version DB: 20250713002
[2025-07-19 14:30:16] OGSpy.INFO: Temps d'exécution: 1s
[2025-07-19 14:30:16] OGSpy.INFO: Cache nettoyé: 5 fichier(s) supprimé(s)
[2025-07-19 14:30:16] OGSpy.INFO: === FIN AUTO-UPGRADE ===
```

## Tests de connexion

### ConfigGenerator avec MySQLi

Le générateur de configuration utilise MySQLi au lieu de PDO pour une meilleure compatibilité :

```php
// Test de connexion automatique
$mysqli = new mysqli($host, $user, $password, $database);
if ($mysqli->connect_error) {
    throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
}
```

### Avantages MySQLi

- Activé par défaut sur la plupart des hébergements
- Pas besoin d'extension PDO supplémentaire
- Cohérence avec le reste du projet OGSpy
- Compatible avec tous les hébergements mutualisés

## Création d'une nouvelle migration

### Étapes

1. **Créer le fichier** : `migrations/YYYYMMDDNNN_description.php`
2. **Définir la classe** : `Migration_YYYYMMDDNNN_Description`
3. **Implémenter up()** : Code de migration
4. **Implémenter down()** : Code de rollback (optionnel)

### Exemple pratique

```bash
# Fichier : migrations/20250720001_add_user_preferences.php
```

```php
<?php

class Migration_20250720001_AddUserPreferences
{
    public function getVersion(): string
    {
        return '20250720001';
    }

    public function getDescription(): string
    {
        return 'Ajout table préférences utilisateur';
    }

    public function up($db): void
    {
        global $table_prefix;
        
        $db->sql_query("CREATE TABLE {$table_prefix}user_preferences (
            user_id INT NOT NULL,
            pref_key VARCHAR(50) NOT NULL,
            pref_value TEXT,
            PRIMARY KEY (user_id, pref_key),
            FOREIGN KEY (user_id) REFERENCES {$table_prefix}user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down($db): void
    {
        global $table_prefix;
        $db->sql_query("DROP TABLE {$table_prefix}user_preferences");
    }
}
```

## Troubleshooting

### Erreurs communes

**Logger manquant :**
```
InvalidArgumentException: Logger requis pour AutoUpgradeManager
```
→ Solution : Vérifier que `common.php` est inclus et `$log` disponible

**Classe de migration introuvable :**
```
Classe de migration 'Migration_20250713001_UpgradeTo400' introuvable
```
→ Solution : Vérifier le nom de classe et la convention de nommage

**Permissions insuffisantes :**
```
Impossible d'écrire le fichier de configuration
```
→ Solution : Vérifier les permissions sur `/cache` et `/config`

**Verrous bloqués :**
```
Mise à jour en cours...
```
→ Solution : `php upgrade_cli.php unlock` ou attendre 5 minutes

### Commandes de diagnostic

```bash
# Vérification complète
php upgrade_cli.php status

# Forçage en cas de blocage
php upgrade_cli.php force

# Consultation des logs détaillées
php upgrade_cli.php logs
```

## Compatibilité

### Hébergements supportés

✅ **Hébergements mutualisés** : Optimisé pour les restrictions courantes  
✅ **Serveurs dédiés** : Fonctionnalités complètes  
✅ **VPS** : Performance optimale  

### Prérequis

- PHP 7.4+ avec MySQLi
- MySQL 5.7+ ou MariaDB 10.2+
- Permissions d'écriture sur `/cache`, `/logs`, `/config`
- Monolog disponible (inclus dans `common.php`)

### Limitations connues

- Pas de modification des limites PHP (`set_time_limit`, `ini_set`)
- Logger obligatoire (erreur explicite si absent)
- Maximum 10 migrations simultanées recommandé
- Format de migration basé sur des classes PHP

# Installation OGSpy 4.0

Ce répertoire contient le système d'installation et de mise à jour d'OGSpy 4.0, incluant une interface web moderne et des outils CLI pour l'automatisation.

## 🚀 Installation rapide

### Interface Web
Accédez à `http://votre-serveur/ogspy/install/` pour utiliser l'interface d'installation graphique avec le thème spatial.

### Installation CLI (recommandée pour l'automatisation)
```bash
php upgrade_cli.php install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--lock]
```

## 📋 Prérequis

- PHP 7.4 ou supérieur
- Extensions PHP : mysqli, json, mbstring
- Base de données MySQL/MariaDB
- Permissions d'écriture sur les dossiers `cache/`, `logs/`, `config/`

## 🛠️ Commandes CLI disponibles

### Installation automatisée
```bash
# Installation basique
php upgrade_cli.php install localhost root mypass ogspy admin admin123

# Installation complète avec options
php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --lock
```

**Paramètres :**
- `db_host` : Serveur de base de données
- `db_user` : Utilisateur DB
- `db_password` : Mot de passe DB
- `db_name` : Nom de la base de données
- `admin_user` : Nom d'utilisateur administrateur
- `admin_password` : Mot de passe administrateur
- `admin_email` : Email administrateur (optionnel)
- `table_prefix` : Préfixe des tables (défaut: `ogspy_`)
- `--lock` : Verrouille l'installation après succès

### Gestion des mises à jour
```bash
# Vérifier le statut des migrations
php upgrade_cli.php check

# Lancer une mise à jour automatique
php upgrade_cli.php upgrade

# Forcer une mise à jour (supprime les verrous)
php upgrade_cli.php force

# Afficher le statut complet du système
php upgrade_cli.php status
```

### Gestion des verrous
```bash
# Supprimer les verrous de mise à jour
php upgrade_cli.php unlock

# Déverrouiller l'interface d'installation web
php upgrade_cli.php unlock-install
```

### Tests et diagnostics
```bash
# Lancer tous les tests (installation + mise à niveau)
php upgrade_cli.php test

# Test d'installation uniquement
php upgrade_cli.php test-install

# Test de mise à niveau uniquement
php upgrade_cli.php test-upgrade

# Test de performance des migrations
php upgrade_cli.php test-performance
```

### Logs et maintenance
```bash
# Consulter les logs de mise à jour
php upgrade_cli.php logs

# Désinstaller complètement OGSpy (ATTENTION: supprime tout)
php upgrade_cli.php uninstall

# Afficher l'aide complète
php upgrade_cli.php help
```

### Exemples d'utilisation

#### Installation rapide en local
```bash
php upgrade_cli.php install localhost root password ogspy admin admin123 --lock
```

#### Installation complète avec configuration
```bash
php upgrade_cli.php install db.example.com ogspy_user mypassword ogspy_prod admin secretpass admin@domain.com ogspy_ --lock
```

#### Diagnostic et maintenance
```bash
# Vérifier l'état du système
php upgrade_cli.php status

# En cas de problème avec les verrous
php upgrade_cli.php unlock
php upgrade_cli.php force

# Consulter les derniers logs
php upgrade_cli.php logs
```

#### Tests de validation
```bash
# Test complet avant mise en production
php upgrade_cli.php test

# Test uniquement les performances
php upgrade_cli.php test-performance
```

## 📊 Détails des commandes

### `install` - Installation automatisée
- Vérifie les prérequis PHP et extensions
- Teste la connexion à la base de données
- Génère le fichier de configuration `id.php`
- Exécute toutes les migrations nécessaires
- Crée le compte administrateur
- Option de verrouillage de sécurité

### `check` - Vérification du statut
- Affiche la version actuelle de la base de données
- Liste les migrations en attente
- Indique si l'auto-upgrade est possible

### `upgrade` - Mise à jour standard
- Lance l'auto-upgrade en respectant les verrous
- Exécute les migrations en attente
- Affiche le résultat détaillé

### `force` - Mise à jour forcée
- Supprime tous les verrous existants
- Force l'exécution des migrations
- À utiliser uniquement en cas de blocage

### `status` - Statut complet du système
- Version du logiciel et de la base de données
- État des verrous
- Configuration de l'auto-upgrade

### `unlock` - Gestion des verrous
- Supprime les verrous de mise à jour bloquants
- Permet de débloquer un processus interrompu

### `unlock-install` - Déverrouillage installation
- Supprime le fichier `install.lock`
- Rend l'interface web d'installation accessible

### `test-*` - Suite de tests
- **`test`** : Tests complets (installation + upgrade)
- **`test-install`** : Test d'installation fraîche uniquement
- **`test-upgrade`** : Test de mise à niveau uniquement  
- **`test-performance`** : Mesure les performances des migrations

### `logs` - Consultation des logs
- Affiche les logs du jour courant
- Consulte les derniers logs disponibles
- Utile pour diagnostiquer les problèmes

### `uninstall` - Désinstallation complète
- ⚠️ **ATTENTION** : Opération destructive
- Supprime toutes les tables de la base de données
- Efface les fichiers générés (cache, logs, config)
- Demande confirmation avant exécution

## 🔧 Actions disponibles (résumé)

| Action | Description |
|--------|-------------|
| `install` | Installation automatisée complète |
| `check` | Vérifie les migrations en attente |
| `upgrade` | Lance la mise à jour automatique |
| `force` | Force la mise à jour (supprime verrous) |
| `status` | Affiche le statut complet du système |
| `logs` | Consulte les logs de mise à jour |
| `unlock` | Supprime les verrous de mise à jour |
| `unlock-install` | Déverrouille l'interface d'installation |
| `uninstall` | Désinstalle complètement le système |
| `test` | Lance tous les tests |
| `test-install` | Test d'installation uniquement |
| `test-upgrade` | Test de mise à niveau uniquement |
| `test-performance` | Test de performance des migrations |
| `help` | Affiche l'aide complète |

## Logs et Diagnostic

### Intégration Monolog

Le système utilise le logger Monolog configuré dans `common.php` :

- **Logger principal** : `$log` - Messages généraux dans `/logs/OGSpy.log`
- **Logger SQL** : `$logSQL` - Requêtes dans `/logs/OGSpy-sql.log`
- **Logger SQL lent** : `$logSlowSQL` - Requêtes lentes dans `/logs/OGSpy-sql-slow.log`

### Format des logs migrations

```
[2025-07-19 14:30:15] OGSpy.INFO: === DÉBUT AUTO-UPGRADE ===
[2025-07-19 14:30:15] OGSpy.INFO: Migrations à exécuter: 2
[2025-07-19 14:30:15] OGSpy.INFO: Exécution de la migration: 20250713001 - upgrade to 4 0 0
[2025-07-19 14:30:16] OGSpy.INFO: Migration 20250713001 exécutée avec succès en 0.543s
[2025-07-19 14:30:16] OGSpy.INFO: SUCCÈS: 2 migration(s) réussie(s)
[2025-07-19 14:30:16] OGSpy.INFO: Nouvelle version DB: 20250713002
[2025-07-19 14:30:16] OGSpy.INFO: Temps d'exécution: 1s
[2025-07-19 14:30:16] OGSpy.INFO: Cache nettoyé: 5 fichier(s) supprimé(s)
[2025-07-19 14:30:16] OGSpy.INFO: === FIN AUTO-UPGRADE ===
```

## Tests de connexion

### ConfigGenerator avec MySQLi

Le générateur de configuration utilise MySQLi au lieu de PDO pour une meilleure compatibilité :

```php
// Test de connexion automatique
$mysqli = new mysqli($host, $user, $password, $database);
if ($mysqli->connect_error) {
    throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
}
```

### Avantages MySQLi

- Activé par défaut sur la plupart des hébergements
- Pas besoin d'extension PDO supplémentaire
- Cohérence avec le reste du projet OGSpy
- Compatible avec tous les hébergements mutualisés

## Création d'une nouvelle migration

### Étapes

1. **Créer le fichier** : `migrations/YYYYMMDDNNN_description.php`
2. **Définir la classe** : `Migration_YYYYMMDDNNN_Description`
3. **Implémenter up()** : Code de migration
4. **Implémenter down()** : Code de rollback (optionnel)

### Exemple pratique

```bash
# Fichier : migrations/20250720001_add_user_preferences.php
```

```php
<?php

class Migration_20250720001_AddUserPreferences
{
    public function getVersion(): string
    {
        return '20250720001';
    }

    public function getDescription(): string
    {
        return 'Ajout table préférences utilisateur';
    }

    public function up($db): void
    {
        global $table_prefix;
        
        $db->sql_query("CREATE TABLE {$table_prefix}user_preferences (
            user_id INT NOT NULL,
            pref_key VARCHAR(50) NOT NULL,
            pref_value TEXT,
            PRIMARY KEY (user_id, pref_key),
            FOREIGN KEY (user_id) REFERENCES {$table_prefix}user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down($db): void
    {
        global $table_prefix;
        $db->sql_query("DROP TABLE {$table_prefix}user_preferences");
    }
}
```

## Troubleshooting

### Erreurs communes

**Logger manquant :**
```
InvalidArgumentException: Logger requis pour AutoUpgradeManager
```
→ Solution : Vérifier que `common.php` est inclus et `$log` disponible

**Classe de migration introuvable :**
```
Classe de migration 'Migration_20250713001_UpgradeTo400' introuvable
```
→ Solution : Vérifier le nom de classe et la convention de nommage

**Permissions insuffisantes :**
```
Impossible d'écrire le fichier de configuration
```
→ Solution : Vérifier les permissions sur `/cache` et `/config`

**Verrous bloqués :**
```
Mise à jour en cours...
```
→ Solution : `php upgrade_cli.php unlock` ou attendre 5 minutes

### Commandes de diagnostic

```bash
# Vérification complète
php upgrade_cli.php status

# Forçage en cas de blocage
php upgrade_cli.php force

# Consultation des logs détaillées
php upgrade_cli.php logs
```

## Compatibilité

### Hébergements supportés

✅ **Hébergements mutualisés** : Optimisé pour les restrictions courantes  
✅ **Serveurs dédiés** : Fonctionnalités complètes  
✅ **VPS** : Performance optimale  

### Prérequis

- PHP 7.4+ avec MySQLi
- MySQL 5.7+ ou MariaDB 10.2+
- Permissions d'écriture sur `/cache`, `/logs`, `/config`
- Monolog disponible (inclus dans `common.php`)

### Limitations connues

- Pas de modification des limites PHP (`set_time_limit`, `ini_set`)
- Logger obligatoire (erreur explicite si absent)
- Maximum 10 migrations simultanées recommandé
- Format de migration basé sur des classes PHP

# Installation OGSpy 4.0

Ce répertoire contient le système d'installation et de mise à jour d'OGSpy 4.0, incluant une interface web moderne et des outils CLI pour l'automatisation.

## 🚀 Installation rapide

### Interface Web
Accédez à `http://votre-serveur/ogspy/install/` pour utiliser l'interface d'installation graphique avec le thème spatial.

### Installation CLI (recommandée pour l'automatisation)
```bash
php upgrade_cli.php install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--lock]
```

## 📋 Prérequis

- PHP 7.4 ou supérieur
- Extensions PHP : mysqli, json, mbstring
- Base de données MySQL/MariaDB
- Permissions d'écriture sur les dossiers `cache/`, `logs/`, `config/`

## 🛠️ Commandes CLI disponibles

### Installation automatisée
```bash
# Installation basique
php upgrade_cli.php install localhost root mypass ogspy admin admin123

# Installation complète avec options
php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --lock
```

**Paramètres :**
- `db_host` : Serveur de base de données
- `db_user` : Utilisateur DB
- `db_password` : Mot de passe DB
- `db_name` : Nom de la base de données
- `admin_user` : Nom d'utilisateur administrateur
- `admin_password` : Mot de passe administrateur
- `admin_email` : Email administrateur (optionnel)
- `table_prefix` : Préfixe des tables (défaut: `ogspy_`)
- `--lock` : Verrouille l'installation après succès

### Gestion des mises à jour
```bash
# Vérifier le statut des migrations
php upgrade_cli.php check

# Lancer une mise à jour automatique
php upgrade_cli.php upgrade

# Forcer une mise à jour (supprime les verrous)
php upgrade_cli.php force

# Afficher le statut complet du système
php upgrade_cli.php status
```

### Gestion des verrous
```bash
# Supprimer les verrous de mise à jour
php upgrade_cli.php unlock

# Déverrouiller l'interface d'installation web
php upgrade_cli.php unlock-install
```

### Tests et diagnostics
```bash
# Lancer tous les tests (installation + mise à niveau)
php upgrade_cli.php test

# Test d'installation uniquement
php upgrade_cli.php test-install

# Test de mise à niveau uniquement
php upgrade_cli.php test-upgrade

# Test de performance des migrations
php upgrade_cli.php test-performance
```

### Logs et maintenance
```bash
# Consulter les logs de mise à jour
php upgrade_cli.php logs

# Désinstaller complètement OGSpy (ATTENTION: supprime tout)
php upgrade_cli.php uninstall

# Afficher l'aide complète
php upgrade_cli.php help
```

### Exemples d'utilisation

#### Installation rapide en local
```bash
php upgrade_cli.php install localhost root password ogspy admin admin123 --lock
```

#### Installation complète avec configuration
```bash
php upgrade_cli.php install db.example.com ogspy_user mypassword ogspy_prod admin secretpass admin@domain.com ogspy_ --lock
```

#### Diagnostic et maintenance
```bash
# Vérifier l'état du système
php upgrade_cli.php status

# En cas de problème avec les verrous
php upgrade_cli.php unlock
php upgrade_cli.php force

# Consulter les derniers logs
php upgrade_cli.php logs
```

#### Tests de validation
```bash
# Test complet avant mise en production
php upgrade_cli.php test

# Test uniquement les performances
php upgrade_cli.php test-performance
```

## 📊 Détails des commandes

### `install` - Installation automatisée
- Vérifie les prérequis PHP et extensions
- Teste la connexion à la base de données
- Génère le fichier de configuration `id.php`
- Exécute toutes les migrations nécessaires
- Crée le compte administrateur
- Option de verrouillage de sécurité

### `check` - Vérification du statut
- Affiche la version actuelle de la base de données
- Liste les migrations en attente
- Indique si l'auto-upgrade est possible

### `upgrade` - Mise à jour standard
- Lance l'auto-upgrade en respectant les verrous
- Exécute les migrations en attente
- Affiche le résultat détaillé

### `force` - Mise à jour forcée
- Supprime tous les verrous existants
- Force l'exécution des migrations
- À utiliser uniquement en cas de blocage

### `status` - Statut complet du système
- Version du logiciel et de la base de données
- État des verrous
- Configuration de l'auto-upgrade

### `unlock` - Gestion des verrous
- Supprime les verrous de mise à jour bloquants
- Permet de débloquer un processus interrompu

### `unlock-install` - Déverrouillage installation
- Supprime le fichier `install.lock`
- Rend l'interface web d'installation accessible

### `test-*` - Suite de tests
- **`test`** : Tests complets (installation + upgrade)
- **`test-install`** : Test d'installation fraîche uniquement
- **`test-upgrade`** : Test de mise à niveau uniquement  
- **`test-performance`** : Mesure les performances des migrations

### `logs` - Consultation des logs
- Affiche les logs du jour courant
- Consulte les derniers logs disponibles
- Utile pour diagnostiquer les problèmes

### `uninstall` - Désinstallation complète
- ⚠️ **ATTENTION** : Opération destructive
- Supprime toutes les tables de la base de données
- Efface les fichiers générés (cache, logs, config)
- Demande confirmation avant exécution

## 🔧 Actions disponibles (résumé)

| Action | Description |
|--------|-------------|
| `install` | Installation automatisée complète |
| `check` | Vérifie les migrations en attente |
| `upgrade` | Lance la mise à jour automatique |
| `force` | Force la mise à jour (supprime verrous) |
| `status` | Affiche le statut complet du système |
| `logs` | Consulte les logs de mise à jour |
| `unlock` | Supprime les verrous de mise à jour |
| `unlock-install` | Déverrouille l'interface d'installation |
| `uninstall` | Désinstalle complètement le système |
| `test` | Lance tous les tests |
| `test-install` | Test d'installation uniquement |
| `test-upgrade` | Test de mise à niveau uniquement |
| `test-performance` | Test de performance des migrations |
| `help` | Affiche l'aide complète |

## Logs et Diagnostic

### Intégration Monolog

Le système utilise le logger Monolog configuré dans `common.php` :

- **Logger principal** : `$log` - Messages généraux dans `/logs/OGSpy.log`
- **Logger SQL** : `$logSQL` - Requêtes dans `/logs/OGSpy-sql.log`
- **Logger SQL lent** : `$logSlowSQL` - Requêtes lentes dans `/logs/OGSpy-sql-slow.log`

### Format des logs migrations

```
[2025-07-19 14:30:15] OGSpy.INFO: === DÉBUT AUTO-UPGRADE ===
[2025-07-19 14:30:15] OGSpy.INFO: Migrations à exécuter: 2
[2025-07-19 14:30:15] OGSpy.INFO: Exécution de la migration: 20250713001 - upgrade to 4 0 0
[2025-07-19 14:30:16] OGSpy.INFO: Migration 20250713001 exécutée avec succès en 0.543s
[2025-07-19 14:30:16] OGSpy.INFO: SUCCÈS: 2 migration(s) réussie(s)
[2025-07-19 14:30:16] OGSpy.INFO: Nouvelle version DB: 20250713002
[2025-07-19 14:30:16] OGSpy.INFO: Temps d'exécution: 1s
[2025-07-19 14:30:16] OGSpy.INFO: Cache nettoyé: 5 fichier(s) supprimé(s)
[2025-07-19 14:30:16] OGSpy.INFO: === FIN AUTO-UPGRADE ===
```

## Tests de connexion

### ConfigGenerator avec MySQLi

Le générateur de configuration utilise MySQLi au lieu de PDO pour une meilleure compatibilité :

```php
// Test de connexion automatique
$mysqli = new mysqli($host, $user, $password, $database);
if ($mysqli->connect_error) {
    throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
}
```

### Avantages MySQLi

- Activé par défaut sur la plupart des hébergements
- Pas besoin d'extension PDO supplémentaire
- Cohérence avec le reste du projet OGSpy
- Compatible avec tous les hébergements mutualisés

## Création d'une nouvelle migration

### Étapes

1. **Créer le fichier** : `migrations/YYYYMMDDNNN_description.php`
2. **Définir la classe** : `Migration_YYYYMMDDNNN_Description`
3. **Implémenter up()** : Code de migration
4. **Implémenter down()** : Code de rollback (optionnel)

### Exemple pratique

```bash
# Fichier : migrations/20250720001_add_user_preferences.php
```

```php
<?php

class Migration_20250720001_AddUserPreferences
{
    public function getVersion(): string
    {
        return '20250720001';
    }

    public function getDescription(): string
    {
        return 'Ajout table préférences utilisateur';
    }

    public function up($db): void
    {
        global $table_prefix;
        
        $db->sql_query("CREATE TABLE {$table_prefix}user_preferences (
            user_id INT NOT NULL,
            pref_key VARCHAR(50) NOT NULL,
            pref_value TEXT,
            PRIMARY KEY (user_id, pref_key),
            FOREIGN KEY (user_id) REFERENCES {$table_prefix}user(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down($db): void
    {
        global $table_prefix;
        $db->sql_query("DROP TABLE {$table_prefix}user_preferences");
    }
}
```

## Troubleshooting

### Erreurs communes

**Logger manquant :**
```
InvalidArgumentException: Logger requis pour AutoUpgradeManager
```
→ Solution : Vérifier que `common.php` est inclus et `$log` disponible

**Classe de migration introuvable :**
```
Classe de migration 'Migration_20250713001_UpgradeTo400' introuvable
```
→ Solution : Vérifier le nom de classe et la convention de nommage

**Permissions insuffisantes :**
```
Impossible d'écrire le fichier de configuration
```
→ Solution : Vérifier les permissions sur `/cache` et `/config`

**Verrous bloqués :**
```
Mise à jour en cours...
```
→ Solution : `php upgrade_cli.php unlock` ou attendre 5 minutes

### Commandes de diagnostic

```bash
# Vérification complète
php upgrade_cli.php status

# Forçage en cas de blocage
php upgrade_cli.php force

# Consultation des logs détaillées
php upgrade_cli.php logs
```

## Compatibilité

### Hébergements supportés

✅ **Hébergements mutualisés** : Optimisé pour les restrictions courantes  
✅ **Serveurs dédiés** : Fonctionnalités complètes  
✅ **VPS** : Performance optimale  

### Prérequis

- PHP 7.4+ avec MySQLi
- MySQL 5.7+ ou MariaDB 10.2+
- Permissions d'écriture sur `/cache`, `/logs`, `/config`
- Monolog disponible (inclus dans `common.php`)

### Limitations connues

- Pas de modification des limites PHP (`set_time_limit`, `ini_set`)
- Logger obligatoire (erreur explicite si absent)
- Maximum 10 migrations simultanées recommandé
- Format de migration basé sur des classes PHP

# Installation OGSpy 4.0

Ce répertoire contient le système d'installation et de mise à jour d'OGSpy 4.0, incluant une interface web moderne et des outils CLI pour l'automatisation.

## 🚀 Installation rapide

### Interface Web
Accédez à `http://votre-serveur/ogspy/install/` pour utiliser l'interface d'installation graphique avec le thème spatial.

### Installation CLI (recommandée pour l'automatisation)
```bash
php upgrade_cli.php install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--lock]
```

## 📋 Prérequis

- PHP 7.4 ou supérieur
- Extensions PHP : mysqli, json, mbstring
- Base de données MySQL/MariaDB
- Permissions d'écriture sur les dossiers `cache/`, `logs/`, `config/`

## 🛠️ Commandes CLI disponibles

### Installation automatisée
```bash
# Installation basique
php upgrade_cli.php install localhost root mypass ogspy admin admin123

# Installation complète avec options
php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --lock
```

**Paramètres :**
- `db_host` : Serveur de base de données
- `db_user` : Utilisateur DB
- `db_password` : Mot de passe DB
- `db_name` : Nom de la base de données
- `admin_user` : Nom d'utilisateur administrateur
- `admin_password` : Mot de passe administrateur
- `admin_email` : Email administrateur (optionnel)
- `table_prefix` : Préfixe des tables (défaut: `ogspy_`)
- `--lock` : Verrouille l'installation après succès

### Gestion des mises à jour
```bash
# Vérifier le statut des migrations
php upgrade_cli.php check

# Lancer une mise à jour automatique
php upgrade_cli.php upgrade

# Forcer une mise à jour (supprime les verrous)
php upgrade_cli.php force

# Afficher le statut complet du système
php upgrade_cli.php status
```

### Gestion des verrous
```bash
# Supprimer les verrous de mise à jour
php upgrade_cli.php unlock

# Déverrouiller l'interface d'installation web
php upgrade_cli.php unlock-install
```

### Tests et diagnostics
```bash
# Lancer tous les tests (installation + mise à niveau)
php upgrade_cli.php test

# Test d'installation uniquement
php upgrade_cli.php test-install

# Test de mise à niveau uniquement
php upgrade_cli.php test-upgrade

# Test de performance des migrations
php upgrade_cli.php test-performance
```

### Logs et maintenance
```bash
# Consulter les logs de mise à jour
php upgrade_cli.php logs

# Désinstaller complètement OGSpy (ATTENTION: supprime tout)
php upgrade_cli.php uninstall

# Afficher l'aide complète
php upgrade_cli.php help
```

### Exemples d'utilisation

#### Installation rapide en local
```bash
php upgrade_cli.php install localhost root password ogspy admin admin123 --lock
```

#### Installation complète avec configuration
```bash
php upgrade_cli.php install db.example.com ogspy_user mypassword ogspy_prod admin secretpass admin@domain.com ogspy_ --lock
```

#### Diagnostic et maintenance
```bash
# Vérifier l'état du système
php upgrade_cli.php status

# En cas de problème avec les verrous
php upgrade_cli.php unlock
php upgrade_cli.php force

# Consulter les derniers logs
php upgrade_cli.php logs
```

#### Tests de validation
```bash
# Test complet avant mise en production
php upgrade_cli.php test

# Test uniquement les performances
php upgrade_cli.php test-performance
```

## 📊 Détails des commandes

### `install` - Installation automatisée
- Vérifie les prérequis PHP et extensions
- Teste la connexion à la base de données
- Génère le fichier de configuration `id.php`
- Exécute toutes les migrations nécessaires
- Crée le compte administrateur
- Option de verrouillage de sécurité

### `check` - Vérification du statut
- Affiche la version actuelle de la base de données
- Liste les migrations en attente
- Indique si l'auto-upgrade est possible

### `upgrade` - Mise à jour standard
- Lance l'auto-upgrade en respectant les verrous
- Exécute les migrations en attente
- Affiche le résultat détaillé

### `force` - Mise à jour forcée
- Supprime tous les verrous existants
- Force l'exécution des migrations
- À utiliser uniquement en cas de blocage

### `status` - Statut complet du système
- Version du logiciel et de la base de données
- État des verrous
- Configuration de l'auto-upgrade

### `unlock` - Gestion des verrous
- Supprime les verrous de mise à jour bloquants
- Permet de débloquer un processus interrompu

### `unlock-install` - Déverrouillage installation
- Supprime le fichier `install.lock`
- Rend l'interface web d'installation accessible

### `test-*` - Suite de tests
- **`test`** : Tests complets (installation + upgrade)
- **`test-install`** : Test d'installation fraîche uniquement
- **`test-upgrade`** : Test de mise à niveau uniquement  
- **`test-performance`** : Mesure les performances des migrations

### `logs` - Consultation des logs
- Affiche les logs du jour courant
- Consulte les derniers logs disponibles
- Utile pour diagnostiquer les problèmes

### `uninstall` - Désinstallation complète
- ⚠️ **ATTENTION** : Opération destructive
- Supprime toutes les tables de la base de données
- Efface les fichiers générés (cache, logs, config)
- Demande confirmation avant exécution

## 🔧 Actions disponibles (résumé)

| Action | Description |
|--------|-------------|
| `install` | Installation automatisée complète |
| `check` | Vérifie les migrations en attente |
| `upgrade` | Lance la mise à jour automatique |
| `force` | Force la mise à jour (supprime verrous) |
| `status` | Affiche le statut complet du système |
| `logs` | Consulte les logs de mise à jour |
| `unlock` | Supprime les verrous de mise à jour |
| `unlock-install` | Déverrouille l'interface d'installation |
| `uninstall` | Désinstalle complètement le système |
| `test` | Lance tous les tests |
| `test-install` | Test d'installation uniquement |
| `test-upgrade` | Test de mise à niveau uniquement |
| `test-performance` | Test de performance des migrations |
| `help` | Affiche l'aide complète |
