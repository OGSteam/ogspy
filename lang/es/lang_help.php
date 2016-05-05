<?php
/**
 * Created by IntelliJ IDEA.
 * User: antho
 * Date: 05/08/2015
 * Time: 18:15
 */

$help["admin_server_status"] = "Cuando el servidor esté desactivado sólo los miembros con privilegios de administrador tienen acceso a la funcionalidad de servidor";
$help["admin_server_status_message"] = "El mensaje se mostrara a los miembros \" de base  \"mientras el servidor esté deshabilitado";
$help["admin_save_transaction"] = "Las transacciones se corresponden a:<br>- Sistemas solares<br>- Reportes de espionaje<br>- Ranking de jugadores y alianzas";
$help["admin_member_manager"] = "Autoriza la creación, actualización y eliminación de usuarios";
$help["admin_ranking_manager"] = "Autoriza la eliminación de los jugadores y alianzas";
$help["admin_check_ip"] = "Algunos jugadores sufren desconexiones de forma inesperada (AOL, Proxy, etc.) <br> Activa esta opción para poder desctivar los controles en el perfil";
$help["admin_session_infini"] = "Si eliges que las sesiones sean de forma indefinida, muchas personas no podrán utilizar la misma cuenta de forma simultánea";
$help["drop_sessions"] = "Vacíe la tabla de sesiones, para liberar a la administración ero obliga a todos los usuarios a conectarse de nuevo.";

$help["search_strict"] = "<font color=orange>Jugador buscado :</font><br><i>Liquid snake</i><br><font color=orange>Criterio de búsqueda: </font><br><i>snake</i><br><br>=> <font color=lime>Resultado positivo</font> Si la opción \"strict\" está desactivada<br>=> <font color=red>Resultado negativo</font> Si la opción \"strict\" está activada";

$help["home_commandant"] = "Página de Imperio de la cuenta comandante";

$help["profile_login"] = "Debe contener entre 3 y 15 caracteres (no se aceptan los caracteres especiales)";
$help["profile_pseudo_email"] = "Si se completa, recibirá in correo de algunos módulos";
$help["profile_main_planet"] = "La vista de galaxia se abrirá directamente en el sistema solar";
$help["profile_password"] = "Debe contener entre 6 y 15 caracteres (no se admiten caracteres especiales)";
$help["profile_galaxy"] = "Debe contener un número <br> entre 1 y 999";
$help["profile_disable_ip_check"] = "La verificación de la dirección IP permite proteger contra el robo de sesión. <br> <br/>";
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

$help['display_mips'] = "Muestra u oculta la dirección IP de los usuarios de OGSpy pero no se visualizan las alianzas protegidas.";
$help['member_stats'] = "Visualizar un tabla de estadísticas de los miembros en base a la página de estadísticas";
$help['member_connected'] = "Visualizar los (*) que permiten saber quien está conectado<br>Desactivar si la visualización de los miembros no está activa.";
$help['member_registration'] = "Visualizar en la caché una tabla que contiene el enlace a este foro OGSpy";
$help['ally_name'] = "Nombre de la alianza en OGSpy";
$help['forum_link'] = "Enlace a una sección del foro, ver el MP del administrador de OGSpy";
$help['first_displayed_module'] = "Módulo que muestra cuando los usuarios acceden a OGSpy";
$help['first_displayed_module_admin'] = "Módulo que aparece cuando la conexión de los administradores de OGSpy";
