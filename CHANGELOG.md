# Notes

## 3.3.8 - en cours

- **Added:** #127 Formula library PHP, Ajout centralisation prix des unités Ogame
- **Added:** #148 Formula library PHP, helper formule - centralisation des formules
- **Added:** #150 Formula library PHP, Ajout centralisation infos flotte/def (vitesse,fret,rapidfire,etc.)
- **Added:** Formula library PHP, Ajout centralisation requirements des unités Ogame
- **Added:** Formula library PHP, Centralisation des prix, coût et cumul et destruction, temps de construction pour def/vso/bat/recherche
- **Added:** Formula library PHP, Ajout centralisation portée missiles et phalange
- **Added:** Ajout affichage point dans l'espace perso
- **Added:** Ajout des langues Bosniens et Croate (bs, bs_BA, hr, hr_HR)
- **Added:** Ajout prise en charge Ogame V7 et des nouveaux boosters
- **Changed:** Centralisation des javascripts
- **Fixed:** #109 timer, sortir le javascript de la date
- **Fixed:** #117 table TABLE_MOD_USER_CFG
- **Fixed:** #137 Mot de passe : Gérer les caractères spéciaux
- **Fixed:** #144,145 espace perso/simu, correction formules
- **Fixed:** #149 formule phalange pour explorateur
- **Fixed:** #163 Gestion des Sessions : Ligne dupliquées
- **Fixed:** #164 Nombre de case des planètes et lunes
- **Fixed:** #166 Statistiques des membres cassées dans l'état cartographique
- **Fixed:** #167 table universe incomplete
- **Fixed:** #171 Critère recherche spéciale inactif
- **Fixed:** #173 Feature/advanced search (by Binu)
- **Fixed:** #160 ajustement largeur menu pour éviter les sauts de lignes (by Steffronte)
- **Fixed:** #165 La production ne prends pas en compte les foreuses ni le bonus de classe collecteur
- **Fixed:** #293 Page Recherche : Légende indisponible
- **Fixed:** #298 Retirer les refs externe, images avatar dev
- **Fixed:** Correction formule coût et prix.
- **Fixed:** Multiple correction PHP (notice, warning, error) et HTML
- **Security:** #171 Compatibilité PHP8, éléments dépréciés
- **Security:** Suppression référence externe (image page OGSteam)
- **Removed:** #168 Erreur SQL lors de la modification de l'ordre des colonies dans l'espace personnel

## 3.3.7 - 2020-03-10

- **Added:** #69,#70 Autoloader
- **Added:** #143 Espace personnel : Ajout Foreuse
- **Changed:** DB Models #121, #94, #93, #89, #90, #91, #74, #75, #76, #77, #78, #79, #80, #81, #82, #83, #84, #85, #86, #87r
- **Fixed:** #133 3.3.7 Suppression Re depuis galaxie
- **Fixed:** #134 Champs Metal, Cristal, Deut de ParsedRE -> BigInt
- **Fixed:** #135 Impossible de mettre à jour le mot de passe d'un utilisateur
- **Fixed:** #138 Incohérence entre les planètes et les lunes
- **Fixed:** #139 User last visit not updated anymore
- **Fixed:** #151 Réinitialisation mot de passe utilisateur lors de la génération du token
- **Fixed:** #154 Sauvegarde Email Utilisateur inopérante
- **Fixed:** #155 Espace Personnel : Variation général : Pas de points
- **Fixed:** #157 Error on Resize Univers
- **Fixed:** #158 Aucun chiffre affiché dans le RC remonté sur l'ogsy
- **Fixed:** #161 Suppression colonie dans l'espace personnel en erreu
- **Fixed:** #162 Affichage Date Rapport d'espionnage bloqué à 1970
- **Security:** #100 Sanitize DB variables

## 3.3.6 - 2019-06-15

- **Fixed:** #120 Vue Galaxie : Affichage RE Multiples
- **Added:** #123 Supprimer le dossier install après l'installation
- **Fixed:** #124 Colonne Dock Manquante dans la table parsedspy après Mise à jour
- **Security:** #44 PHP 7.0 Requis

## 3.3.5 - 2019-04-16

