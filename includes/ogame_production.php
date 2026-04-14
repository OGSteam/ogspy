<?php
/**
 * OGSpy OGame Production Functions
 * 
 * This file contains all production-related functions for OGame calculations.
 * Functions extracted from ogame.php to improve maintainability.
 */

///////////////////// PRODUCTION fonctions : ///////////////////////////////////
// Production helpers (structs, elements, coordinates) are required centrally from common.php
// to keep include ordering deterministic across the application and test suite.
/**
 * @brief Calculates resource production bonuses based on the given position.
 *
 * @param int $position Planetary position, which determines resource production bonuses.
 *                      Valid positions provide specific bonuses for metal ('M') or crystal ('C'):
 *                      - 1: +40% crystal ('C')
 *                      - 2: +30% crystal ('C')
 *                      - 3: +20% crystal ('C')
 *                      - 8: +35% metal ('M')
 *                      - 7, 9: +23% metal ('M')
 *                      - 6, 10: +17% metal ('M')
 *                      Positions not explicitly defined return no bonuses.
 *
 * @return array Associative array representing resource multipliers in the form ['M' => float, 'C' => float, 'D' => float],
 *               where 'M' is metal, 'C' is crystal, and 'D' is deuterium. Default values are 0.
 */
function ogame_production_position($position)
{
    $result = ogame_array_ressource(0, 0, 0);

    switch ($position) {
        case 1:
            $result['C'] = 0.4; // +40% cristal
            break;
        case 2:
            $result['C'] = 0.3; // +30% cristal
            break;
        case 3:
            $result['C'] = 0.2; // +20% cristal
            break;
        case 8:
            $result['M'] = 0.35; // +35% métal
            break;
        case 7: //no break
        case 9:
            $result['M'] = 0.23; // +23% métal
            break;
        case 6: //no break
        case 10:
            $result['M'] = 0.17; // +17% métal
            break;
        default:
            break;
    }
    // helpers are required centrally from common.php
    return $result;
}

// Ensure legacy wrappers are available when other includes load production helpers.
// The legacy file delegates to the ogame_production_* helpers when possible.
if (file_exists(__DIR__ . '/ogame_legacy.php')) {
    require_once __DIR__ . '/ogame_legacy.php';
}

/**
 * @brief Calculates the maximum number of foreuses a player can have based on mine levels and player attributes.
 *
 * @param int $mine_M Level of the metal mine.
 * @param int $mine_C Level of the crystal mine.
 * @param int $mine_D Level of the deuterium mine.
 * @param array $player_data Player data containing attributes:
 *                            - 'off_geologue' (int): Geologist bonus activation status (default is 0).
 *                            - 'off_full' (int): Full bonus activation status (default is 0).
 *                            - 'class' (string): Player class (default is 'none').
 *
 * @return float Returns the maximum number of foreuses.
 */
function ogame_production_foreuse_max($mine_M, $mine_C, $mine_D, $player_data)
{
    static $FOR_BONUS_COL_GEO = 0.1;    //+10% de foreuse pour COL+GEO
    if (!isset($player_data['off_geologue'])) {
        $player_data['off_geologue'] = 0;
    }
    if (!isset($player_data['off_full'])) {
        $player_data['off_full'] = 0;
    }
    if (!isset($player_data['class'])) {
        $player_data['class'] = 'none';
    }

    $nb_foreuse_max = 8 * ($mine_M + $mine_C + $mine_D);
    if ($player_data['class'] === 'COL' && ($player_data['off_geologue'] != 0 || $player_data['off_full'] != 0)) {
        $nb_foreuse_max = $nb_foreuse_max * (1 + $FOR_BONUS_COL_GEO);
    }

    return floor($nb_foreuse_max);
}

/**
 * @brief Calculates the production bonus and the number of maxed-out drills (foreuses) for a player based on their buildings and class.
 *
 * @param array $user_building Array containing the user's building levels (keys: 'M', 'C', 'D', 'FOR').
 *                              'M' (int): Level of metal mine.
 *                              'C' (int): Level of crystal mine.
 *                              'D' (int): Level of deuterium synthesizer.
 *                              'FOR' (int): Number of drills (foreuses).
 * @param array $player_data Array containing the player's data.
 *                             'off_geologue' (int): Whether the geologist is active (0 or 1).
 *                             'off_full' (int): Whether the full officer bonus is active (0 or 1).
 *                             'class' (string): Player's class ('COL', 'GEN', 'EXP', or 'none').
 * @return array Associative array with the following keys:
 *               'bonus' (float): The calculated production bonus (max 50%).
 *               'nb_FOR_maxed' (int): The number of drills taken into account (limited by max calculation).
 */
