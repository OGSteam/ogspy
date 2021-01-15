<?php
/**
 * OGame Games Formulas and Data
 * @package OGSpy
 * @subpackage Ogame Data
 * @author Kyser
 * @copyright Copyright &copy; 2012, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7697 $)
 * @created 15/11/2005
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
if (!isset($server_config['speed_uni'])) {
    $server_config['speed_uni'] = 1;
}

/**
 * Gets the hourly production of a Mine or a Energy plant.
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
    //TODO : supprimer références globales
    global $server_config;
    $speed_uni = $server_config['speed_uni'];

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
    $bonus_foreuse = 0.0002; //0.02% / foreuse
    $bonus_foreuse_max = 0;
    //Valeur de la classe en valeur ajoutée.
    if ($classe == 1) {
        $bonus_class_mine = 0.25; //+25%
        $bonus_class_energie = 0.10; //+10%
        $bonus_foreuse = $bonus_foreuse * 1.5; //+50%
        if ($officier != 0) {
            $bonus_foreuse_max = 0.1; //+10%
        }
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
    
    // print_r("officier=$officier, geo=$geo, ing=$ing, C_m=$bonus_class_mine, C_E=$bonus_class_energie, C_f=$bonus_foreuse, speed=$speed_uni\n");
    switch ($building) {
        case "M":
            $prod_base = floor(30 * (1 + $bonus_position) * $speed_uni);
            $result = 30 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $bonus_position);
            $result = $result * $speed_uni; // vitesse uni
            // $result_foreuse = $result * $bonus_foreuse; //foreuse sur produc de base des mines
            $result = $result * (1 + $geo + 0.01 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "C":
            $prod_base = floor(15 * (1 + $bonus_position) * $speed_uni);
            $result = 20 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $bonus_position);
            $result = $result * $speed_uni; // vitesse uni
            // $result_foreuse = $result * $bonus_foreuse; //foreuse sur produc de base des mines
            $result = $result * (1 + $geo + 0.0066 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "D":
            $result = 10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $temperature_max); //<Ogame V7
            //$result = 10 * $level * pow(1.1, $level) * floor((1.44 - 0.004 * $temperature_max)*10)/10;  //troncature à la décimale
            $result = $result * $speed_uni; // vitesse uni
            // $result_foreuse = $result * $bonus_foreuse; //foreuse sur produc de base des mines
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
    if($nb_foreuse > $nb_max) {
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
 *  @brief Find number max of foreuse.
 *  
 *  @param [in] int $level_M The metal mine level
 *  @param [in] int $level_C The cristal mine level
 *  @param [in] int $level_D The deuterium mine level
 *  @param [in] int $officier geologue option enabled (=1) or not(=0) or full Officer(=2)
 *  @param [in] int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 *  @return int number max of foreus
 */
function foreuse_max($level_M, $level_C, $level_D, $officier = 0, $classe = 0) {
    $bonus_foreuse_max = 0;
    
    if ($classe == 1 && $officier != 0) {
        $bonus_foreuse_max = 0.1; //+10%
    }
    return ($level_M + $level_C + $level_D) * 8 * (1 + $bonus_foreuse_max);
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
    //TODO : supprimer références globales
    global $server_config;
    $speed_uni = $server_config['speed_uni'];
    
    switch ($building) {
        case "M":   //no break
        case "C":
            $result = 10 * $level * pow(1.1, $level);
            // $result = ceil($result); //troncature supérieure
            $result = floor($result); // troncature
            break;

        case "D":
            $result = 20 * $level * pow(1.1, $level);
            // $result = ceil($result); //troncature supérieure
            $result = floor($result); // troncature
            break;

        case "CEF":
            $result = $server_config['speed_uni'] * (10 * $level * pow(1.1, $level));
            $result = round($result); // arrondi
            break;

        case "FOR":
            $result = $level * 50;
            break;

        default:
            $result = 0;
            break;
    }

    return ($result);
}

/**
 * Gets the production usage of the current planet.
 *
 * @param int $M Metal Mine Level
 * @param int $C Cristal Mine Level
 * @param int $D Deuterieum Mine Level
 * @param int $CES Solar Plant Level
 * @param int $CEF Fusion Plant Level
 * @param int $SAT Number of sattelites
 * @param int $FOR Number of foreuse
 * @param int $temperature_max Max temprature of the current planet
 * @param int $off_ing Officer ingenieur option enabled (=1) or not(=0) or full Officer(=2)
 * @param int $NRJ Current value of the user Energy Technology
 * @param int $per_M Metal Mine production percent (0=0%, 1=100%)
 * @param int $per_C Cristal Mine production percent (0=0%, 1=100%)
 * @param int $per_D Deuterieum Mine production percent (0=0%, 1=100%)
 * @param int $per_CES Solar Plant production percent (0=0%, 1=100%)
 * @param int $per_CEF Fusion Plant production percent (0=0%, 1=100%)
 * @param int $per_SAT sattelites production percent (0=0%, 1=100%)
 * @param int $per_FOR foreuse production percent (0=0%, 1=100%)
 * @param int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @param null $booster
 * @return array("ratio", "conso_E", "prod_E", "prod_CES", "prod_CEF", "prod_SAT", "conso_M", "conso_C", "conso_D", "conso_FOR")
 */
function ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
               $per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $FOR = 0, $per_FOR = 0, $classe = 0, $booster = NULL)
{
    $consommation_E = 0; // la consommation
    $prod_boost_E = 0;
    $conso_M   = consumption("M", $M) * $per_M;
    $conso_C   = consumption("C", $C) * $per_C;
    $conso_D   = consumption("D", $D) * $per_D;
    $conso_FOR = consumption("FOR", $FOR) * $per_FOR;
    $consommation_E += $conso_M + $conso_C + $conso_D + $conso_FOR;

    $production_E = 0; // la production
    $prod_CES = production('CES', $CES, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CES;
    $prod_CEF = production('CEF', $CEF, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CEF;
    $prod_SAT = production('SAT', $SAT, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_SAT;
    $production_E += $prod_CES + $prod_CEF + $prod_SAT;

    if ($booster != NULL) { // si booster
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

    return array("ratio" => $ratio, "conso_E" => $consommation_E, "prod_E" => $production_E,
        "prod_CES" => $prod_CES, "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "prod_boost_E" => $prod_boost_E,
        "conso_M" => $conso_M, "conso_C" => $conso_C, "conso_D" => $conso_D, "conso_FOR" => $conso_FOR);
}

/**
 * Calculates the Production corresponding to the current ratio
 * @param int $M Metal Mine Level
 * @param int $C Cristal Mine Level
 * @param int $D Deuterieum Mine Level
 * @param int $CES Solar Plant Level
 * @param int $CEF Fusion Plant Level
 * @param int $SAT Number of sattelites
 * @param int $temperature_max Max temprature of the current planet
 * @param int $off_ing Officer ingenieur option enabled (=1) or not(=0)
 * @param int $off_geo Officer geologue option enabled (=1) or not(=0)
 * @param int $off_full full Officer enabled (=1) or not(=0)
 * @param int $NRJ Current value of the user Energy Technology
 * @param int $Plasma Current value of the user Plasma Technology
 * @param int $per_M Metal Mine production percent (0=0%, 1=100%)
 * @param int $per_C Cristal Mine production percent (0=0%, 1=100%)
 * @param int $per_D Deuterieum Mine production percent (0=0%, 1=100%)
 * @param int $per_CES Solar Plant production percent (0=0%, 1=100%)
 * @param int $per_CEF Fusion Plant production percent (0=0%, 1=100%)
 * @param int $per_SAT
 * @param null $booster
 * @return array
 */
function bilan_production_ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing = 0, $off_geo = 0, $off_full = 0, $NRJ = 0, $Plasma = 0,
$per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $booster = NULL,  $FOR = 0, $per_FOR = 0, $classe = 0, $position = 0, $speed_uni = 1)
{

    if ($off_full == 1) {
        $off_ing = $off_geo = 2;
    }
    $tmp = ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
            $per_M, $per_C, $per_D, $per_CES, $per_CEF, $per_SAT, $FOR, $per_FOR, $classe, $booster);
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

    if ($booster != NULL) { // si booster
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
        $boost_C = ($booster['booster_c_val'] / 100) * (production('C', $C, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(15 * (1 + $bonus_position_M) * $speed_uni)) * $per_C * $ratio;
        $boost_D = ($booster['booster_d_val'] / 100) * (production('D', $D, 0, $temperature_max, 0, 0, 0, $position, $speed_uni)) * $per_D * $ratio;
        
        $prod_M += round($boost_M);
        $prod_C += round($boost_C);
        $prod_D += round($boost_D);
    }
	 
    return array("M" => $prod_M, "C" => $prod_C, "D" => $prod_D, "FOR" => $prod_FOR, "ratio" => $ratio,
            "conso_E" => $consommation_E, "prod_E" => $production_E, "prod_CES" => $prod_CES,
            "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "prod_boost_E" => $prod_boost_E, "conso_M" => $conso_M,
            "conso_C" => $conso_C, "conso_D" => $conso_D, "conso_FOR" => $conso_FOR);
}

/**
 *  @brief Return planet position from coordinates.
 *  @param [in] $coordinates planet coordinates (galaxy:system:position)
 *  @return int planet position
 */
function find_planet_position($coordinates) {
    $position = 0;
    
    $coordinates_tmp = explode(':', $coordinates);
    if (count($coordinates_tmp) === 3) {
        $position = (int) $coordinates_tmp[2];
    }
    return $position;
}

/**
 * Calculates the Planet storage capacity (Taille Hangar)
 * @param int $level Storage building Level
 * @return double capacity
 */
function depot_capacity($level)
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
 * @return the maximum number of planets
 */
function astro_max_planete($level)
{
    global $server_config;
    return ($server_config['astro_strict'] && $level < 15) ? 9 : ceil($level / 2) + 1;
}

/**
 *  @brief Calculates the price to upgrade an Ogame element to a defined level (only building and research).
 *  
 *  @param [in] string $name Name of building or research, like in name in database
 *  @param [in] int $level The wanted level
 *  @return array('M', 'C','D, 'NRJ') ressources required to upgrade the building or technologies
 */
function ogame_element_upgrade($name, $level)
{
    switch ($name) {
// Bâtiment :
        case "M":
            $M = 60 * pow(1.5, ($level - 1));
            $C = 15 * pow(1.5, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "C":
            $M = 48 * pow(1.6, ($level - 1));
            $C = 24 * pow(1.6, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "D":
            $M = 225 * pow(1.5, ($level - 1));
            $C = 75 * pow(1.5, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "CES":
            $M = 75 * pow(1.5, ($level - 1));
            $C = 30 * pow(1.5, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "CEF":
            $M = 900 * pow(1.8, ($level - 1));
            $C = 360 * pow(1.8, ($level - 1));
            $D = 180 * pow(1.8, ($level - 1));
            $NRJ = 0;
            break;

        case "UdR":
            $M = 400 * pow(2, ($level - 1));
            $C = 120 * pow(2, ($level - 1));
            $D = 200 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "UdN":
            $M = 1000000 * pow(2, ($level - 1));
            $C = 500000 * pow(2, ($level - 1));
            $D = 100000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "CSp":
            $M = 400 * pow(2, ($level - 1));
            $C = 200 * pow(2, ($level - 1));
            $D = 100 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "HM":
            $M = 1000 * pow(2, ($level - 1));
            $C = 0;
            $D = 0;
            $NRJ = 0;
            break;

        case "HC":
            $M = 1000 * pow(2, ($level - 1));
            $C = 500 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "HD":
            $M = 1000 * pow(2, ($level - 1));
            $C = 1000 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "Lab":
            $M = 200 * pow(2, ($level - 1));
            $C = 400 * pow(2, ($level - 1));
            $D = 200 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Ter":
            $M = 0;
            $C = 50000 * pow(2, ($level - 1));
            $D = 100000 * pow(2, ($level - 1));
            $NRJ = 1000 * pow(2, ($level - 1));
            break;

        case "DdR":
            $M = 20000 * pow(2, ($level - 1));
            $C = 40000 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "Silo":
            $M = 20000 * pow(2, ($level - 1));
            $C = 20000 * pow(2, ($level - 1));
            $D = 1000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Dock":
            $M = 200 * pow(5, ($level - 1));
            $C = 0;
            $D = 50 * pow(5, ($level - 1));
            $NRJ = 50 * pow(2.5, ($level - 1));
            break;

        case "BaLu":
            $M = 20000 * pow(2, ($level - 1));
            $C = 40000 * pow(2, ($level - 1));
            $D = 20000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Pha":
            $M = 20000 * pow(2, ($level - 1));
            $C = 40000 * pow(2, ($level - 1));
            $D = 20000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "PoSa":
            $M = 2000000 * pow(2, ($level - 1));
            $C = 4000000 * pow(2, ($level - 1));
            $D = 2000000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

// Recherches :
        case "Esp":
            $M = 200 * pow(2, ($level - 1));
            $C = 1000 * pow(2, ($level - 1));
            $D = 200 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Ordi":
            $M = 0;
            $C = 400 * pow(2, ($level - 1));
            $D = 600 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Armes":
            $M = 800 * pow(2, ($level - 1));
            $C = 200 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "Bouclier":
            $M = 200 * pow(2, ($level - 1));
            $C = 600 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "Protection":
            $M = 1000 * pow(2, ($level - 1));
            $C = 0;
            $D = 0;
            $NRJ = 0;
            break;

        case "NRJ":
            $M = 0;
            $C = 800 * pow(2, ($level - 1));
            $D = 400 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Hyp":
            $M = 0;
            $C = 4000 * pow(2, ($level - 1));
            $D = 2000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "RC":
            $M = 400 * pow(2, ($level - 1));
            $C = 0;
            $D = 600 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "RI":
            $M = 2000 * pow(2, ($level - 1));
            $C = 4000 * pow(2, ($level - 1));
            $D = 600 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "PH":
            $M = 10000 * pow(2, ($level - 1));
            $C = 20000 * pow(2, ($level - 1));
            $D = 6000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Laser":
            $M = 200 * pow(2, ($level - 1));
            $C = 100 * pow(2, ($level - 1));
            $D = 0;
            $NRJ = 0;
            break;

        case "Ions":
            $M = 1000 * pow(2, ($level - 1));
            $C = 300 * pow(2, ($level - 1));
            $D = 100 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Plasma":
            $M = 2000 * pow(2, ($level - 1));
            $C = 4000 * pow(2, ($level - 1));
            $D = 1000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "RRI":
            $M = 240000 * pow(2, ($level - 1));
            $C = 400000 * pow(2, ($level - 1));
            $D = 160000 * pow(2, ($level - 1));
            $NRJ = 0;
            break;

        case "Graviton":
            $M = 0;
            $C = 0;
            $D = 0;
            $NRJ = 300000 * pow(3, ($level - 1));
            break;

        case "Astrophysique":
            $M = 4000 * pow(1.75, ($level - 1));
            $C = 8000 * pow(1.75, ($level - 1));
            $D = 4000 * pow(1.75, ($level - 1));
            $NRJ = 0;
            break;

        default:
            $M = 0;
            $C = 0;
            $D = 0;
            $NRJ = 0;
            break;
    }

    return array('M' => round($M), 'C' => round($C), 'D' => round($D), 'NRJ' => round($NRJ));
}

/**
 *  @brief Calculates the price to upgrade an building.
 *  @param [in] string $building The name, like name in database
 *  @param [in] int $level The wanted level
 *  @return array('M', 'C','D, 'NRJ') ressources required to upgrade
 */
function building_upgrade($building, $level)  { return ogame_element_upgrade($building, $level); }
/**
 *  @brief Calculates the price to upgrade an research.
 *  @param [in] string $research The name, like name in database
 *  @param [in] int $level The wanted level
 *  @return array('M', 'C','D, 'NRJ') ressources required to upgrade
 */
function research_upgrade($research, $level)  { return ogame_element_upgrade($research, $level); }

/**
 *  @brief Calculates the price of an Ogame element to it current level.
 *  
 *  @param [in] string $name Name of building/research/fleet/defence, like in name in database
 *  @param [in] int $level The current level or the number of fleet/defence
 *  @return array('M', 'C','D, 'NRJ') ressources used to it current level
 */
function ogame_element_cumulate($name, $level)
{
    $NRJ = 0;
    switch ($name) {
// Bâtiment non x2 :
        case "M":
            $M = 60 * (1 - pow(1.5, $level)) / (-0.5);
            $C = 15 * (1 - pow(1.5, $level)) / (-0.5);
            $D = 0;
            break;

        case "C":
            $M = 48 * (1 - pow(1.6, $level)) / (-0.6);
            $C = 24 * (1 - pow(1.6, $level)) / (-0.6);
            $D = 0;
            break;

        case "D":
            $M = 225 * (1 - pow(1.5, $level)) / (-0.5);
            $C = 75 * (1 - pow(1.5, $level)) / (-0.5);
            $D = 0;
            break;

        case "CES":
            $M = 75 * (1 - pow(1.5, $level)) / (-0.5);
            $C = 30 * (1 - pow(1.5, $level)) / (-0.5);
            $D = 0;
            break;

        case "CEF":
            $M = 900 * (1 - pow(1.8, $level)) / (-0.8);
            $C = 360 * (1 - pow(1.8, $level)) / (-0.8);
            $D = 180 * (1 - pow(1.8, $level)) / (-0.8);
            break;

        case "Dock":
            $M = 200 * (1 - pow(5, $level)) / (-4);
            $C = 0;
            $D = 50 * (1 - pow(5, $level)) / (-4);
            $NRJ = 50 * (1 - pow(2.5, $level)) / (-1.5);
            break;

// Recherches non x2 :
        case "Graviton":
            $M = 0;
            $C = 0;
            $D = 0;
            $NRJ = 300000 * (1 - pow(3, $level)) / (-2);
            break;

        case "Astrophysique":
            $M = 4000 * (1 - pow(1.75, $level)) / (-0.75);
            $C = 8000 * (1 - pow(1.75, $level)) / (-0.75);
            $D = 4000 * (1 - pow(1.75, $level)) / (-0.75);
            break;

// Flottes :
        case "PT":
            $M = 2000 * $level;
            $C = 2000 * $level;
            $D = 0;
            break;

        case "GT":
            $M = 6000 * $level;
            $C = 6000 * $level;
            $D = 0;
            break;

        case "CLE":
            $M = 3000 * $level;
            $C = 1000 * $level;
            $D = 0;
            break;

        case "CLO":
            $M = 6000 * $level;
            $C = 4000 * $level;
            $D = 0;
            break;

        case "CR":
            $M = 20000 * $level;
            $C =  7000 * $level;
            $D =  2000 * $level;
            break;

        case "VB":
            $M = 45000 * $level;
            $C = 15000 * $level;
            $D = 0;
            break;

        case "VC":
            $M = 10000 * $level;
            $C = 20000 * $level;
            $D = 10000 * $level;
            break;

        case "REC":
            $M = 10000 * $level;
            $C =  6000 * $level;
            $D =  2000 * $level;
            break;

        case "SE":
            $M = 0;
            $C = 1000 * $level;
            $D = 0;
            break;

        case "BMD":
            $M = 50000 * $level;
            $C = 25000 * $level;
            $D = 15000 * $level;
            break;

        case "DST":
            $M = 60000 * $level;
            $C = 50000 * $level;
            $D = 15000 * $level;
            break;

        case "TRA":
            $M = 30000 * $level;
            $C = 40000 * $level;
            $D = 15000 * $level;
            break;

        case "EDLM":
            $M = 5000000 * $level;
            $C = 4000000 * $level;
            $D = 1000000 * $level;
            break;

        case "FOR":
            $M = 2000 * $level;
            $C = 2000 * $level;
            $D = 1000 * $level;
            break;

        case "ECL":
            $M =  8000 * $level;
            $C = 15000 * $level;
            $D =  8000 * $level;
            break;

        case "FAU":
            $M = 85000 * $level;
            $C = 55000 * $level;
            $D = 20000 * $level;
            break;

        case "SAT":
        case "Sat":
            $M = 0;
            $C = 2000 * $level;
            $D =  500 * $level;
            break;

// Défenses :
        case "LM":
            $M = 2000 * $level;
            $C = 0;
            $D = 0;
            break;

        case "LLE":
            $M = 1500 * $level;
            $C =  500 * $level;
            $D = 0;
            break;

        case "LLO":
            $M = 6000 * $level;
            $C = 2000 * $level;
            $D = 0;
            break;

        case "CG":
            $M = 20000 * $level;
            $C = 15000 * $level;
            $D =  2000 * $level;
            break;

        case "AI":
            $M = 5000 * $level;
            $C = 3000 * $level;
            $D = 0;
            break;

        case "LP":
            $M = 50000 * $level;
            $C = 50000 * $level;
            $D = 30000 * $level;
            break;

        case "PB":
            $M = 10000 * $level;
            $C = 10000 * $level;
            $D = 0;
            break;

        case "GB":
            $M = 50000 * $level;
            $C = 50000 * $level;
            $D = 0;
            break;

        case "MIC":
            $M = 8000 * $level;
            $C = 0;
            $D = 2000 * $level;
            break;

        case "MIP":
            $M = 12500 * $level;
            $C = 2500 * $level;
            $D = 10000 * $level;
            break;

        default: //Pour les bâtiments et recherches en x2
            list($M, $C, $D, $NRJ) = array_values(building_upgrade($name, 1));
            $M = $M * -(1 - pow(2, $level));
            $C = $C * -(1 - pow(2, $level));
            $D = $D * -(1 - pow(2, $level));
            $NRJ = $NRJ * -(1 - pow(2, $level));
            break;
    }

    return array('M' => round($M), 'C' => round($C), 'D' => round($D), 'NRJ' => round($NRJ));
}

/**
 *  @brief Calculates the price of a building to it current level.
 *  @param [in] string $building Name of building, like in name in database
 *  @param [in] $level The current level
 *  @return array('M', 'C','D, 'NRJ') ressources used to it current level
 */
function building_cumulate($building, $level) { return ogame_element_cumulate($building, $level); }
/**
 *  @brief Calculates the price of a number of defence.
 *  @param [in] string $defence Name of defence, like in name in database
 *  @param [in] $number The current number
 *  @return array('M', 'C','D, 'NRJ') ressources used to have this defence number
 */
function defence_cumulate($defence, $number)  { return ogame_element_cumulate($defence, $number); }
/**
 *  @brief Calculates the price of a number of fleet.
 *  @param [in] string $fleet Name of fleet, like in name in database
 *  @param [in] $number The current number
 *  @return array('M', 'C','D, 'NRJ') ressources used to have this fleet number
 */
function fleet_cumulate($fleet, $number)      { return ogame_element_cumulate($fleet, $number); }
/**
 *  @brief Calculates the price of a research to it current level.
 *  @param [in] string $research Name of research, like in name in database
 *  @param [in] $level The current level
 *  @return array('M', 'C','D, 'NRJ') ressources used to it current level
 */
function research_cumulate($research, $level) { return ogame_element_cumulate($research, $level); }

/**
 *  @brief Give database names of a buiding/research/fleet/defence.
 *  
 *  @return array('BAT'=>array, 'RECH'=>array, 'VSO'=>array, 'DEF'=>array)
 *  
 */
function ogame_get_element_names() {
    $names = array();
    
    $names['BAT'] = array('M', 'C', 'D', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'HM', 'HC', 'HD', 'Lab', 'Ter', 'DdR', 'Silo', 'Dock', 'BaLu', 'Pha', 'PoSa');
    $names['RECH'] = array('Esp', 'Ordi', 'Armes', 'Bouclier', 'Protection', 'NRJ', 'Hyp', 'RC', 'RI', 'PH', 'Laser', 'Ions', 'Plasma', 'RRI', 'Graviton', 'Astrophysique');
    $names['VSO'] = array('PT', 'GT', 'CLE', 'CLO', 'CR', 'VB', 'VC', 'REC', 'SE', 'BMD', 'DST', 'EDLM', 'TRA', 'SAT', 'FOR', 'FAU', 'ECL');
    $names['DEF'] = array('LM', 'LLE', 'LLO', 'CG', 'AI', 'LP', 'PB', 'GB', 'MIC', 'MIP');
    
    return $names;
}

/**
 *  @brief Détermine si c'est un bâtiment, une recherche, un vaisseau ou une défense.
 *  
 *  @param [in] string $nom Nom à recherche, correspond au nom en BDD
 *  @return false|string 'BAT' pour bâtiment, 'RECH' pour recherche, 'DEF' pour défense, 'VSO' pour vaisseau et false sinon
 *  
 */
function ogame_is_element($nom)
{
    switch ($nom) {
// Bâtiments :
        case 'M':    //Mine de métal
        case 'C':    //Mine de cristal
        case 'D':    //Synthétiseur de deutérium
        case 'CES':  //Centrale électrique solaire
        case 'CEF':  //Centrale électrique de fusion
        case 'UdR':  //Usine de robots
        case 'UdN':  //Usine de nanites
        case 'CSp':  //Chantier spatial
        case 'HM':   //Hangar de métal
        case 'HC':   //Hangar de cristal
        case 'HD':   //Réservoir de deutérium
        case 'Lab':  //Laboratoire
        case 'Ter':  //Terraformeur
        case 'DdR':  //Dépot de ravitaillement
        case 'Silo': //Silo de missiles
        case 'Dock': //Dock spatial
        case 'BaLu': //Base lunaire
        case 'Pha':  //Phalange de capteur
        case 'PoSa': //Porte de saut spatial
            return 'BAT';
            break;
// Recherches :
        case 'Esp':           //Technologie espionage
        case 'Ordi':          //Technologie ordinateur
        case 'Armes':         //Technologie armes
        case 'Bouclier':      //Technologie bouclier
        case 'Protection':    //Technologie protection des vaisseaux spatiaux
        case 'NRJ':           //Technologie énergie
        case 'Hyp':           //Technologie hyperespace
        case 'RC':            //Réacteur à combustion
        case 'RI':            //Réacteur à impulsion
        case 'PH':            //Propulsion hyperespace
        case 'Laser':         //Technologie laser
        case 'Ions':          //Technologie à ions
        case 'Plasma':        //Technologie plasma
        case 'RRI':           //Réseau de recherche intergalactique
        case 'Graviton':      //Technologie graviton
        case 'Astrophysique': //Astrophysique
            return 'RECH';
            break;
// Flottes :
        case 'PT':   //Petit transporteur
        case 'GT':   //Grand transporteur
        case 'CLE':  //Chasseur léger
        case 'CLO':  //Chasseur lourd
        case 'CR':   //Croiseur
        case 'VB':   //Vaisseau de bataille
        case 'VC':   //Vaisseau de colonisation
        case 'REC':  //Recycleur
        case 'SE':   //Sonde d'espionnage
        case 'BMD':  //Bombardier
        case 'DST':  //Destructeur
        case 'TRA':  //Traqueur
        case 'EDLM': //Étoile de la mort
        case 'FOR':  //Foreuse
        case 'ECL':  //Éclaireur
        case 'FAU':  //Faucheur
        case 'SAT':  //Satellite solaire
        case 'Sat':
            return 'VSO';
            break;
// Défenses :
        case 'LM':  //Lanceur de missiles
        case 'LLE': //Artillerie laser légère
        case 'LLO': //Artillerie laser lourde
        case 'CG':  //Canon de Gauss
        case 'AI':  //Artillerie à ions
        case 'LP':  //Lanceur de plasma
        case 'PB':  //Petit bouclier
        case 'GB':  //Grand bouclier
        case 'MIC': //Missile d'interception
        case 'MIP': //Missile interplanétaire
            return 'DEF';
            break;
        default:
            return false;
    }
}

/**
 *  @brief Is an Ogame defence ?
 *  @param [in] string $nom Name to look, like name in database
 *  @return bool
 */
function is_a_defence($nom)  { return ogame_is_element($nom) === 'DEF'; }
/**
 *  @brief Is an Ogame fleet ?
 *  @param [in] string $nom Name to look, like name in database
 *  @return bool
 */
function is_a_fleet($nom)    { return ogame_is_element($nom) === 'VSO'; }
/**
 *  @brief Is an Ogame building ?
 *  @param [in] string $nom Name to look, like name in database
 *  @return bool
 */
function is_a_building($nom) { return ogame_is_element($nom) === 'BAT'; }
/**
 *  @brief Is an Ogame research ?
 *  @param [in] string $nom Name to look, like name in database
 *  @return bool
 */
function is_a_research($nom) { return ogame_is_element($nom) === 'RECH'; }

/**
 *  @brief Calculates the price of all element of type (building,defence,fleet,research).
 *  
 *  @param [in] $user Array of element (for other than research, each planet or moon)
 *  @param [in] string $type Type of element ('BAT' pour bâtiment, 'RECH' pour recherche, 'DEF' pour défense, 'VSO' pour vaisseau)
 *  @return float Total price (M+C+D).
 */
function ogame_all_cumulate($user, $type)
{
    $total = 0;
    
    if ($type === 'RECH') {
        $data = $user;  //1 seul array, les technos
    } else {
        $data = current($user); //plusieurs array, les planètes/lunes, donc juste la 1er
    }
    while($data){
        foreach ($data as $key=>$level) {
            if ($level == "") {
                $level = 0;
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
 *  @brief Calculates the price of all building.
 *  
 *  @param [in] $user_building Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_building_cumulate($user_building) { return ogame_all_cumulate($user_building, 'BAT'); }
/**
 *  @brief Calculates the price of all defence.
 *  
 *  @param [in] $user_defence Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_defence_cumulate($user_defence)   { return ogame_all_cumulate($user_defence, 'DEF'); }
/**
 *  @brief Calculates the price of all fleet.
 *  
 *  @param [in] $user_fleet Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_fleet_cumulate($user_fleet)       { return ogame_all_cumulate($user_fleet, 'VSO'); }
/**
 *  @brief Calculates the price of all research.
 *  
 *  @param [in] $user_techno Info of technologies
 *  @return float Total price (M+C+D)
 */
function all_technology_cumulate($user_techno) { return ogame_all_cumulate($user_techno, 'RECH'); }

/**
 * Calculates the price of all lunas
 * @param $user_building
 * @param string $user_defence The list of buildings with corresponding levels on the luna
 * @return double bild :-)
 */
function all_lune_cumulate($user_building, $user_defence)
{
    return all_defence_cumulate($user_defence) + all_building_cumulate($user_building);
}

/**
 *  @brief Calculates deut consommation for parking of a fleet.
 *  
 *  @param [in] int $conso The conso of the fleet
 *  @param [in] int $hour Number of hours in parking
 *  @return float Deut conso for this hour of parking
 */
function ogame_fleet_conso_statio($conso, $hour) {
    $result = $hour * $conso / 10;
    if ($result < 1) {
        $result = 1;
    }
    return floor($result);
}

/**
 *  @brief Calculates technical data of a fleet or defence.
 *  
 *  @param [in] string $nom The name, like name in Database
 *  @param [in] array $user_techno The array of technologies
 *  @param [in] string|int $classe The user class //array('none','COL','GEN','EXP') - (1=Collectionneur)[0=aucune, 2=général, 3=explorateur])
 *  @return array('structure','bouclier','attaque','vitesse','fret','conso',(array)'rapidfire',(bool)'civil',(array)'cout') of the wanted fleet or defence.
 *      rapidfire=array('PT'=>x, ...) array of all fleet and defence; if x>0 then again else from
 *      cout=array of ogame_element_cumulate()=array('M','C','D','NRJ)
 */
function ogame_elements_details($nom, $user_techno = null, $classe = 0)
{
    static $RC_COEF     = 0.1;
    static $RI_COEF     = 0.2;
    static $PH_COEF     = 0.3;
    static $HYP_COEF    = 0.05;
    static $COMBAT_COEF = 0.1;
    static $CLASS_NAME = array('none', 'COL', 'GEN', 'EXP');
    //Valeurs IN par défaut :
    if ($user_techno == null || !isset($user_techno['Armes']))      { $user_techno['Armes'] = 0; }
    if ($user_techno == null || !isset($user_techno['Bouclier']))   { $user_techno['Bouclier'] = 0; }
    if ($user_techno == null || !isset($user_techno['Protection'])) { $user_techno['Protection'] = 0; }
    if ($user_techno == null || !isset($user_techno['RC']))         { $user_techno['RC'] = 0; }
    if ($user_techno == null || !isset($user_techno['RI']))         { $user_techno['RI'] = 0; }
    if ($user_techno == null || !isset($user_techno['PH']))         { $user_techno['PH'] = 0; }
    if ($user_techno == null || !isset($user_techno['Hyp']))        { $user_techno['Hyp'] = 0; }
    if (isset($CLASS_NAME[$classe])) { $classe = $CLASS_NAME[$classe]; }
    if (array_search($classe, $CLASS_NAME) === false) { $classe = $CLASS_NAME[0]; }
    //Valeurs OUT par défaut :
    $structure    = 0;
    $bouclier     = 0;
    $attaque      = 0;
    $vitesse      = 0;
    $fret         = 0;
    $conso        = 0;
    $rapidfire    = array();
    $civil        = true;
    $cout         = ogame_element_cumulate($nom,1);
    
    $names = ogame_get_element_names();
    $user_techno['speed'] = 0;
    $techno_RC_coef  = $user_techno['RC']  * $RC_COEF;
    $techno_RI_coef  = $user_techno['RI']  * $RI_COEF;
    $techno_PH_coef  = $user_techno['PH']  * $PH_COEF;
    $techno_Hyp_coef = $user_techno['Hyp'] * $HYP_COEF;
    $techno_Armes_coef      = $user_techno['Armes']      * $COMBAT_COEF;
    $techno_Bouclier_coef   = $user_techno['Bouclier']   * $COMBAT_COEF;
    $techno_Protection_coef = $user_techno['Protection'] * $COMBAT_COEF;
    
    switch ($nom) {
// Flottes :
        case 'PT':   //Petit transporteur
            $structure = 4000;
            $bouclier  = 10;
            $attaque   = 5;
            $vitesse   = ($user_techno['RI']) < 5 ? 5000 : 10000;
            $fret      = 5000;
            $conso     = ($user_techno['RI']) < 5 ? 10 : 20;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5, 'CLO'=>-3,'TRA'=>-3,'EDLM'=>-250);
            $user_techno['speed'] = ($user_techno['RI']) < 5 ? $techno_RC_coef : $techno_RI_coef;
            break;
        case 'GT':   //Grand transporteur
            $structure = 12000;
            $bouclier  = 25;
            $attaque   = 5;
            $vitesse   = 7500;
            $fret      = 25000;
            $conso     = 50;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5, 'TRA'=>-3,'EDLM'=>-250);
            $user_techno['speed'] = $techno_RC_coef;
            break;
        case 'CLE':  //Chasseur léger
            $structure = 4000;
            $bouclier  = 10;
            $attaque   = 50;
            $vitesse   = 12500;
            $fret      = 50;
            $conso     = 20;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5, 'CR'=>-6,'ECL'=>-3,'EDLM'=>-200);
            $civil     = false;
            $user_techno['speed'] = $techno_RC_coef;
            break;
        case 'CLO':  //Chasseur lourd
            $structure = 10000;
            $bouclier  = 25;
            $attaque   = 150;
            $vitesse   = 10000;
            $fret      = 100;
            $conso     = 75;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'PT'=>3, 'TRA'=>-4,'ECL'=>-2,'EDLM'=>-100);
            $civil     = false;
            $user_techno['speed'] = $techno_RI_coef;
            break;
        case 'CR':   //Croiseur
            $structure = 27000;
            $bouclier  = 50;
            $attaque   = 400;
            $vitesse   = 15000;
            $fret      = 800;
            $conso     = 300;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'CLE'=>6,'LM'=>10, 'TRA'=>-4,'ECL'=>-3,'EDLM'=>-33);
            $civil     = false;
            $user_techno['speed'] = $techno_RI_coef;
            break;
        case 'VB':   //Vaisseau de bataille
            $structure = 60000;
            $bouclier  = 200;
            $attaque   = 1000;
            $vitesse   = 10000;
            $fret      = 1500;
            $conso     = 500;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'ECL'=>5, 'TRA'=>-7,'FAU'=>-7,'EDLM'=>-30);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'VC':   //Vaisseau de colonisation
            $structure = 30000;
            $bouclier  = 100;
            $attaque   = 50;
            $vitesse   = 2500;
            $fret      = 7500;
            $conso     = 1000;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5, 'EDLM'=>-250);
            $user_techno['speed'] = $techno_RI_coef;
            break;
        case 'REC':  //Recycleur
            $structure = 16000;
            $bouclier  = 10;
            $attaque   = 1;
            $vitesse   = ($user_techno['PH']) < 15 ? ( ($user_techno['RI']) < 17 ? 2000 : 4000 ) : 6000;
            $fret      = 20000;
            $conso     = ($user_techno['PH']) < 15 ? ( ($user_techno['RI']) < 17 ? 300 : 600 ) : 900;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5, 'EDLM'=>-250);
            $user_techno['speed'] = ($user_techno['PH']) < 15 ? ( ($user_techno['RI']) < 17 ? $techno_RC_coef : $techno_RI_coef ) : $techno_PH_coef;
            break;
        case 'SE':   //Sonde d'espionnage
            $structure = 1000;
            $vitesse   = 100000000;
            $fret      = 0; //Quid des unis à fret sonde ?
            $conso     = 1;
            $rapidfire = array('CLE'=>-5,'CLO'=>-5,'CR'=>-5,'VB'=>-5,'TRA'=>-5,'BMD'=>-5,'DST'=>-5,'EDLM'=>-1250,'FAU'=>-5,'ECL'=>-5,'PT'=>-5,'GT'=>-5,'VC'=>-5,'REC'=>-5);
            $user_techno['speed'] = $techno_RC_coef;
            break;
        case 'BMD':  //Bombardier
            $structure = 75000;
            $bouclier  = 500;
            $attaque   = 1000;
            $vitesse   = ($user_techno['PH']) < 8 ? 400 : 500;
            $fret      = 500;
            $conso     = 700;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'LM'=>20,'LLE'=>20,'LLO'=>10,'AI'=>10,'CG'=>5,'LP'=>5, 'FAU'=>-4,'EDLM'=>-25);
            $civil     = false;
            $user_techno['speed'] = ($user_techno['PH']) < 8 ? $techno_RI_coef : $techno_PH_coef;
            break;
        case 'DST':  //Destructeur
            $structure = 110000;
            $bouclier  = 500;
            $attaque   = 2000;
            $vitesse   = 5000;
            $fret      = 2000;
            $conso     = 1000;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'LLE'=>10,'TRA'=>2, 'FAU'=>-3,'EDLM'=>-5);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'TRA':  //Traqueur
            $structure = 70000;
            $bouclier  = 400;
            $attaque   = 700;
            $vitesse   = 10000;
            $fret      = 750;
            $conso     = 250;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'CLO'=>4,'CR'=>4,'VB'=>7,'PT'=>3,'GT'=>3, 'DST'=>-2,'EDLM'=>-15);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'EDLM': //Étoile de la mort
            $structure = 9000000;
            $bouclier  = 50000;
            $attaque   = 200000;
            $vitesse   = 100;
            $fret      = 1000000;
            $conso     = 1;
            $rapidfire = array('SE'=>1250,'SAT'=>1250,'CLE'=>200,'CLO'=>100,'CR'=>33,'VB'=>30,'BMD'=>25,'DST'=>5,'PT'=>250,'GT'=>250,'VC'=>250,'REC'=>250,'LM'=>200,'LLE'=>200,'LLO'=>100,'AI'=>100,'CG'=>50,'TRA'=>15,'ECL'=>30,'FAU'=>10,'FOR'=>1250);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'FOR':  //Foreuse
            $structure = 4000;
            $bouclier  = 1;
            $attaque   = 1;
            $rapidfire = array('CLE'=>-5,'CLO'=>-5,'CR'=>-5,'VB'=>-5,'TRA'=>-5,'BMD'=>-5,'DST'=>-5,'EDLM'=>-1250,'FAU'=>-5,'ECL'=>-5,'PT'=>-5,'GT'=>-5,'VC'=>-5,'REC'=>-5);
            break;
        case 'ECL':  //Éclaireur
            $structure = 23000;
            $bouclier  = 100;
            $attaque   = 200;
            $vitesse   = 12000;
            $fret      = 10000;
            $conso     = 300;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'CR'=>3,'CLE'=>3,'CLO'=>2, 'VB'=>-5,'EDLM'=>-30);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'FAU':  //Faucheur
            $structure = 140000;
            $bouclier  = 700;
            $attaque   = 2800;
            $vitesse   = 7000;
            $fret      = 10000;
            $conso     = 1100;
            $rapidfire = array('SE'=>5,'SAT'=>5,'FOR'=>5,'VB'=>7,'BMD'=>4,'DST'=>3, 'AI'=>-2,'EDLM'=>-10);
            $civil     = false;
            $user_techno['speed'] = $techno_PH_coef;
            break;
        case 'SAT':  //Satellite solaire
        case 'Sat':
            $structure = 2000;
            $bouclier  = 1;
            $attaque   = 1;
            $rapidfire = array('CLE'=>-5,'CLO'=>-5,'CR'=>-5,'VB'=>-5,'TRA'=>-5,'BMD'=>-5,'DST'=>-5,'EDLM'=>-1250,'FAU'=>-5,'ECL'=>-5,'PT'=>-5,'GT'=>-5,'VC'=>-5,'REC'=>-5);
            break;
// Défenses :
        case 'LM':  //Lanceur de missiles
            $structure = 2000;
            $bouclier  = 20;
            $attaque   = 80;
            $rapidfire = array('CR'=>-10,'BMD'=>-20,'EDLM'=>-200);
            break;
        case 'LLE': //Artillerie laser légère
            $structure = 2000;
            $bouclier  = 25;
            $attaque   = 100;
            $rapidfire = array('BMD'=>-20,'DST'=>-20,'EDLM'=>-200);
            break;
        case 'LLO': //Artillerie laser lourde
            $structure = 8000;
            $bouclier  = 100;
            $attaque   = 250;
            $rapidfire = array('BMD'=>-10,'EDLM'=>-100);
            break;
        case 'CG':  //Canon de Gauss
            $structure = 35000;
            $bouclier  = 200;
            $attaque   = 1100;
            $rapidfire = array('BMD'=>-5,'EDLM'=>-50);
            break;
        case 'AI':  //Artillerie à ions
            $structure = 8000;
            $bouclier  = 500;
            $attaque   = 150;
            $rapidfire = array('FAU'=>2, 'BMD'=>-10,'EDLM'=>-100);
            break;
        case 'LP':  //Lanceur de plasma
            $structure = 100000;
            $bouclier  = 300;
            $attaque   = 3000;
            $rapidfire = array('BMD'=>-5);
            break;
        case 'PB':  //Petit bouclier
            $structure = 20000;
            $bouclier  = 2000;
            $attaque   = 1;
            $rapidfire = array('BMD'=>-20,'DST'=>-10,'EDLM'=>-200);
            break;
        case 'GB':  //Grand bouclier
            $structure = 100000;
            $bouclier  = 10000;
            $attaque   = 1;
            break;
        case 'MIC': //Missile d'interception
            $structure = 8000;
            $bouclier  = 1;
            $attaque   = 1;
            break;
        case 'MIP': //Missile interplanétaire
            $structure = 15000;
            $bouclier  = 1;
            $attaque   = 12000;
            break;
        default:
            break;
    }
    //fill rapidfire with other fleet/defence
    foreach (array_merge($names['VSO'], $names['DEF']) as $fleet) {
        if (!isset($rapidfire[$fleet])) {
            $rapidfire[$fleet] = 0;
        }
    }
    
    /*
    COL : +100% vitesse transporteur ; +25% fret transporteur
    GEN : +100% vitesse vso combat/REC or EDLM ; -25% conso ; +20% fret REC/ECL ; +2 lvl techno combat
    ECL : none
    */
    $bonus_class = 0;
    $structure = round($structure + $structure * $techno_Protection_coef + $structure * $bonus_class);
    
    $bonus_class = 0;
    $bouclier  = round($bouclier  + $bouclier * $techno_Bouclier_coef    + $bouclier * $bonus_class);
    
    $bonus_class = 0;
    if ($classe === 'GEN') {
        $bonus_class = 2 * $COMBAT_COEF;    //+2 lvl
    }
    $attaque   = round($attaque   + $attaque * $techno_Armes_coef        + $attaque * $bonus_class);
    
    $bonus_class = 0;
    if ($classe === 'COL') {
        if ($nom === 'PT' || $nom === 'GT') {
            $bonus_class = 1; //+100%
        }
    } elseif ($classe === 'GEN') {
        if (!$civil && $nom !== 'EDLM' || $nom === 'REC') {
            $bonus_class = 1; //+100%
        }
    }
    $vitesse   = round($vitesse   + $vitesse * $user_techno['speed']     + $vitesse * $bonus_class);
    
    $bonus_class = 0;
    if ($classe === 'COL') {
        if ($nom === 'PT' || $nom === 'GT') {
            $bonus_class = 0.25; //+25%
        }
    } elseif ($classe === 'GEN') {
        if ($nom === 'REC' || $nom === 'ECL') {
            $bonus_class = 0.2; //+20%
        }
    }
    $fret      = round($fret      + $fret * $techno_Hyp_coef             + $fret * $bonus_class);
    
    $bonus_class = 0;
    if ($classe === 'GEN') {
        $bonus_class = -0.25;    //-25%
    }
    $conso     = round($conso     + $conso * $bonus_class);
    
    return array('nom'=>$nom, 'structure'=>$structure, 'bouclier'=>$bouclier, 'attaque'=>$attaque, 
                 'vitesse'=>$vitesse, 'fret'=>$fret, 'conso'=>$conso, 'rapidfire'=>$rapidfire,
                 'civil'=>$civil, 'cout'=>$cout);
}

/**
 * Calcule la distance entre a et b, a - b ; en tenant en compte des univers arrondis.
 * type = Représente le type de distance à calculer
 *      0 : Galaxie
 *      1 : Système
 *      2 : Planète
 * typeArrondi = true pour un univers arrondi selon le type donnée
 * @param $a
 * @param $b
 * @param $type
 * @param bool $typeArrondi
 * @return number
 */
function calc_distance($a, $b, $type, $typeArrondi = true)
{//a-b
    global $server_config;

    $max_type = 0;

    switch ($type) {
        case 0: //Galaxy
            $max_type = $server_config['num_of_galaxies']; //9
            break;
        case 1: //System
            $max_type = $server_config['num_of_systems']; //499
            break;
    }
    if ($typeArrondi) {
        if (abs($a - $b) < $max_type / 2) {
            return abs($a - $b); //|a-b|
        } else {
            return abs(abs($a - $b) - $max_type); //||a-b| - base|
        }
    } else {
        return abs($a - $b); //|a-b|
    }
}