- **Added:** #10 Abscence du Dock Spatial
- **Added:** #12 Ajout params pour install
- **Added:** #51 Information de version incomplet
- **Added:** #63 Paramètre Vitesse univers configurable à l'installation
- **Added:** #67 ajout player_id ally id
- **Added:** #73 Token d'authentification pour API
- **Fixed:** #14 Limite Nombre de pts Classement atteint
- **Fixed:** #49 Affichage message erreur espace personnelle (Empire)
- **Fixed:** #53 Docker : Echec à la création du container
- **Fixed:** #57 bug update mod
- **Fixed:** #61 Fichier lang_mail.php manquant dans pt-br
- **Fixed:** #71 fix_token_3.3.4 (Mail)
- **Fixed:** #97 Points par membres manquant pour les classement alliance
- **Fixed:** #98 Update vers OGspy 3.3.5 depuis auto update
- **Security:** #45 Agrandir la taille des mots de passe à 64 caractères
- **Security:** #46 Chiffrement des mots de passes
- **Removed:** #64  Remove GCM feature (Obsolete)

## 3.3.4 - 2018-10-15

- **Added:** Mail, Administration mail enhancement
- **Added:** Mail, Création Utilisateur
- **Added:** Token, Class Token
- **Added:** bibliothèque PHPMailer
- **Added:** Support Italien
- **Changed:** A Propos
- **Changed:** Images des membres de l'OGSteam
- **Fixed:** Ajout plasma pour calculer la production de deut
- **Fixed:** Erreur Installation si prefixe table manquant
- **Fixed:** Comptage des Boosters dans l'espace personnel incorrect
- **Fixed:** Page Empire : Warning Boosters bug
- **Fixed:** Lien Forum Alliance : Erreur lorsque l'on saisi un lien Https
- **Fixed:** Mise à jour pseudo ig dans le profil

## 3.3.2 - 2017-02-17

- **Fixed:** Correctif affichage erreur MySQL
- **Fixed:** Mise à jour fichiers de langue

## 3.3.1 - 2016-06-26

- Internationalisation - Anglais désormais complet
- Phalanges Circulaires
- Logo installation mis à jour
- Mise à jour schéma BDD

## 3.3.0 - 2016-05-24

- Internationalisation
- Compatibilité PHP 7
- Univers Circulaires
- Corrections multiples

## 3.2.0 - 2015-05-11

- Passage en UTF-8 pour une meilleur gestion des caractères spéciaux
- Compatibilité PHP 5.5
- Sortie de OGSpy pour Android
- Correction erreur de suppression classement alliance
- Mise en forme de la présentation des nombres dans l'espace personnel
- BBCODE avec les identifiants à la création du nouvel utilisateur

## 3.1.3 - 2013-04-01

- Correctif pour la suppression d'une planète dans l'espace Personel
- Modification du champ phalanx pour mettre une valeur par défaut
- Réorganisation du profil utilisateur et ajout de l'adresse mail
- Taille d'un champ de la BDD pour les univers > 9 Galaxies
- Correctif Formules de Production
- Ajout du compte Commandant
- Mise à jour de l'équipe OGSteam

## 3.1.2 - 2012-12-24

- Compatibilité OGame 5.X
- Correctif de la supression d'un utilisateur
- Correctif du système de Mise à jour
- Mise à jour de l'équipe OGSteam

## 3.1.1 - 2012-11-17

- Compatibilité OGame 4.X
- Correction de la maintenance automatique
- Mise à jour de l'équipe OGSteam

## 3.1.0

- Compatibilité OGame 3.X:
- Nouveaux classements militaires
- Nouveaux bâtiments.
- Support IPv6.
- Nouveau skin.

## 3.0.8

- Affichage RE vue galaxie : affichage de 2 RE : 1 de planète, et 1 de lune, si il(s) existe(nt)
- Modification accés à la base de donnée.
- Mise en place d'un systeme de mise en cache.
- Attribution d'un identifiant unique pour chaque installation.
- Suppression de tous les appels directs à la base de donnée.
- Supression des fichiers obsolétes.
- Mise en conformité des pseudos ingame - Correctifs divers

## 3.0.7

- Remplacement de la technologie Expéditions par Astrophysique
- Support d'un nombre de planètes supérieur à 9(Désormais lié à la Technologie Astrophysique)
- Désactivation de l'import par copier - coller
- Remise à jour des Liens vers les sites de l'OGSteam
- Nouvelle Gestion des Id Planètes
- Mise a jour des diverses formules de calcul
- Mise en conformité réglement ogame v1

## 3.0.5

- Compatibilité avec OGame 0.78c
- Depots de ravitaillement (optionnel)
- Vitesse de l'univers paramètrable
- Ajout des expéditions
- RC directement parsé dans OGSpy
- Changement de la structure de la base de donnée (optimisation ++++)
- Affichage des RC enregistrés directement sur la vue galaxie

## 3.04b

