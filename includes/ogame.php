<?php
/* Small struct helpers (arrays) moved to includes/ogame_structs.php */
/* Element functions were moved to includes/ogame_elements.php */

/**
 * @brief Calculates technical data of Ogame requirement.
 *
 * @param[in] string $nom The name, like name in Database
 * @return array('none','COL','GEN','EXP' : bool for class, 'CES',etc. : int for all bat/rech name in database)
 */
/* Requirement and cost/cumulate functions are now required centrally from common.php */

///////////////////// FLOTTE fonctions : ///////////////////////////////////////
/**
 * Calculates the fuel consumption for a stationary fleet mission over a given duration.
 *
 * @param float $conso The fuel consumption rate of the fleet (consumption per hour at 100% efficiency).
 * @param int $hour The duration of the stationary mission in hours.
 * @return int The total fuel consumption for the mission, with a minimum of 1 unit unless the duration is zero.
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
 * @brief Calculates the slowest speed in a fleet.
 *
 * @param array $fleet Array of fleet and their number (e.g., array('PT' => 10, etc.)).
 * @param array $user_techno List of technologies affecting fleet speed (e.g., array('RC', 'RI', 'PH')). Default is null.
 * @param string $class User class ('COL', 'GEN', 'EXP', 'none'). Default is 'none'.
 *
 * @return int The slowest speed of the fleet in units per second.
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
 * Calculates the distance and type of travel between two coordinates.
 *
 * @param mixed $a Starting coordinates.
 * @param mixed $b Target coordinates.
 * @param array|null $user_techno Optional array containing user technologies ('RC', 'RI', 'PH', etc.).
 * @param string $class User class ('COL', 'GEN', 'EXP', 'none').
 * @param array|null $server_config Optional server configuration with the following keys:
 *                                  - 'num_of_galaxies' (int): Number of available galaxies (default is 9).
 *                                  - 'num_of_systems' (int): Number of solar systems per galaxy (default is 499).
 *                                  - 'donutGalaxy' (int): Whether galaxies are donut-shaped (1 for yes, 0 for no, default is 1).
 *                                  - 'donutSystem' (int): Whether systems are donut-shaped (1 for yes, 0 for no, default is 1).
 * @return array Returns an array with the following keys:
 *               - 'distance' (int): The calculated distance.
 *               - 'type' (string): The travel type ('p', 's', or 'g').
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
// Coordinate helpers moved to includes/ogame_structs.php


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
