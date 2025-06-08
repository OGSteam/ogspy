<?php

/** @file includes/ogame.php
 * OGame games formulas and data.
 * @package OGSpy
 * @subpackage Ogame formula library
 * @author Pitch314
 * @copyright Copyright &copy; 2021, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 4
 * @created 15/11/2005 by Kyser
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/////////////////// STRUCT type fonctions : ////////////////////////////////////
/**
 * @brief Creates an associative array to represent resources.
 *
 * @param int $metal Amount of metal.
 * @param int $cristal Amount of crystal.
 * @param int $deut Amount of deuterium.
 * @param int $NRJ Amount of energy (default is 0).
 * @param int $AM Amount of antimatter (default is 0).
 * @return array Associative array containing resources with keys 'M', 'C', 'D', 'NRJ', and 'AM'.
 */
function ogame_array_ressource($metal, $cristal, $deut, $NRJ = 0, $AM = 0)
{
    return array('M' => $metal, 'C' => $cristal, 'D' => $deut, 'NRJ' => $NRJ, 'AM' => $AM);
}

/**
 * @brief Creates a detailed array representation for a specific game element with provided parameters.
 *
 * @param int $structure The structure value of the element.
 * @param int $bouclier The shield value of the element.
 * @param int $attaque The attack value of the element.
 * @param int $vitesse The speed value of the element. Default is 0.
 * @param int $fret The cargo capacity of the element. Default is 0.
 * @param int $conso The consumption value of the element. Default is 0.
 * @param bool $civil Indicates if the element is classified as civilian. Default is true.
 * @return array Returns an associative array containing the structure, shield, attack, speed, cargo capacity, consumption, rapidfire, and civilian status of the element.
 */
function ogame_array_detail($structure, $bouclier, $attaque, $vitesse = 0, $fret = 0, $conso = 0, $civil = true)
{
    return array(
        'structure' => $structure, 'bouclier' => $bouclier, 'attaque' => $attaque,
        'vitesse' => $vitesse, 'fret' => $fret, 'conso' => $conso,
        'rapidfire' => array(), 'civil' => $civil
    );
}

///////////////////// PRODUCTION fonctions : ///////////////////////////////////
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

    return $result;
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
            $production_mine_base['M'] = ogame_production_building('M', $user_building, null, null, $server_config)['M'];
            $production_mine_base['C'] = ogame_production_building('C', $user_building, null, null, $server_config)['C'];
            $production_mine_base['D'] = ogame_production_building('D', $user_building, null, null, $server_config)['D'];
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

/**
 * @brief Calculates planet production and consumption.
 *
 * @param[in] array $user_building   Planet info ('M','C','D','CES','CEF','SAT','FOR','temperature_max','coordinates','M_percentage','C_percentage','D_percentage','CES_percentage','CEF_percentage','Sat_percentage','FOR_percentage',array 'booster_tab') 0 as default value
 * @param[in] array $user_technology Techno info ('NRJ','Plasma')
 * @param[in] array $user_data       User info (array('user_class'=>'COL'/...,'off_commandant','off_amiral','off_ingenieur','off_geologue', or 'off_full')
 * @param[in] array $server_config   Ogame universe info ('speed_uni')
 * @return array('prod_reel,'prod_theorique','ratio','conso_E','prod_E',  //Production totale
 *      'prod_CES','prod_CEF','prod_SAT','prod_FOR',   //production énergie de chaque unité
 *      'prod_M','prod_C','prod_D','prod_base', //production ressources de chaque mine
 *      'prod_booster','prod_off','prod_Plasma','prod_classe',   //production des bonus
 *      'M','C','D','NRJ','AM', =>héritage du type ressource pour les valeurs retournées.
 *      'nb_FOR_maxed',
 *      ) à part conso_E/prod_E (float) les autres sont array('M','C','D','NRJ','AM')
 *
 * @details remplace les fonctions ratio et bilan_production_ratio
 */
function ogame_production_planet($user_building, $user_technology = null, $player_data = null, $server_config = null)
{
    global $log;

    // Initialiser les variables par défaut, car elles peuvent ne pas être définies
    $user_technology = $user_technology ?? [];
    $player_data = $player_data ?? [];
    static $DEFAULT_TYPE_RESS = ['M' => 0, 'C' => 0, 'D' => 0, 'NRJ' => 0, 'AM' => 0];
    $names = ogame_get_element_names();

    //Valeurs OUT par défaut :
    $result = [
        'prod_reel' => 0, 'prod_theorique' => 0, 'ratio' => 0, 'conso_E' => 0, 'prod_E' => 0,
        'prod_CES' => 0, 'prod_CEF' => 0, 'prod_SAT' => 0, 'prod_FOR' => 0,
        'prod_M' => 0, 'prod_C' => 0, 'prod_D' => 0, 'prod_base' => 0,
        'prod_booster' => 0, 'prod_off' => 0, 'prod_Plasma' => 0, 'prod_classe' => 0,
        'nb_FOR_maxed' => 0
    ];


    //Definition des Bonus d'énergie et de ressources
    static $NRJ_BONUS_ING = 0.1;   //+10% pour ingénieur
    static $NRJ_BONUS_FULL = 0.02;  //+2% pour full officier
    static $NRJ_BONUS_COL = 0.1;    //+10% NRJ pour COL
    static $RESS_BONUS_COL = 0.25;  //+25% pour COL
    static $RESS_BONUS_GEO = 0.1;   //+10% pour géologue
    static $RESS_BONUS_FULL = 0.02;  //+2% pour full officier
    static $RESS_PLASMA_M = 0.01;
    static $RESS_PLASMA_C = 0.0066;
    static $RESS_PLASMA_D = 0.0033;


    $result['prod_reel'] = $DEFAULT_TYPE_RESS;
    $result['prod_theorique'] = $DEFAULT_TYPE_RESS;
    $result['prod_booster'] = $DEFAULT_TYPE_RESS;
    $result['prod_off'] = $DEFAULT_TYPE_RESS;
    $result['prod_Plasma'] = $DEFAULT_TYPE_RESS;
    $result['prod_classe'] = $DEFAULT_TYPE_RESS;
    $result = array_merge($result, $DEFAULT_TYPE_RESS); // Compatibilité par héritage

    //Valeurs IN par défaut :
    $user_technology['Plasma'] = $user_technology['Plasma'] ?? 0;

    // Valeurs par défaut pour $player_data
    $player_data['off_commandant'] = $player_data['off_commandant'] ?? 0;
    $player_data['off_amiral'] = $player_data['off_amiral'] ?? 0;
    $player_data['off_ingenieur'] = $player_data['off_ingenieur'] ?? 0;
    $player_data['off_geologue'] = $player_data['off_geologue'] ?? 0;
    $player_data['off_full'] = $player_data['off_full'] ?? 0;
    $player_data['class'] = $player_data['class'] ?? 'none';
    $player_data['production_theorique'] = $player_data['production_theorique'] ?? false;

    // Valider la classe
    if (!in_array($player_data['class'], $names['CLASS'], true)) {
        $player_data['class'] = $names['CLASS'][0];
    }
    // Valeurs par défaut pour $user_building
    $user_building['M_percentage'] = $user_building['M_percentage'] ?? 100;
    $user_building['C_percentage'] = $user_building['C_percentage'] ?? 100;
    $user_building['D_percentage'] = $user_building['D_percentage'] ?? 100;
    $user_building['CES_percentage'] = $user_building['CES_percentage'] ?? 100;
    $user_building['CEF_percentage'] = $user_building['CEF_percentage'] ?? 100;
    $user_building['Sat_percentage'] = $user_building['Sat_percentage'] ?? 100;
    $user_building['FOR_percentage'] = $user_building['FOR_percentage'] ?? 100;

    // Initialiser le tableau booster_tab s'il n'existe pas
    $user_building['booster_tab'] = $user_building['booster_tab'] ?? [];

    // Définition des valeurs par défaut pour $user_building['booster_tab']
    $user_building['booster_tab']['booster_e_val'] = $user_building['booster_tab']['booster_e_val'] ?? 0;
    $user_building['booster_tab']['booster_m_val'] = $user_building['booster_tab']['booster_m_val'] ?? 0;
    $user_building['booster_tab']['booster_c_val'] = $user_building['booster_tab']['booster_c_val'] ?? 0;
    $user_building['booster_tab']['booster_d_val'] = $user_building['booster_tab']['booster_d_val'] ?? 0;

    // Règles de dépendance
    if ($player_data['off_full'] != 0) {
        $player_data['off_ingenieur'] = 1;
        $player_data['off_geologue'] = 1;
    }
    if ($player_data['off_commandant'] != 0 && $player_data['off_amiral'] != 0 &&
        $player_data['off_ingenieur'] != 0 && $player_data['off_geologue'] != 0) {
        $player_data['off_full'] = 1;
    }
    if ($player_data['class'] != 'COL' && $user_building['FOR_percentage'] > 100) {
        $user_building['FOR_percentage'] = 100;
    }

//Calcul valeurs de base
    $prod_base = ogame_production_building('base', $user_building, null, null, $server_config);
    $server_config['final_calcul'] = false;
    $prod_mine_M = ogame_production_building('M', $user_building, $user_technology, $player_data, $server_config);
    $prod_mine_C = ogame_production_building('C', $user_building, $user_technology, $player_data, $server_config);
    $prod_mine_D = ogame_production_building('D', $user_building, $user_technology, $player_data, $server_config);
    $prod_bat_CES = ogame_production_building('CES', $user_building, $user_technology, $player_data, $server_config);
    $prod_bat_CEF = ogame_production_building('CEF', $user_building, $user_technology, $player_data, $server_config);
    $prod_vso_SAT = ogame_production_building('SAT', $user_building, $user_technology, $player_data, $server_config);
    $prod_vso_FOR = ogame_production_building('FOR', $user_building, $user_technology, $player_data, $server_config);
    $result['prod_base'] = $prod_base;
    $result['prod_M'] = $prod_mine_M;
    $result['prod_C'] = $prod_mine_C;
    $result['prod_D'] = $prod_mine_D;
    $result['prod_CES'] = $prod_bat_CES;
    $result['prod_CEF'] = $prod_bat_CEF;
    $result['prod_SAT'] = $prod_vso_SAT;
    $result['prod_FOR'] = $prod_vso_FOR;


    $log->debug("Planet Buildings: " . json_encode($user_building));
    $log->debug("Production de base: " . json_encode($result['prod_base']));
    $log->debug("Production des mines M: " . json_encode($result['prod_M']));
    $log->debug("C: " . json_encode($result['prod_C']));
    $log->debug("D: " . json_encode($result['prod_D']));
    $log->debug("Production Energie bâtiments CES: " . json_encode($result['prod_CES']));
    $log->debug("CEF: " . json_encode($result['prod_CEF']));
    $log->debug("SAT: " . json_encode($result['prod_SAT']));
    $log->debug("FOR: " . json_encode($result['prod_FOR']));

//Calcul de la consommation d'énergie théorique
    $conso_M = round($prod_mine_M['NRJ'] * $user_building['M_percentage'] / 100);
    $conso_C = round($prod_mine_C['NRJ'] * $user_building['C_percentage'] / 100);
    $conso_D = round($prod_mine_D['NRJ'] * $user_building['D_percentage'] / 100);
    $conso_FOR = round($prod_vso_FOR['NRJ'] * max(1, $user_building['FOR_percentage'] * 2 / 100 - 1)); // [50 * max( 1 ; 1 + (pourcentage_production - 100%) * %_malus_overload / 10% ) ]
    $consommation_E = $conso_M + $conso_C + $conso_D + $conso_FOR;

    $result['conso_E'] = $consommation_E;
    $result['prod_M']['NRJ'] = $conso_M;
    $result['prod_C']['NRJ'] = $conso_C;
    $result['prod_D']['NRJ'] = $conso_D;
    $result['prod_FOR']['NRJ'] = $conso_FOR;

    $log->debug("Consommation Mine Metal: " . json_encode($result['prod_M']));
    $log->debug("Consommation Mine Cristal: " . json_encode($result['prod_C']));
    $log->debug("Consommation Mine Deuterium: " . json_encode($result['prod_D']));
    $log->debug("Consommation Foreuse       : " . json_encode($result['prod_FOR']));

    if (!$player_data['production_theorique']) {  //Alors calcul du ratio puis sa prod associé
        $log->debug("Production Energie Reelle : Limitation choisie par l'utilisateur ou Production Energie Reelle");
        //Calcul de la production d'énergie
        $prod_CES = $prod_bat_CES['NRJ'] * $user_building['CES_percentage'] / 100;
        $prod_CEF = $prod_bat_CEF['NRJ'] * $user_building['CEF_percentage'] / 100;
        $prod_SAT = $prod_vso_SAT['NRJ'] * $user_building['Sat_percentage'] / 100;
        $production_E = $prod_CES + $prod_CEF + $prod_SAT;

        // Bonus de production d'énergie Boosters
        $result['prod_booster']['NRJ'] = round($production_E * $user_building['booster_tab']['booster_e_val'] / 100,1);
        $log->debug("Production Energie Booster: " . ($result['prod_booster']['NRJ']));

        // Si le commandant est actif, on ajoute son bonus de production d'énergie
        if ($player_data['class'] == "COL") {
            $result['prod_classe']['NRJ'] = round($production_E * $NRJ_BONUS_COL, 1);
            $log->debug("Production Energie Classe COL: " . ($result['prod_classe']['NRJ']));
        }

        // Si l'ingénieur est actif, on ajoute son bonus de production d'énergie
        if ($player_data['off_ingenieur'] != 0) {
            $result['prod_off']['NRJ'] = round($production_E * $NRJ_BONUS_ING,1);
            $log->debug("Production Energie Bonus Ingénieur: " . ($result['prod_off']['NRJ']));
        }
        // Si l'officier full est actif, on ajoute son bonus de production d'énergie
        if ($player_data['off_full'] != 0) {
            $result['prod_off']['NRJ'] += round($production_E * $NRJ_BONUS_FULL,1);
            $log->debug("Production Energie Bonus Full Officier: " . ($result['prod_off']['NRJ']));
        }


        $result['prod_CES']['NRJ'] = floor($prod_CES);
        $result['prod_CEF']['NRJ'] = floor($prod_CEF);
        $result['prod_SAT']['NRJ'] = floor($prod_SAT);
        $production_E = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];
        $production_E += $result['prod_booster']['NRJ'] + $result['prod_off']['NRJ'] + $result['prod_classe']['NRJ'];

        $log->debug("Production Energie with Bonus:", [ $production_E ]);


        //Calcul ratio
        $ratio_temp = ($consommation_E == 0) ? 1 : ( $production_E / $consommation_E); // fix division par 0
        $ratio_temp = abs($ratio_temp); // le ratio n'est pas negatif / c'est le necessaire sur la production max
        if ($ratio_temp > 1) {
            $ratio = 1;
        } else {
            $ratio = $ratio_temp;
        }
        $log->info("Calcul du ratio de production d'énergie",
            [
                'consommation_E' => $consommation_E,
                'production_E' => $production_E,
                'ratio_calculé' => $ratio_temp,
                'ratio_retenu' => $ratio
            ]);
        $result['NRJ'] = $production_E;
        $result['ratio'] = $ratio;
    } else { //Pour le cas d'un calcul théorique
        $log->debug("Production Energie Maximale théorique (100%)");
        $user_building['M_percentage'] = 100;
        $user_building['C_percentage'] = 100;
        $user_building['D_percentage'] = 100;
        $user_building['CES_percentage'] = 100;
        $user_building['CEF_percentage'] = 100;
        $user_building['Sat_percentage'] = 100;
        if ($user_building['FOR_percentage'] < 100) {
            $user_building['FOR_percentage'] = 100;
        }
        $result['prod_FOR']['NRJ'] = round($prod_vso_FOR['NRJ'] * max(1, $user_building['FOR_percentage'] * 2 / 100 - 1));
        $production_E = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];

        $result['prod_booster']['NRJ'] = round($production_E * $user_building['booster_tab']['booster_e_val'] / 100);
        if ($player_data['class'] === 'COL') {
            $result['prod_classe']['NRJ'] = round($production_E * $NRJ_BONUS_COL,1);
        }
        if ($player_data['off_ingenieur'] != 0) {
            $result['prod_off']['NRJ'] = round($production_E * $NRJ_BONUS_ING,1);
        }
        if ($player_data['off_full'] != 0) {
            $result['prod_off']['NRJ'] += round($production_E * $NRJ_BONUS_FULL,1);
        }
        $result['prod_CES']['NRJ'] = floor($result['prod_CES']['NRJ']);
        $result['prod_CEF']['NRJ'] = floor($result['prod_CEF']['NRJ']);
        $result['prod_SAT']['NRJ'] = floor($result['prod_SAT']['NRJ']);
        $production_E = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];
        $production_E += $result['prod_booster']['NRJ'] + $result['prod_off']['NRJ'] + $result['prod_classe']['NRJ'];
        $ratio = 1;
        $result['NRJ'] = $production_E;
    }
    $result['prod_E'] = $production_E;
