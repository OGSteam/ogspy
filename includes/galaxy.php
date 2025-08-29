<?php

/**
 * Fonctions relatives aux donnees galaxies/planetes
 *
 * @package OGSpy
 * @subpackage galaxy
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.05 ($Rev: 7699 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\AstroObject_Model;
use Ogsteam\Ogspy\Model\Rankings_Player_Model;
use Ogsteam\Ogspy\Model\Rankings_Ally_Model;
use Ogsteam\Ogspy\Model\User_Favorites_Model;
use Ogsteam\Ogspy\Model\Spy_Model;
use Ogsteam\Ogspy\Model\Combat_Report_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Model\Player_Model;
use Ogsteam\Ogspy\Model\Player_Technology_Model;
use Ogsteam\Ogspy\Model\Player_Defense_Model;
use Ogsteam\Ogspy\Model\Player_Building_Model;
use Ogsteam\Ogspy\Helper\ToolTip_Helper;


use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;

/**
 * Checks user permissions for specific galaxy-related actions.
 *
 * @param string $action The action the user wishes to perform, such as 'import_planet', 'export_planet',
 *                       'import_spy', 'export_spy', 'import_ranking', 'export_ranking', 'drop_ranking',
 *                       'set_ranking', or 'set_rc'.
 * @return void This method terminates execution or redirects the user if permissions are insufficient,
 *              otherwise it proceeds with the requested action.
 * @global array $user_auth Defines user authorization levels for specific functionalities.
 * @global array $user_data Contains user-specific data, including roles and permissions.
 */
