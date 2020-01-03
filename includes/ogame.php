<?php
/**
 * OGame Games Formulas and Data
 * @package OGSpy
 * @subpackage Ogame Data
 * @author Kyser
 * @copyright Copyright &copy; 2012, http://ogsteam.fr/
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
 * @param int $speed_uni The univers economy speed
 * @return int The result of the production on the specified building.
 */
function production($building, $level, $officier = 0, $temperature_max = 0, $NRJ = 0, $Plasma = 0, $classe = 0, $speed_uni = 1)
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
    //Valeur de la classe en valeur ajoutée.
    if ($classe == 1) {
        $bonus_class_mine = 0.25; //+25%
        $bonus_class_energie = 0.10; //+10%
        $bonus_foreuse = $bonus_foreuse * 1.5; //+50%
    } else {
        $bonus_class_mine = 0;
        $bonus_class_energie = 0;
    }
    // print_r("officier=$officier, geo=$geo, ing=$ing, C_m=$bonus_class_mine, C_E=$bonus_class_energie, C_f=$bonus_foreuse, speed=$speed_uni\n");
    switch ($building) {
        case "M":
            $prod_base = 30 * $speed_uni;
            $result = 30 * $level * pow(1.1, $level); // formule de base
            $result = $result * $speed_uni; // vitesse uni
            // $result_foreuse = $result * $bonus_foreuse; //foreuse sur produc de base des mines
            $result = $result * (1 + $geo + 0.01 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "C":
            $prod_base = 15 * $speed_uni;
            $result = 20 * $level * pow(1.1, $level); // formule de base
            $result = $result * $speed_uni; // vitesse uni
            // $result_foreuse = $result * $bonus_foreuse; //foreuse sur produc de base des mines
            $result = $result * (1 + $geo + 0.0066 * $Plasma + $bonus_class_mine);
            $result = floor($result); // troncature
            $result = $result + $prod_base; // prod de base
            break;

        case "D":
            // $result = 10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $temperature_max); //<Ogame V7
            $result = 10 * $level * pow(1.1, $level) * floor((1.44 - 0.004 * $temperature_max)*10)/10;  //troncature à la décimale
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
 * @param int $classe Classe option chosen (1=Collectionneur)[0=aucune, 2=général, 3=explorateur]
 * @param int $speed_uni The univers economy speed
 * @return array('M', 'C', 'D') The result foreuse production of metal 'M', cristal 'C' and deuterium 'D'
 */
function production_foreuse($nb_foreuse, $level_M, $level_C, $level_D, $temperature_max, $classe = 0, $speed_uni = 1)
{
    $bonus_foreuse = 0.0002; //0.02% / foreuse
    //Valeur de la classe en valeur ajoutée.
    if ($classe == 1) {
        $bonus_foreuse = $bonus_foreuse * 1.5; //+50%
    }
    $nb_max = ($level_M + $level_C + $level_D) * 8;
    if($nb_foreuse > $nb_max) {
        $nb_foreuse = $nb_max;
    }
    $result_M = $bonus_foreuse * (production('M', $level_M, 0, $temperature_max, 0, 0, 0, $speed_uni) - 30 * $speed_uni);
    $result_C = $bonus_foreuse * (production('C', $level_C, 0, $temperature_max, 0, 0, 0, $speed_uni) - 15 * $speed_uni);
    $result_D = $bonus_foreuse * (production('D', $level_D, 0, $temperature_max, 0, 0, 0, $speed_uni));
    
    $result_M = round($result_M * $nb_foreuse); //arrondi
    $result_C = round($result_C * $nb_foreuse); //arrondi
    $result_D = round($result_D * $nb_foreuse); //arrondi
    
    return array('M' => $result_M, 'C' => $result_C, 'D' => $result_D);
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
 * @return array("ratio", "conso_E", "prod_E", "prod_CES", "prod_CEF", "prod_SAT", "conso_M", "conso_C", "conso_D", "conso_FOR")
 */
function ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
               $per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $FOR = 0, $per_FOR = 0, $classe = 0)
{
    $consommation_E = 0; // la consommation
    $conso_M   = consumption("M", $M) * $per_M;
    $conso_C   = consumption("C", $C) * $per_C;
    $conso_D   = consumption("D", $D) * $per_D;
    $conso_FOR = consumption("FOR", $FOR) * $per_FOR;
    $consommation_E += $conso_M + $conso_C + $conso_D + $conso_FOR;

    $production_E = 0; // la production
    $prod_CES = production("CES", $CES, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CES;
    $prod_CEF = production("CEF", $CEF, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_CEF;
    $prod_SAT = production("SAT", $SAT, $off_ing, $temperature_max, $NRJ, 0, $classe) * $per_SAT;
    $production_E += $prod_CES + $prod_CEF + $prod_SAT;

    $ratio = 1; // indique le pourcentage à appliquer sur la prod
    $ratio_temp = 1;
    $ratio_temp = ($consommation_E == 0) ? 0 : ($production_E * 100 / $consommation_E) / 100; // fix division par 0
    $ratio = ($ratio_temp >= 1) ? 1 : $ratio_temp;

    $consommation_E = round($consommation_E);
    $production_E = round($production_E);

    return array("ratio" => $ratio, "conso_E" => $consommation_E, "prod_E" => $production_E,
        "prod_CES" => $prod_CES, "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT,
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
$per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $booster = NULL,  $FOR = 0, $per_FOR = 0, $classe = 0, $speed_uni = 1)
{

    if ($off_full == 1) {
        $off_ing = $off_geo = 2;
    }
    $tmp = ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
            $per_M, $per_C, $per_D, $per_CES, $per_CEF, $per_SAT, $FOR, $per_FOR, $classe);
    $ratio = $tmp["ratio"];
    $consommation_E = $tmp["conso_E"];
    $production_E = $tmp["prod_E"];
    $prod_CES = $tmp["prod_CES"];
    $prod_CEF = $tmp["prod_CEF"];
    $prod_SAT = $tmp["prod_SAT"];
    $conso_M = $tmp["conso_M"];
    $conso_C = $tmp["conso_C"];
    $conso_D = $tmp["conso_D"];
    $conso_FOR = $tmp["conso_FOR"];

    if ($ratio > 0) {
        //production de metal avec ratio
        $prod_M = production("M", $M, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $speed_uni) * $per_M;
        $prod_M *= $ratio;
        $prod_M = round($prod_M);

        //production de cristal avec ratio
        $prod_C = production("C", $C, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $speed_uni) * $per_C;
        $prod_C *= $ratio;
        $prod_C = round($prod_C);

        //production de deut avec ratio
        $prod_D = production("D", $D, $off_geo, $temperature_max, $NRJ, $Plasma, $classe, $speed_uni) * $per_D;
        $prod_D *= $ratio;
        $prod_D -= consumption("CEF", $CEF, $speed_uni) * $per_CEF; //on soustrait la conso de deut de la cef
        $prod_D = round($prod_D);
        
        //production des foreuses (métal, cristal et deut)
        $prod_FOR = production_foreuse($FOR, $M, $C, $D, $temperature_max, $classe, $speed_uni);
        $prod_FOR['M'] = round($prod_FOR['M'] * $ratio);
        $prod_FOR['C'] = round($prod_FOR['C'] * $ratio);
        $prod_FOR['D'] = round($prod_FOR['D'] * $ratio);
    } else {
        $prod_M = production("M", 0, 0, 0, 0, 0, 0, $speed_uni); //production de base
        $prod_C = production("C", 0, 0, 0, 0, 0, 0, $speed_uni); //production de base
        $prod_D = production("D", 0, 0, 0, 0, 0, 0, $speed_uni); //production de base
        $prod_FOR = production_foreuse(0, 0, 0, 0, 0, 0, $speed_uni);
    }

    if ($booster != NULL) { // si booster
        $boost_M = ($booster['booster_m_val'] / 100) * (production('M', $M, 0, $temperature_max, 0, 0, 0, $speed_uni) - 30 * $speed_uni);
        $boost_C = ($booster['booster_c_val'] / 100) * (production('C', $C, 0, $temperature_max, 0, 0, 0, $speed_uni) - 15 * $speed_uni);
        $boost_D = ($booster['booster_d_val'] / 100) * (production('D', $D, 0, $temperature_max, 0, 0, 0, $speed_uni));
        
        $prod_M += round($boost_M);
        $prod_C += round($boost_C);
        $prod_D += round($boost_D);
    }
	 
    return array("M" => $prod_M, "C" => $prod_C, "D" => $prod_D, "FOR" => $prod_FOR, "ratio" => $ratio,
            "conso_E" => $consommation_E, "prod_E" => $production_E, "prod_CES" => $prod_CES,
            "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "conso_M" => $conso_M,
            "conso_C" => $conso_C, "conso_D" => $conso_D, "conso_FOR" => $conso_FOR);
}



/**
 * Calculates the Planet storage capacity (Taille Hangar)
 * @param int $level Storage building Level
 * @return double capacity
 */
function depot_capacity($level)
{
    // capacité par défaut
    $capacity = 10000;

    if ($level > 0) {
        $capacity = 5000 * floor((2.5 * exp(20 * $level / 33)));
    }
    $result = round($capacity);

    return $result;
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
 * Calculates the price to upgrade a building to a defined level
 * @param int $level The wanted Level
 * @param string $building The building type
 * @return ressources required to upgrade the building
 */
function building_upgrade($building, $level)
{
    switch ($building) {
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
            $M = 200 * pow(2, ($level - 1));
            $C = 0;
            $D = 50 * pow(2, ($level - 1));
            $NRJ = 50 * pow(2, ($level - 1));
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
            $M = 20000 * pow(2, ($level - 1));
            $C = 20000 * pow(2, ($level - 1));
            $D = 1000 * pow(2, ($level - 1));
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

    return array("M" => $M, "C" => $C, "D" => $D, "NRJ" => $NRJ);
}

/**
 * Calculates the price of the a building corresponding to it current level
 * @param int $level The current Level
 * @param string $building The building type
 * @return ressources used to reach this level
 */
function building_cumulate($building, $level)
{
    switch ($building) {
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

        case "Sat":
            $M = 0;
            $C = 2000 * $level;
            $D = 500 * $level;
            break;

        default:
            list($M, $C, $D) = array_values(building_upgrade($building, 1));
            $M = $M * -(1 - pow(2, $level));
            $C = $C * -(1 - pow(2, $level));
            $D = $D * -(1 - pow(2, $level));
            break;
    }

    return array("M" => $M, "C" => $C, "D" => $D);
}

/**
 * Calculates the price of all buildings
 * @param string $user_building The list of buildings with corresponding levels
 * @return integer bild :-)
 */
function all_building_cumulate($user_building)
{

    $total = 0;

    while ($data = current($user_building)) {

        $bats = array_keys($data);

        foreach ($bats as $key) {

            $level = $data[$key];
            if ($level == "") {
                            $level = 0;
            }

            if ($key == "M" || $key == "C" || $key == "D" || $key == "CES" || $key == "CEF" ||
                $key == "UdR" || $key == "UdN" || $key == "CSp" || $key == "HM" || $key == "HC" ||
                $key == "HD" || $key == "Lab" ||
                $key == "Ter" || $key == "DdR" || $key == "Silo" || $key == "Dock" || $key == "BaLu" || $key == "Pha" ||
                $key == "PoSa"
            ) {
                list($M, $C, $D) = array_values(building_cumulate($key, $level));
                $total += $M + $C + $D;
            }
        }

        next($user_building);
    }

    return $total;
}

/**
 * Calculates the price of all buildings
 * @param string $user_defence The list of defenses with the number of each building
 * @return the bild :-)
 */
function all_defence_cumulate($user_defence)
{

    $total = 0;
    $init_d_prix = array("LM" => 2000, "LLE" => 2000, "LLO" => 8000, "CG" => 37000,
        "AI" => 8000, "LP" => 130000, "PB" => 20000, "GB" => 100000, "MIC" => 10000,
        "MIP" => 25000);
    $keys = array_keys($init_d_prix);

    while ($data = current($user_defence)) {
        if (sizeof($init_d_prix) != sizeof($keys)) {
                    continue;
        }

        for ($i = 0; $i < sizeof($init_d_prix); $i++) {
            $total += $init_d_prix[$keys[$i]] * ($data[$keys[$i]] != "" ? $data[$keys[$i]] : 0);
        }

        next($user_defence);
    }

    return $total;
}

/**
 * Calculates the price of all lunas
 * @param $user_building
 * @param string $user_defence The list of buildings with corresponding levels on the luna
 * @return double bild :-)
 */
function all_lune_cumulate($user_building, $user_defence)
{

    $total = all_defence_cumulate($user_defence) + all_building_cumulate($user_building);

    return $total;
}

/**
 * Calculates the price of all researches
 * @param $user_technology
 * @return the bild for all technologies :-)
 */
function all_technology_cumulate($user_technology)
{

    $total = 0;
    $init_t_prix = array("Esp" => 1400, "Ordi" => 1000, "Armes" => 1000, "Bouclier" =>
        800, "Protection" => 1000, "NRJ" => 1200, "Hyp" => 6000, "RC" => 1000, "RI" =>
        6600, "PH" => 36000, "Laser" => 300, "Ions" => 1400, "Plasma" => 7000, "RRI" =>
        800000, "Graviton" => 0, "Astrophysique" => 16000);
    $keys = array_keys($init_t_prix);

    if (sizeof($init_t_prix) != sizeof($user_technology)) {
            return 0;
    }

    for ($i = 0; $i < sizeof($init_t_prix); $i++) {
        $pow = ($keys[$i] != "Astrophysique") ? 2 : 1.75; // puissance change a cause de l astro ...

        if ($keys[$i] != "Astrophysique") {
            $total += $init_t_prix[$keys[$i]] * (pow($pow, ($user_technology[$keys[$i]] !=
                        "") ? $user_technology[$keys[$i]] : 0) - 1);
        } else {
            $j = 0;
            $user_technology[$keys[$i]] = ($user_technology[$keys[$i]] != "") ? $user_technology[$keys[$i]] : 0;
            while ($j <= $user_technology[$keys[$i]]) {
                $total += $init_t_prix[$keys[$i]] * (pow($pow, ($j - 1)));
                $j++;
            }

        }


    }

    return $total;
}

