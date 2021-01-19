## [3.3.7] - 2019-03-10

### Added
*  Autoloader #69, #70
*  Espace personnel : Ajout Foreuse #143

### Changed
*  DB Models #121, #94, #93, #89, #90, #91, #74, #75, #76, #77, #78, #79, #80, #81, #82, #83, #84, #85, #86, #87

### Fixed
*  Champs Metal, Cristal, Deut de ParsedRE -> BigInt #134
*  Impossible de mettre à jour le mot de passe d'un utilisateur #135
*  Sauvegarde Email Utilisateur inopérante #154
*  Réinitialisation mot de passe utilisateur lors de la génération du token #151
*  User last visit not updated anymore #139
*  Incohérence entre les planètes et les lunes #138
*  Error on Resize Univers #157
*  Espace Personnel : Variation général : Pas de points #155
*  Suppression colonie dans l'espace personnel en erreur #161
*  3.3.7 Suppression Re depuis galaxie #133
*  Affichage Date Rapport d'espionnage bloqué à 1970 #162
*  Aucun chiffre affiché dans le RC remonté sur l'ogsy #158

### Security
*  Sanitize DB variables #100 

## [3.3.6] - 2019-06-15

### Added
*  #123 Supprimer le dossier install après l'installation

### Fixed
*  #120 Vue Galaxie : Affichage RE Multiples
*  #124 Colonne Dock Manquante dans la table parsedspy après Mise à jour

### Security
*  #44 PHP 7.0 Requis


## [3.3.5] - 2019-04-07
*  [Added] #73 Token d'authentification pour API
*  [Added] #67 ajout player_id ally id
*  [Added] #63 Paramètre Vitesse univers configurable à l'installation
*  [Added] #12 Ajout params pour install
*  [Added] #10 Abscence du Dock Spatial
*  [Added] #51 Information de version incomplet
*  [Fixed] #97 Points par membres manquant pour les classement alliance
*  [Fixed] #71 fix_token_3.3.4 (Mail)
*  [Fixed] #49 Affichage message erreur espace personnelle ( Empire )
*  [Fixed] #57 bug update mod
*  [Fixed] #61 Fichier lang_mail.php manquant dans pt-br
*  [Fixed] #14 Limite Nombre de pts Classement atteint
*  [Fixed] #53 Docker : Echec à la création du container
*  [Fixed] #98 Update vers OGspy 3.3.5 depuis auto update
*  [Security] #46 Chiffrement des mots de passes
*  [Security] #45 Agrandir la taille des mots de passe à 64 caractères
*  [Removed] #64  Remove GCM feature (Obsolete)

##[3.3.2]
*  [Fixed] Correctif affichage erreur MySQL
*  [Fixed] Mise à jour fichiers de langue

##[3.3.1]
*  Internationalisation - Anglais désormais complet
*  Phalanges Circulaires
*  Logo installation mis à jour
*  Mise à jour schéma BDD

##[3.3.0]
*  Internationalisation
*  Compatibilité PHP 7
*  Univers Circulaires
*  Corrections multiples

##[3.2.0]
*  Passage en UTF-8 pour une meilleur gestion des caractères spéciaux
*  Compatibilité PHP 5.5
*  Sortie de OGSpy pour Android
*  Correction erreur de suppression classement alliance
*  Mise en forme de la présentation des nombres dans l'espace personnel
*  BBCODE avec les identifiants à la création du nouvel utilisateur

##[3.1.3]
*  Correctif pour la suppression d'une planète dans l'espace Personel
*  Modification du champ phalanx pour mettre une valeur par défaut
*  Réorganisation du profil utilisateur et ajout de l'adresse mail
*  Taille d'un champ de la BDD pour les univers > 9 Galaxies
*  Correctif Formules de Production
*  Ajout du compte Commandant
*  Mise à jour de l'équipe OGSteam

##[3.1.2]
*  Compatibilité OGame 5.X
*  Correctif de la supression d'un utilisateur
*  Correctif du système de Mise à jour
*  Mise à jour de l'équipe OGSteam

##[3.1.1]
*  Compatibilité OGame 4.X
*  Correction de la maintenance automatique
*  Mise à jour de l'équipe OGSteam

##[3.1.0]
*  Compatibilité OGame 3.X:
*  Nouveaux classements militaires
*  Nouveaux bâtiments.
*  Support IPv6.
*  Nouveau skin.

##[3.0.8]
*  Affichage RE vue galaxie : affichage de 2 RE : 1 de planète, et 1 de lune, si il(s) existe(nt)
*  Modification accés à la base de donnée.
*  Mise en place d'un systeme de mise en cache.
*  Attribution d'un identifiant unique pour chaque installation.
*  Suppression de tous les appels directs à la base de donnée.
*  Supression des fichiers obsolétes.
*  Mise en conformité des pseudos ingame - Correctifs divers

##[3.0.7]
*  Remplacement de la technologie Expéditions par Astrophysique
*  Support d'un nombre de planètes supérieur à 9(Désormais lié à la Technologie Astrophysique)
*  Désactivation de l'import par copier - coller
*  Remise à jour des Liens vers les sites de l'OGSteam
*  Nouvelle Gestion des Id Planètes
*  Mise a jour des diverses formules de calcul
*  Mise en conformité réglement ogame v1