function ogame_production_foreuse_bonus($user_building, $player_data)
{
    static $FOR_COEF = 0.0002; //0.02% / foreuse
    static $FOR_BONUS_COL = 0.5;    //+50% pour COL
    $names = ogame_get_element_names();
    //Valeurs OUT par défaut :
    $result = array('bonus' => 0, 'nb_FOR_maxed' => 0);
    //Valeurs IN par défaut :
    if (!isset($user_building['M']) || !is_numeric($user_building['M'])) {
        $user_building['M'] = 0;
    }
    if (!isset($user_building['C']) || !is_numeric($user_building['C'])) {
        $user_building['C'] = 0;
    }
    if (!isset($user_building['D']) || !is_numeric($user_building['D'])) {
        $user_building['D'] = 0;
    }
    if (!isset($user_building['FOR']) || !is_numeric($user_building['FOR'])) {
        $user_building['FOR'] = 0;
    }
    if (!isset($player_data['off_geologue'])) {
        $player_data['off_geologue'] = 0;
    }
    if (!isset($player_data['off_full'])) {
        $player_data['off_full'] = 0;
    }
    if (!isset($player_data['class'])) {
        $player_data['class'] = 'none';
    }
    if (!in_array($player_data['class'], $names['CLASS'], true)) {
        $player_data['class'] = $names['CLASS'][0];
    }

    $bonus_foreuse = $FOR_COEF;
    if ($player_data['class'] === 'COL') {
        $bonus_foreuse = $bonus_foreuse * (1 + $FOR_BONUS_COL);
    }
    $nb_foreuse_max = ogame_production_foreuse_max($user_building['M'], $user_building['C'], $user_building['D'], $player_data);

    if ($user_building['FOR'] > $nb_foreuse_max) {
        $user_building['FOR'] = $nb_foreuse_max;
    }

    $result['bonus'] = min(0.5, $bonus_foreuse * $user_building['FOR']);
    $result['nb_FOR_maxed'] = $user_building['FOR'];

    return $result;
}

/**
 * Calculates the production output of a specific building type in the game OGame, including energy consumption/production.
 *
 * @param string $building The type of building ('base', 'M', 'C', 'D', 'CES', 'CEF', 'SAT', 'FOR').
 * @param array|null $user_building The user's building levels and related information (keys such as 'M', 'C', 'D', 'CES', 'CEF', 'SAT', 'temperature_max', etc.).
 * @param array|null $user_technology The user's technology levels (e.g., 'NRJ' for energy technology).
 * @param array|null $player_data Additional player-specific data, which might influence production (e.g., player class).
 * @param array|null $server_config Server-specific configurations (e.g., 'speed_uni', 'final_calcul').
 * @return array                          An array representing the production results with keys 'M' (metal), 'C' (crystal), 'D' (deuterium), and 'NRJ' (energy),
 *                                        where each key corresponds to the resource/energy quantity calculated.
 */