/* Production Bonus for each Ressource Type */
//Calcul de la production
    $bonus_off_geo = ($player_data['off_geologue'] != 0) ? $RESS_BONUS_GEO : 0;
    $bonus_off_full = ($player_data['off_full'] != 0) ? $RESS_BONUS_FULL : 0;
    $bonus_class = ($player_data['class'] == 'COL') ? $RESS_BONUS_COL : 0;
    $bonus_for = ogame_production_foreuse_bonus($user_building, $player_data);
    $result['nb_FOR_maxed'] = $bonus_for['nb_FOR_maxed'];

//*Métal :
    $production_mine_base = floor($prod_mine_M['M'] * ($user_building['M_percentage'] / 100) * $ratio);

    $prod_off = round($production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full);
    $prod_Plasma = round($production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_M);
    $prod_booster = round($production_mine_base * $user_building['booster_tab']['booster_m_val'] / 100);
    $prod_FOR = round($production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100));
    $prod_classe = round($production_mine_base * $bonus_class);

    $result['M'] = $prod_base['M'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['prod_off']['M'] = $prod_off;
    $result['prod_Plasma']['M'] = $prod_Plasma;
    $result['prod_booster']['M'] = $prod_booster;
    $result['prod_FOR']['M'] = $prod_FOR;
    $result['prod_classe']['M'] = $prod_classe;
    $result['prod_M']['M'] = $production_mine_base;

//*Cristal :
    $production_mine_base = floor($prod_mine_C['C'] * ($user_building['C_percentage'] / 100) * $ratio);

    $prod_off = round($production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full,1);
    $prod_Plasma = round($production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_C,1);
    $prod_booster = round($production_mine_base * $user_building['booster_tab']['booster_c_val'] / 100,1);
    $prod_FOR = round($production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100),1);
    $prod_classe = round($production_mine_base * $bonus_class,1);

    $result['C'] = $prod_base['C'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['prod_off']['C'] = $prod_off;
    $result['prod_Plasma']['C'] = $prod_Plasma;
    $result['prod_booster']['C'] = $prod_booster;
    $result['prod_FOR']['C'] = $prod_FOR;
    $result['prod_classe']['C'] = $prod_classe;
    $result['prod_C']['C'] = $production_mine_base;

//*Deutérium :
    $production_mine_base = floor($prod_mine_D['D'] * ($user_building['D_percentage'] / 100) * $ratio);

    $prod_off = round($production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full,1);
    $prod_Plasma = round($production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_D,1);
    $prod_booster = round($production_mine_base * $user_building['booster_tab']['booster_d_val'] / 100,1);
    $prod_FOR = round($production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100),1);
    $prod_classe = round($production_mine_base * $bonus_class,1);
    $conso_CEF = ceil($prod_bat_CEF['D'] * $user_building['CEF_percentage'] / 100);

    $result['D'] = $prod_base['D'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['D'] = $result['D'] + $conso_CEF;
    $result['prod_off']['D'] = $prod_off;
    $result['prod_Plasma']['D'] = $prod_Plasma;
    $result['prod_booster']['D'] = $prod_booster;
    $result['prod_FOR']['D'] = $prod_FOR;
    $result['prod_classe']['D'] = $prod_classe;
    $result['prod_CEF']['D'] = $conso_CEF;
    $result['prod_D']['D'] = $production_mine_base;

    foreach ($names['RESS'] as $ress) {
        $result['prod_reel'][$ress] = floor($result['prod_base'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_M'][$ress]) + floor($result['prod_C'][$ress]) + floor($result['prod_D'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_CES'][$ress]) + floor($result['prod_CEF'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_SAT'][$ress]) + floor($result['prod_FOR'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_Plasma'][$ress]) + floor($result['prod_booster'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_off'][$ress]) + floor($result['prod_classe'][$ress]);
    }
    if (!$player_data['production_theorique']) {
        $player_data['production_theorique'] = true;
        $tmp = ogame_production_planet($user_building, $user_technology, $player_data, $server_config);
        $result['prod_theorique'] = $tmp['prod_reel'];
    }
    $result['NRJ'] = floor($result['NRJ']);

    return $result;
}

/**
 * Gets the hourly production of a Mine or an Energy plant.
 *
 * @param string $building The building type
 * @param int $level The building level
 * @param int $officier Officer option enabled (=1) or not(=0) or full Officer(=2) [Attention : m / c / d => geologue, ces cef => ingenieur]
 * @param int $temperature_max Max temperature of the current planet
 * @param int $NRJ Current value of the user Energy Technology
 * @param int $Plasma Current value of the user Plasma Technology
 * @param int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @param int $position position of the planet
 * @param int $speed_uni The univers economy speed
 * @return int The result of the production on the specified building.
 */
function production($building, $level, $officier = 0, $temperature_max = 0, $NRJ = 0, $Plasma = 0, $classe = 0, $position = 0, $speed_uni = 1)
{
    //Valeur de l'officier en valeur ajoutée.
    if ($officier == 0) {
        $geo = 0;
    } elseif ($officier == 1) {
        $geo = 0.10; //+10%
    } elseif ($officier == 2) {
        $geo = 0.12; //+12%
    } else {
        $geo = 0;
    }
    $ing = $geo;
    //Valeur de la classe en valeur ajoutée.
    if ($classe == 1) {
        $bonus_class_mine = 0.25; //+25%
        $bonus_class_energie = 0.10; //+10%
    } else {
        $bonus_class_mine = 0;
        $bonus_class_energie = 0;
    }
    //Bonus position
    $bonus_position = 0;
    if ($building === 'C') {
        if ($position == 1) {
            $bonus_position = 0.4;
        } elseif ($position == 2) {
            $bonus_position = 0.3;
        } elseif ($position == 3) {
            $bonus_position = 0.2;
        }
    } elseif ($building === 'M') {
        if ($position == 8) {
            $bonus_position = 0.35;
        } elseif ($position == 9 || $position == 7) {
            $bonus_position = 0.23;
        } elseif ($position == 10 || $position == 6) {
            $bonus_position = 0.17;
        }
    }

    switch ($building) {
        case "M":
            $prod_base = floor(30 * (1 + $bonus_position) * $speed_uni);
            $result = 30 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $bonus_position);
            $result = $result * $speed_uni; // vitesse uni
            $result = $result * (1 + $geo + 0.01 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "C":
            $prod_base = floor(15 * (1 + $bonus_position) * $speed_uni);
            $result = 20 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $bonus_position);
            $result = $result * $speed_uni; // vitesse uni
            $result = $result * (1 + $geo + 0.0066 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "D":
            $result = 10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $temperature_max); //<Ogame V7
            //$result = 10 * $level * pow(1.1, $level) * floor((1.44 - 0.004 * $temperature_max)*10)/10;  //troncature à la décimale
            $result = $result * $speed_uni; // vitesse uni
            $result = $result * (1 + $geo + 0.0033 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            break;

        case "CES":
            $result = 20 * $level * pow(1.1, $level);
            $result = $result * (1 + $ing + $bonus_class_energie); // ingenieur
            $result = floor($result); // troncature inférieure
            break;

        case "CEF":
            $result = 30 * $level * pow((1.05 + $NRJ * 0.01), $level);
            $result = $result * (1 + $ing + $bonus_class_energie); // ingenieur
            $result = floor($result); // troncature inférieure
            break;

        case "SAT":
            $result = ($temperature_max + 140) / 6;
            $result = floor($result) * $level;
            $result = $result * (1 + $ing + $bonus_class_energie);
            $result = floor($result);
            break;

        default:
            $result = 0;
            break;
    }

    return ($result);
}

/**
 * Gets the energy production of satellites.
 *
 * @param int $temperature_max Max temprature of the current planet
 * @param int $off_ing Officer ingenieur option enabled (=1) or not(=0) or full Officer(=2)
 * @param int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @return double The result of the power production by sattelites.
 */
function production_sat($temperature_max, $off_ing = 0, $classe = 0, $nb_sat = 1)
{
    return production('SAT', $nb_sat, $off_ing, $temperature_max, 0, 0, $classe);
}

/**
 * Gets the hourly planet production of foreuse.
 *
 * @param int $nb_foreuse The foreuse number of the current planet
 * @param int $level_M The metal mine level
 * @param int $level_C The cristal mine level
 * @param int $level_D The deuterium mine level
 * @param int $temperature_max Max temprature of the current planet
 * @param int $officier Officer option enabled (=1) or not(=0) or full Officer(=2) [Attention : m / c / d => geologue, ces cef => ingenieur]
 * @param int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @param int $position position of the planet
 * @param int $speed_uni The univers economy speed
 * @return array('M', 'C', 'D') The result foreuse production of metal 'M', cristal 'C' and deuterium 'D'
 */
function production_foreuse($nb_foreuse, $level_M, $level_C, $level_D, $temperature_max, $officier = 0, $classe = 0, $position = 0, $speed_uni = 1)
{
    $bonus_foreuse = 0.0002; //0.02% / foreuse
    $bonus_foreuse_max = 0;
    //Valeur de la classe en valeur ajoutée.
    if ($classe == 1) {
        $bonus_foreuse = $bonus_foreuse * 1.5; //+50%
        if ($officier != 0) {
            $bonus_foreuse_max = 0.1; //+10%
        }
    }
    $nb_max = ($level_M + $level_C + $level_D) * 8 * (1 + $bonus_foreuse_max);
    if ($nb_foreuse > $nb_max) {
        $nb_foreuse = $nb_max;
    }
    //Bonus position
    $bonus_position_M = 0;
    $bonus_position_C = 0;
    if ($position == 1) {
        $bonus_position_C = 0.4;
    } elseif ($position == 2) {
        $bonus_position_C = 0.3;
    } elseif ($position == 3) {
        $bonus_position_C = 0.2;
    } elseif ($position == 8) {
        $bonus_position_M = 0.35;
    } elseif ($position == 9 || $position == 7) {
        $bonus_position_M = 0.23;
    } elseif ($position == 10 || $position == 6) {
        $bonus_position_M = 0.17;
    }
    $result_M = production('M', $level_M, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(30 * (1 + $bonus_position_M) * $speed_uni);
    $result_C = production('C', $level_C, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(15 * (1 + $bonus_position_C) * $speed_uni);
    $result_D = production('D', $level_D, 0, $temperature_max, 0, 0, 0, $position, $speed_uni);

    $result_M = round($result_M * min(0.5, $bonus_foreuse * $nb_foreuse)); //arrondi
    $result_C = round($result_C * min(0.5, $bonus_foreuse * $nb_foreuse)); //arrondi
    $result_D = round($result_D * min(0.5, $bonus_foreuse * $nb_foreuse)); //arrondi

    return array('M' => $result_M, 'C' => $result_C, 'D' => $result_D);
}

/**
 * @brief Find number max of foreuse.
 *
 * @param [in] int $level_M The metal mine level
 * @param [in] int $level_C The cristal mine level
 * @param [in] int $level_D The deuterium mine level
 * @param [in] int $officier geologue option enabled (=1) or not(=0) or full Officer(=2)
 * @param [in] int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @return int number max of foreus
 */
function foreuse_max($level_M, $level_C, $level_D, $officier = 0, $classe = 0)
{
    $names = ogame_get_element_names();
    if (isset($names['CLASS'][$classe])) {
        $classe = $names['CLASS'][$classe];
    }
    if (!in_array($classe, $names['CLASS'], true)) {
        $classe = $names['CLASS'][0];
    }

    return ogame_production_foreuse_max($level_M, $level_C, $level_D, array('off_geologue' => $officier, 'class' => $classe));
}

/**
 * Gets the power consumption of the current building
 *
 * @param string $building The building type
 * @param int $level The building level (or number for foreuse)
 * @param int $speed_uni The univers economy speed
 * @return int The building consumption
 */
function consumption($building, $level, $speed_uni = 1)
{
    if (ogame_is_a_building($building) === false) {
        return 0;
    }
    $result = ogame_production_building($building, array($building => $level), null, null, array('speed_uni' => $speed_uni));

    if ($building === 'CEF') {
        return -$result['D'];
    }
    if ($building === 'M' || $building === 'C' || $building === 'D' || $building === 'FOR') {
        return -$result['NRJ'];
    }
    return 0;
}

/**
 * @brief Calculates the energy production-to-consumption ratio along with detailed energy and resource stats.
 *
 * @param int $M Quantity of Metal mines.
 * @param int $C Quantity of Crystal mines.
 * @param int $D Quantity of Deuterium synthesizers.
 * @param int $CES Quantity of Solar plants.
 * @param int $CEF Quantity of Fusion reactors.
 * @param int $SAT Quantity of Solar satellites.
 * @param int $temperature_max Maximum temperature of the planet for energy calculations.
 * @param int $off_ing Engineering bonus or research bonus.
 * @param int $NRJ Energy technology level.
 * @param float $per_M Percentage factor to apply to Metal mine consumption.
 * @param float $per_C Percentage factor to apply to Crystal mine consumption.
 * @param float $per_D Percentage factor to apply to Deuterium synthesizer consumption.
 * @param float $per_CES Percentage factor to apply to Solar plant production.
 * @param float $per_CEF Percentage factor to apply to Fusion reactor production.
 * @param float $per_SAT Percentage factor to apply to Solar satellite production.
 * @param int $FOR Quantity of Terraformers.
 * @param float $per_FOR Percentage factor to apply to Terraformer consumption.
 * @param int $classe Class-specific production bonus.
 * @param array $booster Array representing energy booster parameters, including booster value.
 *
 * @return array Returns an associative array containing:
 *               - "ratio" (float): The energy production-to-consumption ratio.
 *               - "conso_E" (int): Total energy consumption.
 *               - "prod_E" (int): Total energy production.
 *               - "prod_CES" (float): Energy produced by Solar plants.
 *               - "prod_CEF" (float): Energy produced by Fusion reactors.
 *               - "prod_SAT" (float): Energy produced by Solar satellites.
 *               - "prod_boost_E" (float): Energy boost provided by the booster.
 *               - "conso_M" (float): Energy consumed by Metal mines.
 *               - "conso_C" (float): Energy consumed by Crystal mines.
 *               - "conso_D" (float): Energy consumed by Deuterium synthesizers.
 *               - "conso_FOR" (float): Energy consumed by Terraformers.
 */
function ratio(
    $M,
    $C,
    $D,
    $CES,
    $CEF,
    $SAT,
    $temperature_max,
    $off_ing,
    $NRJ,
    $per_M = 1,
    $per_C = 1,
    $per_D = 1,
    $per_CES = 1,
    $per_CEF = 1,
    $per_SAT = 1,
    $FOR = 0,
    $per_FOR = 0,
    $classe = 0,
    $booster = null
)
{
    $consommation_E = 0; // la consommation
    $prod_boost_E = 0;
    $conso_M = consumption("M", $M) * $per_M;
    $conso_C = consumption("C", $C) * $per_C;
    $conso_D = consumption("D", $D) * $per_D;
    $conso_FOR = consumption("FOR", $FOR) * $per_FOR;
    $consommation_E += $conso_M + $conso_C + $conso_D + $conso_FOR;

    $production_E = 0; // la production
    $prod_CES = production('CES', $CES, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CES;
    $prod_CEF = production('CEF', $CEF, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CEF;
    $prod_SAT = production('SAT', $SAT, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_SAT;
    $production_E += $prod_CES + $prod_CEF + $prod_SAT;

    if ($booster != null) { // si booster
        $boost_CES = ($booster['booster_e_val'] / 100) * (production('CES', $CES, 0, $temperature_max, $NRJ) * $per_CES);
        $boost_CEF = ($booster['booster_e_val'] / 100) * (production('CEF', $CEF, 0, $temperature_max, $NRJ) * $per_CEF);
        $boost_SAT = ($booster['booster_e_val'] / 100) * (production('SAT', $SAT, 0, $temperature_max, $NRJ) * $per_SAT);

        $prod_boost_E = round($boost_CES) + round($boost_CEF) + round($boost_SAT);
    }
    $production_E += $prod_boost_E;

    $ratio = 1; // indique le pourcentage à appliquer sur la prod
    $ratio_temp = 1;
    $ratio_temp = ($consommation_E == 0) ? 0 : ($production_E * 100 / $consommation_E) / 100; // fix division par 0
    $ratio = ($ratio_temp >= 1) ? 1 : $ratio_temp;

    $consommation_E = round($consommation_E);
    $production_E = round($production_E);

    return array(
        "ratio" => $ratio, "conso_E" => $consommation_E, "prod_E" => $production_E,
        "prod_CES" => $prod_CES, "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "prod_boost_E" => $prod_boost_E,
        "conso_M" => $conso_M, "conso_C" => $conso_C, "conso_D" => $conso_D, "conso_FOR" => $conso_FOR
    );
}

/**
 *  Calculates various production and ratio values of resources.
 *
 * @param int $M Level of metal mine.
 * @param int $C Level of crystal mine.
 * @param int $D Level of deuterium mine.
 * @param int $CES Number of solar plants.
 * @param int $CEF Number of fusion reactors.
 * @param int $SAT Number of solar satellites.
 * @param int $temperature_max Maximum planet temperature.
 * @param int $off_ing Engineer officer level (default 0).
 * @param int $off_geo Geologist officer level (default 0).
 * @param int $off_full Enable full officer boost (default 0).
 * @param int $NRJ Energy technology level (default 0).
 * @param int $Plasma Plasma technology level (default 0).
 * @param float $per_M Percentage adjustment for metal production (default 1).
 * @param float $per_C Percentage adjustment for crystal production (default 1).
 * @param float $per_D Percentage adjustment for deuterium production (default 1).
 * @param float $per_CES Percentage adjustment for solar plant energy (default 1).
 * @param float $per_CEF Percentage adjustment for fusion reactor energy (default 1).
 * @param float $per_SAT Percentage adjustment for solar satellite energy (default 1).
 * @param array $booster Boosters active (default null, array with booster_m_val, booster_c_val, booster_d_val keys).
 * @param int $FOR Number of drills powered (default 0).
 * @param float $per_FOR Percentage adjustment for drill production (default 0).
 * @param int $classe Class of the account (default 0, e.g., none, Collector, General, Discoverer).
 * @param int $position Planet position in the system (default 0).
 * @param float $speed_uni Universe speed multiplier (default 1).
 * @return array Returns an associative array with the following keys:
 *                - M: Total metal production.
 *                - C: Total crystal production.
 *                - D: Total deuterium production.
 *                - FOR: Drill production array ('M', 'C', 'D').
 *                - ratio: Production ratio.
 *                - conso_E: Energy consumption.
 *                - prod_E: Energy production.
 *                - prod_CES: Energy from solar plants.
 *                - prod_CEF: Energy from fusion reactors.
 *                - prod_SAT: Energy from solar satellites.
 *                - prod_boost_E: Boosted energy production.
 *                - conso_M: Metal consumption.
 *                - conso_C: Crystal consumption.
 *                - conso_D: Deuterium consumption.
 *                - conso_FOR: Drill consumption.
 */
function bilan_production_ratio(
    $M,
    $C,
    $D,
    $CES,
    $CEF,
    $SAT,
    $temperature_max,
    $off_ing = 0,
    $off_geo = 0,
    $off_full = 0,
    $NRJ = 0,
    $Plasma = 0,
    $per_M = 1,
    $per_C = 1,
    $per_D = 1,
    $per_CES = 1,
    $per_CEF = 1,
    $per_SAT = 1,
    $booster = null,
    $FOR = 0,
    $per_FOR = 0,
    $classe = 0,
    $position = 0,
    $speed_uni = 1
)
{
    trigger_error("Les fonctions bilan_production_ratio,ratio,production,consumption,production_sat,production_foreuse dépréciées depuis 3.3.8, préférer les fonctions ogame_*. Ici ogame_production_planet.", E_USER_NOTICE);

    if ($off_full == 1) {
        $off_ing = $off_geo = 2;
    }
    $tmp = ratio(
        $M,
        $C,
        $D,
        $CES,
        $CEF,
        $SAT,
        $temperature_max,
        $off_ing,
        $NRJ,
        $per_M,
        $per_C,
        $per_D,
        $per_CES,
        $per_CEF,
        $per_SAT,
        $FOR,
        $per_FOR,
        $classe,
        $booster
    );
    $ratio = $tmp["ratio"];
    $consommation_E = $tmp["conso_E"];
    $production_E = $tmp["prod_E"];
    $prod_CES = $tmp["prod_CES"];
    $prod_CEF = $tmp["prod_CEF"];
    $prod_SAT = $tmp["prod_SAT"];
    $prod_boost_E = $tmp["prod_boost_E"];
    $conso_M = $tmp["conso_M"];
    $conso_C = $tmp["conso_C"];
    $conso_D = $tmp["conso_D"];
    $conso_FOR = $tmp["conso_FOR"];
    //$position : ATTENTION : a-t-on directement la position ou pas ? ou on doit faire un calcul à partir des coordonnées !!!

    if ($ratio > 0) {
        //production de metal avec ratio
        $prod_M = production("M", $M, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $position, $speed_uni) * $per_M;
        $prod_M *= $ratio;
        $prod_M = round($prod_M);

        //production de cristal avec ratio
        $prod_C = production("C", $C, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $position, $speed_uni) * $per_C;
        $prod_C *= $ratio;
        $prod_C = round($prod_C);

        //production de deut avec ratio
        $prod_D = production("D", $D, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $position, $speed_uni) * $per_D;
        $prod_D *= $ratio;
        $prod_D -= consumption("CEF", $CEF, $speed_uni) * $per_CEF; //on soustrait la conso de deut de la cef
        $prod_D = round($prod_D);

        //production des foreuses (métal, cristal et deut)
        $prod_FOR = production_foreuse($FOR, $M, $C, $D, $temperature_max, $classe, $position, $speed_uni);
        $prod_FOR['M'] = round($prod_FOR['M'] * $ratio);
        $prod_FOR['C'] = round($prod_FOR['C'] * $ratio);
        $prod_FOR['D'] = round($prod_FOR['D'] * $ratio);
    } else {
        $prod_M = production("M", 0, 0, 0, 0, 0, 0, $position, $speed_uni); //production de base
        $prod_C = production("C", 0, 0, 0, 0, 0, 0, $position, $speed_uni); //production de base
        $prod_D = production("D", 0, 0, 0, 0, 0, 0, $position, $speed_uni); //production de base
        $prod_FOR = production_foreuse(0, 0, 0, 0, 0, 0, $position, $speed_uni);
    }

    if ($booster != null) { // si booster
        //Bonus position
        $bonus_position_M = 0;
        $bonus_position_C = 0;
        if ($position == 1) {
            $bonus_position_C = 0.4;
        } elseif ($position == 2) {
            $bonus_position_C = 0.3;
        } elseif ($position == 3) {
            $bonus_position_C = 0.2;
        } elseif ($position == 8) {
            $bonus_position_M = 0.35;
        } elseif ($position == 9 || $position == 7) {
            $bonus_position_M = 0.23;
        } elseif ($position == 10 || $position == 6) {
            $bonus_position_M = 0.17;
        }
        $boost_M = ($booster['booster_m_val'] / 100) * (production('M', $M, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(30 * (1 + $bonus_position_M) * $speed_uni)) * $per_M * $ratio;
        $boost_C = ($booster['booster_c_val'] / 100) * (production('C', $C, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(15 * (1 + $bonus_position_C) * $speed_uni)) * $per_C * $ratio;
        $boost_D = ($booster['booster_d_val'] / 100) * (production('D', $D, 0, $temperature_max, 0, 0, 0, $position, $speed_uni)) * $per_D * $ratio;

        $prod_M += round($boost_M);
        $prod_C += round($boost_C);
        $prod_D += round($boost_D);
    }

    return array(
        "M" => $prod_M, "C" => $prod_C, "D" => $prod_D, "FOR" => $prod_FOR, "ratio" => $ratio,
        "conso_E" => $consommation_E, "prod_E" => $production_E, "prod_CES" => $prod_CES,
        "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "prod_boost_E" => $prod_boost_E, "conso_M" => $conso_M,
        "conso_C" => $conso_C, "conso_D" => $conso_D, "conso_FOR" => $conso_FOR
    );
}

////////////////// OGAME CARACTERISTIQUES fonctions : //////////////////////////
/**
 * @brief Retrieves a structured list of element names categorized by type.
 *
 * @return array A multidimensional array containing categorized element names. Categories include:
 *               'BAT' => Buildings,
 *               'RECH'=> Research technologies,
 *               'VSO' => Spaceships (fleet),
 *               'DEF' => Defensive structures,
 *               'CLASS'=> Classes,
 *               'RESS' => Resources.
 */
function ogame_get_element_names()
{
    $names = [];
    $names['BAT'] = [
        'M',    // Mine de métal
        'C',    // Mine de cristal
        'D',    // Synthétiseur de deutérium
        'CES',  // Centrale électrique solaire
        'CEF',  // Centrale électrique de fusion
        'UdR',  // Usine de robots
        'UdN',  // Usine de nanites
        'CSp',  // Chantier spatial
        'HM',   // Hangar de métal
        'HC',   // Hangar de cristal
        'HD',   // Réservoir de deutérium
        'Lab',  // Laboratoire
        'Ter',  // Terraformeur
        'DdR',  // Dépôt de ravitaillement
        'Silo', // Silo de missiles
        'Dock', // Dock spatial
        'BaLu', // Base lunaire
        'Pha',  // Phalange de capteur
        'PoSa', // Porte de saut spatial
    ];
    $names['RECH'] = [ // Recherches :
        'Esp',           // Technologie espionnage
        'Ordi',          // Technologie ordinateur
        'Armes',         // Technologie armes
        'Bouclier',      // Technologie bouclier
        'Protection',    // Technologie protection des vaisseaux spatiaux
        'NRJ',           // Technologie énergie
        'Hyp',           // Technologie hyperespace
        'RC',            // Réacteur à combustion
        'RI',            // Réacteur à impulsion
        'PH',            // Propulsion hyperespace
        'Laser',         // Technologie laser
        'Ions',          // Technologie à ions
        'Plasma',        // Technologie plasma
        'RRI',           // Réseau de recherche intergalactique
        'Graviton',      // Technologie graviton
        'Astrophysique', // Astrophysique
    ];
    $names['VSO'] = [ // Flottes :
        'PT',   // Petit transporteur
        'GT',   // Grand transporteur
        'CLE',  // Chasseur léger
        'CLO',  // Chasseur lourd
        'CR',   // Croiseur
        'VB',   // Vaisseau de bataille
        'VC',   // Vaisseau de colonisation
        'REC',  // Recycleur
        'SE',   // Sonde d'espionnage
        'BMD',  // Bombardier
        'DST',  // Destructeur
        'EDLM', // Étoile de la mort
        'TRA',  // Traqueur
        'SAT',  // Satellite solaire
        'FOR',  // Foreuse
        'FAU',  // Faucheur
        'ECL',  // Éclaireur
    ];
    $names['DEF'] = [ // Défenses :
        'LM',   // Lanceur de missiles
        'LLE',  // Artillerie laser légère
        'LLO',  // Artillerie laser lourde
        'CG',   // Canon de Gauss
        'AI',   // Artillerie à ions
        'LP',   // Lanceur de plasma
        'PB',   // Petit bouclier
        'GB',   // Grand bouclier
        'MIC',  // Missile d'interception
        'MIP',  // Missile interplanétaire
    ];
    $names['CLASS'] = [ // Classes :
        'none', // Aucune classe
        'COL',  // Classe collecteur
        'GEN',  // Classe général
        'EXP',  // Classe explorateur
    ];
    $names['RESS'] = [ // Ressources :
        'M',   // Métal
        'C',   // Cristal
        'D',   // Deutérium
        'NRJ', // Énergie
        'AM',  // AM
    ];

    return $names;
}

/**
 * Determines the category label of a given element name.
 *
 * @param string $nom The name of the element to check.
 * @return string|bool The category label of the element if found (e.g., 'VSO', 'SE'), or false if the element is not found.
 */
function ogame_is_element($nom)
{
    $names = ogame_get_element_names();
    foreach ($names as $label => $name) {
        if (in_array($nom, $name, true)) {
            return $label;
        }
    }

    return false;
}

/**
 * @brief Checks if a given element is a defense structure in the game.
 *
 * @param string $nom The name or identifier of the element to check.
 * @return bool Returns true if the element is a defense structure, false otherwise.
 */
function ogame_is_a_defence($nom)
{
    return ogame_is_element($nom) === 'DEF';
}

/**
 * Checks if the given element name corresponds to a fleet.
 *
 * @param string $nom The name of the element to check.
 * @return bool Returns true if the element is a fleet, false otherwise.
 */
function ogame_is_a_fleet($nom)
{
    return ogame_is_element($nom) === 'VSO';
}

/**
 * @brief Determines if the given element is a building in OGame.
 *
 * @param string $nom The name of the element to be checked.
 * @return bool True if the element is a building, false otherwise.
 */
function ogame_is_a_building($nom)
{
    return ogame_is_element($nom) === 'BAT';
}

/**
 * Determines if the given element name represents a research element.
 *
 * @param string $nom The name of the element to check.
 * @return bool Returns true if the element is a research element, otherwise false.
 */
function ogame_is_a_research($nom)
{
    return ogame_is_element($nom) === 'RECH';
}

/**
 * @brief Return base details of Ogame def/vso.
 *
 * @param[in] string $name The name as in Database, all for all element
 * @return array('structure','bouclier','attaque','vitesse','fret','conso',(array)'rapidfire',(bool)'civil')
 *      rapidfire=array('PT'=>x, ...) array of all fleet and defence; if x>0 then again else from
 */
function ogame_elements_details_base($name = 'all')
{
    $details_base = array();
    $names = ogame_get_element_names();
    //Coût de base des vaisseaux                 structure,bouclier,attaque,vitesse   ,fret    ,conso,civil)
    $details_base['PT'] = ogame_array_detail(4000, 10, 5, 5000, 5000, 10);
    $details_base['GT'] = ogame_array_detail(12000, 25, 5, 7500, 25000, 50);
    $details_base['CLE'] = ogame_array_detail(4000, 10, 50, 12500, 50, 20, false);
    $details_base['CLO'] = ogame_array_detail(10000, 25, 150, 10000, 100, 75, false);
    $details_base['CR'] = ogame_array_detail(27000, 50, 400, 15000, 800, 300, false);
    $details_base['VB'] = ogame_array_detail(60000, 200, 1000, 10000, 1500, 500, false);
    $details_base['VC'] = ogame_array_detail(30000, 100, 50, 2500, 7500, 1000);
    $details_base['REC'] = ogame_array_detail(16000, 10, 1, 2000, 20000, 300);
    $details_base['SE'] = ogame_array_detail(1000, 0, 0, 100000000, 0, 1);
    $details_base['BMD'] = ogame_array_detail(75000, 500, 1000, 400, 500, 700, false);
    $details_base['DST'] = ogame_array_detail(110000, 500, 2000, 5000, 2000, 1000, false);
    $details_base['TRA'] = ogame_array_detail(70000, 400, 700, 10000, 750, 250, false);
    $details_base['EDLM'] = ogame_array_detail(9000000, 50000, 200000, 100, 1000000, 1, false);
    $details_base['FOR'] = ogame_array_detail(4000, 1, 1);
    $details_base['ECL'] = ogame_array_detail(23000, 100, 200, 12000, 10000, 300, false);
    $details_base['FAU'] = ogame_array_detail(140000, 700, 2800, 7000, 10000, 1100, false);
    $details_base['SAT'] = ogame_array_detail(2000, 1, 1);
    //Coût de base des défenses
    $details_base['LM'] = ogame_array_detail(2000, 20, 80);
    $details_base['LLE'] = ogame_array_detail(2000, 25, 100);
    $details_base['LLO'] = ogame_array_detail(8000, 100, 250);
    $details_base['CG'] = ogame_array_detail(35000, 200, 1100);
    $details_base['AI'] = ogame_array_detail(8000, 500, 150);
    $details_base['LP'] = ogame_array_detail(100000, 300, 3000);
    $details_base['PB'] = ogame_array_detail(20000, 2000, 1);
    $details_base['GB'] = ogame_array_detail(100000, 10000, 1);
    $details_base['MIC'] = ogame_array_detail(8000, 1, 1);
    $details_base['MIP'] = ogame_array_detail(15000, 1, 12000);
    //rapidfire
    $details_base['PT']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'CLO' => -3, 'TRA' => -3, 'EDLM' => -250);
    $details_base['GT']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'TRA' => -3, 'EDLM' => -250);
    $details_base['CLE']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'CR' => -6, 'ECL' => -3, 'EDLM' => -200);
    $details_base['CLO']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'PT' => 3, 'TRA' => -4, 'ECL' => -2, 'EDLM' => -100);
    $details_base['CR']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'CLE' => 6, 'LM' => 10, 'TRA' => -4, 'ECL' => -3, 'EDLM' => -33);
    $details_base['VB']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'ECL' => 5, 'TRA' => -7, 'FAU' => -7, 'EDLM' => -30);
    $details_base['VC']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'EDLM' => -250);
    $details_base['REC']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'EDLM' => -250);
    $details_base['SE']['rapidfire'] = array('CLE' => -5, 'CLO' => -5, 'CR' => -5, 'VB' => -5, 'TRA' => -5, 'BMD' => -5, 'DST' => -5, 'EDLM' => -1250, 'FAU' => -5, 'ECL' => -5, 'PT' => -5, 'GT' => -5, 'VC' => -5, 'REC' => -5);
    $details_base['BMD']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'LM' => 20, 'LLE' => 20, 'LLO' => 10, 'AI' => 10, 'CG' => 5, 'LP' => 5, 'FAU' => -4, 'EDLM' => -25);
    $details_base['DST']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'LLE' => 10, 'TRA' => 2, 'FAU' => -3, 'EDLM' => -5);
    $details_base['TRA']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'CLO' => 4, 'CR' => 4, 'VB' => 7, 'PT' => 3, 'GT' => 3, 'DST' => -2, 'EDLM' => -15);
    $details_base['EDLM']['rapidfire'] = array('SE' => 1250, 'SAT' => 1250, 'CLE' => 200, 'CLO' => 100, 'CR' => 33, 'VB' => 30, 'BMD' => 25, 'DST' => 5, 'PT' => 250, 'GT' => 250, 'VC' => 250, 'REC' => 250, 'LM' => 200, 'LLE' => 200, 'LLO' => 100, 'AI' => 100, 'CG' => 50, 'TRA' => 15, 'ECL' => 30, 'FAU' => 10, 'FOR' => 1250);
    $details_base['FOR']['rapidfire'] = array('CLE' => -5, 'CLO' => -5, 'CR' => -5, 'VB' => -5, 'TRA' => -5, 'BMD' => -5, 'DST' => -5, 'EDLM' => -1250, 'FAU' => -5, 'ECL' => -5, 'PT' => -5, 'GT' => -5, 'VC' => -5, 'REC' => -5);
    $details_base['ECL']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'CR' => 3, 'CLE' => 3, 'CLO' => 2, 'VB' => -5, 'EDLM' => -30);
    $details_base['FAU']['rapidfire'] = array('SE' => 5, 'SAT' => 5, 'FOR' => 5, 'VB' => 7, 'BMD' => 4, 'DST' => 3, 'AI' => -2, 'EDLM' => -10);
    $details_base['SAT']['rapidfire'] = array('CLE' => -5, 'CLO' => -5, 'CR' => -5, 'VB' => -5, 'TRA' => -5, 'BMD' => -5, 'DST' => -5, 'EDLM' => -1250, 'FAU' => -5, 'ECL' => -5, 'PT' => -5, 'GT' => -5, 'VC' => -5, 'REC' => -5);
    //rapidfire des défenses
    $details_base['LM']['rapidfire'] = array('CR' => -10, 'BMD' => -20, 'EDLM' => -200);
    $details_base['LLE']['rapidfire'] = array('BMD' => -20, 'DST' => -20, 'EDLM' => -200);
    $details_base['LLO']['rapidfire'] = array('BMD' => -10, 'EDLM' => -100);
    $details_base['CG']['rapidfire'] = array('BMD' => -5, 'EDLM' => -50);
    $details_base['AI']['rapidfire'] = array('FAU' => 2, 'BMD' => -10, 'EDLM' => -100);
    $details_base['LP']['rapidfire'] = array('BMD' => -5);
    //fill rapidfire with other fleet/defence
    foreach ($details_base as &$elem) {
        foreach (array_merge($names['VSO'], $names['DEF']) as $fleet) {
            if (!isset($elem['rapidfire'][$fleet])) {
                $elem['rapidfire'][$fleet] = 0;
            }
        }
    }

    if ($name === 'all') {
        return $details_base;
    }
    if (!isset($details_base[$name])) {
        return ogame_array_detail(0, 0, 0);
    }
    return $details_base[$name];
}

