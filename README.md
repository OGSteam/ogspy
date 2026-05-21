# OGSpy

[![GitHub Issues](https://img.shields.io/github/issues/OGSTeam/ogspy.svg)](https://github.com/OGSTeam/ogspy/issues) [![Version Courante](https://img.shields.io/badge/version-4.0.0-green.svg)](https://github.com/OGSTeam/ogspy) [![Demo](https://img.shields.io/badge/demo-online-green.svg)](https://ogspy.fr/demo)

Le projet crée en 2006 est une aide pour un jeu de gestion de vaisseaux spatiaux.
Le but de cet outil est de récupérer l'ensemble des informations du Jeu pour ensuite les regrouper et les exploiter.

Visitez notre forum [OGSteam.eu](https://forum.ogsteam.eu) pour en savoir plus.

---

## Documentation

Notre espace documentaire est construit par nos utilisateurs via notre wiki. Vous pouvez y trouver les descriptifs de nos applications ainsi que les procédures d'installation.

[Wiki OGSteam](https://wiki.ogsteam.eu)

Des tutoriels d'installation sont aussi disponibles sur notre chaine Youtube : [Youtube](https://www.youtube.com/playlist?list=PLF1RvCcSTS6M28sPpadlerKcuwhhTBtrQ)

### Fonctionnalités

- Enregistrement des Galaxies et des classements
- Recherches des emplacements joueurs
- Stockage des rapports d'espionnages et de combats
- Gestion des utilisateurs et groupes
- Comparaison de la progression des joueurs
- Simulation des productions
- Possibilité d'ajouter de nombreuses extensions

---

### Prérequis serveur

Avant d'installer OGSpy, assurez-vous que votre serveur répond aux exigences suivantes :

#### PHP

| Élément | Version minimale | Version recommandée |
|---------|-----------------|---------------------|
| PHP     | 8.1             | 8.4                 |

#### Extensions PHP requises

| Extension  | Rôle |
|------------|------|
| `mysqli`   | Connexion à la base de données |
| `json`     | Encodage/décodage JSON |
| `mbstring` | Gestion des chaînes multi-octets |
| `openssl`  | Chiffrement et tokens sécurisés |
| `zlib`     | Compression |
| `zip`      | Gestion des archives ZIP |

#### Extensions PHP recommandées (utilisées par certains mods)

| Extension   | Mod concerné | Rôle |
|-------------|-------------|------|
| `curl`      | `bthof`     | Test de connectivité HTTP (optionnel, vérifié à l'exécution) |
| `simplexml` | `superapix` | Lecture de fichiers XML |

#### Base de données

| Logiciel | Version minimale | Version recommandée |
|----------|-----------------|---------------------|
| MariaDB  | 10.4            | 12.2                |

> **Note :** MySQL 5.7+ est également compatible mais MariaDB est recommandé.

#### Serveur web

Apache 2.4+ ou Nginx 1.18+ avec support PHP-FPM.

#### Installation rapide (Ubuntu / Debian)

> **Ubuntu 26.04 LTS** : PHP 8.4 est disponible nativement, aucun PPA nécessaire.  
> **Ubuntu 22.04 / 24.04** : PHP 8.4 nécessite le PPA `ondrej/php` :
> ```bash
> add-apt-repository ppa:ondrej/php -y && apt update
> ```

```bash
# Installer Apache, MariaDB et PHP avec les extensions requises
apt install -y mariadb-server apache2 libapache2-mod-php8.4 \
  php8.4 php8.4-mysql php8.4-mbstring php8.4-zip php8.4-xml php8.4-curl

systemctl restart apache2
```

Créer la base de données :

```sql
mysql -u root
CREATE DATABASE ogspy;
CREATE USER 'ogspy'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON ogspy.* TO 'ogspy'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Télécharger et extraire la dernière release :

```bash
# Télécharger la dernière release depuis GitHub
LATEST_ZIP=$(curl -s https://api.github.com/repos/OGSteam/ogspy/releases/latest \
  | grep "browser_download_url.*\.zip" | cut -d '"' -f 4)
curl -L "$LATEST_ZIP" -o ogspy.zip
unzip ogspy.zip -d /var/www/html/
```

Puis lancer l'installation via la CLI :

```bash
cd /var/www/html
php install/upgrade_cli.php install localhost ogspy password ogspy admin admin123 admin@example.com ogspy_
```

Ou via l'interface web : `http://votre-serveur/install/`.

---

### Installation et usages

Vous trouverez sur le wiki le manuel d'installation d'OGSpy sur un serveur web. Mais il existe aussi des hébergeurs qui installent le site pour vous.

[Wiki de l'OGSteam](https://wiki.ogsteam.eu/doku.php)

### Démo

Vous pouvez tester un serveur OGSpy, une démo est disponible :

- Serveur de test : <https://ogspy.fr/demo>
- Nom d'utilisateur : demo
- Mot de passe : ogsteam

---

## Contribuer au projet

## Gestion des montées de version

OGSpy dispose d'un système de migration automatisé qui gère les montées de version de manière transparente.

### Processus pour une nouvelle version

**Montée de version simplifiée : il suffit de merger sur master. De build et de Retro Merge sur Develop. Le Github Actions insérera la nouvelle version à la création du package.

Il restera à publier la Release crée en daft.

#### Synchronisation automatique

Le système de migration détecte automatiquement les changements de version et synchronise la base de données :

- ✅ **Détection automatique** : Compare `$ogspy_version` avec la version en base
- ✅ **Synchronisation transparente** : Met à jour automatiquement si différente
- ✅ **Aucune migration requise** : Pas besoin de créer de fichier de migration pour une simple montée de version

#### Si nouvelles migrations nécessaires

Uniquement si la nouvelle version nécessite des modifications de schéma ou de données :

1. Créez les migrations correspondantes dans `install/migrations/`
2. La synchronisation de version reste automatique

### Avantages

- **Compatible CI/CD** : Fonctionne parfaitement avec les pipelines automatisés
- **Détection intelligente** : Compare automatiquement les versions et synchronise si nécessaire
- **Zéro maintenance** : Plus besoin de créer des migrations vides pour les montées de version
- **Historique propre** : Seules les vraies migrations (schéma/données) sont dans l'historique

- Vous pouvez nous aider sur le développement
- Nous avons besoins de vos idées pour améliorer l'outil
- Des volontaires pour la documentations dans toutes les langues connues
- Traductions

Vous pouvez nous poser toutes les questions nécessaires sur notre forum.

### Equipe projet

Responsable équipe : [DarkNoon](https://github.com/darknoon29)

#### Développement

- [Machine](https://github.com/machine62)
- [Jedinight](https://github.com/jedi-night)
- Superbox
- [Pitch314](https://github.com/pitch314)
- Shad
- Xaviernuma
- Ninety
- [Itori](https://github.com/Itori)
- [Mascotte](https://github.com/mascotte88)

#### Tests

- [Roms0406](https://github.com/Roms0406)

#### Graphismes

- Chris Alys

### Branches de travail

- master - Branche principale, utilisée uniquement pour publier les versions finales de nos outils.
- development - Branche qui contient les développements pour les futures versions.
- release-3.X.Y - Branche contenant les correctifs pour une future version.

## License

>Vous pouvez consulter la license [ici](https://github.com/OGSTeam/ogspy/blob/master/LICENSE)

Le projet est diffusé sous la license **GPLV2**.