##[3.0.5]
*  Compatibilité avec OGame 0.78c
*  Depots de ravitaillement (optionnel)
*  Vitesse de l'univers paramètrable
*  Ajout des expéditions
*  RC directement parsé dans OGSpy
*  Changement de la structure de la base de donnée (optimisation ++++)
*  Affichage des RC enregistrés directement sur la vue galaxie

##[3.04b]
*  Suppression du fond transparent pour l'ajout des membres (admin)
*  Ajout de flag admin paramétrables sur les mods
*  Ajout d'une option de journalisation des erreurs php
*  autoupdate: descriptions des mods, plus d'infos sur les droits d'ecritures
*  Ajout d'une table de configuration pour les mods avec les fonctions appropriés
*  Correction bug sur recherche "stricte"
*  Correction du bug de droits insuffisants pour copier/coller les infos
*  Amélioration securité
*  Correction bug "Illegal mix of collations"
*  Correction bug d'ajout de membres

##[3.04]
*  Ajout du mod_Xtense à la base d'OGSpy
*  Ajout du mod_autoupdate à la base d'OGSpy
*  Ajout d'une fonction "Ajouter tout les membres" pour les groupes
*  Correction de bugs lié au passage d'Ogame en version 0.77b

##[3.03]
*  Mise en place du choix de galaxies et de sytèmes par galaxies

##[3.02c]
*  Ordonnancement des mods dans l'administration
*  Assouplissement des contrôles sur l'injection de systèmes solaires et rapport d'espionnage
*  Modifications mineures de l'interface de l'administration
*  Possibilité de désactiver le contrôle des adresses IP provoquant des déconnexions intempestives (AOL, Proxy, etc)
*  Correction d'anomalies diverses

##[3.02b]
*  Correction de bugs mineurs

##[3.02]
*  Gestion des utilisateurs par groupe
*  Cartographie alliance
*  Amélioration de l'interface par l'utilisation de tooltips
*  Prise en compte des phalanges et portes spatiales
*  Affichage des systèmes solaires et lunes obsolètes
*  Mémorisation de rapport d'espionnage dans l'espace personnel
*  Optimisation du code pour de meilleurs délais de réponse
*  Espace personnel enrichi avec affichage de graphiques
*  Calcul de la participation des membres dans la section statistiques
*  Gestionnaire d'intégration de mods
*  Correction de bugs mineurs
*  Incompatibilité avec les versions d'OGS antérieures à la 2.0

##[0.301b]
*  Correction mauvais affichage des joueurs absents
*  Correction du bug empêchant de rentrer le classement dans la période 16h-24h
*  Bug javascript empêchant de faire des simulations avec Internet Explorer corrigé
*  Correction de bugs mineurs

##[0.301]
*  Disponibilité du script de migration des bases de données OGSS -> OGSpy
*  Nombre de satellites passé à 5 chiffres dans l'espace personnel
*  Ajout d'un nouveau critère de recherche selon les rapports d'espionnage (Merci ben.12)
*  Possibilité de visualiser plusieurs systèmes sur une même page par l'intermédiaire de la page statistiques
*  Optimisation de l'affichage du classement joueur
*  Affichage des systèmes mis à jour dans la section statistiques par secteur
*  Correction bug exportation des rapports d'espionnage par système qui envoyait tous les rapports connus vers OGS au lieu du système demandé
*  Purge automatique des classements et des rapports d'espionnage selon l'ancienneté ou le nombre maximal autorisé. (Paramétrable dans l'administration)
*  Possibilité de supprimer les classements au cas par cas
*  Importation du classement directement sur le serveur
*  Possibilité d'avoir de nombreuses statistiques par le biais de BBClone
*  Faille de sécurité concernant les sessions corrigées

##[0.300f]
*  Les rapports d'espionnage sont affichés du plus récent au plus ancien
*  Message dans le journal lorsque l'on envoie le classement
*  Exportation de rapports d'espionnage selon une date
*  Correction du bug d'affichage classement
*  Résumé après envoi de rapports d'espionnage
*  Correction du bug de recherche qui empêchait les pages suivantes avec comme un critère différent des coordonnées
*  Correction bug dans l'espace personnel, calcul de la production d'énergie et de deutérium faussée

##[0.300e]
*  Correction du bug de recherche qui n'affichait pas les pages avec IE
*  Correction du bug de non compatibilité de requetes SQL avec certains serveurs MySQL
*  Affichage PHPInfo - Modules PHP dans l'administration
*  Correction bug gestion empire (apparition des planètes d'autres joueurs après modification)
*  Possibilité de paramétrer le lien du forum affiché sur le menu par l'administration
*  Correction du bug d'importation de certains rapports d'espionnage
*  Possibilité de contrôler que le serveur soit à jour dans l'administration

##[0.300d]
*  Correction du bug du panneau d'administration et de connexion avec OGS lié à un champ manquant dans la base de données
*  Correction bug de recherche des joueurs sans ally
*  Correction du bug dans l'espace personnel au sujet du nombre de cases utilisées par planète

##[0.300c]
*  Correction du bug d'importation des rapports d'espionnage
*  Correction bug empêchant de modifier les paramètres serveur selon la configuration d'installation employée pour OGSpy
*  Correction de bugs mineurs

##[0.300b]
*  Modification des requêtes incompatibles avec MySQL 4.0

##[0.300]
*  Restructuration intégrale du code
*  Nouvelle interface utilisateur