/**
 * @brief Calculates technical data of a fleet or defence.
 *
 * @param[in] string     $name        The name, like name in Database
 * @param[in] array      $user_techno The array of technologies ('Armes','Bouclier','Protection','RC','RI','PH','Hyp', le reste est ignoré)
 * @param[in] string|int $classe      The user class //array('none','COL','GEN','EXP') - (1=Collectionneur)[0=aucune, 2=général, 3=explorateur])
 * @return array('nom','structure','bouclier','attaque','vitesse','fret','conso',(array)'rapidfire',(bool)'civil',(array)'cout') of the wanted fleet or defence.
 *      rapidfire=array('PT'=>x, ...) array of all fleet and defence; if x>0 then again else from
 *      cout=array of ogame_element_cumulate()=array('M','C','D','NRJ)
 */
function ogame_elements_details($name, $user_techno = null, $classe = 'none')
{
    static $RC_COEF = 0.1;
    static $RI_COEF = 0.2;
    static $PH_COEF = 0.3;
    static $HYP_COEF = 0.05;
    static $COMBAT_COEF = 0.1;
    $names = ogame_get_element_names();
    //Valeurs IN par défaut :
    if (!isset($user_techno['Armes']) || !is_numeric($user_techno['Armes'])) {
        $user_techno['Armes'] = 0;
    }
    if (!isset($user_techno['Bouclier']) || !is_numeric($user_techno['Bouclier'])) {
        $user_techno['Bouclier'] = 0;
    }
    if (!isset($user_techno['Protection']) || !is_numeric($user_techno['Protection'])) {
        $user_techno['Protection'] = 0;
    }
    if (!isset($user_techno['RC']) || !is_numeric($user_techno['RC'])) {
        $user_techno['RC'] = 0;
    }
    if (!isset($user_techno['RI']) || !is_numeric($user_techno['RI'])) {
        $user_techno['RI'] = 0;
    }
    if (!isset($user_techno['PH']) || !is_numeric($user_techno['PH'])) {
        $user_techno['PH'] = 0;
    }
    if (!isset($user_techno['Hyp']) || !is_numeric($user_techno['Hyp'])) {
        $user_techno['Hyp'] = 0;
    }
    if (isset($names['CLASS'][$classe])) {
        $classe = $names['CLASS'][$classe];
    }
    if (!in_array($classe, $names['CLASS'], true)) {
        $classe = $names['CLASS'][0];
    }
    if ($name === 'Sat') {
        $name = 'SAT';
    }

    $base_detail = ogame_elements_details_base($name);
    $cout = ogame_element_cumulate($name, 1);
    $user_techno['speed'] = 0;  //local variable pour la vitesse
    $techno_RC_coef = $user_techno['RC'] * $RC_COEF;
    $techno_RI_coef = $user_techno['RI'] * $RI_COEF;
    $techno_PH_coef = $user_techno['PH'] * $PH_COEF;
    $techno_Hyp_coef = $user_techno['Hyp'] * $HYP_COEF;
    $techno_Armes_coef = $user_techno['Armes'] * $COMBAT_COEF;
    $techno_Bouclier_coef = $user_techno['Bouclier'] * $COMBAT_COEF;
    $techno_Protection_coef = $user_techno['Protection'] * $COMBAT_COEF;

    //Calcul vitesse
    if ($name === 'PT' || $name === 'GT' || $name === 'CLE' || $name === 'SE' || $name === 'REC') { //vso avec le réacteur à combustion.
        $user_techno['speed'] = $techno_RC_coef;
    } elseif ($name === 'CLO' || $name === 'CR' || $name === 'VC' || $name === 'BMD') {
        $user_techno['speed'] = $techno_RI_coef;
    } elseif ($name === 'VB' || $name === 'DST' || $name === 'TRA' || $name === 'EDLM' || $name === 'ECL' || $name === 'FAU') {
        $user_techno['speed'] = $techno_PH_coef;
    }
    //cas particulier
    if ($name === 'PT' && $user_techno['RI'] >= 5) {
        $base_detail['vitesse'] = 10000;
        $base_detail['conso'] = 20;
        $user_techno['speed'] = $techno_RI_coef;
    }
    if ($name === 'REC') {
        if ($user_techno['RI'] >= 17) {
            $base_detail['vitesse'] = 4000;
            $base_detail['conso'] = 600;
            $user_techno['speed'] = $techno_RI_coef;
        }
        if ($user_techno['PH'] >= 15) {
            $base_detail['vitesse'] = 6000;
            $base_detail['conso'] = 900;
            $user_techno['speed'] = $techno_PH_coef;
        }
    }
    if ($name === 'BMD' && $user_techno['PH'] >= 8) {
        $base_detail['vitesse'] = 500;
        $user_techno['speed'] = $techno_PH_coef;
    }

    /*
    COL : +100% vitesse transporteur ; +25% fret transporteur
    GEN : +100% vitesse vso combat/REC or EDLM ; -25% conso ; +20% fret REC/ECL ; +2 lvl techno combat
    EXP : none
    */
    $structure = $base_detail['structure'];
    $bouclier = $base_detail['bouclier'];
    $attaque = $base_detail['attaque'];
    $vitesse = $base_detail['vitesse'];
    $fret = $base_detail['fret'];
    $conso = $base_detail['conso'];

    $bonus_class = 0;
    if ($classe === 'GEN') {
        $bonus_class = 2 * $COMBAT_COEF;    //+2 lvl
    }
    $structure = round($structure + $structure * $techno_Protection_coef + $structure * $bonus_class);
    $bouclier = round($bouclier + $bouclier * $techno_Bouclier_coef + $bouclier * $bonus_class);
    $attaque = round($attaque + $attaque * $techno_Armes_coef + $attaque * $bonus_class);

    $bonus_class = 0;
    if ($classe === 'COL') {
        if ($name === 'PT' || $name === 'GT') {
            $bonus_class = 1; //+100%
        }
    } elseif ($classe === 'GEN') {
        if (!$base_detail['civil'] && $name !== 'EDLM' || $name === 'REC') {
            $bonus_class = 1; //+100%
        }
    }
    $vitesse = round($vitesse + $vitesse * $user_techno['speed'] + $vitesse * $bonus_class);

    $bonus_class = 0;
    if ($classe === 'COL') {
        if ($name === 'PT' || $name === 'GT') {
            $bonus_class = 0.25; //+25%
        }
    } elseif ($classe === 'GEN') {
        if ($name === 'REC' || $name === 'ECL') {
            $bonus_class = 0.2; //+20%
        }
    }
    $fret = round($fret + $fret * $techno_Hyp_coef + $fret * $bonus_class);

    $bonus_class = 0;
    if ($classe === 'GEN') {
        $bonus_class = -0.25;    //-25%
    }
    $conso = round($conso + $conso * $bonus_class);
    if ($conso < 1) {
        $conso = 1;
    }

    $base_detail['structure'] = $structure;
    $base_detail['bouclier'] = $bouclier;
    $base_detail['attaque'] = $attaque;
    $base_detail['vitesse'] = $vitesse;
    $base_detail['fret'] = $fret;
    $base_detail['conso'] = $conso;
    $base_detail['cout'] = $cout;
    $base_detail['nom'] = $name;

    return $base_detail;
}

