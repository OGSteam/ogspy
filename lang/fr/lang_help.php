<?php
/**
 * Help Language File
 * @package OGSpy
 * @subpackage i18n
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.0
 */
$help["admin_server_status"] = "Lorsque le serveur est désactivé, seul les membres avec le statut d'administrateur ont accès aux fonctionnalités du serveur";
$help["admin_server_status_message"] = "Le message sera affiché aux membres \"de base\" lorsque le serveur sera désactivé";
$help["admin_save_transaction"] = "Les transactions correspondent aux :<br>- Systèmes solaires<br>- Rapports d'espionnage<br>- Classements joueurs et alliances";
$help["admin_member_manager"] = "Autorise la création, la mise à jour et la suppression des utilisateurs";
$help["admin_ranking_manager"] = "Autorise la suppression des classements joueurs et alliances";
$help["admin_check_ip"] = "Certains utilisateurs subissent des déconnexions intempestives (AOL, Proxy, etc).<br>Activez cette option afin qu'ils puissent désactiver la vérification dans leur profil";
$help["admin_session_infini"] = "Si vous choisissez des sessions indéfinies dans le temps, plusieurs individus ne pourront plus utiliser le même compte en même temps";
$help["drop_sessions"] = "Vide la table des sessions, cela allége l'administration mais oblige tout les utilisateurs à se reconnecter.";

$help["search_strict"] = "<font color=orange>Joueur recherché :</font><br><i>Liquid snake</i><br><font color=orange>Critère de recherche :</font><br><i>snake</i><br><br>=> <font color=lime>Résultat positif</font> si l'option \"strict\" est désactivée<br>=> <font color=red>Résultat négatif</font> si l'option \"strict\" est activée";

$help["home_commandant"] = "Page empire du compte commandant";

$help["profile_login"] = "Doit contenir entre 3 et 15 caractères (les caractères spéciaux ne sont pas acceptés)";
$help["profile_pseudo_email"] = "Si rempli, vous recevrez des mails de la part de certains mods";
$help["profile_main_planet"] = "La vue Galaxie sera ouverte directement sur ce système solaire";
$help["profile_password"] = "Doit contenir entre 6 et 15 caractères (les caractères spéciaux ne sont pas acceptés)";
$help["profile_galaxy"] = "Doit contenir un nombre<br> de 1 à 999";
$help["profile_disable_ip_check"] = "La vérification de l'adresse IP permet de vous protéger contre le vol de session.<br><br>";
$help["profile_disable_ip_check"] .= "Si vous êtes déconnecté régulièrement (AOL, Proxy, etc), désactivez la vérification.<br><br>";
$help["profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activée</i>";

$help["galaxy_phalanx"] = "Chargez des rapports d'espionnage pour afficher les phalanges hostiles";

$help["ratio_block"] = "Vous avez un ratio inferieur au seuil, vous ne pouvez pas accéder aux mods";
$help["profile_speed_uni"] = "Indiquez le multiplicateur de vitesse de votre univers (1 par défaut)";
$help["profile_ddr"] = "Cocher si le dépôt de ravitaillement est présent dans votre univers";
$help["astro_strict"] = "Cocher si l'univers est ancien. Cela permet d'avoir 9 planetes sans avoir la technologie astrophysique correspondante.";
$help["uni_arrondi"] = "Pour version Ogame >5.8.5 En cours de projet dans la Gameforge.<br > Exemple : Distance galaxie entre 1 et 9 = 1G (si arrondi); Distance systeme entre 1 et 499 = 1S (si arrondi).<br > Formule : dist(a,b)=||a-b|-unitMax|  (ou unitMax=499(system), unitMax=9(galaxy).";
$help["config_cache"] = "Durée de vie du cache config en seconde.";
$help["mod_cache"] = "Durée de vie du cache mod en seconde..";

/* admin page */

$help['display_mips'] = "Affiche ou cache les MIP des users de OGSpy, mais ne les affichent qu'a ceux qui peuvent voir les alliances protégées.";
$help['member_stats'] = "Affiche ou cache le tableau de statistique des membres en bas de la page statistiques";
$help['member_connected'] = "Affiche les (*) qui permettent de savoir qui est connecté<br>Désactivé si l'affichage des membres n'est pas activé";
$help['member_registration'] = "Affiche ou cache le tableau contenan le lien du forum de cet OGSpy";
$help['ally_name'] = "Nom de l'alliance de cet OGSpy";
$help['forum_link'] = "Lien d'une section du forum, voir le PM de l'administrateur OGSpy";
$help['first_displayed_module'] = "Module affiché lors de la connexion des utilisateurs à cet OGSpy";
$help['first_displayed_module_admin'] = "Module affiché lors de la connexion des administrateurs de cet OGSpy";
