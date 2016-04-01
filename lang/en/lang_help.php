<?php
/**
 * Created by IntelliJ IDEA.
 * User: antho
 * Date: 05/08/2015
 * Time: 18:15
 */

$help["admin_server_status"] = "When the server is disabled, only members with administrator privileges have access to server functionality";
$help["admin_server_status_message"] = "The message will be displayed to the members \"basic\" when the server is disabled";
$help["admin_save_transaction"] = "Les transactions correspondent aux :<br>- Systèmes solaires<br>- Rapports d'espionnage<br>- Classements joueurs et alliances";
$help["admin_member_manager"] = "Authorizes the creation, updating and deleting users";
$help["admin_ranking_manager"] = "Allows for the deletion of the players and alliances rankings";
$help["admin_check_ip"] = "Certains utilisateurs subissent des déconnexions intempestives (AOL, Proxy, etc).<br>Activez cette option afin qu'ils puissent désactiver la vérification dans leur profil";
$help["admin_session_infini"] = "If you choose sessions indefinite in time, many people can no longer use the same account simultaneously";
$help["drop_sessions"] = "Empty the table of sessions, it alleviates the administration but forces all users to reconnect.";

$help["search_strict"] = "<font color=orange>Joueur recherché :</font><br><i>Liquid snake</i><br><font color=orange>Critère de recherche :</font><br><i>snake</i><br><br>=> <font color=lime>Résultat positif</font> si l'option \"strict\" est désactivée<br>=> <font color=red>Résultat négatif</font> si l'option \"strict\" est activée";

$help["home_commandant"] = "Page empire commander Account";

$help["profile_login"] = "Must contain between 3 and 15 characters (special characters are not accepted)";
$help["profile_pseudo_email"] = "If completed, you will receive emails from some mods";
$help["profile_main_planet"] = "The Galaxy view will open directly on this solar system";
$help["profile_password"] = "Must contain between 6 and 15 characters (special characters are not accepted)";
$help["profile_galaxy"] = "Doit contenir un nombre<br> de 1 à 999";
$help["profile_disable_ip_check"] = "La vérification de l'adresse IP permet de vous protéger contre le vol de session.<br><br>";
$help["profile_disable_ip_check"] .= "Si vous êtes déconnecté régulièrement (AOL, Proxy, etc), désactivez la vérification.<br><br>";
$help["profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activée</i>";

$help["galaxy_phalanx"] = "Load espionage reports to display the hostile phalanxes";

$help["ratio_block"] = "You have a lower ratio than the threshold, you can not access the mods";
$help["profile_speed_uni"] = "Specify the speed multiplier of your universe (1 by default)";
$help["profile_ddr"] = "Check whether the supply depot is present in your universe";
$help["astro_strict"] = "Check if the universe is old. This is to have nine planets without the corresponding technology astrophysics.";
$help["uni_arrondi"] = "For version Ogame> 5.8.5 In course project in the Gameforge.<br> Example: Distance galaxy between 1 and 9 =1G (if rounded); Remote system between 1 and 499 = 1S (if rounded).<br> Formula: dist(a,b) = ( (a,b)-unitMax ) (or unitMax = 499 (system), unitMax = 9 (galaxy).";
$help["config_cache"] = "Life config cache in seconds.";
$help["mod_cache"] = "Life mod cache second ..";

/* admin page */

$help['display_mips'] = "Shows or hides the MIP of users of OGSpy, but do appear only to those who can see protected alliances.";
$help['member_stats'] = "Shows or hides the statistical register of members down the statistics page";
$help['member_connected'] = "Affiche les (*) qui permettent de savoir qui est connecté<br>Désactivé si l'affichage des membres n'est pas activé";
$help['member_registration'] = "Shows or hides the table contenan the forum link this OGSpy";
$help['ally_name'] = "Name of the covenant of this OGSpy";
$help['forum_link'] = "Link to a section of the forum, see the PM the administrator OGSpy";
$help['first_displayed_module'] = "Module displayed when users log in this OGSpy";
$help['first_displayed_module_admin'] = "Module displayed when connecting administrators of this OGSpy";