/**
 * @brief Calculates technical data of all fleet/defence.
 *
 * @param[in] array      $user_techno The array of technologies
 * @param[in] string|int $classe The user class //array('none','COL','GEN','EXP') - (1=Collectionneur)[0=aucune, 2=général, 3=explorateur])
 * @return array of all fleet/defence with are array of details from ogame_elements_details()
 */
function ogame_all_details($user_techno = null, $classe = 0)
{
    $result = array();
    $names = ogame_get_element_names();

    foreach (array_merge($names['VSO'], $names['DEF']) as $element) {
        $result[$element] = ogame_elements_details($element, $user_techno, $classe);
    }

    return $result;
}

/**
 * @brief Calculates technical data of Ogame requirement.
 *
 * @param[in] string $nom The name, like name in Database
 * @return array('none','COL','GEN','EXP' : bool for class, 'CES',etc. : int for all bat/rech name in database)
 */
function ogame_elements_requirement($name = 'all')
{
    $requis = [];
    $requis['rien'] = [];
    $requis['CEF'] = ['D' => 5, 'NRJ' => 3];
    $requis['UdN'] = ['UdR' => 10, 'Ordi' => 10];
    $requis['CSp'] = ['UdR' => 2];
    $requis['Ter'] = ['UdN' => 1, 'NRJ' => 12];
    $requis['Silo'] = ['CSp' => 1];
    $requis['Dock'] = ['CSp' => 2];
    $requis['Pha'] = ['BaLu' => 1];
    $requis['PoSa'] = ['BaLu' => 1, 'Hyp' => 7];
// Prérequis des technos
    $requis['Esp'] = ['Lab' => 3];
    $requis['Ordi'] = ['Lab' => 1];
    $requis['Armes'] = ['Lab' => 4];
    $requis['Bouclier'] = ['Lab' => 6, 'NRJ' => 3];
    $requis['Protection'] = ['Lab' => 2];
    $requis['NRJ'] = ['Lab' => 1];
    $requis['Hyp'] = ['Lab' => 7, 'NRJ' => 5, 'Bouclier' => 5];
    $requis['RC'] = ['Lab' => 1, 'NRJ' => 1];
    $requis['RI'] = ['Lab' => 2, 'NRJ' => 1];
    $requis['PH'] = ['Lab' => 7, 'Hyp' => 3];
    $requis['Laser'] = ['Lab' => 1, 'NRJ' => 2];
    $requis['Ions'] = ['Lab' => 4, 'NRJ' => 4, 'Laser' => 5];
    $requis['Plasma'] = ['Lab' => 4, 'NRJ' => 8, 'Laser' => 10, 'Ions' => 5];
    $requis['RRI'] = ['Lab' => 10, 'Hyp' => 8, 'Ordi' => 8];
    $requis['Graviton'] = ['Lab' => 12];
    $requis['Astrophysique'] = ['Lab' => 3, 'Esp' => 4, 'RI' => 3];
// Prérequis des vaisseaux
    $requis['PT'] = ['CSp' => 2, 'RC' => 2];
    $requis['GT'] = ['CSp' => 4, 'RC' => 6];
    $requis['CLE'] = ['CSp' => 1, 'RC' => 1];
    $requis['CLO'] = ['CSp' => 3, 'Protection' => 2, 'RI' => 2];
    $requis['CR'] = ['CSp' => 5, 'RI' => 4, 'Ions' => 2];
    $requis['VB'] = ['CSp' => 7, 'PH' => 4];
    $requis['VC'] = ['CSp' => 4, 'RI' => 3];
    $requis['REC'] = ['CSp' => 4, 'RC' => 6, 'Bouclier' => 2];
    $requis['SE'] = ['CSp' => 3, 'RC' => 3, 'Esp' => 2];
    $requis['BMD'] = ['CSp' => 8, 'RI' => 6, 'Plasma' => 5];
    $requis['DST'] = ['CSp' => 9, 'Hyp' => 5, 'PH' => 6];
    $requis['EDLM'] = ['CSp' => 12, 'Hyp' => 6, 'PH' => 7, 'Graviton' => 1];
    $requis['TRA'] = ['CSp' => 8, 'Hyp' => 5, 'PH' => 5, 'Laser' => 12];
    $requis['SAT'] = ['CSp' => 1];
    $requis['FOR'] = ['CSp' => 5, 'RC' => 4, 'Protection' => 4, 'Laser' => 4, 'COL' => true];
    $requis['FAU'] = ['CSp' => 10, 'Hyp' => 6, 'PH' => 7, 'Bouclier' => 6, 'GEN' => true];
    $requis['ECL'] = ['CSp' => 5, 'PH' => 2, 'EXP' => true];
// Prérequis des défenses
    $requis['LM'] = ['CSp' => 1];
    $requis['LLE'] = ['CSp' => 2, 'Laser' => 3];
    $requis['LLO'] = ['CSp' => 4, 'Laser' => 6, 'NRJ' => 3];
    $requis['CG'] = ['CSp' => 6, 'NRJ' => 6, 'Armes' => 3, 'Bouclier' => 1];
    $requis['AI'] = ['CSp' => 4, 'Ions' => 4];
    $requis['LP'] = ['CSp' => 8, 'Plasma' => 7];
    $requis['PB'] = ['CSp' => 1, 'Bouclier' => 2];
    $requis['GB'] = ['CSp' => 6, 'Bouclier' => 6];
    $requis['MIC'] = ['CSp' => 1, 'Silo' => 1];
    $requis['MIP'] = ['CSp' => 1, 'Silo' => 4, 'RI' => 1];

    $names = ogame_get_element_names();
    foreach ($requis as &$elem_requis) { //fill with other building/research
        foreach (array_merge($names['BAT'], $names['RECH']) as $element) {
            if (!isset($elem_requis[$element])) {
                $elem_requis[$element] = 0;
            }
        }
        $elem_requis['none'] = true;
        foreach ($names['CLASS'] as $element) {
            if (!isset($elem_requis[$element])) {
                $elem_requis[$element] = false;
            }
        }
        if ($elem_requis['COL'] === true || $elem_requis['GEN'] === true || $elem_requis['EXP'] === true) {
            $elem_requis['none'] = false;
        }
    }

    if ($name === 'all') {
        unset($requis['rien']);
        return $requis;
    }
    if (!isset($requis[$name])) {
        $name = 'rien';
    }
    return $requis[$name];
}

