<?php
/**
 * Fonctions relatives aux donnees galaxies/planetes
 *
 * @package OGSpy
 * @subpackage galaxy
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.05 ($Rev: 7699 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Universe_Model;
use Ogsteam\Ogspy\Model\Rankings_Player_Model;
use Ogsteam\Ogspy\Model\Rankings_Ally_Model;
use Ogsteam\Ogspy\Model\User_Favorites_Model;
use Ogsteam\Ogspy\Model\Spy_Model;
use Ogsteam\Ogspy\Model\Combat_Report_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Helper;
use Ogsteam\Ogspy\Model\User_Technology_Model;
use Ogsteam\Ogspy\Model\User_Defense_Model;
use Ogsteam\Ogspy\Model\User_Building_Model;


use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;

/**
 * Vérification des droits OGSpy
 *
 * @param string $action Droit interrogé
 */

function galaxy_check_auth($action)
{
    global $user_data, $user_auth;

    switch ($action) {
        case "import_planet":
            if ($user_auth["ogs_set_system"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des systèmes solaires -->" . "\n");
            }
            break;

        case "export_planet":
            if ($user_auth["ogs_get_system"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des systèmes solaires -->" . "\n");
            }
            break;

        case "import_spy":
            if ($user_auth["ogs_set_spy"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des rapports d'espionnage -->" . "\n");
            }
            break;

        case "export_spy":
            if ($user_auth["ogs_get_spy"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des rapports d'espionnage -->" . "\n");
            }
            break;

        case "import_ranking":
            if ($user_auth["ogs_set_ranking"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des classements -->" . "\n");
            }
            break;

        case "export_ranking":
            if ($user_auth["ogs_get_ranking"] != 1 && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des classements -->" . "\n");
            }
            break;

        case "drop_ranking":
            if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_ranking"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        case "set_ranking":
            if (($user_auth["server_set_ranking"] != 1) && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        case "set_rc":
            if (($user_auth["server_set_rc"] != 1) && $user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        default:
            die("<!-- [ErrorFatal=18] Données transmises incorrectes  -->");
    }
}


/**
 * Affichage des galaxies
 *
 * @global int $Ogspy->Params->galaxy
 * @global int $Ogspy->Params->system
 * @global string $Ogspy->Params->coordinates
 * @global array $user_data
 * @global array $server_config
 * @return array contenant un systeme solaire correspondant a $Ogspy->Params->galaxy et $Ogspy->Params->system
 */
function galaxy_show()
{
    global $user_data, $server_config;
    global $Ogspy;
    if (isset($Ogspy->Params->coordinates)) {
        @list($Ogspy->Params->galaxy, $Ogspy->Params->system) = explode(":", $Ogspy->Params->coordinates);
    }
    if (isset($Ogspy->Params->galaxy) && isset($Ogspy->Params->system)) {
        if (intval($Ogspy->Params->galaxy) < 1) {
            $Ogspy->Params->galaxy = 1;
        }
        if (intval($Ogspy->Params->galaxy) > intval($server_config['num_of_galaxies'])) {
            $Ogspy->Params->galaxy = intval($server_config['num_of_galaxies']);
        }
        if (intval($Ogspy->Params->system) < 1) {
            $Ogspy->Params->system = 1;
        }
        if (intval($Ogspy->Params->system) > intval($server_config['num_of_systems'])) {
            $Ogspy->Params->system = intval($server_config['num_of_systems']);
        }
    }
    if (!isset($Ogspy->Params->galaxy) || !isset($Ogspy->Params->system)) {
        $Ogspy->Params->galaxy = $user_data["user_galaxy"];
        $Ogspy->Params->system = $user_data["user_system"];
        if ($Ogspy->Params->galaxy == 0 || $Ogspy->Params->system == 0) {
            $Ogspy->Params->galaxy = 1;
            $Ogspy->Params->system = 1;
        }
    }
    $Universe_Model = new Universe_Model();
    $population = $Universe_Model->get_system($Ogspy->Params->galaxy, $Ogspy->Params->system, $Ogspy->Params->system);
    $population = filter_system($population[$Ogspy->Params->system]);
    return array("population" => $population, "galaxy" => $Ogspy->Params->galaxy, "system" => $Ogspy->Params->system);
}

/**
 * @param $system
 * @return mixed
 */
function filter_system($system)
{
    global $server_config;
    $Spy_Model = new Spy_Model();
    $Combat_Report_Model = new Combat_Report_Model();
    $allied = array();
    if ($server_config["allied"] != "") {
        $allied = explode(",", $server_config["allied"]);
    }
    foreach ($system as $planet) {
        $report_spy = $Spy_Model->get_nb_spy_by_planet($planet['galaxy'], $planet['system'], $planet['row']);
        $report_rc = $Combat_Report_Model->get_nb_combat_report_by_planet($planet['galaxy'], $planet['system'], $planet['row']);
        $planet["report_spy"] = $planet["report_rc"] = $planet["hided"] = $planet["allied"] = "";
        $friend = in_array($planet['ally'], $allied);
        $planet["report_spy"] = $report_spy;
        $planet["report_rc"] = $report_rc;
        $planet["allied"] = $friend;
        $system[$planet['row']] = $planet;
    }
    return $system;
}


/**
 * Affichage des systemes
 *
 * @global int $Ogspy->Params->galaxy
 * @global int $Ogspy->Params->system_down
 * @global int $Ogspy->Params->system_up
 * @global array $user_data
 * @global array $user_auth
 * @global array $server_config
 * @return array contenant les  systeme solaire compris entre $Ogspy->Params->system_down et $Ogspy->Params->system_up
 */
function galaxy_show_sector()
{
    global $server_config;
    global $Ogspy;
    if (isset($Ogspy->Params->galaxy) && isset($Ogspy->Params->system_down) && isset($Ogspy->Params->system_up)) {
        if (intval($Ogspy->Params->galaxy) < 1) {
            $Ogspy->Params->galaxy = 1;
        }
        if (intval($Ogspy->Params->galaxy) > intval($server_config['num_of_galaxies'])) {
            $Ogspy->Params->galaxy = intval($server_config['num_of_galaxies']);
        }
        if (intval($Ogspy->Params->system_down) < 1) {
            $Ogspy->Params->system_down = 1;
        }
        if (intval($Ogspy->Params->system_down) > intval($server_config['num_of_systems'])) {
            $Ogspy->Params->system_down = intval($server_config['num_of_systems']);
        }
        if (intval($Ogspy->Params->system_up) < 1) {
            $Ogspy->Params->system_up = 1;
        }
        if (intval($Ogspy->Params->system_up) > intval($server_config['num_of_systems'])) {
            $Ogspy->Params->system_up = intval($server_config['num_of_systems']);
        }
    }
    if (!isset($Ogspy->Params->galaxy) || !isset($Ogspy->Params->system_down) || !isset($Ogspy->Params->system_up)) {
        $Ogspy->Params->galaxy = 1;
        $Ogspy->Params->system_down = 1;
        $Ogspy->Params->system_up = 25;
    }
    $Universe_Model = new Universe_Model();
    $population = $Universe_Model->get_system($Ogspy->Params->galaxy, $Ogspy->Params->system_down, $Ogspy->Params->system_up);
    for ($system = $Ogspy->Params->system_down; $system <= $Ogspy->Params->system_up; $system++) {
        $population[$system] = filter_system($population[$system]);
        $population[$system]['timestamp'] = $population[$system][1]['timestamp'];
    }
    return array("population" => $population, "galaxy" => $Ogspy->Params->galaxy, "system_down" => $Ogspy->Params->system_down, "system_up" => $Ogspy->Params->system_up);
}

/**
 * Fonctions de recherches
 *
 * @global array $user_data
 * @global array $user_auth
 * @global array $server_config
 * @global string $Ogspy->Params->string_search
 * @global string $Ogspy->Params->type_search type de recherche a effectuer : (player|ally|planet|colonization|moon|away)
 * @global int $Ogspy->Params->strict
 * @global int $Ogspy->Params->sort (0|1|2) ordre des resultats (order by galaxy/system/row|order by ally/player/galaxy/systems/row|order by player/galaxy/system/row)
 * @global int $Ogspy->Params->sort2 : (0|1) ordre des resultats recherche (asc|desc)
 * @global int $Ogspy->Params->galaxy_down
 * @global int $Ogspy->Params->galaxy_up
 * @global int $Ogspy->Params->system_down
 * @global int $Ogspy->Params->system_up
 * @global int $Ogspy->Params->row_down
 * @global int $Ogspy->Params->row_up
 * @global ??? $Ogspy->Params->row_active
 * @global int $Ogspy->Params->page page courante ( pagination )
 * @return array resultat de la recherche + numero de la page
 */
function galaxy_search()
{
    //todo voir possible pb recherche strict ou non
    global $user_data, $server_config;
    global $Ogspy ;
    if (!check_var($Ogspy->Params->type_search, "Char") || !check_var($Ogspy->Params->strict, "Char") || !check_var($Ogspy->Params->sort, "Num") || !check_var($Ogspy->Params->sort2, "Num") || !check_var($Ogspy->Params->galaxy_down, "Num") || !check_var($Ogspy->Params->galaxy_up, "Num") || !check_var($Ogspy->Params->system_down, "Num") || !check_var($Ogspy->Params->system_up, "Num") || !check_var($Ogspy->Params->row_down, "Num") || !check_var($Ogspy->Params->row_up, "Num") || !check_var($Ogspy->Params->row_active, "Char") || !check_var($Ogspy->Params->page, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    $search_result = array();
    $total_page = 0;
    $allied = array();
    if ($server_config["allied"] != "") {
        $allied = explode(",", $server_config["allied"]);
    }
    $protected = array();
    if ($server_config["ally_protection"] != "") {
        $protected = explode(",", $server_config["ally_protection"]);
    }

    if (!isset($Ogspy->Params->type_search) || (!isset($Ogspy->Params->string_search) && (!isset($Ogspy->Params->galaxy_down) || !isset($Ogspy->Params->galaxy_up) || !isset($Ogspy->Params->system_down) || !isset($Ogspy->Params->system_up) || !isset($Ogspy->Params->row_down) || !isset($Ogspy->Params->row_up)))) {
        return array($search_result, $total_page);
    }
    $data_user = new User_Model();
    $data_user->add_stat_search_made($user_data['user_id'], 1);
    $universeRepository = new Universe_Model();
    $criteria = new SearchCriteria_Helper($server_config);
    if (isset($Ogspy->Params->galaxy_down) && isset($Ogspy->Params->galaxy_up)) {
        $criteria->setGalaxyDown(intval($Ogspy->Params->galaxy_down));
        $criteria->setGalaxyUp(intval($Ogspy->Params->galaxy_up));
    }
    if (isset($Ogspy->Params->system_down) && isset($Ogspy->Params->system_up)) {
        $criteria->setSystemDown(intval($Ogspy->Params->system_down));
        $criteria->setSystemUp(intval($Ogspy->Params->system_up));
    }
    if ($Ogspy->Params->row_active && isset($Ogspy->Params->row_down) && isset($Ogspy->Params->row_up)) {
        $criteria->setRowDown(intval($Ogspy->Params->row_down));
        $criteria->setRowUp(intval($Ogspy->Params->row_up));
    }
    switch ($Ogspy->Params->type_search) {
        case "player":
            if ($Ogspy->Params->string_search == "") {
                break;
            }
            $search = isset($Ogspy->Params->strict) ? $Ogspy->Params->string_search : "%" . $Ogspy->Params->string_search . "%";
            $criteria->setPlayerName($search);
            break;
        case "ally":
            if ($Ogspy->Params->string_search == "") {
                break;
            }
            $search = isset($Ogspy->Params->strict) ? $Ogspy->Params->string_search : "%" . $Ogspy->Params->string_search . "%";
            $criteria->setAllyName($search);
            break;
        case "planet":
            if ($Ogspy->Params->string_search == "") {
                break;
            }
            $search = isset($Ogspy->Params->strict) ? $Ogspy->Params->string_search : "%" . $Ogspy->Params->string_search . "%";
            $criteria->setPlanetName($search);
            break;
        case "colonization":
            $criteria->setPlanetName("");
            break;
        case "moon":
            $criteria->setIsMoon(true);
            break;
        case "away":
            $criteria->setIsInactive(true);
            break;
    }
    if (!$criteria->isValid()) {
        return array($search_result, $total_page);
    }
    if (!isset($Ogspy->Params->sort2)) {
        $Ogspy->Params->sort2 = "0";
    }
    switch ($Ogspy->Params->sort2) {
        case "1":
            $order2 = " DESC";
            break;
        default:
            $order2 = " ASC";
            break;
    }
    if (!isset($Ogspy->Params->sort)) {
        $Ogspy->Params->sort = "1";
    }
    switch ($Ogspy->Params->sort) {
        case "2":
            $order = array('ally' => $order2, 'player' => $order2, 'galaxy' => $order2, 'system' => $order2, 'row' => $order2);
            break;
        case "3":
            $order = array('player' => $order2, 'galaxy' => $order2, 'system' => $order2, 'row' => $order2);
            break;
        default:
            $order = array('galaxy' => $order2, 'system' => $order2, 'row' => $order2);
            break;
    }
    if (!isset($Ogspy->Params->page)) {
        $Ogspy->Params->page = 1;
    }
    $number = 30;
    $limit = intval($Ogspy->Params->page - 1) * $number;
    if ($limit < 0) {
        $limit = 0;
        $Ogspy->Params->page = 1;
    }
    $result = $universeRepository->find($criteria, $order, $limit, $number);
    $total_page = ceil($result['total_row'] / $number);
    $search_result = array();
    foreach ($result['planets'] as $planet) {
        $friend = false;
        if (in_array($planet["ally"], $allied)) {
            $friend = true;
        }
        $hided = false;
        if (in_array($planet["ally"], $protected)) {
            $hided = true;
        }
        $data_spy = new Spy_Model();
        $nb_spy_reports = $data_spy->get_nb_spy_by_planet($planet["galaxy"], $planet["system"], $planet["row"]);
        $search_result[] = array("galaxy" => $planet["galaxy"],
            "system" => $planet["system"],
            "row" => $planet["row"],
            "phalanx" => $planet["phalanx"],
            "gate" => $planet["gate"],
            "last_update_moon" => $planet["last_update_moon"],
            "moon" => $planet["moon"],
            "ally" => $planet["ally"],
            "player" => $planet["player"],
            "report_spy" => $nb_spy_reports,
            "status" => $planet["status"],
            "timestamp" => $planet["last_update"],
            "poster" => $planet["user_name"],
            "allied" => $friend,
            "hided" => $hided
        );
    }
    return array($search_result, $total_page);
}

/**
 * Recuperation des statistiques des galaxies
 *
 * @param int $step
 * @global       object mysql $db
 * @global array $user_data
 * @global array $server_config
 * @return array contenant planete colonise ou non, par galaxy / systems
 */
function galaxy_statistic($step = 50)
{
    global $db, $user_data, $server_config;

    $Universe_Model = new Universe_Model();


    $nb_planets_total = 0;
    $nb_freeplanets_total = 0;
    for ($galaxy = 1; $galaxy <= $server_config['num_of_galaxies']; $galaxy++) {
        for ($system = 1; $system <= $server_config['num_of_systems']; $system = $system + $step) {
            // nb planet
            $nb_planet = $Universe_Model->get_nb_planets($galaxy, $system, $system + $step - 1);
            $nb_planets_total += $nb_planet;
            //nb libre
            $nb_planet_free = $Universe_Model->get_nb_empty_planets($galaxy, $system, $system + $step - 1);
            $nb_freeplanets_total += $nb_planet_free;

            $new = false;

            $last_update = $Universe_Model->get_last_update($galaxy, $system, ($system + $step - 1));
            if ($last_update > $user_data["session_lastvisit"]) {
                $new = true;
            }
            $statistics[$galaxy][$system] = array(
                "planet" => $nb_planet,
                "free" => $nb_planet_free,
                "new" => $new
            );
        }
    }

    return array(
        "map" => $statistics,
        "nb_planets" => $nb_planets_total,
        "nb_planets_free" => $nb_freeplanets_total
    );
}

/**
 * Listing des alliances
 *
 * @return array contenant les noms des alliances
 */
function galaxy_ally_listing()
{
    $ally_list = (new Universe_Model())->get_ally_list();
    return $ally_list;
}

/**
 * Recuperation positions alliance
 *
 * @param int $step
 * @global       object mysql $db
 * @global array $user_data
 * @global array $user_auth
 * @global array $server_config
 * @global array $Ogspy->Params->ally_
 * @global int $nb_colonnes_ally
 * @return array $statictics contenant la position de tous les joueurs de toutes les alliances non protegers par galaxie / systeme
 */
function galaxy_ally_position($step = 50)
{
    global $user_auth, $user_data, $server_config;
    global $Ogspy;

    $Universe_Model = new Universe_Model();

    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        if (!check_var($Ogspy->Params->ally_[$i], "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        if (!isset($Ogspy->Params->ally_[$i])) {
            return array();
        }
    }

    $Ogspy->Params->ally_protection = $allied = array();
    if ($server_config["ally_protection"] != "") {
        $Ogspy->Params->ally_protection = explode(",", $server_config["ally_protection"]);
    }
    if ($server_config["allied"] != "") {
        $allied = explode(",", $server_config["allied"]);
    }

    $statistics = array();
    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        $Ogspy->Params->ally_list[$i - 1] = $Ogspy->Params->ally_[$i];
    }

    foreach ($Ogspy->Params->ally_list as $Ogspy->Params->ally_name) {
        if ($Ogspy->Params->ally_name == "") {
            continue;
        }
        if (in_array($Ogspy->Params->ally_name, $Ogspy->Params->ally_protection) && $user_auth["server_show_positionhided"] == 0 && $user_data["user_admin"] == 0 && $user_data["user_coadmin"] == 0) {
            $statistics[$Ogspy->Params->ally_name][0][0] = null;
            continue;
        }
        $friend = false;
        if (in_array($Ogspy->Params->ally_name, $allied)) {
            $friend = true;
        }

        for ($galaxy = 1; $galaxy <= $server_config['num_of_galaxies']; $galaxy++) {
            for ($system = 1; $system <= $server_config['num_of_systems']; $system = $system + $step) {

                $population = array();
                $population = $Universe_Model->get_ally_position($galaxy, $system, ($system + $step - 1), $Ogspy->Params->ally_name);
                $nb_planet = $Universe_Model->sql_affectedrows();
                //$nb_planet =  count($population);

                $statistics[$Ogspy->Params->ally_name][$galaxy][$system] = array("planet" => $nb_planet, "population" => $population);
            }
        }
    }
    user_set_stat(null, null, 1);

    return $statistics;
}

/**
 * Recuperation des rapports d\'espionnage
 *
 * @return array $reports
 * @global array $server_config
 * @global int $Ogspy->Params->galaxy
 * @global int $Ogspy->Params->system
 * @global int $Ogspy->Params->row
 * @global int $Ogspy->Params->spy_id
 */
function galaxy_reportspy_show()
{
    global $Ogspy, $server_config;
    //todo $Ogspy->Params->spy_id a gerer ?

    if (!check_var($Ogspy->Params->galaxy, "Num") || !check_var($Ogspy->Params->system, "Num") || !check_var($Ogspy->Params->row, "Num")) {
        return false;
    }

    if (!isset($Ogspy->Params->galaxy) || !isset($Ogspy->Params->system) || !isset($Ogspy->Params->row)) {
        return false;
    }
    if ((int)$Ogspy->Params->galaxy < 1 || (int)$Ogspy->Params->galaxy > (int)$server_config['num_of_galaxies'] || (int)$Ogspy->Params->system < 1 || (int)$Ogspy->Params->system > (int)$server_config['num_of_systems'] || (int)$Ogspy->Params->row < 1 || (int)$Ogspy->Params->row > 15) {
        return false;
    }


    $Spy_Model = new Spy_Model();
    $spy_list = $Spy_Model->get_spy_id_list_by_planet(intval($Ogspy->Params->galaxy), intval($Ogspy->Params->system), intval($Ogspy->Params->row));
    $reports = array();
    foreach ($spy_list as $row) {
        $data = UNparseRE($row["id_spy"]);
        $reports[] = array("spy_id" => $row["id_spy"], "sender" => $row["user_name"], "data" => $data, "moon" => $row['is_moon'], "dateRE" => $row['$dateRE']);
    }
    return $reports;
}

/**
 * Recuperation des rapports de combat
 *
 * @global       object mysql $db
 * @global array $server_config
 * @global int $Ogspy->Params->galaxy
 * @global int $Ogspy->Params->system
 * @global int $Ogspy->Params->row
 * @global int $Ogspy->Params->rc_id
 * @return array $reports contenant les rc mis en forme
 */
function galaxy_reportrc_show()
{
    global $Ogspy, $server_config;

    if (!check_var($Ogspy->Params->galaxy, "Num") || !check_var($Ogspy->Params->system, "Num") || !check_var($Ogspy->Params->row, "Num")) {
        return false;
    }

    if (!isset($Ogspy->Params->galaxy) || !isset($Ogspy->Params->system) || !isset($Ogspy->Params->row)) {
        return false;
    }
    if (intval($Ogspy->Params->galaxy) < 1 || intval($Ogspy->Params->galaxy) > intval($server_config['num_of_galaxies']) || intval($Ogspy->Params->system) < 1 || intval($Ogspy->Params->system) > intval($server_config['num_of_systems']) || intval($Ogspy->Params->row) < 1 || intval($Ogspy->Params->row) > 15) {
        return false;
    }

    $Combat_Report_Model = new Combat_Report_Model();
    $report_list = $Combat_Report_Model->get_cr_id_list_by_planet(intval($Ogspy->Params->galaxy), intval($Ogspy->Params->system), intval($Ogspy->Params->row));

    $reports = array();
    foreach ($report_list as $report_id) {
        $reports[] = UNparseRC($report_id);
    }
    return $reports;
}

/**
 * Purge des rapports d\'espionnage
 *
 * @global array $server_config
 *
 */
function galaxy_purge_spy()
{
    global $server_config;

    if (!is_numeric($server_config["max_keepspyreport"])) {
        return;
    }
    $max_keepspyreport = intval($server_config["max_keepspyreport"]);

    $Spy_Model = new Spy_Model();
    $Spy_Model->delete_expired_spies((time() - 60 * 60 * 24 * $max_keepspyreport));
}

/**
 * Recuperation des systemes favoris
 *
 * @global array $user_data
 * @return array $favorite (galaxy/system)
 */
function galaxy_getfavorites()
{
    global $user_data;
    $favorite = (new User_Favorites_Model())->select_user_favorites($user_data["user_id"]);
    return $favorite;
}


/**
 * Affichage classement
 *
 * @param      $model
 * @param      $ranking_table
 * @param null $date
 * @return int
 */
function galaxy_show_ranking($model, $ranking_table, $date = null)
{
    global $Ogspy;
    // Récupération de la taille max des tableaux
    $data_rankings = $model;
    //Récupération de la dernière date de classement
    if ($date == null) {
        $last_ranking = $data_rankings->get_rank_latest_table_date($ranking_table);
    } else {
        $last_ranking = $Ogspy->Params->date;
    }
    if ($last_ranking == null) {
        return -1;
    }
    // Pas de classement disponible
    $ranking = $data_rankings->get_all_ranktable_bydate($last_ranking, 1, 99999);
    return $ranking;
}


/**
 * Affichage classement des joueurs
 *
 * @global string $Ogspy->Params->order_by general|eco|techno|military|military_b|military_l|military_d|honnor
 * @global int $Ogspy->Params->date timestamp du classement voulu
 * @global int $Ogspy->Params->interval
 * @return array array($order, $ranking, $ranking_available, $maxrank);
 *
 * todo revoir entierement affichage de la vue pour simplifier/ameliorer cette fonction,
 * todo verifiction siregression 3.3.4 / 3.3.5 => gain de perf enorme mais si rien dans table general, pas d 'affichage classement (inner join)
 */
function galaxy_show_ranking_player()
{
    global $Ogspy;

    $Rankings_Player_Model = new Rankings_Player_Model();

    if (!isset($Ogspy->Params->order_by)) {
        $Ogspy->Params->order_by = "general";
    }
    $tables = $Rankings_Player_Model->get_rank_tables();
    $name = $Rankings_Player_Model->get_rank_table_ref();

    // verification de la variable pub_order
    if (!in_array($Ogspy->Params->order_by, $name)) {
        $Ogspy->Params->order_by = "general";
    }


    // selection du max rank
    $maxrank = max($Rankings_Player_Model->select_max_rank_row());


    if (!isset($Ogspy->Params->interval)) {
        $Ogspy->Params->interval = 1;
    }
    if (($Ogspy->Params->interval - 1) % 100 != 0 || $Ogspy->Params->interval > $maxrank) {
        $Ogspy->Params->interval = 1;
    }
    $limit_down = $Ogspy->Params->interval;
    $limit_up = $Ogspy->Params->interval + 99;

    $order = array();
    $ranking = array();
    $ranking_available = array();
    $table = array();

    // on determine l id du pub order
    $id = (array_keys($name, $Ogspy->Params->order_by));
    $orderTableName = $tables[$id[0]];
    $orderStringName = $name[$id[0]];

    if (!isset($Ogspy->Params->date)) {
        $last_ranking = $Rankings_Player_Model->get_rank_latest_table_date($orderTableName);

    } else {
        $last_ranking = $Ogspy->Params->date;
    }


    $all_ranktable_bydate = $Rankings_Player_Model->get_all_ranktable_bydate($last_ranking, $limit_down, $limit_up, $orderStringName);


    // reconstruction des tableaux tel que demandé dans page vue ...
    foreach ($all_ranktable_bydate as $ranktable_bydate) {
        // recuperatio du nom
        $currentPlayer = $ranktable_bydate["player_name"];

        //récuperation du rank par defaut
        $order[$ranktable_bydate["postion"]] = $currentPlayer;

        $ranking[$currentPlayer]["ally"] = $ranktable_bydate["ally_name"];
        $ranking[$currentPlayer]["sender"] = "none"; // todo est ce util ?

        $ranking[$currentPlayer]["general"] = array("rank" => $ranktable_bydate["general_rank"], "points" => $ranktable_bydate["general_pts"]);
        $ranking[$currentPlayer]["eco"] = array("rank" => $ranktable_bydate["eco_rank"], "points" => $ranktable_bydate["eco_pts"]);
        $ranking[$currentPlayer]["techno"] = array("rank" => $ranktable_bydate["tech_rank"], "points" => $ranktable_bydate["tech_pts"]);
        $ranking[$currentPlayer]["honnor"] = array("rank" => $ranktable_bydate["milh_rank"], "points" => $ranktable_bydate["milh_pts"]);

        $ranking[$currentPlayer]["military"] = array("rank" => $ranktable_bydate["mil_rank"], "points" => $ranktable_bydate["mil_pts"]);
        $ranking[$currentPlayer]["military_b"] = array("rank" => $ranktable_bydate["milb_rank"], "points" => $ranktable_bydate["milb_pts"]);
        $ranking[$currentPlayer]["military_l"] = array("rank" => $ranktable_bydate["mill_rank"], "points" => $ranktable_bydate["mill_pts"]);
        $ranking[$currentPlayer]["military_d"] = array("rank" => $ranktable_bydate["mild_rank"], "points" => $ranktable_bydate["mild_pts"]);

    }

    $ranking_available = $Rankings_Player_Model->get_all_distinct_date_ranktable($orderTableName);
    $ranking_available = array_unique($ranking_available);

    return array($order, $ranking, $ranking_available, $maxrank);
}

/**
 * Affichage classement des alliances
 *
 * @global string $Ogspy->Params->order_by general|eco|techno|military|military_b|military_l|military_d|honnor
 * @global int $Ogspy->Params->date timestamp du classement voulu
 * @global int $Ogspy->Params->interval
 * @global int $Ogspy->Params->suborder : member
 * todo revoir entierement affichage de la vue pour simplifier/ameliorer cette fonction,
 * todo verifiction siregression 3.3.4 / 3.3.5 => gain de perf enorme mais si rien dans table general, pas d 'affichage classement (inner join) * @return array array($order, $ranking, $ranking_available, $maxrank)
 */
function galaxy_show_ranking_ally()
{
    global $Ogspy;
    $Rankings_Ally_Model = new Rankings_Ally_Model();

    if (!check_var($Ogspy->Params->order_by, "Char") || !check_var($Ogspy->Params->date, "Num") || !check_var($Ogspy->Params->interval, "Num") || !check_var($Ogspy->Params->suborder, "Char")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $tables = $Rankings_Ally_Model->get_rank_tables();
    $name = $Rankings_Ally_Model->get_rank_table_ref();

    // verification de la variable pub_order_by
    if (!in_array($Ogspy->Params->order_by, $name)) {
        $Ogspy->Params->order_by = "general";
    }

    // selection de rank max !
    $maxrank = max($Rankings_Ally_Model->select_max_rank_row());

    if (isset($Ogspy->Params->suborder) && $Ogspy->Params->suborder == "member") {
        $Ogspy->Params->order_by2 = "points_per_member desc";
    } else {
        $Ogspy->Params->order_by2 = "rank";
    }

    if (!isset($Ogspy->Params->interval)) {
        $Ogspy->Params->interval = 1;
    }
    if (($Ogspy->Params->interval - 1) % 100 != 0 || $Ogspy->Params->interval > $maxrank) {
        $Ogspy->Params->interval = 1;
    }
    $limit_down = $Ogspy->Params->interval;
    $limit_up = $Ogspy->Params->interval + 99;

    $order = array();
    $ranking = array();
    $ranking_available = array();
    $table = array();

    // on determine l id du pub order
    $id = (array_keys($name, $Ogspy->Params->order_by));
    $orderTableName = $tables[$id[0]];
    $orderStringName = $name[$id[0]];

    if (!isset($Ogspy->Params->date)) {
        $last_ranking = $Rankings_Ally_Model->get_rank_latest_table_date($orderTableName);

    } else {
        $last_ranking = $Ogspy->Params->date;
    }
    $all_ranktable_bydate = $Rankings_Ally_Model->get_all_ranktable_bydate($last_ranking, $limit_down, $limit_up, $orderStringName);

    // reconstruction des tableaux tel que demandé dans page vue ...
    foreach ($all_ranktable_bydate as $ranktable_bydate) {
        // recuperatio du nom
        $currentAlly = $ranktable_bydate["ally_name"];

        //récuperation du rank par defaut
        $order[$ranktable_bydate["postion"]] = $currentAlly;

        $ranking[$currentAlly]["number_member"] = $ranktable_bydate["member"];
        $ranking[$currentAlly]["ally"] = $ranktable_bydate["ally_name"];
        $ranking[$currentAlly]["sender"] = "none"; // todo est ce util ?

        $ranking[$currentAlly]["general"] = array("rank" => $ranktable_bydate["general_rank"], "points" => $ranktable_bydate["general_pts"]);
        $ranking[$currentAlly]["eco"] = array("rank" => $ranktable_bydate["eco_rank"], "points" => $ranktable_bydate["eco_pts"]);
        $ranking[$currentAlly]["techno"] = array("rank" => $ranktable_bydate["tech_rank"], "points" => $ranktable_bydate["tech_pts"]);
        $ranking[$currentAlly]["honnor"] = array("rank" => $ranktable_bydate["milh_rank"], "points" => $ranktable_bydate["milh_pts"]);

        $ranking[$currentAlly]["military"] = array("rank" => $ranktable_bydate["mil_rank"], "points" => $ranktable_bydate["mil_pts"]);
        $ranking[$currentAlly]["military_b"] = array("rank" => $ranktable_bydate["milb_rank"], "points" => $ranktable_bydate["milb_pts"]);
        $ranking[$currentAlly]["military_l"] = array("rank" => $ranktable_bydate["mill_rank"], "points" => $ranktable_bydate["mill_pts"]);
        $ranking[$currentAlly]["military_d"] = array("rank" => $ranktable_bydate["mild_rank"], "points" => $ranktable_bydate["mild_pts"]);
    }

    $ranking_available = $Rankings_Ally_Model->get_all_distinct_date_ranktable($orderTableName);
    $ranking_available = array_unique($ranking_available);

    return array($order, $ranking, $ranking_available, $maxrank);
}

/**
 * Affichage classement d'un joueur particulier
 *
 * @param string $player nom du joueur recherche
 * @param boolean $last le dernier classement ou tous les classements
 * @return array $ranking
 */
function galaxy_show_ranking_unique_player($player, $last = false)
{

    $ranking = array();
    $tRanking = (new Rankings_Player_Model())->get_all_ranktable_byplayer($player);
    foreach ($tRanking as $rank) {
        $ranking[$rank["datadate"]]["general"]["rank"] = $rank["general_rank"];
        $ranking[$rank["datadate"]]["general"]["points"] = $rank["general_rank"];

        if ((int)$rank["eco_rank"] > 0) {
            $ranking[$rank["datadate"]]["eco"]["rank"] = $rank["eco_rank"];
            $ranking[$rank["datadate"]]["eco"]["points"] = $rank["eco_pts"];
        }

        if ((int)$rank["tech_rank"] > 0) {
            $ranking[$rank["datadate"]]["techno"]["rank"] = $rank["tech_rank"];
            $ranking[$rank["datadate"]]["techno"]["points"] = $rank["tech_pts"];
        }

        if ((int)$rank["milh_rank"] > 0) {
            $ranking[$rank["datadate"]]["honnor"]["rank"] = $rank["milh_rank"];
            $ranking[$rank["datadate"]]["honnor"]["points"] = $rank["milh_pts"];
        }

        if ((int)$rank["mil_rank"] > 0) {
            $ranking[$rank["datadate"]]["military"]["rank"] = $rank["mil_rank"];
            $ranking[$rank["datadate"]]["military"]["points"] = $rank["mil_pts"];
        }

        if ((int)$rank["milb_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_b"]["rank"] = $rank["milb_rank"];
            $ranking[$rank["datadate"]]["military_b"]["points"] = $rank["milb_pts"];
        }

        if ((int)$rank["mill_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_l"]["rank"] = $rank["mill_rank"];
            $ranking[$rank["datadate"]]["military_l"]["points"] = $rank["mill_pts"];
        }

        if ((int)$rank["mild_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_d"]["rank"] = $rank["mild_rank"];
            $ranking[$rank["datadate"]]["military_d"]["points"] = $rank["mild_pts"];
        }

        if ($last) {
            break;
        }
    }

    return $ranking;
}

/**
 * Affichage classement d'un joueur particulier formatage pour chart_js
 *
 * @param string $player nom du joueur recherche
 * @param boolean $last le dernier classement ou tous les classements
 * @return array $ranking
 */
function galaxy_show_ranking_unique_player_forJS($player, $date_min = null, $date_max = null, $last = false)
{

    $ranking = array();
    $tRanking = (new Rankings_Player_Model())->get_all_ranktable_byplayer($player);
    foreach ($tRanking as $rank) {
        if ($rank["datadate"] >= $date_min && $rank["datadate"] <= $date_max) // ajouter dans la requete ca serait top
        {

            $ranking["rank"]["general (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["general_rank"] . "]";
            $ranking["points"]["general (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["general_pts"] . "]";

            if ((int)$rank["eco_rank"] > 0) {
                $ranking["rank"]["Economique (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["eco_rank"] . "]";
                $ranking["points"]["Economique (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["eco_pts"] . "]";
            }

            if ((int)$rank["tech_rank"] > 0) {
                $ranking["rank"]["Recherche (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["tech_rank"] . "]";
                $ranking["points"]["Recherche (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["tech_pts"] . "]";
            }

            if ((int)$rank["milh_rank"] > 0) {
                $ranking["rank"]["Honneur (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milh_rank"] . "]";
                $ranking["points"]["Honneur (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milh_pts"] . "]";
            }

            if ((int)$rank["mil_rank"] > 0) {
                $ranking["rank"]["Militaire (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mil_rank"] . "]";
                $ranking["points"]["Militaire (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mil_pts"] . "]";
            }
            if ((int)$rank["milb_rank"] > 0) {
                $ranking["rank"]["Militaire Construits (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milb_rank"] . "]";
                $ranking["points"]["Militaire Construits (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milb_pts"] . "]";
            }

            if ((int)$rank["mill_rank"] > 0) {
                $ranking["rank"]["Perte militaire (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mill_rank"] . "]";
                $ranking["points"]["Perte militaire (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mill_pts"] . "]";
            }

            if ((int)$rank["mild_rank"] > 0) {
                $ranking["rank"]["destruction (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mild_rank"] . "]";
                $ranking["points"]["destruction (" . $player . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mild_pts"] . "]";
            }

            if ($last) {
                break;
            }
        }


    }

    return $ranking;
}

/**
 * Affichage classement d\'une ally particuliere
 *
 * @param string $ally nom de l alliance recherche
 * @param boolean $last le dernier classement ou tous les classements
 * @return array $ranking
 */
function galaxy_show_ranking_unique_ally($ally, $last = false)
{
    $ranking = array();
    $tRanking = (new Rankings_Ally_Model())->get_all_ranktable_byally($ally);
    // formatage pour la vue
    foreach ($tRanking as $rank) {
        $ranking[$rank["datadate"]]["number_member"] = $rank["member"];

        $ranking[$rank["datadate"]]["general"]["rank"] = $rank["general_rank"];
        $ranking[$rank["datadate"]]["general"]["points"] = $rank["general_pts"];
        $ranking[$rank["datadate"]]["general"]["points_per_member"] = $rank["general_pts_mb"];

        if ((int)$rank["eco_rank"] > 0) {
            $ranking[$rank["datadate"]]["eco"]["rank"] = $rank["eco_rank"];
            $ranking[$rank["datadate"]]["eco"]["points"] = $rank["eco_pts"];
            $ranking[$rank["datadate"]]["eco"]["points_per_member"] = $rank["eco_pts_mb"];
        }

        if ((int)$rank["tech_rank"] > 0) {
            $ranking[$rank["datadate"]]["techno"]["rank"] = $rank["tech_rank"];
            $ranking[$rank["datadate"]]["techno"]["points"] = $rank["tech_pts"];
            $ranking[$rank["datadate"]]["techno"]["points_per_member"] = $rank["tech_pts_mb"];
        }

        if ((int)$rank["milh_rank"] > 0) {
            $ranking[$rank["datadate"]]["honnor"]["rank"] = $rank["milh_rank"];
            $ranking[$rank["datadate"]]["honnor"]["points"] = $rank["milh_pts"];
            $ranking[$rank["datadate"]]["honnor"]["points_per_member"] = $rank["milh_pts_mb"];
        }

        if ((int)$rank["mil_rank"] > 0) {
            $ranking[$rank["datadate"]]["military"]["rank"] = $rank["mil_rank"];
            $ranking[$rank["datadate"]]["military"]["points"] = $rank["mil_pts"];
            $ranking[$rank["datadate"]]["military"]["points_per_member"] = $rank["mil_pts_mb"];
        }

        if ((int)$rank["milb_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_b"]["rank"] = $rank["milb_rank"];
            $ranking[$rank["datadate"]]["military_b"]["points"] = $rank["milb_pts"];
            $ranking[$rank["datadate"]]["military_b"]["points_per_member"] = $rank["milb_pts_mb"];
        }

        if ((int)$rank["mill_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_l"]["rank"] = $rank["mill_rank"];
            $ranking[$rank["datadate"]]["military_l"]["points"] = $rank["mill_pts"];
            $ranking[$rank["datadate"]]["military_l"]["points_per_member"] = $rank["mill_pts_mb"];
        }

        if ((int)$rank["mild_rank"] > 0) {
            $ranking[$rank["datadate"]]["military_d"]["rank"] = $rank["mild_rank"];
            $ranking[$rank["datadate"]]["military_d"]["points"] = $rank["mild_pts"];
            $ranking[$rank["datadate"]]["military_d"]["points_per_member"] = $rank["mild_pts_mb"];
        }

        if ($last) {
            break;
        }
    }

    return $ranking;
}

/**
 * Suppression automatique de classements joueurs & alliances
 *
 * @global array $server_config
 */
function galaxy_purge_ranking()
{
    global $server_config;

    if (!is_numeric($server_config["max_keeprank"])) {
        return;
    }
    $max_keeprank = intval($server_config["max_keeprank"]);
    $Rankings_Player_Model = new Rankings_Player_Model();

    $rank_tables = $Rankings_Player_Model->get_rank_tables();

    if ($server_config["keeprank_criterion"] == "day") {
        // classement joueur
        $removeDatadate = (time() - 60 * 60 * 24 * $max_keeprank);
        $Rankings_Player_Model->remove_all_rank_older_than($removeDatadate);
    }

    if ($server_config["keeprank_criterion"] == "quantity") {

        foreach ($rank_tables as $table) {
            // récuperation des datadate en table
            $ranking_available = $Rankings_Player_Model->get_all_distinct_date_ranktable($table);
            if (count($ranking_available) > $max_keeprank) {
                /// dans ce cas, suppression des datas
                $removeDatadate = $ranking_available[$max_keeprank]; // recuperation de la date limit
                $Rankings_Player_Model->remove_all_rank_older_than($removeDatadate, $table);

            }
        }

    }
}

/**
 * Suppression manuelle de classements
 *
 * @global        object mysql $db
 * @global array $server_config
 * @global int $Ogspy->Params->datadate
 *
 */
function galaxy_drop_ranking()
{
    global $Ogspy;

    if (!check_var($Ogspy->Params->datadate, "Num") || !check_var($Ogspy->Params->subaction, "Char")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    //Vérification des droits
    galaxy_check_auth("drop_ranking");

    if (!isset($Ogspy->Params->datadate) || !isset($Ogspy->Params->subaction)) {
        redirection("index.php");
    }

    if ($Ogspy->Params->subaction == "player") {

        $Rankings_Player_Model = new Rankings_Player_Model();
        $Rankings_Player_Model->remove_all_rank_by_datadate(intval($Ogspy->Params->datadate));
    } elseif ($Ogspy->Params->subaction == "ally") {
        //todo varidable dtadate = 0 ici :/ voir affichage
        $Rankings_Ally_Model = new Rankings_Ally_Model();
        $Rankings_Ally_Model->remove_all_rank_by_datadate(intval($Ogspy->Params->datadate));
    }
    redirection("index.php?action=ranking&subaction=" . $Ogspy->Params->subaction);
}

/**
 * Listing des phalanges
 *
 * @param int $galaxy
 * @param int $system
 * @global       object mysql $db
 * @global array $server_config
 * @global array $user_data
 * @global array $user_auth
 * @return array $phalanxer (galaxy, system, row, phalanx, gate, name, ally, player)
 */
function galaxy_get_phalanx($galaxy, $system)
{
    global $server_config, $user_data, $user_auth;

    $ally_protection = array();
    if ($server_config["ally_protection"] != "") {
        $ally_protection = explode(",", $server_config["ally_protection"]);
    }

    $phalanxer = array();
    $data_computed = array();

    $data = (new Universe_Model())->get_phalanx($galaxy);

    if (count($data) > 0) {
        //Construction liste phalanges
        foreach ($data as $phalanx) {
            $arrondi_type = 0;
            $phalanx_range = (pow($phalanx['level'], 2) - 1);
            $system_lower_range = $phalanx['system'] - $phalanx_range;
            if ($system_lower_range < 1) {
                $system_lower_range = $system_lower_range + $server_config['num_of_systems'];
                $arrondi_type = 1;
            }; //Partie négative : 1:490 -> 1:5
            $system_higher_range = $phalanx['system'] + $phalanx_range;
            if ($system_higher_range > $server_config['num_of_systems']) {
                $system_higher_range = $system_higher_range - $server_config['num_of_systems'];
                $arrondi_type = 2;
            };

            //Cas 1 : Dans la même galaxie

            if ($system >= $system_lower_range && $system <= $system_higher_range && $arrondi_type == 0) {
                $add_to_list = true;
                //Cas 2 : Phanlange en début de galaxie -> 2 zones possibles : 1 en fin de galaxie et 1 en début
            } elseif (($system <= $system_higher_range && $system <= $system_lower_range && $arrondi_type == 1) ||
                ($system >= $system_higher_range && $system >= $system_lower_range && $arrondi_type == 1)) {
                $add_to_list = true;
                //Cas 3 : Phanlange en fin de galaxie -> 2 zones possibles : 1 en fin de galaxie et 1 en début
            } elseif (($system >= $system_lower_range && $system >= $system_higher_range && $arrondi_type == 2) ||
                ($system <= $system_lower_range && $system <= $system_higher_range && $arrondi_type == 2)) {
                $add_to_list = true;
            } else {
                // Phalange non hostile
                $add_to_list = false;
            }

            if ($add_to_list == true) {
                $data_computed[] = array(
                    'galaxy' => $phalanx["galaxy"],
                    'system' => $phalanx["system"],
                    'row' => $phalanx["row"],
                    'name' => $phalanx["name"],
                    'ally' => $phalanx["ally"],
                    'player' => $phalanx["player"],
                    'gate' => $phalanx["gate"],
                    'level' => $phalanx["level"],
                    'range_down' => $system_lower_range,
                    'range_up' => $system_higher_range
                );
            }
        }

        foreach ($data_computed as $phalange) { // Filtre alliance amies et masquées
            if (!in_array($phalange["ally"], $ally_protection) || $phalange["ally"] == "" || $user_auth["server_show_positionhided"] == 1 || $user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                $phalanxer[] = $phalange;
            }
        }
    }
    return $phalanxer;
}

/**
 * Affichage des systemes solaires obsoletes
 *
 * @global        object mysql $db
 * @global int $Ogspy->Params->perimeter
 * @global int $Ogspy->Params->since
 * @global string $Ogspy->Params->typesearch (M|P)
 * @todo Query :  "select distinct galaxy, system" . $row_field . " from " . TABLE_UNIVERSE . " where moon = '" . $moon . "' and " . $field . " between " . $indice_sup ." and " . $indice_inf ."  and galaxy = " . intval($Ogspy->Params->perimeter) . " order by galaxy, system, row limit 0, 51";
 * @todo Query :  "select distinct galaxy, system" . $row_field . " from " . TABLE_UNIVERSE . " where moon = '" . $moon . "' and " . $field . " between " . $indice_sup ." and " . $indice_inf ."  order by galaxy, system, row limit 0, 51";
 * @return array $obsolete
 */
function galaxy_obsolete()
{
    global $db;
    global $Ogspy;

    $obsolete = array();
    if (isset($Ogspy->Params->perimeter) && isset($Ogspy->Params->since) && is_numeric($Ogspy->Params->perimeter) && is_numeric($Ogspy->Params->since)) {
        if (!isset($Ogspy->Params->typesearch) || ($Ogspy->Params->typesearch != "M" && $Ogspy->Params->typesearch != "P")) {
            $Ogspy->Params->typesearch = "P";
        }


        $timestamp = time();
        // tableau regroupant les valeurs possibles
        $since = array(0, 7, 14, 21, 28, 42, 56, $timestamp - 1);

        // on regarde l existence de la variable
        if (!in_array((int)$Ogspy->Params->since, $since)) {
            return $obsolete;
        }

        // on recupere l indice de recherche
        $indice = array_search((int)$Ogspy->Params->since, $since);

        // l indice ne peut pas etre le premier ou le dernier
        if ($indice == 0 || $indice == (count($since) - 1)) {
            return $obsolete;
        }
        $indice_sup = $timestamp - 60 * 60 * 24 * $since[$indice + 1];
        $indice_inf = $timestamp - 60 * 60 * 24 * $since[$indice - 1];

        // on peut maintenant lancer une requete générique
        // on peut maintenant lancer une requete générique
        $formoon = true;
        if ($Ogspy->Params->typesearch == "P") {
            $formoon = false;
        }
        $obsolete = (new Universe_Model())->get_galaxy_obsolete($Ogspy->Params->perimeter, $indice_inf, $indice_sup, $indice, $since, $formoon);


    }


    return $obsolete;
}

/**
 * Reconstruction des RE
 *
 * @global array $table_prefix
 * @global       object mysql $db
 * @param string $id_RE RE a reconstituer
 * @return string $template_RE reconstitue
 */
function UNparseRE($id_RE)
{
    //todo nom de variable pas du tout expressive :/
    global $db, $lang;

    $Spy_Model = new Spy_Model();
    $Universe_Model = new Universe_Model();


    $show = array(
        'flotte' => 0,
        'defense' => 0,
        'batiment' => 0,
        'recherche' => 0
    );

    $flotte = array(
        'PT' => $lang['GAME_FLEET_PT'],
        'GT' => $lang['GAME_FLEET_GT'],
        'CLE' => $lang['GAME_FLEET_CLE'],
        'CLO' => $lang['GAME_FLEET_CLO'],
        'CR' => $lang['GAME_FLEET_CR'],
        'VB' => $lang['GAME_FLEET_VB'],
        'VC' => $lang['GAME_FLEET_VC'],
        'REC' => $lang['GAME_FLEET_REC'],
        'SE' => $lang['GAME_FLEET_SE'],
        'BMD' => $lang['GAME_FLEET_BMD'],
        'DST' => $lang['GAME_FLEET_DST'],
        'EDLM' => $lang['GAME_FLEET_EDLM'],
        'SAT' => $lang['GAME_FLEET_SAT'],
        'TRA' => $lang['GAME_FLEET_TRA']
    );

    $defs = array(
        'LM' => $lang['GAME_DEF_LM'],
        'LLE' => $lang['GAME_DEF_LLE'],
        'LLO' => $lang['GAME_DEF_LLO'],
        'CG' => $lang['GAME_DEF_CG'],
        'AI' => $lang['GAME_DEF_AI'],
        'LP' => $lang['GAME_DEF_LP'],
        'PB' => $lang['GAME_DEF_PB'],
        'GB' => $lang['GAME_DEF_GB'],
        'MIC' => $lang['GAME_DEF_MIC'],
        'MIP' => $lang['GAME_DEF_MIP']
    );

    $bats = array(
        'M' => $lang['GAME_BUILDING_M'],
        'C' => $lang['GAME_BUILDING_C'],
        'D' => $lang['GAME_BUILDING_D'],
        'CES' => $lang['GAME_BUILDING_CES'],
        'CEF' => $lang['GAME_BUILDING_CEF'],
        'UdR' => $lang['GAME_BUILDING_UDR'],
        'UdN' => $lang['GAME_BUILDING_UDN'],
        'CSp' => $lang['GAME_BUILDING_CSP'],
        'HM' => $lang['GAME_BUILDING_HM'],
        'HC' => $lang['GAME_BUILDING_HC'],
        'HD' => $lang['GAME_BUILDING_HD'],
        'Lab' => $lang['GAME_BUILDING_LAB'],
        'Ter' => $lang['GAME_BUILDING_TER'],
        'DdR' => $lang['GAME_BUILDING_DDR'],
        'Silo' => $lang['GAME_BUILDING_SILO'],
        'Dock' => $lang['GAME_BUILDING_DOCK'],
        'BaLu' => $lang['GAME_BUILDING_BALU'],
        'Pha' => $lang['GAME_BUILDING_PHA'],
        'PoSa' => $lang['GAME_BUILDING_POSA']
    );

    $techs = array(
        'Esp' => $lang['GAME_TECH_ESP'],
        'Ordi' => $lang['GAME_TECH_ORDI'],
        'Armes' => $lang['GAME_TECH_WEAP'],
        'Bouclier' => $lang['GAME_TECH_SHIELD'],
        'Protection' => $lang['GAME_TECH_ARMOR'],
        'NRJ' => $lang['GAME_TECH_ENERGY'],
        'Hyp' => $lang['GAME_TECH_HYP'],
        'RC' => $lang['GAME_TECH_CD'],
        'RI' => $lang['GAME_TECH_ID'],
        'PH' => $lang['GAME_TECH_HD'],
        'Laser' => $lang['GAME_TECH_LASER'],
        'Ions' => $lang['GAME_TECH_ION'],
        'Plasma' => $lang['GAME_TECH_PLASMA'],
        'RRI' => $lang['GAME_TECH_IRN'],
        'Graviton' => $lang['GAME_TECH_GRAV'],
        'Astrophysique' => $lang['GAME_TECH_ASTRO']
    );

    $row = $Spy_Model->get_spy_Id($id_RE);

    $c = explode(":", $row['coordinates']);
    $rowPN = $Universe_Model->get_player_name($c[0], $c[1], $c[2]);

    $tRows = $Spy_Model->get_all_spy_coordinates($row['coordinates']); /// contiens tous les re dispo sur les coordonnées
    $sep_mille = ".";

    // /!\ todo pattern "Lune" n'ezst plus determinant
    if (preg_match('/\(Lune\)/', $row['planet_name'])) {
        $moon = 1;
    } else {
        $moon = 0;
    }

    $dateRE = date('m-d H:i:s', $row['dateRE']);
    $template = '<table border="0" cellpadding="2" cellspacing="0" align="center">
    <tr>
        <td class="l" colspan="4" class="c">' . $lang['GAME_SPYREPORT_RES'] . ' ' . $row['planet_name'] . ' [' . $row['coordinates'] . '] (' . $lang['GAME_SPYREPORT_PLAYER'] . ' \'' . $rowPN['player'] . '\') le ' . $dateRE . '</td>
    </tr>
    <tr>
        <td class="c" style="text-align:right;">' . $lang['GAME_RES_METAL'] . ':</td>
        <th>' . number_format($row['metal'], 0, ',', $sep_mille) . '</th>
        <td class="c" style="text-align:right;">' . $lang['GAME_RES_CRYSTAL'] . ':</td>
        <th>' . number_format($row['cristal'], 0, ',', $sep_mille) . '</th>
    </tr>
    <tr>
        <td class="c" style="text-align:right;">' . $lang['GAME_RES_DEUT'] . ':</td>
        <th>' . number_format($row['deuterium'], 0, ',', $sep_mille) . '</th>
        <td class="c" style="text-align:right;">' . $lang['GAME_RES_ENERGY'] . ':</td>
        <th>' . number_format($row['energie'], 0, ',', $sep_mille) . '</th>
    </tr>
    <tr>
        <th colspan="4">';
    if ($row['activite'] > 0) {
        $template .= $lang['GAME_SPYREPORT_ACTIVITY'] . ' ' . $row['activite'] . ' ' . $lang['GAME_SPYREPORT_LASTMINUTES'] . '.';
    } else {
        $template .= $lang['GAME_SPYREPORT_NOACTIVITY'];
    }
    $template .= '</th>
    </tr>' . "\n";
    foreach ($flotte as $key => $value) {
        if ($row[$key] != -1) {
            $show['flotte'] = 1;
            continue;
        }
    }
    if ($show['flotte'] == 0) {
        // besoin d 'une eventuelle reconstitution de re'
        foreach ($tRows as $tmpRow) {
            if ($row["planet_name"] == $tmpRow["planet_name"] && $show['flotte'] == 0) // on recherche sur la meme planete ou lune
            {

                $total = $tmpRow["PT"] + $tmpRow["GT"] + $tmpRow["CLE"] + $tmpRow["CLO"] + $tmpRow["CR"] + $tmpRow["VB"] + $tmpRow["VC"] + $tmpRow["REC"] + $tmpRow["SE"] + $tmpRow["BMD"] + $tmpRow["DST"] + $tmpRow["EDLM"] + $tmpRow["SAT"] + $tmpRow["TRA"];
                if ((int)$total != -14) {
                    $row["PT"] = $tmpRow["PT"];
                    $row["GT"] = $tmpRow["GT"];
                    $row["CLE"] = $tmpRow["CLE"];
                    $row["CLO"] = $tmpRow["CLO"];
                    $row["CR"] = $tmpRow["CR"];
                    $row["VB"] = $tmpRow["VB"];
                    $row["VC"] = $tmpRow["VC"];
                    $row["REC"] = $tmpRow["REC"];
                    $row["SE"] = $tmpRow["SE"];
                    $row["BMD"] = $tmpRow["BMD"];
                    $row["DST"] = $tmpRow["DST"];
                    $row["EDLM"] = $tmpRow["EDLM"];
                    $row["SAT"] = $tmpRow["SAT"];
                    $row["TRA"] = $tmpRow["TRA"];

                    $show['flotte'] = 1;
                }
            }
        }
    }


    foreach ($defs as $key => $value) {
        if ($row[$key] != -1) {
            $show['flotte'] = 1;
            $show['defense'] = 1;
            continue;
        }
    }

    if ($show['defense'] == 0) {
        foreach ($tRows as $tmpRow) {
            if ($row["planet_name"] == $tmpRow["planet_name"] && $show['defense'] == 0) // on recherche sur la meme planete ou lune
            {
                $total = $tmpRow["LM"] + $tmpRow["LLE"] + $tmpRow["LLO"] + $tmpRow["CG"] + $tmpRow["AI"] + $tmpRow["LP"] + $tmpRow["PB"] + $tmpRow["GB"] + $tmpRow["MIC"] + $tmpRow["MIP"];
                if ((int)$total != -10) {
                    $row["LM"] = $tmpRow["LM"];
                    $row["LLE"] = $tmpRow["LLE"];
                    $row["LLO"] = $tmpRow["LLO"];
                    $row["CG"] = $tmpRow["CG"];
                    $row["AI"] = $tmpRow["AI"];
                    $row["LP"] = $tmpRow["LP"];
                    $row["PB"] = $tmpRow["PB"];
                    $row["GB"] = $tmpRow["GB"];
                    $row["MIC"] = $tmpRow["MIC"];
                    $row["BMD"] = $tmpRow["BMD"];
                    $row["MIP"] = $tmpRow["MIP"];

                    $show['defense'] = 1;
                }
            }
        }
    }

    foreach ($bats as $key => $value) {
        if ($row[$key] != -1) {
            $show['flotte'] = 1;
            $show['defense'] = 1;
            $show['batiment'] = 1;
            continue;
        }
    }
    if ($show['batiment'] == 0) {
        foreach ($tRows as $tmpRow) {
            if ($row["planet_name"] == $tmpRow["planet_name"] && $show['batiment'] == 0) // on recherche sur la meme planete ou lune
            {
                $total = $tmpRow["M"] + $tmpRow["C"] + $tmpRow["D"] + $tmpRow["CES"] + $tmpRow["CEF"] + $tmpRow["UdR"] + $tmpRow["UdN"] + $tmpRow["CSp"] + $tmpRow["HM"] + $tmpRow["HC"] + $tmpRow["HD"] + $tmpRow["Lab"] + $tmpRow["Ter"] + $tmpRow["Silo"] + $tmpRow["Dock"] + $tmpRow["DdR"] + $tmpRow["BaLu"] + $tmpRow["Pha"] + $tmpRow["PoSa"];
                if ((int)$total != -19) {

                    $row["M"] = $tmpRow["M"];
                    $row["C"] = $tmpRow["C"];
                    $row["D"] = $tmpRow["D"];
                    $row["CES"] = $tmpRow["CES"];
                    $row["CEF"] = $tmpRow["CEF"];
                    $row["UdR"] = $tmpRow["UdR"];
                    $row["UdN"] = $tmpRow["UdN"];
                    $row["CSp"] = $tmpRow["CSp"];
                    $row["HM"] = $tmpRow["HM"];
                    $row["HC"] = $tmpRow["HC"];
                    $row["HD"] = $tmpRow["HD"];
                    $row["Lab"] = $tmpRow["Lab"];
                    $row["Ter"] = $tmpRow["Ter"];
                    $row["Silo"] = $tmpRow["Silo"];
                    $row["Dock"] = $tmpRow["Dock"];
                    $row["DdR"] = $tmpRow["DdR"];
                    $row["BaLu"] = $tmpRow["BaLu"];
                    $row["Pha"] = $tmpRow["Pha"];
                    $row["PoSa"] = $tmpRow["PoSa"];

                    $show['batiment'] = 1;
                }
            }
        }
    }

    foreach ($techs as $key => $value) {
        if ($row[$key] != -1) {
            $show['flotte'] = 1;
            $show['defense'] = 1;
            $show['batiment'] = 1;
            $show['recherche'] = 1;
            continue;
        }
    }

    if ($show['recherche'] == 0) {
        foreach ($tRows as $tmpRow) {
            if ($show['recherche'] == 0) // onn recherche sur toutes les planetes /lunes ( recherche commune )
            {

                $total = $tmpRow["Esp"] + $tmpRow["Ordi"] + $tmpRow["Armes"] + $tmpRow["Bouclier"] + $tmpRow["NRJ"] + $tmpRow["Hyp"] + $tmpRow["RC"] + $tmpRow["RI"] + $tmpRow["PH"] + $tmpRow["Laser"] + $tmpRow["Ions"] + $tmpRow["Plasma"] + $tmpRow["RRI"] + $tmpRow["Graviton"] + $tmpRow["Astrophysique"];
                if ((int)$total != -15) {
                    $row["Esp"] = $tmpRow["Esp"];
                    $row["Ordi"] = $tmpRow["Ordi"];
                    $row["Armes"] = $tmpRow["Armes"];
                    $row["Bouclier"] = $tmpRow["Bouclier"];
                    $row["Hyp"] = $tmpRow["Hyp"];
                    $row["NRJ"] = $tmpRow["NRJ"];
                    $row["RC"] = $tmpRow["RC"];
                    $row["RI"] = $tmpRow["RI"];
                    $row["PH"] = $tmpRow["PH"];
                    $row["Laser"] = $tmpRow["Laser"];
                    $row["Plasma"] = $tmpRow["Plasma"];
                    $row["Graviton"] = $tmpRow["Graviton"];
                    $row["Astrophysique"] = $tmpRow["Astrophysique"];
                    $row["Ions"] = $tmpRow["Ions"];
                    $row["RRI"] = $tmpRow["RRI"];

                    $show['recherche'] = 1;
                }
            }
        }
    }

    if ($show['flotte'] == 1) {
        $template .= '  <tr>
        <td class="l" colspan="4">' . $lang['GAME_CAT_FLEET'] . '</td>
    </tr>
    <tr>' . "\n";
        $count = 0;
        foreach ($flotte as $key => $value) {
            if ($row[$key] > 0) {
                $template .= '    <td class="c" style="text-align:right;">' . $flotte[$key] . '</td>
        <th>' . number_format($row[$key], 0, ',', $sep_mille) . '</th>' . "\n";
                if ($count == 0) {
                    $count = 1;
                } else {
                    $template .= '  </tr>
    <tr>' . "\n";
                    $count = 0;
                }
            }
        }
        if ($count == 1) {
            $template .= '    <td class="c" style="text-align:right;">&nbsp;</td>
        <th>&nbsp;</th>' . "\n";
        }
        $template .= '  </tr>' . "\n";
    }
    if ($show['defense'] == 1) {
        $template .= '  <tr>
        <td class="l" colspan="4">' . $lang['GAME_CAT_DEF'] . '</td>
    </tr>
    <tr>' . "\n";
        $count = 0;
        foreach ($defs as $key => $value) {
            if ($row[$key] > 0) {
                $template .= '    <td class="c" style="text-align:right;">' . $defs[$key] . '</td>
        <th>' . number_format($row[$key], 0, ',', $sep_mille) . '</th>' . "\n";
                if ($count == 0) {
                    $count = 1;
                } else {
                    $template .= '  </tr>
    <tr>' . "\n";
                    $count = 0;
                }
            }
        }
        if ($count == 1) {
            $template .= '    <td class="c" style="text-align:right;">&nbsp;</td>
        <th>&nbsp;</th>' . "\n";
        }
        $template .= '  </tr>' . "\n";
    }
    if ($show['batiment'] == 1) {
        $template .= '  <tr>
        <td class="l" colspan="4">' . $lang['GAME_CAT_BUILDINGS'] . '</td>
    </tr>
    <tr>' . "\n";
        $count = 0;
        foreach ($bats as $key => $value) {
            if ($row[$key] > 0) {
                $template .= '    <td class="c" style="text-align:right;">' . $bats[$key] . '</td>
        <th>' . $row[$key] . '</th>' . "\n";
                if ($count == 0) {
                    $count = 1;
                } else {
                    $template .= '  </tr>
    <tr>' . "\n";
                    $count = 0;
                }
            }
        }
        if ($count == 1) {
            $template .= '    <td class="c" style="text-align:right;">&nbsp;</td>
        <th>&nbsp;</th>' . "\n";
        }
        $template .= '  </tr>' . "\n";
    }
    if ($show['recherche'] == 1) {
        $template .= '  <tr>
        <td class="l" colspan="4">' . $lang['GAME_CAT_LAB'] . '</td>
    </tr>
    <tr>' . "\n";
        $count = 0;
        foreach ($techs as $key => $value) {
            if ($row[$key] > 0) {
                $template .= '    <td class="c" style="text-align:right;">' . $techs[$key] . '</td>
        <th>' . $row[$key] . '</th>' . "\n";
                if ($count == 0) {
                    $count = 1;
                } else {
                    $template .= '  </tr>
    <tr>' . "\n";
                    $count = 0;
                }
            }
        }
        if ($count == 1) {
            $template .= '    <td class="c" style="text-align:right;">&nbsp;</td>
        <th>&nbsp;</th>' . "\n";
        }
        $template .= '  </tr>' . "\n";
    }
    $template .= '  <tr>
        <th colspan="4">' . $lang['GAME_SPYREPORT_PROBADEST'] . ' :' . $row['proba'] . '%</th>
    </tr>
</table>';
    return ($template);
}


/**
 * coordonnees des missiles A PORTEE
 *
 * @param int $galaxy
 * @param int $system
 * @return string
 */
function portee_missiles($galaxy, $system)
{
    global  $server_config;
    //todo prevoir jointure de table

    $User_Model = new User_Model();
    $User_Building_Model = new User_Building_Model();
    $User_Technology_Model = new User_Technology_Model();
    $User_Defense_Model = new User_Defense_Model();


    $missil_ok = '';
    $total_missil = 0;
    // recherche niveau missile


    $tUser_building = $User_Building_Model->get_building_by_silo(3);

    $ok_missil = '';
    foreach ($tUser_building as $User_building) {

        $base_joueur = $User_building["user_id"];
        $base_id_planet = $User_building["planet_id"];
        $base_coord = $User_building["coordinates"];
        $base_missil = $User_building["Silo"];

        // sépare les coords
        $missil_coord = explode(':', $base_coord);
        $galaxie_missil = $missil_coord[0];
        $sysSol_missil = $missil_coord[1];
        $planet_missil = $missil_coord[2]; // Inutile ?

        // recherche le niveau du réacteur du joueur
        $tUser_Technology =  $User_Technology_Model->select_user_technologies($base_joueur);
        $niv_reac_impuls = $tUser_Technology["RI"];

        if ($niv_reac_impuls > 0) {

            // recherche du nombre de missile dispo
            $tUser_defense = $User_Defense_Model->select_user_defense_planete($base_joueur, $base_id_planet);
            $missil_dispo =  $tUser_defense["MIP"];


            $info_users = $User_Model->select_user_data($base_joueur);
            $nom_missil_joueur = $info_users[0]["user_name"];


            // calcul de la porté du silo
            $porte_missil = ($niv_reac_impuls * 5) - 1; // Portée : (Lvl 10 * 5) - 1 = 49

            // calcul de la fenetre
            $vari_missil_moins_tmp = ($sysSol_missil - $porte_missil) % $server_config['num_of_systems'];     // ne peux pas
            $vari_missil_moins = (($sysSol_missil - $porte_missil) < 1 )  ? 1     : $vari_missil_moins_tmp;   //  etre negatif !!!!
            $vari_missil_plus = ($sysSol_missil + $porte_missil) % $server_config['num_of_systems'];


            // création des textes si missil à portée
            if ($galaxy == $galaxie_missil && $system >= $vari_missil_moins && $system <= $vari_missil_plus) {

                $missil_ok .= displayMIP($nom_missil_joueur, $missil_dispo, $galaxie_missil, $sysSol_missil, $base_coord, $ok_missil, $total_missil);
            }
        }
    }
    return $missil_ok;
}

/**
 * @param $nom_missil_joueur
 * @param $missil_dispo
 * @param $galaxie_missil
 * @param double $sysSol_missil
 * @param $base_coord
 * @param string $ok_missil
 * @param integer $total_missil
 * @return string
 */
function displayMIP($nom_missil_joueur, $missil_dispo, $galaxie_missil, $sysSol_missil, $base_coord, $ok_missil, $total_missil)
{
    global $lang;

    if (!$missil_dispo) {
        $missil_dispo = $lang['GALAXY_MIP_UNKNOWN'];
    }

    $color_missil_ally1 = '<span style=\'color: #00FF00; \'>';
    $color_missil_ally2 = '</span>';
    $tooltip = '<table width=\'250\'>';
    $tooltip .= '<tr><td colspan=\'2\' class=\'c\' align=\'center\'>' . $lang['GALAXY_MIP_TITLE'] . '</td></tr>';
    $tooltip .= '<tr><td class=\'c\' width=\'70\'>' . $lang['GALAXY_MIP_NAME'] . ' : </td><th width=\'30\'>' . $nom_missil_joueur . '</th></tr>';
    $tooltip .= '<tr><td class=\'c\' width=\'70\'>' . $lang['GALAXY_MIP_AVAILABLE_MISSILES'] . ' : </td><th width=\'30\'>' . $missil_dispo . '</th></tr>';
    $tooltip .= '</table>';
    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $tooltip = htmlentities($tooltip, ENT_COMPAT | ENT_HTML401, "UTF-8");
    } else {
        $tooltip = htmlentities($tooltip, ENT_COMPAT, "UTF-8");
    }
    $door = '<a id="linkdoor" href="?action=galaxy&galaxy=' . $galaxie_missil . '&system=' . $sysSol_missil . '"';
    //$door .= ' onmouseover="this.T_WIDTH=260;this.T_TEMP=15000;return escape(' . $tooltip . ')"';
    $total_missil += (int)$missil_dispo;
    $missil_ready = "<span style='color: #DBBADC; '> " . $total_missil . " " . $lang['GALAXY_MIP_MIPS'] . " </span>";

    //<a href="index.htm" onmouseover="return escape('Some text')">Homepage </a>
    $ok_missil .= $nom_missil_joueur ." - ". $door . $missil_ready . $color_missil_ally1 . $base_coord . $color_missil_ally2;


    if ($ok_missil) {
        $missil_ok = "<br><span style='color: #FFFF66; '> " . $lang['GALAXY_MIP_UNDERFIRE'] . " : </span>" . $ok_missil . "</a>";
    } else {
        $missil_ok = "<span style='color: #FFFF66; '> " . $lang['GALAXY_MIP_NOMIPS_AROUND'] . "</span>";
    }

    return $missil_ok;
}