function ogame_production_building($building, $user_building = null, $user_technology = null, $player_data = null, $server_config = null)
{
    global $log;
    static $BASE_M = 30;
    static $BASE_C = 15;
    //Valeurs OUT par défaut :
    $result = ogame_array_ressource(0, 0, 0);
    //Valeurs IN par défaut :
    // Ensure inputs are arrays to avoid null offset warnings
    $user_technology = is_array($user_technology) ? $user_technology : array();
    $user_building = is_array($user_building) ? $user_building : array();
    $player_data = is_array($player_data) ? $player_data : array();
    $server_config = is_array($server_config) ? $server_config : array();

    $user_technology['NRJ'] = $user_technology['NRJ'] ?? 0;
    $user_building['M'] = $user_building['M'] ?? 0;
    $user_building['C'] = $user_building['C'] ?? 0;
    $user_building['D'] = $user_building['D'] ?? 0;
    $user_building['CES'] = $user_building['CES'] ?? 0;
    $user_building['CEF'] = $user_building['CEF'] ?? 0;
    $user_building['FOR'] = $user_building['FOR'] ?? 0;
    $user_building['SAT'] = $user_building['SAT'] ?? ($user_building['Sat'] ?? 0);
    $user_building['temperature_max'] = $user_building['temperature_max'] ?? 0;
    $user_building['coordinates'] = $user_building['coordinates'] ?? 0;
    $server_config['speed_uni'] = $server_config['speed_uni'] ?? 1;
    $server_config['final_calcul'] = $server_config['final_calcul'] ?? true;

    $user_building['position'] = ogame_find_planet_position($user_building['coordinates']);
    $bonus_position = ogame_production_position($user_building['position']);

    switch ($building) {
        case 'base':
            $result['M'] = floor($BASE_M * (1 + $bonus_position['M']) * $server_config['speed_uni']);
            $result['C'] = floor($BASE_C * (1 + $bonus_position['C']) * $server_config['speed_uni']);
            break;
        case 'M':
            $level = $user_building['M'];
            $coef_base = (1 + $bonus_position['M']) * $server_config['speed_uni'];
            $result['M'] = 30 * $level * pow(1.1, $level) * $coef_base;
            $result['NRJ'] = -floor(10 * $level * pow(1.1, $level));
            break;
        case 'C':
            $level = $user_building['C'];
            $coef_base = (1 + $bonus_position['C']) * $server_config['speed_uni'];
            $result['C'] = 20 * $level * pow(1.1, $level) * $coef_base;
            $result['NRJ'] = -floor(10 * $level * pow(1.1, $level));
            break;
        case 'D':
            $level = $user_building['D'];
            $coef_base = (1 + $bonus_position['D']) * $server_config['speed_uni'];
            $result['D'] = 10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $user_building['temperature_max']) * $coef_base;
            $result['NRJ'] = -floor(20 * $level * pow(1.1, $level));
            break;
        case 'CES':
            $level = $user_building['CES'];
            $result['NRJ'] = floor(20 * $level * pow(1.1, $level));
            break;
        case 'CEF':
            $level = $user_building['CEF'];
            $result['NRJ'] = floor(30 * $level * pow((1.05 + $user_technology['NRJ'] * 0.01), $level));
            $result['D'] = -floor(10 * $level * pow(1.1, $level)) * $server_config['speed_uni'];
            break;
        case 'SAT':
            $number = $user_building['SAT'];
            $result['NRJ'] = floor(($user_building['temperature_max'] + 140) / 6) * $number;
            break;
        case 'FOR':
            $number = $user_building['FOR'];
            // Pass along the caller's technology and player data so nested calculations
            // have the same context and do not receive nulls.
            $prodM = ogame_production_building('M', $user_building, $user_technology, $player_data, $server_config);
            $prodC = ogame_production_building('C', $user_building, $user_technology, $player_data, $server_config);
            $prodD = ogame_production_building('D', $user_building, $user_technology, $player_data, $server_config);
            if (!is_array($prodM) || !array_key_exists('M', $prodM)) {
                throw new \ErrorException('ogame_production_building(M) did not return expected array with key M');
            }
            if (!is_array($prodC) || !array_key_exists('C', $prodC)) {
                throw new \ErrorException('ogame_production_building(C) did not return expected array with key C');
            }
            if (!is_array($prodD) || !array_key_exists('D', $prodD)) {
                throw new \ErrorException('ogame_production_building(D) did not return expected array with key D');
            }
            $production_mine_base['M'] = $prodM['M'];
            $production_mine_base['C'] = $prodC['C'];
            $production_mine_base['D'] = $prodD['D'];
            $bonus_for = ogame_production_foreuse_bonus($user_building, $player_data);

            $result['M'] = round($production_mine_base['M'] * $bonus_for['bonus']);
            $result['C'] = round($production_mine_base['C'] * $bonus_for['bonus']);
            $result['D'] = round($production_mine_base['D'] * $bonus_for['bonus']);
            $result['NRJ'] = -50 * $bonus_for['nb_FOR_maxed'];
            break;
        default:
            break;
    }
    if ($server_config['final_calcul']) {
        $result['M'] = floor($result['M']);
        $result['C'] = floor($result['C']);
        $result['D'] = floor($result['D']);
    }

    return $result;
}