/**
 * @brief Calculates technical data of Ogame requirement of all building/research/fleet/defence.
 *
 * @return array of all building/research/fleet/defence with are array of requirement from ogame_elements_requirement()
 */
function ogame_all_requirement()
{
    $result = array();
    $names = ogame_get_element_names();

    foreach (array_merge($names['BAT'], $names['RECH'], $names['VSO'], $names['DEF']) as $element) {
        $result[$element] = ogame_elements_requirement($element);
    }

    return $result;
}

/**
 * Verifies if the prerequisites for a given element in the game are met based on the user's building
 * and technology levels.
 *
 * @param string $ogame_element_name The name of the element to check prerequisites for.
 * @param array $user_building_list An associative array representing the user's building levels,
 *                                  where keys are building names and values are their respective levels.
 * @param array $user_technology_list An associative array representing the user's technology levels,
 *                                    where keys are technology names and values are their respective levels.
 * @return bool Returns true if all prerequisites are met; otherwise, returns false.
 */
function prerequis_Valid($ogame_element_name, $user_building_list, $user_technology_list)
{
    global $log;
    // recuperation des prerequis pour l element indiqué
    $reqs = ogame_elements_requirement($ogame_element_name);

    foreach ($reqs as $reqName => $reqValue) {
        //prerequis recherche
        if (ogame_is_a_research($reqName) && $reqValue > 0) {
            if ($reqValue > $user_technology_list[$reqName]) {
                $log->debug("Requires $reqName technology for Tech $ogame_element_name");
                return false;
            }
        }
        // prerequis bat
        if (ogame_is_a_building($reqName) && $reqValue > 0) {
            if ($reqValue > $user_building_list[$reqName]) {
                $log->debug("Requires $reqName building for Tech $ogame_element_name");
                return false;
            }
        }
    }
    // tout autre cas, les prerequis sont bons
    return true;
}

