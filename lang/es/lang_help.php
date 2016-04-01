<?php
/**
 * Created by IntelliJ IDEA.
 * User: antho
 * Date: 05/08/2015
 * Time: 18:15
 */

$help["admin_server_status"] = "Cuando el servidor esté desactivado sólo los miembros con privilegios de administrador tienen acceso a la funcionalidad de servidor";
$help["admin_server_status_message"] = "El mensaje se mostrara a los miembros \" de base  \"mientras el servidor esté deshabilitado";
$help["admin_save_transaction"] = "Les transactions correspondent aux :<br>- Systèmes solaires<br>- Rapports d'espionnage<br>- Classements joueurs et alliances";
$help["admin_member_manager"] = "Autoriza la creación, actualización y eliminación de usuarios";
$help["admin_ranking_manager"] = "Autoriza la eliminación de los jugadores y alianzas";
$help["admin_check_ip"] = "Certains utilisateurs subissent des déconnexions intempestives (AOL, Proxy, etc).<br>Activez cette option afin qu'ils puissent désactiver la vérification dans leur profil";
$help["admin_session_infini"] = "Si eliges que las sesiones sean de forma indefinida, muchas personas no podrán utilizar la misma cuenta de forma simultánea";
$help["drop_sessions"] = "Vacíe la tabla de sesiones, para liberar a la administración ero obliga a todos los usuarios a conectarse de nuevo.";

$help["search_strict"] = "<font color=orange>Joueur recherché :</font><br><i>Liquid snake</i><br><font color=orange>Critère de recherche :</font><br><i>snake</i><br><br>=> <font color=lime>Résultat positif</font> si l'option \"strict\" est désactivée<br>=> <font color=red>Résultat négatif</font> si l'option \"strict\" est activée";

$help["home_commandant"] = "Página de Imperio de la cuenta comandante";

$help["profile_login"] = "Debe contener entre 3 y 15 caracteres (no se aceptan los caracteres especiales)";
$help["profile_pseudo_email"] = "Si se completa, recibirá in correo de algunos módulos";
$help["profile_main_planet"] = "La vista de galaxia se abrirá directamente en el sistema solar";
$help["profile_password"] = "Debe contener entre 6 y 15 caracteres (no se admiten caracteres especiales)";
$help["profile_galaxy"] = "Doit contenir un nombre<br> de 1 à 999";
$help["profile_disable_ip_check"] = "La vérification de l'adresse IP permet de vous protéger contre le vol de session.<br><br>";
$help["profile_disable_ip_check"] .= "Si vous êtes déconnecté régulièrement (AOL, Proxy, etc), désactivez la vérification.<br><br>";
$help["profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activée</i>";

$help["galaxy_phalanx"] = "Cargar los informes de espionaje para mostrar las naves enemigas";

$help["ratio_block"] = "Tienes una proporción inferior al umbral, no es posible acceder a los módulos";
$help["profile_speed_uni"] = "Especificar la velocidad de tu universo (por defecto 1)";
$help["profile_ddr"] = "Comprobar si el depósito de la alianza está presente en el universo";
$help["astro_strict"] = "Comprobar si el universo es antiguo. Esto significa disponer de 9 planetas sin tener el nivel de astrofísica correspondiente.";
$help["uni_arrondi"] = "Para la versión de OGame >5.8.5 Proyecto en curso en GameForge. <br > Ejemplo: Distancia entre la galaxia 1 y 9 = 1 Galaxia (si es circular); Distancia entre los sistemas 1 y 499 = 1 Sistema (si es circular). <br > Formula: dist(a,b)=||a-b|-unitMax| (o unitMax=499(sistema), unitMax=9(galaxia).";
$help["config_cache"] = "Configuración en segundos de la memoria caché.";
$help["mod_cache"] = "Configuración en segundos del módulo de caché.";

/* admin page */

$help['display_mips'] = "Affiche ou cache les MIP des users de OGSpy, mais ne les affichent qu'a ceux qui peuvent voir les alliances protégées.";
$help['member_stats'] = "Affiche ou cache le tableau de statistique des membres en bas de la page statistiques";
$help['member_connected'] = "Affiche les (*) qui permettent de savoir qui est connecté<br>Désactivé si l'affichage des membres n'est pas activé";
$help['member_registration'] = "Affiche ou cache le tableau contenan le lien du forum de cet OGSpy";
$help['ally_name'] = "Nom de l'alliance de cet OGSpy";
$help['forum_link'] = "Lien d'une section du forum, voir le PM de l'administrateur OGSpy";
$help['first_displayed_module'] = "Module affiché lors de la connexion des utilisateurs à cet OGSpy";
$help['first_displayed_module_admin'] = "Module affiché lors de la connexion des administrateurs de cet OGSpy";
