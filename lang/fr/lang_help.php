<?php
/**
 * Help Language File
 * @package OGSpy
 * @subpackage i18n
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.0
 */
$lang["help_admin_server_status"] = "Lorsque le serveur est désactivé, seul les membres avec le statut d'administrateur ont accès aux fonctionnalités du serveur";
$lang["help_admin_server_status_message"] = "Le message sera affiché aux membres lorsque le serveur sera désactivé";
$lang["help_admin_save_transaction"] = "Les transactions correspondent aux :<br>- Systèmes solaires<br>- Rapports d'espionnage<br>- Classements joueurs et alliances";
$lang["help_admin_member_manager"] = "Autorise la création, la mise à jour et la suppression des utilisateurs";
$lang["help_admin_ranking_manager"] = "Autorise la suppression des classements joueurs et alliances";
$lang["help_admin_check_ip"] = "Certains utilisateurs subissent des déconnexions intempestives. Activez cette option afin qu'ils puissent désactiver la vérification dans leur profil";
$lang["help_admin_session_infini"] = "Si vous choisissez des sessions indéfinies dans le temps, plusieurs individus ne pourront plus utiliser le même compte en même temps";
$lang["help_drop_sessions"] = "Vide la table des sessions, cela allége l'administration mais oblige tout les utilisateurs à se reconnecter.";

$lang["help_search_strict"] = "<font color=orange>Joueur recherché :</font><br><i>Liquid snake</i><br><font color=orange>Critère de recherche :</font><br><i>snake</i><br><br>=> <font color=lime>Résultat positif</font> si l'option \"strict\" est désactivée<br>=> <font color=red>Résultat négatif</font> si l'option \"strict\" est activée";

$lang["help_home_commandant"] = "Page empire du compte commandant";

$lang["help_profile_login"] = "Doit contenir entre 3 et 64 caractères (les caractères spéciaux : ; &rsquo; et &quot; ne sont pas acceptés)";
$lang["help_profile_pseudo_email"] = "Si rempli, vous recevrez des mails de la part de certains mods";
$lang["help_profile_main_planet"] = "La vue Galaxie sera ouverte directement sur ce système solaire";
$lang["help_profile_password"] = "Doit contenir entre 6 et 15 caractères (les caractères spéciaux (; &rsquo; &quot;) ne sont pas acceptés)";
$lang["help_profile_galaxy"] = "Doit contenir un nombre de 1 à 999";
$lang["help_profile_disable_ip_check"] = "La vérification de l'adresse IP permet de vous protéger contre le vol de session.";
$lang["help_profile_disable_ip_check"] .= "Si vous êtes déconnecté régulièrement (VPN, Proxy, etc), désactivez la vérification.<br><br>";
$lang["help_profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activée</i>";

$lang["help_galaxy_phalanx"] = "Chargez des rapports d'espionnage pour afficher les phalanges hostiles";

$lang["help_ratio_block"] = "Vous avez un ratio inférieur au seuil, vous ne pouvez pas accéder aux mods";
$lang["help_profile_speed_uni"] = "Indiquez le multiplicateur de vitesse de votre univers (1 par défaut)";
$lang["profile_speed_fleet_peaceful"] = "Indiquez le multiplicateur de vitesse des flottes pacifiques de votre univers (1 par défaut)";
$lang["profile_speed_fleet_war"] = "Indiquez le multiplicateur de vitesse des flottes hostiles de votre univers (1 par défaut)";
$lang["profile_speed_fleet_holding"] = "Indiquez le multiplicateur de vitesse de stationnement chez un allié de votre univers (1 par défaut)";
$lang["help_profile_ddr"] = "Cocher si le dépôt de ravitaillement est présent dans votre univers";
$lang["help_astro_strict"] = "Cocher si l'univers est ancien. Cela permet d'avoir 9 planetes sans avoir la technologie astrophysique correspondante.";
$lang["help_config_cache"] = "Durée de vie du cache config en seconde.";
$lang["help_donutSystem"] = "Si les systèmes sont ronds alors 499 vers 1 est une distance de 1.";
$lang["help_donutGalaxy"] = "Si les galaxie sont rondes alors 9 vers 1 est une distance de 1.";
$lang["help_mod_cache"] = "Durée de vie du cache mod en seconde..";

/* admin page */

$lang['help_display_mips'] = "Affiche ou cache les MIP des users de OGSpy, mais ne les affichent qu'a ceux qui peuvent voir les alliances protégées.";
$lang['help_member_stats'] = "Affiche ou cache le tableau de statistique des membres en bas de la page statistiques";
$lang['help_member_connected'] = "Affiche les (*) qui permettent de savoir qui est connecté\nDésactivé si l'affichage des membres n'est pas activé";
$lang['help_member_registration'] = "Affiche ou cache le tableau contenant le lien du forum de cet OGSpy";
$lang['help_ally_name'] = "Nom de l'alliance de cet OGSpy";
$lang['help_forum_link'] = "Lien d'une section du forum, voir le PM de l'administrateur OGSpy";
$lang['help_first_displayed_module'] = "Module affiché lors de la connexion des utilisateurs à cet OGSpy";
$lang['help_first_displayed_module_admin'] = "Module affiché lors de la connexion des administrateurs de cet OGSpy";