/**
 * @brief Return base price of an Ogame bat/vso/def/rech
 *
 * @param[in] string $name The name as in Database, all for all element
 * @return array('M', 'C','D, 'NRJ') of the chosen element (array of these if 'all')
 */
function ogame_element_cout_base($name = 'all')
{
    $cout_base = array();
    //Coût de base des bâtiments                             métal , cristal, deutérium, NRJ
    $cout_base['M'] = ogame_array_ressource(60, 15, 0);
    $cout_base['C'] = ogame_array_ressource(48, 24, 0);
    $cout_base['D'] = ogame_array_ressource(225, 75, 0);
    $cout_base['CES'] = ogame_array_ressource(75, 30, 0);
    $cout_base['CEF'] = ogame_array_ressource(900, 360, 180);
    $cout_base['UdR'] = ogame_array_ressource(400, 120, 200);
    $cout_base['UdN'] = ogame_array_ressource(1000000, 500000, 100000);
    $cout_base['CSp'] = ogame_array_ressource(400, 200, 100);
    $cout_base['HM'] = ogame_array_ressource(1000, 0, 0);
    $cout_base['HC'] = ogame_array_ressource(1000, 500, 0);
    $cout_base['HD'] = ogame_array_ressource(1000, 1000, 0);
    $cout_base['Lab'] = ogame_array_ressource(200, 400, 200);
    $cout_base['Ter'] = ogame_array_ressource(0, 50000, 100000, 1000);
    $cout_base['DdR'] = ogame_array_ressource(20000, 40000, 0);
    $cout_base['Silo'] = ogame_array_ressource(20000, 20000, 1000);
    $cout_base['Dock'] = ogame_array_ressource(200, 0, 50, 50);
    $cout_base['BaLu'] = ogame_array_ressource(20000, 40000, 20000);
    $cout_base['Pha'] = ogame_array_ressource(20000, 40000, 20000);
    $cout_base['PoSa'] = ogame_array_ressource(2000000, 4000000, 2000000);
    //Coût de base des recherches
    $cout_base['Esp'] = ogame_array_ressource(200, 1000, 200);
    $cout_base['Ordi'] = ogame_array_ressource(0, 400, 600);
    $cout_base['Armes'] = ogame_array_ressource(800, 200, 0);
    $cout_base['Bouclier'] = ogame_array_ressource(200, 600, 0);
    $cout_base['Protection'] = ogame_array_ressource(1000, 0, 0);
    $cout_base['NRJ'] = ogame_array_ressource(0, 800, 400);
    $cout_base['Hyp'] = ogame_array_ressource(0, 4000, 2000);
    $cout_base['RC'] = ogame_array_ressource(400, 0, 600);
    $cout_base['RI'] = ogame_array_ressource(2000, 4000, 600);
    $cout_base['PH'] = ogame_array_ressource(10000, 20000, 6000);
    $cout_base['Laser'] = ogame_array_ressource(200, 100, 0);
    $cout_base['Ions'] = ogame_array_ressource(1000, 300, 100);
    $cout_base['Plasma'] = ogame_array_ressource(2000, 4000, 1000);
    $cout_base['RRI'] = ogame_array_ressource(240000, 400000, 160000);
    $cout_base['Graviton'] = ogame_array_ressource(0, 0, 0, 300000);
    $cout_base['Astrophysique'] = ogame_array_ressource(4000, 8000, 4000);
    //Coût de base des vaisseaux
    $cout_base['PT'] = ogame_array_ressource(2000, 2000, 0);
    $cout_base['GT'] = ogame_array_ressource(6000, 6000, 0);
    $cout_base['CLE'] = ogame_array_ressource(3000, 1000, 0);
    $cout_base['CLO'] = ogame_array_ressource(6000, 4000, 0);
    $cout_base['CR'] = ogame_array_ressource(20000, 7000, 2000);
    $cout_base['VB'] = ogame_array_ressource(45000, 15000, 0);
    $cout_base['VC'] = ogame_array_ressource(10000, 20000, 10000);
    $cout_base['REC'] = ogame_array_ressource(10000, 6000, 2000);
    $cout_base['SE'] = ogame_array_ressource(0, 1000, 0);
    $cout_base['BMD'] = ogame_array_ressource(50000, 25000, 15000);
    $cout_base['DST'] = ogame_array_ressource(60000, 50000, 15000);
    $cout_base['TRA'] = ogame_array_ressource(30000, 40000, 15000);
    $cout_base['EDLM'] = ogame_array_ressource(5000000, 4000000, 1000000);
    $cout_base['FOR'] = ogame_array_ressource(2000, 2000, 1000);
    $cout_base['ECL'] = ogame_array_ressource(8000, 15000, 8000);
    $cout_base['FAU'] = ogame_array_ressource(85000, 55000, 20000);
    $cout_base['SAT'] = ogame_array_ressource(0, 2000, 500);
    //Coût de base des défenses
    $cout_base['LM'] = ogame_array_ressource(2000, 0, 0);
    $cout_base['LLE'] = ogame_array_ressource(1500, 500, 0);
    $cout_base['LLO'] = ogame_array_ressource(6000, 2000, 0);
    $cout_base['CG'] = ogame_array_ressource(20000, 15000, 2000);
    $cout_base['AI'] = ogame_array_ressource(5000, 3000, 0);
    $cout_base['LP'] = ogame_array_ressource(50000, 50000, 30000);
    $cout_base['PB'] = ogame_array_ressource(10000, 10000, 0);
    $cout_base['GB'] = ogame_array_ressource(50000, 50000, 0);
    $cout_base['MIC'] = ogame_array_ressource(8000, 0, 2000);
    $cout_base['MIP'] = ogame_array_ressource(12500, 2500, 10000);

    if ($name === 'all') {
        return $cout_base;
    }
    if (!isset($cout_base[$name])) {
        return ogame_array_ressource(0, 0, 0);
    }
    return $cout_base[$name];
}