- Suppression du fond transparent pour l'ajout des membres (admin)
- Ajout de flag admin paramétrables sur les mods
- Ajout d'une option de journalisation des erreurs php
- autoupdate: descriptions des mods, plus d'infos sur les droits d'ecritures
- Ajout d'une table de configuration pour les mods avec les fonctions appropriés
- Correction bug sur recherche "stricte"
- Correction du bug de droits insuffisants pour copier/coller les infos
- Amélioration securité
- Correction bug "Illegal mix of collations"
- Correction bug d'ajout de membres

## 3.04

- Ajout du mod_Xtense à la base d'OGSpy
- Ajout du mod_autoupdate à la base d'OGSpy
- Ajout d'une fonction "Ajouter tout les membres" pour les groupes
- Correction de bugs lié au passage d'Ogame en version 0.77b

## 3.03

- Mise en place du choix de galaxies et de sytèmes par galaxies

## 3.02c

- Ordonnancement des mods dans l'administration
- Assouplissement des contrôles sur l'injection de systèmes solaires et rapport d'espionnage
- Modifications mineures de l'interface de l'administration
- Possibilité de désactiver le contrôle des adresses IP provoquant des déconnexions intempestives (AOL, Proxy, etc)
- Correction d'anomalies diverses

## 3.02b

- Correction de bugs mineurs

## 3.02

- Gestion des utilisateurs par groupe
- Cartographie alliance
- Amélioration de l'interface par l'utilisation de tooltips
- Prise en compte des phalanges et portes spatiales
- Affichage des systèmes solaires et lunes obsolètes
- Mémorisation de rapport d'espionnage dans l'espace personnel
- Optimisation du code pour de meilleurs délais de réponse
- Espace personnel enrichi avec affichage de graphiques
- Calcul de la participation des membres dans la section statistiques
- Gestionnaire d'intégration de mods
- Correction de bugs mineurs
- Incompatibilité avec les versions d'OGS antérieures à la 2.0

## 0.301b

- Correction mauvais affichage des joueurs absents
- Correction du bug empêchant de rentrer le classement dans la période 16h-24h
- Bug javascript empêchant de faire des simulations avec Internet Explorer corrigé
- Correction de bugs mineurs

## 0.301

- Disponibilité du script de migration des bases de données OGSS -> OGSpy
- Nombre de satellites passé à 5 chiffres dans l'espace personnel
- Ajout d'un nouveau critère de recherche selon les rapports d'espionnage (Merci ben.12)
- Possibilité de visualiser plusieurs systèmes sur une même page par l'intermédiaire de la page statistiques
- Optimisation de l'affichage du classement joueur
- Affichage des systèmes mis à jour dans la section statistiques par secteur
- Correction bug exportation des rapports d'espionnage par système qui envoyait tous les rapports connus vers OGS au lieu du système demandé
- Purge automatique des classements et des rapports d'espionnage selon l'ancienneté ou le nombre maximal autorisé. (Paramétrable dans l'administration)
- Possibilité de supprimer les classements au cas par cas
- Importation du classement directement sur le serveur
- Possibilité d'avoir de nombreuses statistiques par le biais de BBClone
- Faille de sécurité concernant les sessions corrigées

## 0.300f

- Les rapports d'espionnage sont affichés du plus récent au plus ancien
- Message dans le journal lorsque l'on envoie le classement
- Exportation de rapports d'espionnage selon une date
- Correction du bug d'affichage classement
- Résumé après envoi de rapports d'espionnage
- Correction du bug de recherche qui empêchait les pages suivantes avec comme un critère différent des coordonnées
- Correction bug dans l'espace personnel, calcul de la production d'énergie et de deutérium faussée

## 0.300e

- Correction du bug de recherche qui n'affichait pas les pages avec IE
- Correction du bug de non compatibilité de requetes SQL avec certains serveurs MySQL
- Affichage PHPInfo - Modules PHP dans l'administration
- Correction bug gestion empire (apparition des planètes d'autres joueurs après modification)
- Possibilité de paramétrer le lien du forum affiché sur le menu par l'administration
- Correction du bug d'importation de certains rapports d'espionnage
- Possibilité de contrôler que le serveur soit à jour dans l'administration

## 0.300d

- Correction du bug du panneau d'administration et de connexion avec OGS lié à un champ manquant dans la base de données
- Correction bug de recherche des joueurs sans ally
- Correction du bug dans l'espace personnel au sujet du nombre de cases utilisées par planète

## 0.300c

- Correction du bug d'importation des rapports d'espionnage
- Correction bug empêchant de modifier les paramètres serveur selon la configuration d'installation employée pour OGSpy
- Correction de bugs mineurs

## 0.300b

- Modification des requêtes incompatibles avec MySQL 4.0

## 0.300

- Restructuration intégrale du code
- Nouvelle interface utilisateur