function galaxy_check_auth($action)
{
    global $user_data, $user_auth;

    switch ($action) {
        case "import_planet":
            if ($user_auth["ogs_set_system"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des systèmes solaires -->" . "\n");
            }
            break;

        case "export_planet":
            if ($user_auth["ogs_get_system"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des systèmes solaires -->" . "\n");
            }
            break;

        case "import_spy":
            if ($user_auth["ogs_set_spy"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des rapports d'espionnage -->" . "\n");
            }
            break;

        case "export_spy":
            if ($user_auth["ogs_get_spy"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des rapports d'espionnage -->" . "\n");
            }
            break;

        case "import_ranking":
            if ($user_auth["ogs_set_ranking"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour exporter des classements -->" . "\n");
            }
            break;

        case "export_ranking":
            if ($user_auth["ogs_get_ranking"] != 1 && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                die("<!-- [AccessDenied] Accès refusé -->" . "\n" . "<!-- Vous n'avez pas les droits pour importer des classements -->" . "\n");
            }
            break;

        case "drop_ranking":
            if ($user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_ranking"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        case "set_ranking":
            if (($user_auth["server_set_ranking"] != 1) && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        case "set_rc":
            if (($user_auth["server_set_rc"] != 1) && $user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }
            break;

        default:
            die("<!-- [ErrorFatal=18] Données transmises incorrectes  -->");
    }
}


/**
 * Retrieves and displays population data for a specific galaxy and system.
 *
 * This method calculates the galaxy and system based on provided parameters
 * or defaults to the user's settings. It retrieves the system's population
 * data from the AstroObject_Model and applies filters before returning.
 *
 * @return array Returns an array containing:
 *               - "population": Filtered population data for the specified system.
 *               - "galaxy": The resolved galaxy number.
 *               - "system": The resolved system number.
 * @global array $server_config Server configuration containing galaxy and system limits.
 * @global int $pub_galaxy Public galaxy variable.
 * @global int $pub_system Public system variable.
 * @global string $pub_coordinates Public coordinates in the format 'galaxy:system'.
 * @global array $user_data User data containing default galaxy and system values.
 */
function galaxy_show()
{
    global $user_data, $server_config;
    global $pub_galaxy, $pub_system, $pub_coordinates;
    if (isset($pub_coordinates)) {
        @list($pub_galaxy, $pub_system) = explode(":", $pub_coordinates);
    }
    if (isset($pub_galaxy) && isset($pub_system)) {
        if (intval($pub_galaxy) < 1) {
            $pub_galaxy = 1;
        }
        if (intval($pub_galaxy) > intval($server_config['num_of_galaxies'])) {
            $pub_galaxy = intval($server_config['num_of_galaxies']);
        }
        if (intval($pub_system) < 1) {
            $pub_system = 1;
        }
        if (intval($pub_system) > intval($server_config['num_of_systems'])) {
            $pub_system = intval($server_config['num_of_systems']);
        }
    }
    if (!isset($pub_galaxy) || !isset($pub_system)) {
        $pub_galaxy = $user_data["default_galaxy"];
        $pub_system = $user_data["default_system"];
        if ($pub_galaxy == 0 || $pub_system == 0) {
            $pub_galaxy = 1;
            $pub_system = 1;
        }
    }
    $Universe_Model = new AstroObject_Model();
    $population = $Universe_Model->get_system($pub_galaxy, $pub_system, $pub_system);
    $population = filter_system($population[$pub_system]);
    return array("population" => $population, "galaxy" => $pub_galaxy, "system" => $pub_system);
}

/**
 * Filters and processes information for each planet within a given system.
 *
 * @param array $system Array of planets, where each planet contains data such as galaxy, system, row, and ally_name.
 * @return array Modified system array with additional information about spy reports, combat reports, and allied status for each planet.
 * @global array $server_config Configuration data that includes allied information.
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
        $friend = in_array($planet['ally_name'], $allied);
        $planet["report_spy"] = $report_spy;
        $planet["report_rc"] = $report_rc;
        $planet["allied"] = $friend;
        $system[$planet['row']] = $planet;
    }
    return $system;
}


/**
 * Displays information about a specified sector in the galaxy, including its population and system details.
 *
 * @return array An associative array containing:
 *  - "population": Array of system data for the specified range of systems filtered by the system settings.
 *  - "galaxy": The validated galaxy number.
 *  - "system_down": The validated lower bound of the system range.
 *  - "system_up": The validated upper bound of the system range.
 * @global int $pub_galaxy Input value for the galaxy number.
 * @global int $pub_system_down Lower bound of the system range.
 * @global int $pub_system_up Upper bound of the system range.
 * @global array $server_config Configuration details, including the number of galaxies and systems.
 */
function galaxy_show_sector()
{
    global $server_config;
    global $pub_galaxy, $pub_system_down, $pub_system_up;
    if (isset($pub_galaxy) && isset($pub_system_down) && isset($pub_system_up)) {
        if (intval($pub_galaxy) < 1) {
            $pub_galaxy = 1;
        }
        if (intval($pub_galaxy) > intval($server_config['num_of_galaxies'])) {
            $pub_galaxy = intval($server_config['num_of_galaxies']);
        }
        if (intval($pub_system_down) < 1) {
            $pub_system_down = 1;
        }
        if (intval($pub_system_down) > intval($server_config['num_of_systems'])) {
            $pub_system_down = intval($server_config['num_of_systems']);
        }
        if (intval($pub_system_up) < 1) {
            $pub_system_up = 1;
        }
        if (intval($pub_system_up) > intval($server_config['num_of_systems'])) {
            $pub_system_up = intval($server_config['num_of_systems']);
        }
    }
    if (!isset($pub_galaxy) || !isset($pub_system_down) || !isset($pub_system_up)) {
        $pub_galaxy = 1;
        $pub_system_down = 1;
        $pub_system_up = 25;
    }
    $Universe_Model = new AstroObject_Model();
    $population = $Universe_Model->get_system($pub_galaxy, $pub_system_down, $pub_system_up);
    for ($system = $pub_system_down; $system <= $pub_system_up; $system++) {
        $population[$system] = filter_system($population[$system]);
        $population[$system]['timestamp'] = $population[$system][1]['timestamp'];
    }
    return array("population" => $population, "galaxy" => $pub_galaxy, "system_down" => $pub_system_down, "system_up" => $pub_system_up);
}

/**
 * Searches for celestial objects, such as players, alliances, planets, moons, or specific conditions,
 * within a galaxy based on various defined criteria.
 *
 * @return array Returns an array containing two elements: a 1D array of search results with detailed information
 *               about celestial objects, and the total number of pages of results available.
 * @global array $user_data Contains data on the currently authenticated user.
 * @global array $server_config Contains server configuration settings.
 * @global object $log Logging object for debug and warning messages.
 * @global mixed $pub_string_search The search string used to query specific entities (e.g., player, ally, etc.).
 * @global mixed $pub_type_search The type of search to perform (e.g., "player", "ally", "planet", etc.).
 * @global mixed $pub_strict Determines if the search should be strict or partial.
 * @global mixed $pub_sort Primary sorting parameter for search results.
 * @global mixed $pub_sort2 Secondary sorting parameter for search results.
 * @global int $pub_galaxy_down Lower boundary for galaxy in search constraints.
 * @global int $pub_galaxy_up Upper boundary for galaxy in search constraints.
 * @global int $pub_system_down Lower boundary for solar system in search constraints.
 * @global int $pub_system_up Upper boundary for solar system in search constraints.
 * @global int $pub_row_down Lower boundary for planetary row in search constraints.
 * @global int $pub_row_up Upper boundary for planetary row in search constraints.
 * @global int $pub_page Page number used for paginating search results.
 */
function galaxy_search()
{
    //todo voir possible pb recherche strict ou non
    global $user_data, $server_config, $log;
    global $pub_string_search, $pub_type_search, $pub_strict, $pub_sort, $pub_sort2, $pub_galaxy_down, $pub_galaxy_up, $pub_system_down, $pub_system_up, $pub_row_down, $pub_row_up, $pub_page;
    if (!check_var($pub_type_search, "Char") || !check_var($pub_strict, "Char") || !check_var($pub_sort, "Num") || !check_var($pub_sort2, "Num") || !check_var($pub_galaxy_down, "Num") || !check_var($pub_galaxy_up, "Num") || !check_var($pub_system_down, "Num") || !check_var($pub_system_up, "Num") || !check_var($pub_row_down, "Num") || !check_var($pub_row_up, "Num")  || !check_var($pub_page, "Num")) {
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

    if (!isset($pub_type_search) || (!isset($pub_string_search) && (!isset($pub_galaxy_down) || !isset($pub_galaxy_up) || !isset($pub_system_down) || !isset($pub_system_up) || !isset($pub_row_down) || !isset($pub_row_up)))) {
        return array($search_result, $total_page);
    }
    $data_user = new User_Model();
    $data_user->add_stat_search_made($user_data['id'], 1);
    $universeRepository = new AstroObject_Model();
    $criteria = new SearchCriteria_Helper($server_config);
    if (isset($pub_galaxy_down) && isset($pub_galaxy_up)) {
        $criteria->setGalaxyDown(intval($pub_galaxy_down));
        $criteria->setGalaxyUp(intval($pub_galaxy_up));
    }
    if (isset($pub_system_down) && isset($pub_system_up)) {
        $criteria->setSystemDown(intval($pub_system_down));
        $criteria->setSystemUp(intval($pub_system_up));
    }
    if (isset($pub_row_down) && isset($pub_row_up)) {
        $criteria->setRowDown(intval($pub_row_down));
        $criteria->setRowUp(intval($pub_row_up));
    }
    switch ($pub_type_search) {
        case "player":
            if ($pub_string_search == "") {
                break;
            }
            $search = isset($pub_strict) ? $pub_string_search : "%" . $pub_string_search . "%";
            $criteria->setPlayerName($search);
            break;
        case "ally":
            if ($pub_string_search == "") {
                break;
            }
            $search = isset($pub_strict) ? $pub_string_search : "%" . $pub_string_search . "%";
            $criteria->setAllyName($search);
            break;
        case "planet":
            if ($pub_string_search == "") {
                break;
            }
            $search = isset($pub_strict) ? $pub_string_search : "%" . $pub_string_search . "%";
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
            //Binu : ajout du critère spy
        case "spy":
            $criteria->setIsSpied(true);
            break;
            //fin
    }
    if (!$criteria->isValid()) {
        return array($search_result, $total_page);
    }
    if (!isset($pub_sort2)) {
        $pub_sort2 = "0";
    }
    switch ($pub_sort2) {
        case "1":
            $order2 = " DESC";
            break;
        default:
            $order2 = " ASC";
            break;
    }
    if (!isset($pub_sort)) {
        $pub_sort = "1";
    }
    switch ($pub_sort) {
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
    if (!isset($pub_page)) {
        $pub_page = 1;
    }
    $number = 30;
    $limit = intval($pub_page - 1) * $number;
    if ($limit < 0) {
        $limit = 0;
        $pub_page = 1;
    }
    $result = $universeRepository->find($criteria, $order, $limit, $number);
    $total_page = ceil($result['total_row'] / $number);
    $search_result_1D = array(); // Initialiser comme un tableau 1D
    $log->debug('Planet list from Galaxy (raw from find method)', $result['planets']);

    // $result['planets'] est maintenant un tableau 1D d'objets planète
    if (is_array($result['planets'])) {
        foreach ($result['planets'] as $planet_details) {
            // Vérifier que $planet_details est un tableau et contient les clés nécessaires
            if (!is_array($planet_details) ||
                !isset($planet_details['galaxy']) ||
                !isset($planet_details['system']) ||
                !isset($planet_details['row']) ||
                !array_key_exists('ally_name', $planet_details)) { // Utiliser array_key_exists pour 'ally_name' car il peut être null
                $log->warn('Skipping malformed planet data item in galaxy_search', $planet_details);
                continue;
            }

            $log->debug('Processing planet for search result (1D)', $planet_details);

            $current_planet_output = $planet_details; // Utiliser directement les détails formatés par find()

            // Ajouter les champs spécifiques calculés dans galaxy_search
            $isFriend = in_array($planet_details["ally_name"], $allied);
            $isHidden = in_array($planet_details["ally_name"], $protected);

            $data_spy = new Spy_Model();
            $nb_spy_reports = $data_spy->get_nb_spy_by_planet(
                $planet_details["galaxy"],
                $planet_details["system"],
                $planet_details["row"]
            );

            $current_planet_output["report_spy"] = $nb_spy_reports;
            $current_planet_output["allied"] = $isFriend;
            $current_planet_output["hided"] = $isHidden;
            // Le champ "planet" n'est plus nécessaire ici car "planet_name" est déjà dans $planet_details
            // Le champ "type" est également déjà dans $planet_details

            $search_result_1D[] = $current_planet_output; // Ajouter à la liste 1D
        }
    } else {
        $log->warn('No planets data or invalid format from find() in galaxy_search', $result['planets']);
    }

    $log->debug('Final search_result for galaxy_search (1D structure)', $search_result_1D);
    return array($search_result_1D, $total_page);
}

/**
 * Retrieves statistics of planets for all galaxies and systems, including total planet count, free planets,
 * and information about recently updated systems.
 *
 * @param int $step The increment of systems to process in each step.
 * @return array An associative array containing:
 *               - "map": Statistics of planets indexed by galaxy and system.
 *               - "nb_planets": Total number of planets across all galaxies and systems.
 *               - "nb_planets_free": Total number of free planets across all galaxies and systems.
 * @global array $user_data The user session data, including the last visit timestamp.
 * @global array $server_config Configuration of the server, including the number of galaxies and systems.
 * @global object $Universe_Model The model used for querying galaxy and system data.
 */
function galaxy_statistic($step = 50)
{
    global $user_data, $server_config;

    $Universe_Model = new AstroObject_Model();

    $statistics = array();

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
 * Retrieves the list of alliances.
 *
 * @return array $ally_list The list of all alliances available in the database.
 */
function galaxy_ally_listing()
{
    $ally_list = (new AstroObject_Model())->get_ally_list();
    return $ally_list;
}

/**
 * Analyzes and retrieves the positions of allies across galaxies and systems.
 *
 * This method processes the input data related to ally positions and retrieves
 * their planet distribution and population statistics based on galaxy and system ranges.
 * It considers configurations like ally protections and user access rights.
 *
 * @param int $step The step value for the system range iteration. Default is 50.
 *                  Defines the number of systems to process at once.
 * @return array Returns an associative array containing ally names as keys and
 *               their statistics by galaxy and system as values. Each statistic
 *               includes the number of planets and their population details.
 */
function galaxy_ally_position($step = 50)
{
    global $user_auth, $user_data, $server_config;
    global $pub_ally_, $nb_colonnes_ally;

    $Universe_Model = new AstroObject_Model();

    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        if (!isset($pub_ally_[$i])) {
            return array();
        }
        if (!check_var($pub_ally_[$i], "Text")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }
    }

    $pub_ally_protection = array();
    // $allied = array();       //TODO:Unused_code
    // if ($server_config["allied"] != "") {
    // $allied = explode(",", $server_config["allied"]);
    // }
    if ($server_config["ally_protection"] != "") {
        $pub_ally_protection = explode(",", $server_config["ally_protection"]);
    }

    $statistics = array();
    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        $pub_ally_list[$i - 1] = $pub_ally_[$i];
    }

    foreach ($pub_ally_list as $pub_ally_name) {
        if ($pub_ally_name == "") {
            continue;
        }
        if (in_array($pub_ally_name, $pub_ally_protection) && $user_auth["server_show_positionhided"] == 0 && $user_data["admin"] == 0 && $user_data["coadmin"] == 0) {
            $statistics[$pub_ally_name][0][0] = null;
            continue;
        }
        // $friend = false; //TODO:Unused_code
        // if (in_array($pub_ally_name, $allied)) {
        // $friend = true;
        // }

        for ($galaxy = 1; $galaxy <= $server_config['num_of_galaxies']; $galaxy++) {
            for ($system = 1; $system <= $server_config['num_of_systems']; $system = $system + $step) {

                $population = array();
                $population = $Universe_Model->get_ally_position($galaxy, $system, ($system + $step - 1), $pub_ally_name);
                $nb_planet = $Universe_Model->sql_affectedrows();
                //$nb_planet =  count($population);     //TODO:Unused_code

                $statistics[$pub_ally_name][$galaxy][$system] = array("planet" => $nb_planet, "population" => $population);
            }
        }
    }
    user_set_stat(null, null, 1);

    return $statistics;
}

/**
 * Retrieves and processes spy reports for a specific planet based on galaxy, system, and row coordinates.
 *
 * This method validates the input coordinates against configuration limits and retrieves
 * the spy report details associated with the specified location. The returned data includes
 * information about the spies, such as IDs, senders, recorded data, moon indicator, and timestamps.
 *
 * @return array|bool Returns an array of spy report details where each report contains
 *                    the spy ID, sender name, parsed data, moon indicator, and timestamp.
 *                    Returns false if the input coordinates are invalid or out of range.
 */
function galaxy_reportspy_show()
{
    global $pub_galaxy, $pub_system, $pub_row, $server_config,$log;

    if (!check_var($pub_galaxy, "Num") || !check_var($pub_system, "Num") || !check_var($pub_row, "Num")) {
        return false;
    }

    if (!isset($pub_galaxy) || !isset($pub_system) || !isset($pub_row)) {
        return false;
    }
    if ((int)$pub_galaxy < 1 || (int)$pub_galaxy > (int)$server_config['num_of_galaxies'] || (int)$pub_system < 1 || (int)$pub_system > (int)$server_config['num_of_systems'] || (int)$pub_row < 1 || (int)$pub_row > 15) {
        return false;
    }

    $planetId = (new AstroObject_Model())->get_planetId_by_coordinates($pub_galaxy, $pub_system, $pub_row);
    $Spy_Model = new Spy_Model();
    $spy_list = $Spy_Model->get_spy_id_list_by_planet($planetId);
    $log->debug('Spy list from galaxy_reportspy_show', [$spy_list]);
    $reports = array();
    foreach ($spy_list as $row) {
        $data = UNparseRE($row["spy_id"]);
        $reports[] = array("spy_id" => $row["spy_id"], "sender" => $row["user_name"], "data" => $data, "moon" => $row['is_moon'] ?? 0, "dateRE" => $row['dateRE']);
    }
    return $reports;
}

/**
 * Retrieves the combat reports for a specific planet based on its galaxy, system, and row coordinates.
 *
 * @return array|false Returns an array containing the parsed combat reports for the planet if valid coordinates are provided,
 *                     or false if the input coordinates are invalid or missing.
 * @global int $pub_system System coordinate of the planet
 * @global int $pub_row Row coordinate of the planet
 * @global array $server_config Configuration array containing the number of galaxies and systems
 * @global int $pub_galaxy Galaxy coordinate of the planet
 */
function galaxy_reportrc_show()
{
    global $pub_galaxy, $pub_system, $pub_row, $server_config;

    if (!check_var($pub_galaxy, "Num") || !check_var($pub_system, "Num") || !check_var($pub_row, "Num")) {
        return false;
    }

    if (!isset($pub_galaxy) || !isset($pub_system) || !isset($pub_row)) {
        return false;
    }
    if (intval($pub_galaxy) < 1 || intval($pub_galaxy) > intval($server_config['num_of_galaxies']) || intval($pub_system) < 1 || intval($pub_system) > intval($server_config['num_of_systems']) || intval($pub_row) < 1 || intval($pub_row) > 15) {
        return false;
    }

    $Combat_Report_Model = new Combat_Report_Model();
    $report_list = $Combat_Report_Model->get_cr_id_list_by_planet(intval($pub_galaxy), intval($pub_system), intval($pub_row));

    $reports = array();
    foreach ($report_list as $report_id) {
        $reports[] = UNparseRC($report_id);
    }
    return $reports;
}

/**
 * Purges expired spy reports from the database.
 *
 * This method removes spy reports from the system that have exceeded their expiration time,
 * as defined by the server's maximum retention period configuration.
 *
 * @return void Does not return any value. Performs a cleanup operation on the spy reports.
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
 * Retrieves the list of user-selected favorite items.
 *
 * This method fetches the favorites associated with the currently logged-in user
 * by querying the user favorites model. It provides an array containing the user's
 * favorite entries.
 *
 * @return array Returns an array containing the user's favorite items. Each item
 *               represents a favorite entry stored in the database.
 */
function galaxy_getfavorites()
{
    global $user_data;
    $favorite = (new User_Favorites_Model())->select_user_favorites($user_data["id"]);
    return $favorite;
}

/**
 * Retrieves and displays rankings from a specified ranking table.
 *
 * This method fetches rankings from the provided model and ranking table.
 * If a specific date is not provided, it retrieves the latest available
 * rankings. In case no rankings are found, it returns an error indicator.
 *
 * @param object $model The model instance used to query ranking data.
 * @param string $ranking_table The name of the table containing ranking data.
 * @param string|null $date Optional. The specific date for retrieving rankings.
 *                           If null, the method uses the latest available date.
 * @return array|int Returns an array of rankings data if successful, or -1 if
 *                   no ranking data is available.
 */
function galaxy_show_ranking($model, $ranking_table, $date = null)
{
    global $pub_date;
    // Récupération de la taille max des tableaux
    $data_rankings = $model;
    //Récupération de la dernière date de classement
    if ($date == null) {
        $last_ranking = $data_rankings->get_rank_latest_table_date($ranking_table);
    } else {
        $last_ranking = $pub_date;
    }
    if ($last_ranking == null) {
        return -1;
    }
    // Pas de classement disponible
    $ranking = $data_rankings->get_all_ranktable_bydate($last_ranking, 1, 99999);
    return $ranking;
}

/**
 * Retrieves and processes the ranking data of players.
 *
 * This method handles player ranking data based on specified criteria, including order type,
 * date, and interval. It validates inputs, fetches player ranking data from the model,
 * processes the data into structured arrays, and retrieves additional metadata such as
 * available ranking dates and the highest rank.
 *
 * @return array Returns an array containing four elements:
 *               - An associative array where keys are player positions and values are player names.
 *               - A detailed associative array with player information, including ally names,
 *                 ranks, and points across categories like general, economy, technology,
 *                 honor, and military (with subcategories).
 *               - A unique list of available ranking dates.
 *               - The maximum rank found for the current ranking criteria.
 */
function galaxy_show_ranking_player()
{
    global $pub_order_by, $pub_date, $pub_interval;

    $Rankings_Player_Model = new Rankings_Player_Model();

    if (!isset($pub_order_by)) {
        $pub_order_by = "general";
    }
    $tables = $Rankings_Player_Model->get_rank_tables();
    $name = $Rankings_Player_Model->get_rank_table_ref();

    // verification de la variable pub_order
    if (!in_array($pub_order_by, $name)) {
        $pub_order_by = "general";
    }


    // selection du max rank
    $maxrank = max($Rankings_Player_Model->select_max_rank_row());


    if (!isset($pub_interval)) {
        $pub_interval = 1;
    }
    if (($pub_interval - 1) % 100 != 0 || $pub_interval > $maxrank) {
        $pub_interval = 1;
    }
    $limit_down = $pub_interval;
    $limit_up = $pub_interval + 99;

    $order = array();
    $ranking = array();
    $ranking_available = array();

    // on determine l id du pub order
    $id = (array_keys($name, $pub_order_by));
    $orderTableName = $tables[$id[0]];
    $orderStringName = $name[$id[0]];

    if (!isset($pub_date)) {
        $last_ranking = $Rankings_Player_Model->get_rank_latest_table_date($orderTableName);
    } else {
        $last_ranking = $pub_date;
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
        $ranking[$currentPlayer]["honor"] = array("rank" => $ranktable_bydate["milh_rank"], "points" => $ranktable_bydate["milh_pts"]);

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
 * Retrieves and organizes alliance rankings data by various criteria and intervals.
 *
 * @return array Contains the following components:
 *               - $order: An array mapping ranking positions to alliance names.
 *               - $ranking: An associative array containing detailed ranking information for each alliance.
 *               - $ranking_available: An array of all available distinct ranking dates.
 *               - $maxrank: The maximum rank value derived from the dataset.
 * @global int $pub_date Specifies the date or timeframe for which the rankings should be retrieved.
 * @global int $pub_interval Specifies the ranking interval to display (e.g., top 100, next 100).
 * @global string $pub_suborder Determines sub-ordering categories if applicable.
 * @global string $pub_order_by Specifies the attribute by which the rankings will be ordered.
 */
function galaxy_show_ranking_ally()
{
    global $pub_order_by, $pub_date, $pub_interval, $pub_suborder;
    $Rankings_Ally_Model = new Rankings_Ally_Model();

    if (!check_var($pub_order_by, "Char") || !check_var($pub_date, "Num") || !check_var($pub_interval, "Num") || !check_var($pub_suborder, "Char")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $tables = $Rankings_Ally_Model->get_rank_tables();
    $name = $Rankings_Ally_Model->get_rank_table_ref();

    // verification de la variable pub_order_by
    if (!in_array($pub_order_by, $name)) {
        $pub_order_by = "general";
    }

    // selection de rank max !
    $maxrank = max($Rankings_Ally_Model->select_max_rank_row());

    // if (isset($pub_suborder) && $pub_suborder == "member") {
    // $pub_order_by2 = "points_per_member desc";
    // } else {
    // $pub_order_by2 = "rank";
    // }

    if (!isset($pub_interval)) {
        $pub_interval = 1;
    }
    if (($pub_interval - 1) % 100 != 0 || $pub_interval > $maxrank) {
        $pub_interval = 1;
    }
    $limit_down = $pub_interval;
    $limit_up = $pub_interval + 99;

    $order = array();
    $ranking = array();
    $ranking_available = array();

    // on determine l id du pub order
    $id = (array_keys($name, $pub_order_by));
    $orderTableName = $tables[$id[0]];
    $orderStringName = $name[$id[0]];

    if (!isset($pub_date)) {
        $last_ranking = $Rankings_Ally_Model->get_rank_latest_table_date($orderTableName);
    } else {
        $last_ranking = $pub_date;
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
        $ranking[$currentAlly]["honor"] = array("rank" => $ranktable_bydate["milh_rank"], "points" => $ranktable_bydate["milh_pts"]);

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
 * Retrieves the ranking details of a specific player by date.
 *
 * @param int $playerId The unique identifier of the player whose ranking data is to be retrieved.
 * @param bool $last If set to true, fetches only the most recent ranking data for the player.
 * @return array An associative array containing the player's rankings categorized by date
 *               and types such as general, economy, technology, honor, and military.
 */
function galaxy_show_ranking_unique_player(int $playerId, $last = false)
{

    $ranking = [];
    $tRanking = (new Rankings_Player_Model())->get_all_ranktable_byplayer($playerId);
    foreach ($tRanking as $rank) {
        $ranking[$rank["datadate"]]["general"]["rank"] = $rank["general_rank"];
        $ranking[$rank["datadate"]]["general"]["points"] = $rank["general_pts"];

        if ((int)$rank["eco_rank"] > 0) {
            $ranking[$rank["datadate"]]["eco"]["rank"] = $rank["eco_rank"];
            $ranking[$rank["datadate"]]["eco"]["points"] = $rank["eco_pts"];
        }

        if ((int)$rank["tech_rank"] > 0) {
            $ranking[$rank["datadate"]]["techno"]["rank"] = $rank["tech_rank"];
            $ranking[$rank["datadate"]]["techno"]["points"] = $rank["tech_pts"];
        }

        if ((int)$rank["milh_rank"] > 0) {
            $ranking[$rank["datadate"]]["honor"]["rank"] = $rank["milh_rank"];
            $ranking[$rank["datadate"]]["honor"]["points"] = $rank["milh_pts"];
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
 * Retrieves and organizes ranking data for a specific player based on given parameters.
 *
 * @param int $playerId The unique identifier of the player.
 * @param int|null $date_min Optional. The minimum date (timestamp) to filter rankings. Defaults to null.
 * @param int|null $date_max Optional. The maximum date (timestamp) to filter rankings. Defaults to null.
 * @param bool $last Optional. Whether to return only the first matching ranking record. Defaults to false.
 * @return array An array containing the player's ranking data organized by rank and points categories.
 */
function galaxy_show_ranking_unique_player_forJS(int $playerId, $date_min = null, $date_max = null, $last = false)
{

    $ranking = [];
    $tRanking = (new Rankings_Player_Model())->get_all_ranktable_byplayer($playerId);
    $player = (new Player_Model())->get_player_data($playerId);


    foreach ($tRanking as $rank) {
        if ($rank["datadate"] >= $date_min && $rank["datadate"] <= $date_max) // ajouter dans la requete ca serait top
        {

            $ranking["rank"]["general (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["general_rank"] . "]";
            $ranking["points"]["general (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["general_pts"] . "]";

            if ((int)$rank["eco_rank"] > 0) {
                $ranking["rank"]["Economique (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["eco_rank"] . "]";
                $ranking["points"]["Economique (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["eco_pts"] . "]";
            }

            if ((int)$rank["tech_rank"] > 0) {
                $ranking["rank"]["Recherche (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["tech_rank"] . "]";
                $ranking["points"]["Recherche (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["tech_pts"] . "]";
            }

            if ((int)$rank["milh_rank"] > 0) {
                $ranking["rank"]["Honneur (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milh_rank"] . "]";
                $ranking["points"]["Honneur (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milh_pts"] . "]";
            }

            if ((int)$rank["mil_rank"] > 0) {
                $ranking["rank"]["Militaire (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mil_rank"] . "]";
                $ranking["points"]["Militaire (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mil_pts"] . "]";
            }
            if ((int)$rank["milb_rank"] > 0) {
                $ranking["rank"]["Militaire Construits (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milb_rank"] . "]";
                $ranking["points"]["Militaire Construits (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["milb_pts"] . "]";
            }

            if ((int)$rank["mill_rank"] > 0) {
                $ranking["rank"]["Perte militaire (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mill_rank"] . "]";
                $ranking["points"]["Perte militaire (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mill_pts"] . "]";
            }

            if ((int)$rank["mild_rank"] > 0) {
                $ranking["rank"]["destruction (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mild_rank"] . "]";
                $ranking["points"]["destruction (" . $player['name'] . ")"][] = "[" . $rank["datadate"] * 1000 . ", " . $rank["mild_pts"] . "]";
            }

            if ($last) {
                break;
            }
        }
    }
    return $ranking;
}

/**
 * Generates ranking data for a specific alliance based on various criteria.
 *
 * @param string $ally The identifier for the specific alliance.
 * @param bool $last If true, only the latest ranking data is retrieved.
 * @return array An associative array containing ranking information for the specified alliance,
 *               categorized by date and various ranking categories such as general, economy, technology,
 *               military, honor, and specific military subdivisions.
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
            $ranking[$rank["datadate"]]["honor"]["rank"] = $rank["milh_rank"];
            $ranking[$rank["datadate"]]["honor"]["points"] = $rank["milh_pts"];
            $ranking[$rank["datadate"]]["honor"]["points_per_member"] = $rank["milh_pts_mb"];
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
 * Purges outdated player ranking data based on server-defined criteria.
 *
 * This method analyzes the current server configuration and removes outdated
 * ranking data from the database. It supports both time-based and quantity-based
 * criteria for purging. The time-based criterion deletes data older than a specified
 * number of days, while the quantity-based criterion retains a maximum number
 * of rank entries, deleting older data if the limit is exceeded.
 *
 * @return void This method does not return a value.
 */
function galaxy_purge_ranking(): void
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
 * Deletes all ranking data for players or alliances based on the provided date.
 *
 * This method verifies user input and permissions before proceeding to remove
 * ranking data for either players or alliances using the specified date.
 * The action performed depends on the 'subaction' parameter, which determines
 * whether player or alliance rankings are targeted.
 *
 * @return void No return value. Redirects to the appropriate ranking page upon successful execution.
 */
function galaxy_drop_ranking()
{
    global $pub_datadate, $pub_subaction;

    if (!check_var($pub_datadate, "Num") || !check_var($pub_subaction, "Char")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    //Vérification des droits
    galaxy_check_auth("drop_ranking");

    if (!isset($pub_datadate) || !isset($pub_subaction)) {
        redirection("index.php");
    }

    if ($pub_subaction == "player") {

        $Rankings_Player_Model = new Rankings_Player_Model();
        $Rankings_Player_Model->remove_all_rank_by_datadate(intval($pub_datadate));
    } elseif ($pub_subaction == "ally") {
        //todo varidable dtadate = 0 ici :/ voir affichage
        $Rankings_Ally_Model = new Rankings_Ally_Model();
        $Rankings_Ally_Model->remove_all_rank_by_datadate(intval($pub_datadate));
    }
    redirection("index.php?action=ranking&subaction=" . $pub_subaction);
}

/**
 * Retrieves the list of phalanx systems capable of scanning the specified system.
 *
 * @param int $galaxy The galaxy where the system to be scanned is located.
 * @param int $system The specific system to be scanned for phalanx range.
 * @param string $classe Optional class modifier affecting the range of phalanx, default is 'none'.
 * @return array Returns an array containing information about phalanx systems capable of scanning the specified system.
 *               Each item includes details such as galaxy, system, row, player, ally, gate presence, phalanx level, and range boundaries.
 * @global array $user_data Information about the currently logged-in user (e.g., admin status).
 * @global array $user_auth Authorization settings for the logged-in user.
 * @global array $server_config Holds server configuration settings such as galaxy and system limits.
 */
function galaxy_get_phalanx($galaxy, $system, $classe = 'none')
{
    global $server_config, $user_data, $user_auth;

    $ally_protection = array();
    if ($server_config["ally_protection"] != "") {
        $ally_protection = explode(",", $server_config["ally_protection"]);
    }

    $phalanxer = array();
    $data_computed = array();

    $data = (new AstroObject_Model())->get_phalanx($galaxy);

    if (count($data) > 0) {
        //Construction liste phalanges
        foreach ($data as $phalanx) {
            $arrondi_type = 0;
            $phalanx_range = ogame_phalanx_range($phalanx['level'], $classe);
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
                ($system >= $system_higher_range && $system >= $system_lower_range && $arrondi_type == 1)
            ) {
                $add_to_list = true;
                //Cas 3 : Phanlange en fin de galaxie -> 2 zones possibles : 1 en fin de galaxie et 1 en début
            } elseif (($system >= $system_lower_range && $system >= $system_higher_range && $arrondi_type == 2) ||
                ($system <= $system_lower_range && $system <= $system_higher_range && $arrondi_type == 2)
            ) {
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
            if (!in_array($phalange["ally"], $ally_protection) || $phalange["ally"] == "" || $user_auth["server_show_positionhided"] == 1 || $user_data["admin"] == 1 || $user_data["coadmin"] == 1) {
                $phalanxer[] = $phalange;
            }
        }
    }
    return $phalanxer;
}

/**
 * Identifies and retrieves objects within a specific galaxy perimeter that are considered obsolete.
 *
 * This method processes the input parameters for perimeter, time since obsolescence,
 * and type of search (moons or planets) to retrieve a list of obsolete entities.
 * It validates input data and calculates time indices to perform a query for retrieval.
 *
 * @return array Returns an array of obsolete astronomical objects based on the specified
 *               perimeter, timeframe, and type of object (planets or moons). Returns an
 *               empty array if input validation fails or no obsolete objects are found.
 */
function galaxy_obsolete()
{
    global $pub_perimeter, $pub_since, $pub_typesearch;

    $obsolete = array();
    if (isset($pub_perimeter) && isset($pub_since) && is_numeric($pub_perimeter) && is_numeric($pub_since)) {
        if (!isset($pub_typesearch) || ($pub_typesearch != "M" && $pub_typesearch != "P")) {
            $pub_typesearch = "P";
        }


        $timestamp = time();
        // tableau regroupant les valeurs possibles
        $since = array(0, 7, 14, 21, 28, 42, 56, $timestamp - 1);

        // on regarde l existence de la variable
        if (!in_array((int)$pub_since, $since)) {
            return $obsolete;
        }

        // on recupere l indice de recherche
        $indice = array_search((int)$pub_since, $since);

        // l indice ne peut pas etre le premier ou le dernier
        if ($indice == 0 || $indice == (count($since) - 1)) {
            return $obsolete;
        }
        $indice_sup = $timestamp - 60 * 60 * 24 * $since[$indice + 1];
        $indice_inf = $timestamp - 60 * 60 * 24 * $since[$indice - 1];

        // on peut maintenant lancer une requete générique
        // on peut maintenant lancer une requete générique
        $formoon = true;
        if ($pub_typesearch == "P") {
            $formoon = false;
        }
        $obsolete = (new AstroObject_Model())->get_galaxy_obsolete($pub_perimeter, $indice_inf, $indice_sup, $indice, $since, $formoon);
    }


    return $obsolete;
}

/**
 * Parses and reconstructs espionage report data for a given report ID.
 *
 * This method analyzes and retrieves data related to fleets, defenses, buildings,
 * and technologies for a specific espionage report. It processes this information
 * to present a comprehensive view of the espionage target, handling cases where
 * data reconstruction is required from other related reports.
 *
 * @param int $id_RE The ID of the espionage report to process.
 * @return array Returns an associative array representing the reconstructed espionage
 *               report, including fleet, defense, building, and technology information.
 */
function UNparseRE($id_RE)
{
    global $lang;

    $Spy_Model = new Spy_Model();
    $Universe_Model = new AstroObject_Model();

    $show = [
        'flotte' => 0,
        'defense' => 0,
        'batiment' => 0,
        'recherche' => 0
    ];

    $flotte = [
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
        'TRA' => $lang['GAME_FLEET_TRA'],
        'FOR' => $lang['GAME_FLEET_FOR'],
        'FAU' => $lang['GAME_FLEET_FAU'],
        'ECL' => $lang['GAME_FLEET_ECL']
    ];

    $defs = [
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
    ];

    $bats = [
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
    ];

    $techs = [
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
    ];

    $row = $Spy_Model->get_spy_Id($id_RE);
    $rowPlayerName = $Universe_Model->get_player_name($row['astro_object_id']);

    $tRows = $Spy_Model->get_spy_data($row['astro_object_id']); /// contient tous les re disponible sur la planet ou lune
    $sep_mille = ".";

    foreach ($flotte as $key => $value) {
        if ($row[$key] != -1) {
            $show['flotte'] = 1;
        }
    }
    if ($show['flotte'] == 0) {
        // besoin d 'une eventuelle reconstitution de re'
        foreach ($tRows as $tmpRow) {
            if ($row["astro_object_id"] == $tmpRow["astro_object_id"] && $show['flotte'] == 0) // on recherche sur la meme planete ou lune
            {

                $total = $tmpRow["PT"] + $tmpRow["GT"] + $tmpRow["CLE"] + $tmpRow["CLO"] + $tmpRow["CR"] + $tmpRow["VB"] + $tmpRow["VC"] + $tmpRow["REC"] + $tmpRow["SE"] + $tmpRow["BMD"] + $tmpRow["DST"] + $tmpRow["EDLM"] + $tmpRow["SAT"] + $tmpRow["TRA"] + $tmpRow["FOR"] + $tmpRow["FAU"] + $tmpRow["ECL"];
                if ((int)$total != -17) {
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
                    $row["FOR"] = $tmpRow["FOR"];
                    $row["FAU"] = $tmpRow["FAU"];
                    $row["ECL"] = $tmpRow["ECL"];

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
            if ($row["astro_object_id"] == $tmpRow["astro_object_id"] && $show['defense'] == 0) // on recherche sur la meme planete ou lune
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
            if ($row["astro_object_id"] == $tmpRow["astro_object_id"] && $show['batiment'] == 0) // on recherche sur la meme planete ou lune
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

                $total = $tmpRow["Esp"] + $tmpRow["Ordi"] + $tmpRow["Armes"] + $tmpRow["Bouclier"] + $tmpRow["NRJ"] + $tmpRow["Hyp"] + $tmpRow["RC"] + $tmpRow["RI"] + $tmpRow["PH"] + $tmpRow["Laser"] + $tmpRow["Ions"] + $tmpRow["Plasma"] + $tmpRow["Graviton"] + $tmpRow["Astrophysique"];
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

    $showName = [
        'flotte' => 'flotte',
        'defense' => 'defs',
        'batiment' => 'bats',
        'recherche' => 'techs'
    ];

    // Ces clés de langue sont des suppositions. Ajustez-les si nécessaire.
    // Par exemple: $lang['SECTION_FLEET'] ou $lang['FLOTTE_TITLE']
    $showLang = [
        'flotte' => isset($lang['GAME_SPYREPORT_TITLE_FLEET']) ? $lang['GAME_SPYREPORT_TITLE_FLEET'] : 'Flotte',
        'defense' => isset($lang['GAME_SPYREPORT_TITLE_DEFENSE']) ? $lang['GAME_SPYREPORT_TITLE_DEFENSE'] : 'Défense',
        'batiment' => isset($lang['GAME_SPYREPORT_TITLE_BUILDING']) ? $lang['GAME_SPYREPORT_TITLE_BUILDING'] : 'Bâtiments',
        'recherche' => isset($lang['GAME_SPYREPORT_TITLE_RESEARCH']) ? $lang['GAME_SPYREPORT_TITLE_RESEARCH'] : 'Recherche'
    ];

    $dateRE = date('m-d H:i:s', $row['dateRE']);
    $template = buildSpyReportTemplate($row, $rowPlayerName, $lang, $dateRE, $show, $showLang, $showName, $flotte, $defs, $bats, $techs, $sep_mille);
    return $template;
}

/**
 * Génère le modèle HTML d'un rapport d'espionnage.
 *
 * @param array $row Les données de la planète ou lune espionnée, incluant les ressources, flotte, défenses, bâtiments et recherches.
 * @param string $rowPlayerName Les informations sur le joueur propriétaire de la planète ou lune.
 * @param array $lang Les chaînes de langue utilisées pour les libellés du rapport.
 * @param string $dateRE La date et l'heure du rapport d'espionnage.
 * @param array $show Indique les sections à afficher dans le rapport (flotte, défense, bâtiment, recherche).
 * @param array $showLang Les libellés des catégories à afficher (flotte, défense, bâtiment, recherche).
 * @param array $showName Les noms des catégories à afficher (flotte, défense, bâtiment, recherche).
 * @param array $flotte Les libellés des types de vaisseaux.
 * @param array $defs Les libellés des types de défenses.
 * @param array $bats Les libellés des types de bâtiments.
 * @param array $techs Les libellés des types de recherches.
 * @param string $sep_mille Le séparateur utilisé pour le formatage des nombres.
 * @return string Le code HTML du rapport d'espionnage.
 */
function buildSpyReportTemplate($row, $rowPlayerName, $lang, $dateRE, $show, $showLang, $showName, $flotte, $defs, $bats, $techs, $sep_mille) {

    global $log;
    $log->debug("Building spy report template for player: " , [$rowPlayerName]);
    $log->debug("Building spy report row: ",[ $row ]);

    $Universe_Model = new AstroObject_Model();

    $planetName = $Universe_Model->getPlanetNameByObjectId($row['astro_object_id']);
    $playerName = $rowPlayerName ?? 'Inconnu';

    list($galaxy, $system, $line) = $Universe_Model->getPlanetCoordsByObjectId($row['astro_object_id']);

    $coords = $galaxy . ':' .$system . ':' . $line;


    $html = '<table class="og-table og-table-spy">';

    // En-tête principal
    $html .= '<thead><tr><th colspan="4">' .
        $lang['GAME_SPYREPORT_RES'] . ' ' . $planetName . ' [' . $coords . '] (' .
        $lang['GAME_SPYREPORT_PLAYER'] . ' \'' . $playerName . '\') le ' . $dateRE .
        '</th></tr></thead>';

    // Ressources
    $html .= '<tbody><tr>' .
        '<td class="tdstat">' . $lang['GAME_RES_METAL'] . ':</td>' .
        '<td class="tdvalue">' . number_format($row['metal'], 0, ',', $sep_mille) . '</td>' .
        '<td class="tdstat">' . $lang['GAME_RES_CRYSTAL'] . ':</td>' .
        '<td class="tdvalue">' . number_format($row['crystal'], 0, ',', $sep_mille) . '</td>' .
        '</tr><tr>' .
        '<td class="tdstat">' . $lang['GAME_RES_DEUT'] . ':</td>' .
        '<td class="tdvalue">' . number_format($row['deuterium'], 0, ',', $sep_mille) . '</td>' .
        '<td class="tdstat">' . $lang['GAME_RES_ENERGY'] . ':</td>' .
        '<td class="tdvalue">' . number_format($row['energie'], 0, ',', $sep_mille) . '</td>' .
        '</tr><tr>' .
        '<td class="tdcontent" colspan="4">';

    // Activité
    if ($row['activite'] > 0) {
        $html .= $lang['GAME_SPYREPORT_ACTIVITY'] . ' ' . $row['activite'] . ' ' . $lang['GAME_SPYREPORT_LASTMINUTES'] . '.';
    } else {
        $html .= $lang['GAME_SPYREPORT_NOACTIVITY'];
    }

    $html .= '</td></tr></tbody>';

    // Sections flotte, défense, bâtiment, recherche
    foreach ($show as $type => $showValue) {
        if ($showValue == 1) {
            $html .= '<thead><tr><th colspan="4">' . $showLang[$type] . '</th></tr></thead><tbody>';

            $count = 0;
            $conteneur = $showName[$type];
            $containerVariable = ${$conteneur};

            foreach ($containerVariable as $key => $value) {
                if ($row[$key] > 0) {
                    if ($count == 0) {
                        $html .= '<tr>';
                    }

                    $html .= '<td class="tdstat">' . $containerVariable[$key] . ':</td>' .
                        '<td class="tdvalue">' . number_format($row[$key], 0, ',', $sep_mille) . '</td>';

                    if ($count == 1) {
                        $html .= '</tr>';
                        $count = 0;
                    } else {
                        $count = 1;
                    }
                }
            }

            if ($count == 1) {
                $html .= '<td class="tdstat"></td><td class="tdvalue"></td></tr>';
            }

            $html .= '</tbody>';
        }
    }

    // Probabilité de destruction
    $html .= '<thead><tr><th colspan="4">' .
        $lang['GAME_SPYREPORT_PROBADEST'] . ' : ' . $row['proba'] . '%' .
        '</th></tr></thead>';

    $html .= '</table>';

    return $html;
}

/**
 * Calculates the missile range and determines if a missile can reach a specified galaxy and system.
 *
 * @param int $galaxy The galaxy number to check missile range against.
 * @param int $system The system number within the galaxy to check missile range against.
 * @return string Details about the missiles in range, including player names and available missile count.
 * @global array $server_config Global configuration array containing the server's settings.
 */
function galaxy_portee_missiles($galaxy, $system)
{
    global  $server_config;

    $playerModel = new Player_Model();
    $userBuildingModel = new Player_Building_Model();
    $userTechnologyModel = new Player_Technology_Model();
    $userDefenseModel = new Player_Defense_Model();

    $missil_ok = '';
    $ok_missil = '';
    $total_missil = 0;
    // recherche niveau missile


    $tUser_building = $userBuildingModel->get_building_by_silo(3);

    foreach ($tUser_building as $User_building) {

        $base_joueur = $User_building["player_id"];
        $base_id_planet = $User_building["id"];

        // sépare les coords
        $galaxie_missil = $User_building['galaxy'];
        $sysSol_missil = $User_building['system'];

        // recherche le niveau du réacteur du joueur
        $tUser_Technology =  $userTechnologyModel->select_player_technologies($base_joueur);
        $niv_reac_impuls = $tUser_Technology["RI"] ?? 0;

        if ($niv_reac_impuls > 0) {

            // recherche du nombre de missile dispo
            $tUser_defense = $userDefenseModel->select_player_defense_planete($base_id_planet);
            $missil_dispo = (!isset($tUser_defense['MIP']) ? 0 : $tUser_defense['MIP']);

            // Nom du joueur
            $nom_missil_joueur = $playerModel->get_player_name($base_joueur);

            // Coordonnées de la base
            $base_coord = $galaxie_missil . ':' . $sysSol_missil . ':' . $User_building['row'];

            // calcul de la porté du silo
            $porte_missil = ogame_missile_range($niv_reac_impuls); // Portée : (Lvl 10 * 5) - 1 = 49

            // calcul de la fenetre
            $vari_missil_moins_tmp = (($sysSol_missil - $porte_missil) + $server_config['num_of_systems']) % $server_config['num_of_systems'];
            $vari_missil_moins = (($sysSol_missil - $porte_missil) < 1) ? 1 : $vari_missil_moins_tmp;
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
    $tooltip = $tooltip = htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8');


    $door = '<a id="linkdoor" href="?action=search&type_search=planet&galaxy=' . $galaxie_missil . '&system=' . $sysSol_missil .  '"';
    $total_missil += (int)$missil_dispo;
    $missil_ready = "<span style='color: #DBBADC; '> " . $total_missil . " " . $lang['GALAXY_MIP_MIPS'] . " </span>";

    //<a href="index.htm" onmouseover="return escape('Some text')">Homepage </a>
    $ok_missil .= $nom_missil_joueur . " - " . $door . $missil_ready . $color_missil_ally1 . $base_coord . $color_missil_ally2;

    if ($ok_missil) {
        $missil_ok = "<br><span style='color: #FFFF66; '> " . $lang['GALAXY_MIP_UNDERFIRE'] . " : </span>" . $ok_missil . "</a>";
    } else {
        $missil_ok = "<span style='color: #FFFF66; '> " . $lang['GALAXY_MIP_NOMIPS_AROUND'] . "</span>";
    }

    return $missil_ok;
}

/*
* @return string
*/
function displayGalaxyLegend()
{
    global $lang;

    //creation de la table
    $legend = '<table class="og-table og-small-table og-table-galaxy">';
    $legend .= '<thead>';
    $legend .= '<tr><th colspan="2">' . $lang['GALAXY_LEGEND'] . "</th></tr>";
    $legend .= '</thead>';
    $legend .= '<tbody>';
    $legend .= "<tr><td>" . $lang['GALAXY_INACTIVE_7Days'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-i\">" . $lang['GALAXY_INACTIVE_7Days_SYMBOL'] . "</span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_INACTIVE_28Days'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-i\">" . $lang['GALAXY_INACTIVE_28Days_SYMBOL'] . "</span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_HOLIDAYS'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-v\">" . $lang['GALAXY_HOLIDAYS_SYMBOL'] . "<span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_WEAK_PROTECTION'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-d\">" . $lang['GALAXY_WEAK_PROTECTION_SYMBOL'] . "</span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_MOON'] . "</td><td class=\"tdcontent\"><span class=\"ogame-icon ogame-icon-moon \"><span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_MOON_PHALANX'] . "</td><td class=\"tdcontent\"><span class=\"ogame-icon ogame-icon-phalanx \">4</span><span class=\"ogame-icon ogame-icon-gate \">P</span></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_SPYREPORT'] . "</td><td class=\"tdcontent\"><a>" . $lang['GALAXY_SPYREPORT_SYMBOL'] . "</a></th></tr>";
    $legend .= "<tr><td>" . $lang['GALAXY_COMBATREPORT'] . "</td><td class=\"tdcontent\"><a>" . $lang['GALAXY_COMBATREPORT_SYMBOL'] . "</a></th></tr>";
    $legend .= "<tr class=\"tr-ishided\"><td >" . $lang['GALAXY_ALLY_FRIEND'] . "</td><td class=\"tdcontent\"><a>abc</a></td></tr>";
    $legend .= "<tr class=\"tr-isallied\"><td>" . $lang['GALAXY_ALLY_HIDDEN'] . "</td><td class=\"tdcontent\">abc</td></tr>";
    $legend .= '</tbody>';
    $legend .= "</table>";
    $legend =  htmlspecialchars($legend, ENT_QUOTES, 'UTF-8');

    return $legend;
}


/**
 * @param $player Nom du joueur
 * @return string
 */
function displayGalaxyPlayerTooltip(string $playerName)
{
    global $lang;

    $Player_Model = new Player_Model();
    $playerId = $Player_Model->getPlayerId($playerName);

    $tooltip = '<table class="og-table og-small-table">';
    $tooltip .= "<thead><tr><th colspan=\"3\" >" . $lang['GALAXY_PLAYER'] . " " . $playerName . "</th></tr></thead>";
    $tooltip .= '<tbody>';
    $individual_ranking = galaxy_show_ranking_unique_player($playerId);
    while ($ranking = current($individual_ranking)) {
        $datadate =  date("d F o G:i", key($individual_ranking));
        $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
        $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
        $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
        $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
        $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
        $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
        $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
        $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
        $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : "&nbsp;";
        $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : "&nbsp;";
        $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
        $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
        $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
        $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
        $honor_rank = isset($ranking["honor"]) ? formate_number($ranking["honor"]["rank"]) : "&nbsp;";
        $honor_points = isset($ranking["honor"]) ? formate_number($ranking["honor"]["points"]) : "&nbsp;";

        $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><span class=\"og-highlight\">" . $lang['GALAXY_RANK'] . " " . $datadate . "</span></td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_GENERAL'] . "</td><td class=\"tdcontent\">" . $general_rank . "</td><td class=\"tdcontent\">" . $general_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_ECONOMY'] . "</td><td class=\"tdcontent\">" . $eco_rank . "</td><td class=\"tdcontent\">" . $eco_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_LAB'] . "</td><td class=\"tdcontent\">" . $techno_rank . "</td><td class=\"tdcontent\">" . $techno_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY'] . "</td><td class=\"tdcontent\">" . $military_rank . "</td><td class=\"tdcontent\">" . $military_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_BUILT'] . "</td><td class=\"tdcontent\">" . $military_b_rank . "</td><td class=\"tdcontent\">" . $military_b_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_LOST'] . "</td><td class=\"tdcontent\">" . $military_l_rank . "</td><td class=\"tdcontent\">" . $military_l_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . "</td><td class=\"tdcontent\">" . $military_d_rank . "</td><td class=\"tdcontent\">" . $military_d_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_HONOR'] . "</td><td class=\"tdcontent\">" . $honor_rank . "</td><td class=\"tdcontent\">" . $honor_points . "</td></tr>";
        break;
    }
    $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $playerName . "&amp;strict=on\">" . $lang['GALAXY_SEE_DETAILS'] . "</a></td></tr>";
    $tooltip .= '</tbody>';
    $tooltip .= "</table>";

    $tooltip = htmlentities($tooltip);
    return $tooltip;
}


/**
 * @param $ally Nom de l'alliance
 * @return string
 */
function displayGalaxyAllyTooltip($ally)
{
    global $lang;

    $tooltip = '<table class="og-table og-small-table">';
    $tooltip .= '<thead><tr><th colspan="3">' . $lang['GALAXY_ALLY'] . " " . $ally . '</th></tr></thead>';
    $tooltip .= '<tbody>';

    $individual_ranking = galaxy_show_ranking_unique_ally($ally);
    $ranking = current($individual_ranking);
    $datadate =  date("d F o G:i", key($individual_ranking));
    $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
    $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
    $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
    $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
    $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
    $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
    $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
    $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
    $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : "&nbsp;";
    $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : "&nbsp;";
    $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
    $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
    $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
    $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
    $honor_rank = isset($ranking["honor"]) ? formate_number($ranking["honor"]["rank"]) : "&nbsp;";
    $honor_points = isset($ranking["honor"]) ? formate_number($ranking["honor"]["points"]) : "&nbsp;";
    $number_member = isset($ranking["number_member"]) ? formate_number($ranking["number_member"]) : "&nbsp;";

    $tooltip .= "<tr><td class=\"tdcontent \" colspan=\"3\" ><span class=\"og-highlight\">" . $lang['GALAXY_RANK'] . " " . $datadate . "</span> </td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_GENERAL'] . "</td><td class=\"tdcontent\">" . $general_rank . "</td><td class=\"tdcontent\">" . $general_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_ECONOMY'] . "</td><td class=\"tdcontent\">" . $eco_rank . "</td><td class=\"tdcontent\">" . $eco_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_LAB'] . "</td><td class=\"tdcontent\">" . $techno_rank . "</td><td class=\"tdcontent\">" . $techno_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY'] . "</td><td class=\"tdcontent\">" . $military_rank . "</td><td class=\"tdcontent\">" . $military_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_BUILT'] . "</td><td class=\"tdcontent\">" . $military_b_rank . "</td><td class=\"tdcontent\">" . $military_b_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_LOST'] . "</td><td class=\"tdcontent\">" . $military_l_rank . "</td><td class=\"tdcontent\">" . $military_l_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . "</td><td class=\"tdcontent\">" . $military_d_rank . "</td><td class=\"tdcontent\">" . $military_d_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_HONOR'] . "</td><td class=\"tdcontent\">" . $honor_rank . "</td><td class=\"tdcontent\">" . $honor_points . "</td></tr>";
    $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\" ><span class=\"og-highlight\">" . $number_member . "</span> " . $lang['GALAXY_MEMBERS'] . "</td></tr>";

    $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><a href=\"index.php?action=search&amp;type_search=ally&amp;string_search=" . $ally . "&strict=on\">" . $lang['GALAXY_SEE_DETAILS'] . "</a></td></tr>";
    $tooltip .= '</tbody>';
    $tooltip .= "</table>";

    $tooltip = htmlentities($tooltip);
    return $tooltip;
}

function displayGalaxyTablethead()
{
    global $lang;
    global $link_order_coordinates, $link_order_ally, $link_order_player;

    $html = '<tr>';

    // Colonne Coordonnées
    $html .= '<th>';
    if (is_null($link_order_coordinates)) {
        $html .= '&nbsp;';
    } else {
        $html .= $link_order_coordinates;
    }
    $html .= '</th>';

    // Colonne Planètes
    $html .= '<th>' . $lang['GALAXY_PLANETS'] . '</th>';

    // Colonne Alliances
    $html .= '<th>';
    if (is_null($link_order_ally)) {
        $html .= $lang['GALAXY_ALLIES'];
    } else {
        $html .= $link_order_ally;
    }
    $html .= '</th>';

    // Colonne Joueurs
    $html .= '<th>';
    if (is_null($link_order_player)) {
        $html .= $lang['GALAXY_PLAYERS'];
    } else {
        $html .= $link_order_player;
    }
    $html .= '</th>';

    // Autres colonnes
    $html .= '<th>L</th>';
    $html .= '<th>P</th>';
    $html .= '<th>Ph</th>';
    $html .= '<th>&nbsp;</th>';
    $html .= '<th>&nbsp;</th>';
    $html .= '<th>&nbsp;</th>';

    // Colonne Mises à jour
    $html .= '<th>' . $lang['GALAXY_UPDATES'] . '</th>';

    $html .= '</tr>';

    return $html;
}


/**
 * Génère une ligne de tableau HTML pour afficher les informations d'une galaxie ou d'un système.
 *
 * @param array $populate Tableau associatif contenant les données d'une ligne de la galaxie (row).
 *                        Les clés attendues incluent :
 *                        - 'planet' : Nom de la planète (par défaut " " si non défini).
 *                        - 'ally' : Nom de l'alliance associée.
 *                        - 'player' : Nom du joueur associé.
 *                        - 'row' : Numéro de la ligne.
 *                        - 'galaxy' : Numéro de la galaxie.
 *                        - 'system' : Numéro du système.
 *                        - 'hided' : Booléen indiquant si la ligne est masquée.
 * @param bool $isGalaxy Indique si les données concernent une galaxie (true) ou un autre contexte (false).
 *
 * @return string Retourne une chaîne HTML représentant une ligne de tableau.
 */
function displayGalaxyTabletbodytr($populate, $isGalaxy = true)
{
    global $lang;
    $ToolTip_Helper = new ToolTip_Helper();

    $v = $populate;
    $v["planet_name"] = $v["planet_name"] ?? "";
    $tooltiptab = []; // Initialize tooltiptab

    $ally = $v["ally_name"] ?? "";
    $allyId = $v["ally_id"] ?? 0;
    $player = $v["player_name"] ?? "";
    $playerId = $v["player_id"] ?? "";
    $row = $v["row"];
    $galaxy = $v["galaxy"];
    $system = $v["system"];

    if ($ally != "" && !isset($tooltiptab["ally"][$ally])) {
        $tooltiptab["ally"][] = $ally;
    }

    if ($player != "" && !isset($tooltiptab["player"][$player])) {
        $tooltiptab["player"][] = $player;
    }

    // Ensure tooltip content is added for player
    if (!empty($player)) { // $player is $v["player_name"]
        $player_tooltip_key = "ttp_player_" . $player;
        $tooltip_content_player = displayGalaxyPlayerTooltip($playerId);
        $ToolTip_Helper->addTooltip($player_tooltip_key, $tooltip_content_player);
    }

    // Ensure tooltip content is added for ally
    if (!empty($ally)) { // $ally is $v["ally_name"]
        $ally_tooltip_key = "ttp_alliance_" . $ally;
        $tooltip_content_ally = displayGalaxyAllyTooltip($allyId);
        $ToolTip_Helper->addTooltip($ally_tooltip_key, $tooltip_content_ally);
    }

    $classishided = $v["hided"] ? "tr-ishided" : "";
    $classisallied = $v["allied"] ? "tr-isallied" : "";
    $empytag = $v["planet_name"] == "" ? "empty" : "";

    $states = !empty($v["status"]) ? str_split($v["status"]) : [];



    $content = '<tr class="' . $classishided . ' ' . $classisallied . ' ' . $empytag . '">' .
        '<td class="tdcontent">' .
        ($isGalaxy ? $v["row"] : '<a href="index.php?action=galaxy&amp;galaxy=' . $v["galaxy"] . '&amp;system=' . $v["system"] . '">' . $v["galaxy"] . ':' . $v["system"] . ':' . $v["row"] . '</a>') .
        '</td>' .
        '<td class="tdcontent">' .
        (!empty($v["planet_name"]) ? '&nbsp;' : '<a href="index.php?action=search&amp;type_search=planet&amp;string_search=' . $v["planet_name"] . '&amp;strict=on">' . $v["planet_name"] . '</a>') .
        '</td>' .
        '<td class="tdcontent">' .
        (!empty($v["ally_name"]) ? '<a ' . $ToolTip_Helper->GetHTMLClassContent(["tooltipstered"], "ttp_alliance_" . $v["ally_name"]) . ' href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $ally . '&strict=on">' . $ally . '</a>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent">' .
        (!empty($v["player_name"]) ? '<a ' . $ToolTip_Helper->GetHTMLClassContent(["tooltipstered"], "ttp_player_" . $v["player_name"]) . ' href="index.php?action=search&amp;type_search=player&amp;string_search=' . $player . '&strict=on">' . $player . '</a>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent"><span class="' . ($v["type"] === 'moon' ? 'ogame-icon ogame-icon-moon ' : '') . '">&nbsp;</span></td>' .
        '<td class="tdcontent">' .
        ($v["PoSa"] > 0 ? '<span class="ogame-icon ogame-icon-gate ">P</span>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent">' .
        ($v["Pha"] > 0 ? '<span class="ogame-icon ogame-icon-phalanx ">' . $v["Pha"] . '</span>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent">' .
        implode('', array_map(fn($state) => '<span class="ogame-status-' . $state . '">' . $state . '</span>', $states)) .
        '</td>' .
        '<td class="tdcontent">' .
        ($v["report_spy"] > 0 ? '<a href="#" onClick="window.open(\'index.php?action=show_reportspy&amp;galaxy=' . $galaxy . '&amp;system=' . $system . '&amp;row=' . $row . '\',\'_blank\',\'width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0\');return(false)">' . $lang['GALAXY_SR'] . '</a>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent">' .
        (isset($v["report_rc"]) && $v["report_rc"] > 0 ? '<a href="#" onClick="window.open(\'index.php?action=show_reportrc&amp;galaxy=' . $galaxy . '&amp;system=' . $system . '&amp;row=' . $row . '\',\'_blank\',\'width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0\');return(false)">' . $v["report_rc"] . $lang['GALAXY_CR'] . '</a>' : '&nbsp;') .
        '</td>' .
        '<td class="tdcontent og-galaxy-tdtimestamp">' .
        '<span class="og-galaxy-timestamp">' . (intval($v["last_update"]) != 0 ? date("d F o G:i", $v["last_update"]) : '&nbsp;') . '</span>' .
        '<span class="og-galaxy-poster">- ' . $v["last_update_user_name"] . '</span>' .
        '</td>' .
        '</tr>';

    return $content;
}