///////////////////// COUT fonctions : /////////////////////////////////////////
/**
 * @brief Calculates the evolve coefficient of a building and research.
 *
 * @param[in] string $name Building/research name, as in Database
 * @return array('M', 'C','D, 'NRJ') array of coefficient by ressource
 */
function ogame_element_evolve_coef($name)
{
    $coefficient = ogame_array_ressource(0, 0, 0);
    switch ($name) {
        case 'M': //no break
        case 'D': //no break
        case 'CES':
            $coefficient['M'] = 1.5;
            $coefficient['C'] = 1.5;
            break;
        case 'C':
            $coefficient['M'] = 1.6;
            $coefficient['C'] = 1.6;
            break;
        case 'CEF':
            $coefficient['M'] = 1.8;
            $coefficient['C'] = 1.8;
            $coefficient['D'] = 1.8;
            break;
        case 'Dock':
            $coefficient['M'] = 5;
            $coefficient['C'] = 5;
            $coefficient['D'] = 5;
            $coefficient['NRJ'] = 2.5;
            break;
        case 'Astrophysique':
            $coefficient['M'] = 1.75;
            $coefficient['C'] = 1.75;
            $coefficient['D'] = 1.75;
            break;
        case 'Graviton':
            $coefficient['NRJ'] = 3;
            break;
        default:
            $coefficient['M'] = 2;
            $coefficient['C'] = 2;
            $coefficient['D'] = 2;
            $coefficient['NRJ'] = 2;
            break;
    }

    $type = ogame_is_element($name);
    if ($type !== 'BAT' && $type !== 'RECH') {
        $coefficient = ogame_array_ressource(0, 0, 0);
    }
    return $coefficient;
}

/**
 * @brief Calculates price of an Ogame bat/vso/def/rech
 *
 * @param[in] string $name  The chosen name, as in Database
 * @param[in] int    $level The chosen level for bat/rech or the number of def/vso
 * @return array('M', 'C','D, 'NRJ')
 */
function ogame_element_cout($name, $level)
{
    $result = ogame_array_ressource(0, 0, 0);
    $type = ogame_is_element($name);
    if ($type === false) {
        return $result;
    }

    $coefficient = ogame_element_evolve_coef($name);
    $base_cout = ogame_element_cout_base($name);
    foreach (array_keys($result) as $ress) {
        if ($base_cout[$ress] !== 0) {  // Pour éviter les calculs inutiles !
            if ($type === 'BAT' || $type === 'RECH') {
                $result[$ress] = round($base_cout[$ress] * pow($coefficient[$ress], $level - 1));
                if ($type === 'RECH') {
                    $result[$ress] = round($result[$ress], -2); //Arrondi à la 100 pour les recherches.
                }
            } elseif ($type === 'DEF' || $type === 'VSO') {
                $result[$ress] = $base_cout[$ress] * $level;
            }
        }
    }

    return $result;
}

/**
 * @brief Calculates the price of an Ogame element to it current level.
 *
 * @param[in] string $name  Name of building/research/fleet/defence, like in name in database
 * @param[in] int    $level The current level or the number of fleet/defence
 * @return array('M', 'C','D, 'NRJ') resources used to it current level
 */
function ogame_element_cumulate($name, $level)
{
    $result = ogame_array_ressource(0, 0, 0);
    $type = ogame_is_element($name);
    if ($type === false) {
        return $result;
    }
    if ($type === 'DEF' || $type === 'VSO') {
        return ogame_element_cout($name, $level);
    }

    $coef = ogame_element_evolve_coef($name);
    $base_cout = ogame_element_cout_base($name);
    foreach (array_keys($result) as $ress) {
        if ($base_cout[$ress] !== 0) {  // Pour éviter les calculs inutiles !
            $result[$ress] = round($base_cout[$ress] * (1 - pow($coef[$ress], $level)) / (1 - $coef[$ress]));
            if ($type === 'RECH') {
                $result[$ress] = round($result[$ress], -2); //Arrondi à la 100 pour les recherches.
            }
        }
    }

    return $result;
}

/**
 * @brief Calculates the price of all element of type (building,defence,fleet,research).
 *
 * @param[in] array  $user Array of element each planet or moon
 * @param[in] string $type Type of element ('BAT' pour bâtiment, 'RECH' pour recherche, 'DEF' pour défense, 'VSO' pour vaisseau)
 * @return float Total price (M+C+D).
 */
function ogame_all_cumulate($user, $type)
{
    $total = 0;

    if ($type === 'RECH') {
        $data = $user;  //1 seul array, les technos
    } else {
        $data = current($user); //plusieurs array, les planètes/lunes, donc juste la 1er
    }
    while ($data) {
        foreach ($data as $key => $level) {
            if ($level == "") {
                $level = 0;
            }
            if ($key === 'Sat') {   //Nom dans la BDD ogspy_user_building
                $key = 'SAT';
            }
            if (ogame_is_element($key) === $type) {
                list($M, $C, $D) = array_values(ogame_element_cumulate($key, $level));
                $total += $M + $C + $D;
            }
        }
        if ($type === 'RECH') {
            break;
        }
        next($user);
        $data = current($user);
    }

    return $total;
}

/**
 * @brief Calculates the price of all building.
 * @param[in] $user_building Info of planet or moon
 * @return float Total price (M+C+D)
 */
function all_building_cumulate($user_building)
{
    return ogame_all_cumulate($user_building, 'BAT');
}

/**
 * @brief Calculates the price of all defence.
 * @param[in] $user_defence Info of planet or moon
 * @return float Total price (M+C+D)
 */
function all_defense_cumulate($user_defence)
{
    return ogame_all_cumulate($user_defence, 'DEF');
}

/**
 * @brief Calculates the price of all fleet.
 * @param[in] $user_fleet Info of planet or moon
 * @return float Total price (M+C+D)
 */
function all_fleet_cumulate($user_fleet)
{
    return ogame_all_cumulate($user_fleet, 'VSO');
}

/**
 * @brief Calculates the price of all research.
 * @param[in] $user_techno Info of technologies
 * @return float Total price (M+C+D)
 */
function all_technology_cumulate($user_techno)
{
    return ogame_all_cumulate($user_techno, 'RECH');
}

/**
 * Calculates the price of all lunas
 * @param $user_building
 * @param string $user_defence The list of buildings with corresponding levels on the luna
 * @return double bild :-)
 */
function all_lune_cumulate($user_building, $user_defence)
{
    return all_defense_cumulate($user_defence) + all_building_cumulate($user_building);
}

/**
 * @brief Calculates destroy price of a building.
 *
 * @param[in] string $name       Building name, as in Database
 * @param[in] int    $level      Building level
 * @param[in] int    $techno_ions Level of techno ions
 * @return false|array('M', 'C','D, 'NRJ'), false if undestroyable
 */
function ogame_building_destroy($name, $level, $techno_ions = 0)
{ // Coût de démolition du niveau X à X-1 = arrondi.inf( ((cout construction niveau X à X+1) / coefficient_dévolution^2) * (1 - 0,04 * technologie_ion) )
    //Bâtiments indestructibles :
    if ($name === 'Ter' || $name === 'Dock' || $name === 'BaLu') {
        return false;
    }

    $result = ogame_array_ressource(0, 0, 0);
    $coefficient = ogame_element_evolve_coef($name);
    $couts = ogame_element_cout($name, $level + 1);
    foreach ($couts as $ress => $cout) {
        if ($coefficient[$ress] !== 0) {
            $result[$ress] = floor(($cout / pow($coefficient[$ress], 2)) * (1 - 0.04 * $techno_ions));
        }
    }

    return $result;
}

///////////////////// FLOTTE fonctions : ///////////////////////////////////////
/**
 * @brief Calculates deut consummation for parking/expe of a fleet.
 *
 * @param[in] int $conso The conso of the fleet
 * @param[in] int $hour  Number of hours in parking
 * @return float Deut conso for this hour of parking
 */
function ogame_fleet_conso_statio($conso, $hour)
{
    $result = $hour * $conso / 10;
    if ($result < 1) {
        $result = 1;
    }
    if ($hour == 0) {
        $result = 0;
    }

    return floor($result);
}

/**
 * @brief Calculates the slowest chip speed of a fleet.
 *
 * @param[in] array  $fleet       List of chips
 * @param[in] array  $user_techno List of techno ('RC','RI','PH', le reste est ignoré)
 * @param[in] string $class       User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 * @return int the slowest speed
 */
function ogame_fleet_slowest_speed($fleet, $user_techno = null, $class = 'none')
{
    $names = ogame_get_element_names();
    $details = array();
    $max_speed = ogame_elements_details('SE', $user_techno, $class);   //The fastest fleet
    $min_speed = $max_speed['vitesse'];
    foreach ($names['VSO'] as $elem) {
        if (isset($fleet[$elem]) && $fleet[$elem] != 0) {
            $details = ogame_elements_details($elem, $user_techno, $class);
            if ($min_speed > $details['vitesse']) {
                $min_speed = $details['vitesse'];
            }
        }
    }
    return $min_speed;
}

/**
 * @brief Calculates distance between 2 coordinates.
 *
 * @param[in] string $a, $b         Coordinates ('g:s:p')
 *      'g1:s1:p1'->'g2:s2:p2' : normal distance calcul
 *      ':s1:p1'->'x:s2:p2     : distance between system/planet (only system is ':s1:')
 *      '::p1'->'x:x:p2        : distance between planet
 * @param[in] array  $user_techno List of techno ('RC','RI','PH',  only these are checked)
 * @param[in] string $class       User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 * @param[in] array  $server_config Info of universe ('num_of_galaxies','num_of_systems','donutGalaxy','donutSystem' only these are checked) default 9/499/1/1
 * @return array(int 'distance','type') [default=O,'p'], type='g' for between galaxy, 's' for between system and 'p' for between a sub-system
 */
function ogame_fleet_distance($a, $b, $user_techno = null, $class = 'none', $server_config = null)
{
    $result = array('distance' => 0, 'type' => 'p');
    if (!isset($server_config['num_of_galaxies']) || !is_numeric($server_config['num_of_galaxies'])) {
        $server_config['num_of_galaxies'] = 9;
    }
    if (!isset($server_config['num_of_systems']) || !is_numeric($server_config['num_of_systems'])) {
        $server_config['num_of_systems'] = 499;
    }
    if (!isset($server_config['donutGalaxy']) || !is_numeric($server_config['donutGalaxy'])) {
        $server_config['donutGalaxy'] = 1;
    }
    if (!isset($server_config['donutSystem']) || !is_numeric($server_config['donutSystem'])) {
        $server_config['donutSystem'] = 1;
    }

    $dist_abs = 0;
    $max_type = array('g' => $server_config['num_of_galaxies'], 's' => $server_config['num_of_systems'], 0);
    $uni_arrondi = array('g' => true, 's' => true, 'p' => false); //Par défaut
    if ($server_config['donutGalaxy'] === 0) {
        $max_type['g'] = 0;
        $uni_arrondi['g'] = false;
    }
    if ($server_config['donutSystem'] === 0) {
        $max_type['s'] = 0;
        $uni_arrondi['s'] = false;
    }
    $coord_a = ogame_find_coordinates($a);
    $coord_b = ogame_find_coordinates($b);
    $key = 'p';
    foreach (array_keys($coord_a) as $key) {    //On ne calcule la distance qu'entre des vraies coordonnées.
        if ($coord_a[$key] === 0 || $coord_b[$key] === 0) {
            $coord_a[$key] = 0;
            $coord_b[$key] = 0;
        }
        $dist_abs = abs($coord_a[$key] - $coord_b[$key]);   //|a-b|
        if ($dist_abs !== 0) {
            break;
        }
    }
    $result['type'] = $key;
    $result['distance'] = $dist_abs;    //|a-b|
    if ($uni_arrondi[$key] && ($dist_abs > $max_type[$key] / 2)) {
        $result['distance'] = abs($dist_abs - $max_type[$key]); //||a-b| - base|
    }

    return $result;
}

/**
 * @brief Calculates time and conso to send a fleet.
 *
 * @param[in] string $coord_from,$coord_to    Coordinates begin and end
 * @param[in] array  $fleet                   Array of fleet and their number (array('PT'=>10,etc.))
 * @param[in] int    $speed_per               Percentage of speed wanted
 * @param[in] array  $user_techno             List of techno ('RC','RI','PH', le reste est ignoré)
 * @param[in] string $class                   User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 * @param[in] array  $server_config           Info of universe ('num_of_galaxies','num_of_systems','donutGalaxy','donutSystem' only these are checked) default 9/499/1/1
 * @param[in] string $type                    Indicates specific mission ('statio'/'expe', 'fuite')
 * @param[in] int    $hour_mission            Number of hour of the specific mission
 * @return array('conso', 'time'), time in seconds (one trip only)
 */
function ogame_fleet_send($coord_from, $coord_to, $fleet, $speed_per = 100, $user_techno = null, $class = 'none', $server_config = null, $type = '', $hour_mission = 0)
{
    $result = array('conso' => 0, 'time' => 0);

    $names = ogame_get_element_names();
    $details = array();
    $consos = array();
    $max_speed = ogame_elements_details('SE', $user_techno, $class);   //The fastest fleet
    $min_speed = $max_speed['vitesse'];
    foreach ($names['VSO'] as $elem) {
        $consos[$elem] = 0;
        if (isset($fleet[$elem]) && $fleet[$elem] != 0) {
            $details = ogame_elements_details($elem, $user_techno, $class);
            if ($min_speed > $details['vitesse']) {
                $min_speed = $details['vitesse'];
            }
            $consos[$elem] = $details['conso'] * $fleet[$elem];
        }
    }
    if ($min_speed == 0) { //Ne devrait jamais arriver mais pour éviter une div/0.
        return $result;
    }

    $distance = ogame_fleet_distance($coord_from, $coord_to, $server_config);
    if ($type === 'fuite') {
        $distance['type'] = $type;
    }
    $conso_sum = array_sum($consos);
    switch ($distance['type']) {
        case 'g':   //between galaxy
            // durée = Dans une autre galaxie : 10 + [ 35 000 / %vitesse * Racine(écart de galaxies * 20 000 000 / vitesse du vaisseau)]
            // conso = Entre galaxies : 1 + arrondi.sup[conso * ((4 * distance absolue entre les galaxies) / 7) * (%vitesse / 100 + 1)^2 ]
            $result['time'] = (10 + (35000 / $speed_per * sqrt($distance['distance'] * 20000000 / $min_speed)));
            $result['conso'] = 1 + ($conso_sum * ((4 * $distance['distance']) / 7) * pow($speed_per / 100 + 1, 2));
            break;
        case 's':   //between system (so inside same galaxy)
            // durée = Dans sa galaxie        : 10 + [ 35 000 / %vitesse * Racine((2 700 000 + (écart de systèmes) * 95 000) / vitesse du vaisseau)]
            // conso = Entre systèmes solaires  : 1 + arrondi.sup[conso * ((2.700 + 95 * distance absolue entre les systèmes solaires) / 35.000) * (%vitesse / 100 + 1)^2 ]
            $result['time'] = (10 + (35000 / $speed_per * sqrt((2700000 + $distance['distance'] * 95000) / $min_speed)));
            $result['conso'] = 1 + ($conso_sum * ((2700 + 95 * $distance['distance']) / 35000) * pow($speed_per / 100 + 1, 2));
            break;
        case 'p':   //between sub-system (so in same galaxy and same system)
            if ($distance['distance'] === 0) { // to moon/cdr
                // durée = Jusqu'à son propre cdr : 10 + [ 35 000 / %vitesse * Racine(5 000 / vitesse du vaisseau) ]
                // conso = Entre planète et lune (propre) : 1 + arrondi.sup[conso * ( 5 / 35.000) * (%vitesse / 100 + 1)^2 ]
                $result['time'] = (10 + (35000 / $speed_per * sqrt(5000 / $min_speed)));
                $result['conso'] = 1 + ($conso_sum * (5 / 35000) * pow($speed_per / 100 + 1, 2));
            } else { //to other planet in same system
                // durée = Dans son système solaire : 10 + [ 35 000 / %vitesse * Racine((1 000 000 + distance absolue entre les planètes * 5 000) / vitesse du vaisseau) ]
                // conso = Dans son système solaire : 1 + arrondi.sup[conso * ((1.000 + 5 * distance absolue entre les planètes) / 35.000) * (%vitesse / 100 + 1)^2 ]
                $result['time'] = (10 + (35000 / $speed_per * sqrt((1000000 + $distance['distance'] * 5000) / $min_speed)));
                $result['conso'] = 1 + ($conso_sum * ((1000 + 5 * $distance['distance']) / 35000) * pow($speed_per / 100 + 1, 2));
            }
            break;
        case 'fuite':
            // Fuite de flotte : arrondi.inf[ conso à une distance de 1 * 1.5 ]
            $distance['distance'] = 1 * 1.5;
            $result['conso'] = ($conso_sum * $distance['distance']);  //???
        default:
            break;
    }
    if ($type === 'statio' || $type === 'expe') {
        $result['conso'] += ogame_fleet_conso_statio($conso_sum, $hour_mission);
        // $result['time']  += $hour_mission * 3600;
    }
    $result['time'] = round($result['time']);
    $result['conso'] = ceil($result['conso']);

    return $result;
}

///////////////////// TEMPS fonctions : ////////////////////////////////////////
/**
 * @brief Calculates cumulate lab network.
 *
 * @param[in] array $user_empire       From user_get_empire()
 * @param[in] int   $current_planet_id Current planet to run a research, if not best lab (theory).
 * @return int Number of cumulate lab network
 */
function ogame_labo_cumulate($user_empire, $current_planet_id = -1)
{
    $result = 0;
    //Valeurs IN par défaut :
    if (!isset($user_empire['technology']['RRI']) || !is_numeric($user_empire['technology']['RRI'])) {
        $user_empire['technology']['RRI'] = 0;
    }

    $labs = array();
    $current_lab = -1;
    $nb_labo = 1 + $user_empire['technology']['RRI'];

    foreach ($user_empire['building'] as $planet) {
        if (isset($planet['planet_id']) && isset($planet['Lab']) && is_numeric($planet['Lab'])) {
            if ($planet['planet_id'] !== $current_planet_id) {
                $labs[] = $planet['Lab'];
            } else {
                $current_lab = $planet['Lab'];
            }
        }
    }
    rsort($labs, SORT_NUMERIC);
    if ($current_lab !== -1) {
        $nb_labo--;
        $result = $current_lab;
    }
    if ($nb_labo > count($labs)) {
        $nb_labo = count($labs);
    }
    for ($i = 0; $i < $nb_labo; $i++) {
        $result += $labs[$i];
    }

    return $result;
}

/**
 * @brief Calculates construction time of a OGame element bat/vso/def/rech.
 *
 * @param[in] string $name          The name, like name in Database
 * @param[in] int    $level         The level or number for def/vso
 * @param[in] array  $user_building Array of bat level ('CSp','UdR','UdN','Lab')
 * @param[in] int    $cumul_labo    Number of cumulate lab network (only for rech)
 * @param[in] array  $user_class    User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 * @return float Time in seconds
 */
function ogame_construction_time($name, $level, $user_building, $cumul_labo = 0, $player_class = 'none')
{
    static $RECH_BONUS_EXP = 0.25;   //-25% temps de recherche
    //Valeurs OUT par défaut :
    $result = 0;
    //Valeurs IN par défaut :
    if (!isset($user_building['CSp']) || !is_numeric($user_building['CSp'])) {
        $user_building['CSp'] = 0;
    }
    if (!isset($user_building['UdR']) || !is_numeric($user_building['UdR'])) {
        $user_building['UdR'] = 0;
    }
    if (!isset($user_building['UdN']) || !is_numeric($user_building['UdN'])) {
        $user_building['UdN'] = 0;
    }
    if (!isset($user_building['Lab']) || !is_numeric($user_building['Lab'])) {
        $user_building['Lab'] = 0;
    }

    if ($cumul_labo === 0) {
        $cumul_labo = $user_building['Lab'];
    }
    $type = ogame_is_element($name);
    $cout = ogame_element_cout($name, $level);
    switch ($type) {
        case 'BAT':
            //(Métal + Cristal) / (2500 * MAX(4 - niveau / 2; 1) * (1 + niveau Usine de robots) * 2^niveau Usine de Nanites )
            $result = ($cout['M'] + $cout['C']);
            $tmp = 2500 * max(4 - $level / 2, 1);
            $tmp *= (1 + $user_building['UdR']) * pow(2, $user_building['UdN']);
            $result = $result / $tmp;
            break;
        case 'VSO': //no break
        case 'DEF':
            //(cristal + métal)/5000 * 2/(1 + niveau chantier spatial) * 0,5^niveau nanites
            $result = ($cout['M'] + $cout['C']) / 5000;
            $result *= 2 / (1 + $user_building['CSp']);
            $result *= pow(0.5, $user_building['UdN']);
            break;
        case 'RECH':
            //(métal + cristal) / (1000 * (1 + niveau labo + n meilleurs niveaux des labos autres que le labo de la planète effectuant la recherche))
            $result = ($cout['M'] + $cout['C']) / (1000 * (1 + $cumul_labo));
            if ($player_class === 'EXP') {
                $result = $result * (1 - $RECH_BONUS_EXP);
            }
            break;
        default:
            break;
    }
    if ($result !== 0) {
        $result = floor($result * 60 * 60); //floor à la seconde
        if ($result < 1) {
            $result = 1;
        }
    }

    return $result;
}

///////////////////// DIVERS fonctions : ///////////////////////////////////////
/**
 * @brief Return planet position from coordinates.
 * @param[in] string $coordinates planet coordinates (galaxy:system:position)
 * @return int planet position
 */
function ogame_find_planet_position($coordinates)
{
    $position = ogame_find_coordinates($coordinates);

    return $position['p'];
}

/**
 * @brief Return coordinates in array.
 *
 * @param[in] string $string_coord Coordinates, in string like in Database ('2:3:4')
 * @return array('g','s','p') of int, default is 0 ('::6' give planet position of 6)
 */
function ogame_find_coordinates($string_coord)
{
    $result = array('g' => 0, 's' => 0, 'p' => 0);

    $coordinates_tmp = explode(':', $string_coord);
    if (count($coordinates_tmp) === 3) {
        $result['g'] = (int)$coordinates_tmp[0];
        $result['s'] = (int)$coordinates_tmp[1];
        $result['p'] = (int)$coordinates_tmp[2];
    }

    return $result;
}


/**
 * @brief Calculates the planet storage capacity (taille hangar).
 *
 * @param[in] int $level Storage building level
 * @return float capacity
 */
function ogame_depot_capacity($level)
{
    $capacity = 10000;  // capacité par défaut

    if ($level > 0) {
        $capacity = 5000 * floor(2.5 * exp(20 * $level / 33));
    }

    return $capacity;
}

/**
 * Returns the maximum numbers of planet slots available according to the Astrophysic level
 * @param int $level Astrophysic Level
 * @return int the maximum number of planets
 */
function astro_max_planete($level)
{
    global $server_config;
    return ($server_config['astro_strict'] && $level < 15) ? 9 : ceil($level / 2) + 1;
}


/**
 * @brief Calculates phalanx range.
 *
 * @param[in] int   $level         Level of the phalanx
 * @param[in] array $user_class    User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 * @return float Range in system
 */
function ogame_phalanx_range($level, $player_class = 'none')
{
    static $PHA_BONUS_EXP = 0.2;   //-20%

    $bonus_class = 0;
    if ($player_class === 'EXP') {
        $bonus_class = $PHA_BONUS_EXP;
    }

    return round((pow($level, 2) - 1) * (1 + $bonus_class));
}

/**
 * @brief Calculates MIP range.
 *
 * @param[in] int $impulsion Techno impulsion (RI)
 * @return int Range in system
 */
function ogame_missile_range($impulsion = 1)
{
    return 5 * $impulsion - 1;
}

/**
 * @brief Calculates MIP speed.
 *
 * @param[in] int $nb_system Number of sub-system from current planet
 * @param[in] int $speed_uni Universe speed
 * @return int Speed in seconds
 */
function ogame_missile_speed($nb_system, $speed_uni = 1)
{
    return (30 + 60 * $nb_system) * $speed_uni;
}

/**
 * @brief Calculates additional case given by terraformer.
 *
 * @param[in] int $level The terra level
 * @return int Number of additional case
 */
function ogame_terra_case($level)
{
    return floor(5.5 * $level);
}
