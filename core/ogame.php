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

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\Model\Combat_Report_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
if (!isset($server_config['speed_uni'])) {
    $server_config['speed_uni'] = 1;
}

/**
 * Gets the hourly production of a Mine or a solar plant.
 * @param string $building The building type
 * @param int $level The building level
 * @param int $officier Officer option enabled (=1) or not(=0) or full Officer(=2)
 * @param int $temperature_max Max temprature of the current planet
 * @param int $NRJ Current value of the user Energy Technology
 * @param int $Plasma Current value of the user Plasma Technology
 * @return int the result of the production on the specified building.
 */
function production($building, $level, $officier = 0, $temperature_max = 0, $NRJ = 0, $Plasma = 0)
{
    // attention officier
    // pour m / c / d => geologue
    // pour ces cef => ingenieur
    global $server_config;

    //Valeur de l'officier en valeur ajouté.
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
    switch ($building) {
        case "M":
            $prod_base = 30;
            $result = 30 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $geo + 0.01 * $Plasma);
            $result = round($result); // arrondi 
            $result = $result + $prod_base; // prod de base
            $result = $server_config['speed_uni'] * $result; // vitesse uni
            break;

        case "C":
            $prod_base = 15;
            $result = 20 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $geo + 0.0066 * $Plasma);
            $result = round($result); // arrondi
            $result = $result + $prod_base; // prod de base
            $result = $server_config['speed_uni'] * $result; // vitesse uni
            break;

        case "D":
            $result = (10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $temperature_max));
            $result = $result * (1 + $geo); // geologue
            $result = round($result); // arrondi
            $result = $server_config['speed_uni'] * $result; // vitesse uni
            break;

        case "CES":
            $result = 20 * $level * pow(1.1, $level);
            $result = $result * (1 + $ing); // ingenieur
            $result = floor($result); // troncature inférieure
            break;

        case "CEF":
            $result = 30 * $level * pow((1.05 + $NRJ * 0.01), $level);
            $result = $result * (1 + $ing); // ingenieur
            $result = floor($result); // troncature inférieure
            break;

        default:
            $result = 0;
            break;
    }

    return ($result);
}

/**
 * Gets the energy production of satellites.
 * @param int $temperature_max Max temprature of the current planet
 * @param int $off_ing Officer ingenieur option enabled (=1) or not(=0) or full Officer(=2)
 * @return float the result of the power production by sattelites.
 */
function production_sat($temperature_max, $off_ing = 0)
{
    if ($off_ing == 0) {
        $ing = 1;
    } elseif ($off_ing == 1) {
        $ing = 1.10; //110%
    } elseif ($off_ing == 2) {
        $ing = 1.12; //112%
    } else {
        $ing = 1;
    }
    return floor($ing * (($temperature_max + 140) / 6));
}

/**
 * Gets the power consumption of the current building
 * @param string $building The building type
 * @param int $level Min The building Level
 * @return int the building consumption
 */
function consumption($building, $level)
{
    global $server_config;
    switch ($building) {
        case "M":   //no break
        case "C":
            $result = 10 * $level * pow(1.1, $level);
            $result = ceil($result); //troncature supérieure
            break;

        case "D":
            $result = 20 * $level * pow(1.1, $level);
            $result = ceil($result); //troncature supérieure
            break;

        case "CEF":
            $result = $server_config['speed_uni'] * (10 * $level * pow(1.1, $level));
            $result = round($result); // arrondi
            break;

        default:
            $result = 0;
            break;
    }

    return ($result);
}

/**
 * Gets the production usage of the current planet
 * @param int $M Metal Mine Level
 * @param int $C Cristal Mine Level
 * @param int $D Deuterium Mine Level
 * @param int $CES Solar Plant Level
 * @param int $CEF Fusion Plant Level
 * @param int $SAT Number of sattelites
 * @param int $temperature_max Max temprature of the current planet
 * @param int $off_ing Officer ingenieur option enabled (=1) or not(=0) or full Officer(=2)
 * @param int $NRJ Current value of the user Energy Technology
 * @param int $per_M Metal Mine production percent (0=0%, 1=100%)
 * @param int $per_C Cristal Mine production percent (0=0%, 1=100%)
 * @param int $per_D Deuterieum Mine production percent (0=0%, 1=100%)
 * @param int $per_CES Solar Plant production percent (0=0%, 1=100%)
 * @param int $per_CEF Fusion Plant production percent (0=0%, 1=100%)
 * @param int $per_SAT sattelites production percent (0=0%, 1=100%)
 * @return array("ratio", "conso_E", "prod_E", "prod_CES", "prod_CEF", "prod_SAT", "conso_M", "conso_C", "conso_D")
 */
function ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
                $per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1)
{
    $consommation_E = 0; // la consommation
    $conso_M = consumption("M", $M) * $per_M;
    $conso_C = consumption("C", $C) * $per_C;
    $conso_D = consumption("D", $D) * $per_D;
    $consommation_E += $conso_M + $conso_C + $conso_D;

    $production_E = 0; // la production
    $prod_CES = production("CES", $CES, $off_ing) * $per_CES;
    $prod_CEF = production("CEF", $CEF, $off_ing, $temperature_max, $NRJ) * $per_CEF;
    $prod_SAT = $SAT * production_sat($temperature_max, $off_ing) * $per_SAT;
    $production_E += $prod_CES + $prod_CEF + $prod_SAT;

    /*$ratio = 1; // indique le pourcentage a appliquer sur la prod
    $ratio_temp = 1;*/
    $ratio_temp = ($consommation_E == 0) ? 0 : ($production_E * 100 / $consommation_E) / 100; // fix division par 0
    $ratio = ($ratio_temp >= 1) ? 1 : $ratio_temp;

    $consommation_E = round($consommation_E);
    $production_E = round($production_E);

    return array("ratio" => $ratio, "conso_E" => $consommation_E, "prod_E" => $production_E,
        "prod_CES" => $prod_CES, "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT,
        "conso_M" => $conso_M, "conso_C" => $conso_C, "conso_D" => $conso_D);
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
$per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $booster = NULL)
{

    if ($off_full == 1) {
        $off_ing = $off_geo = 2;
    }
    $tmp = ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_max, $off_ing, $NRJ,
            $per_M, $per_C, $per_D, $per_CES, $per_CEF, $per_SAT);
    $ratio = $tmp["ratio"];
    $consommation_E = $tmp["conso_E"];
    $production_E = $tmp["prod_E"];
    $prod_CES = $tmp["prod_CES"];
    $prod_CEF = $tmp["prod_CEF"];
    $prod_SAT = $tmp["prod_SAT"];
    $conso_M = $tmp["conso_M"];
    $conso_C = $tmp["conso_C"];
    $conso_D = $tmp["conso_D"];

    if ($ratio > 0) {
        //production de metal avec ratio
        $prod_M = production("M", $M, $off_geo, $temperature_max, $NRJ, $Plasma) * $per_M;
        $prod_M *= $ratio;
        $prod_M = round($prod_M);

        //production de cristal avec ratio
        $prod_C = production("C", $C, $off_geo, $temperature_max, $NRJ, $Plasma) * $per_C;
        $prod_C *= $ratio;
        $prod_C = round($prod_C);

        //production de deut avec ratio
        $prod_D = production("D", $D, $off_geo, $temperature_max) * $per_D;
        $prod_D *= $ratio;
        $prod_D -= consumption("CEF", $CEF) * $per_CEF; //on soustrait la conso de deut de la cef
        $prod_D = round($prod_D);
    } else {
        $prod_M = production("M", 0); //production de base
        $prod_C = production("C", 0); //production de base
        $prod_D = production("D", 0); //production de base
    }

    if ($booster != NULL)
    {
        // si booster
        $prod_M = $prod_M * (1 + $booster['booster_m_val'] / 100);
        $prod_C = $prod_C * (1 + $booster['booster_c_val'] / 100);
        $prod_D = $prod_D * (1 + $booster['booster_d_val'] / 100);
    }
	 
    return array("M" => $prod_M, "C" => $prod_C, "D" => $prod_D, "ratio" => $ratio,
            "conso_E" => $consommation_E, "prod_E" => $production_E, "prod_CES" => $prod_CES,
            "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "conso_M" => $conso_M,
            "conso_C" => $conso_C, "conso_D" => $conso_D);
}



/**
 * Calculates the Planet storage capacity (Taille Hangar)
 * @param int $level Storage building Level
 * @return int the capacity
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
 * @return int the maximum number of planets
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
 * @return array ressources required to upgrade the building
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
 * @return array ressources used to reach this level
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
 * @return int the bild :-)
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
                $key == "Ter" || $key == "DdR" || $key == "Silo" || $key == "BaLu" || $key == "Pha" ||
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
 * @return int the bild :-)
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
 * @return int $total the bild :-)
 */
function all_lune_cumulate($user_building, $user_defence)
{

    $total = all_defence_cumulate($user_defence) + all_building_cumulate($user_building);

    return $total;
}

/**
 * Calculates the price of all researches
 * @param $user_technology
 * @return int $total the bild for all technologies :-)
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

/**
 * Reconstruction des RC
 * @global $db
 * @param int $id_RC RC à reconstituer
 * @return string $template_RC reconstitué
 */
function UNparseRC($id_RC)
{
    global $db, $lang;

    $key_ships = array('PT' => $lang['GAME_FLEET_PT_S'], 'GT' => $lang['GAME_FLEET_GT_S'], 'CLE' => $lang['GAME_FLEET_CLE_S'],
        'CLO' => $lang['GAME_FLEET_CLO_S'], 'CR' => $lang['GAME_FLEET_CR_S'], 'VB' => $lang['GAME_FLEET_VB_S'], 'VC' =>
            $lang['GAME_FLEET_VC_S'], 'REC' => $lang['GAME_FLEET_REC_S'], 'SE' => $lang['GAME_FLEET_SE_S'], 'BMD' => $lang['GAME_FLEET_BMD_S'],
        'DST' => $lang['GAME_FLEET_DST_S'], 'EDLM' => $lang['GAME_FLEET_EDLM_S'], 'SAT' => $lang['GAME_FLEET_SAT_S'], 'TRA' => $lang['GAME_FLEET_TRA_S']);
    $key_defs = array('LM' => $lang['GAME_DEF_LM_S'], 'LLE' => $lang['GAME_DEF_LLE_S'], 'LLO' => $lang['GAME_DEF_LLO_S'],
        'CG' => $lang['GAME_DEF_CG_S'], 'AI' => $lang['GAME_DEF_AI_S'], 'LP' => $lang['GAME_DEF_LP_S'], 'PB' =>
            $lang['GAME_DEF_PB_S'], 'GB' => $lang['GAME_DEF_GB_S']);
    $base_ships = array('PT' => array(4000, 10, 5), 'GT' => array(12000, 25, 5),
        'CLE' => array(4000, 10, 50), 'CLO' => array(10000, 25, 150), 'CR' => array(27000,
            50, 400), 'VB' => array(60000, 200, 1000), 'VC' => array(30000, 100, 50), 'REC' =>
            array(16000, 10, 1), 'SE' => array(1000, 0, 0), 'BMD' => array(75000, 500, 1000),
        'DST' => array(110000, 500, 2000), 'EDLM' => array(9000000, 50000, 200000),
        'SAT' => array(2000, 1, 1), 'TRA' => array(70000, 400, 700));
    $base_defs = array('LM' => array(2000, 20, 80), 'LLE' => array(2000, 25, 100),
        'LLO' => array(8000, 100, 250), 'CG' => array(35000, 200, 1100), 'AI' => array(8000,
            500, 150), 'LP' => array(100000, 300, 3000), 'PB' => array(20000, 2000, 1), 'GB' =>
            array(100000, 10000, 1));

    // Récupération des constantes du RC
    $repository = new Combat_Report_Model();
    $report = $repository->get_combat_report($id_RC);

    $dateRC = date($lang['GAME_CREPORT_DATE'], $report['dateRC']);
    $template = $lang['GAME_CREPORT_FIGHT'] . ' (' . $dateRC . "):\n\n";

    // On formate les résultats
    $nf_gain_M = number_format($report['gain_M'], 0, ',', '.');
    $nf_gain_C = number_format($report['gain_C'], 0, ',', '.');
    $nf_gain_D = number_format($report['gain_D'], 0, ',', '.');
    $nf_pertes_A = number_format($report['pertes_A'], 0, ',', '.');
    $nf_pertes_D = number_format($report['pertes_D'], 0, ',', '.');
    $nf_debris_M = number_format($report['debris_M'], 0, ',', '.');
    $nf_debris_C = number_format($report['debris_C'], 0, ',', '.');


    // Récupération de chaque round du RC
    foreach ($report['rounds'] as $round) {

        $nf_attaque_tir = number_format($round['attaque_tir'], 0, ',', '.');
        $nf_attaque_puissance = number_format($round['attaque_puissance'], 0, ',', '.');
        $nf_attaque_bouclier = number_format($round['attaque_bouclier'], 0, ',', '.');
        $nf_defense_tir = number_format($round['defense_tir'], 0, ',', '.');
        $nf_defense_puissance = number_format($round['defense_puissance'], 0, ',', '.');
        $nf_defense_bouclier = number_format($round['defense_bouclier'], 0, ',', '.');

        // Récupération de chaque attaquant du RC
        $query = 'SELECT player, coordinates, Armes, Bouclier, Protection, PT, GT, CLE, CLO, CR, VB, VC, REC, 
      SE, BMD, DST, EDLM, TRA FROM ' . TABLE_ROUND_ATTACK .
            ' WHERE id_rcround = ' . $round['id_rcround'];
        $result_attack = $db->sql_query($query);
        while (list($player, $coordinates, $Armes, $Bouclier, $Protection, $PT, $GT, $CLE,
            $CLO, $CR, $VB, $VC, $REC, $SE, $BMD, $DST, $EDLM, $TRA) = $db->sql_fetch_row($result_attack)) {
            $key = '';
            $ship = 0;
            $vivant_att = false;
            $template .= $lang['GAME_CREPORT_ATT'] . ' ' . $player;
            $ship_type = $lang['GAME_CREPORT_TYPE'];
            $ship_nombre = $lang['GAME_CREPORT_NB'];
            $ship_armes = $lang['GAME_CREPORT_WEAPONS'];
            $ship_bouclier = $lang['GAME_CREPORT_SHIELD'];
            $ship_protection = $lang['GAME_CREPORT_PROTECTION'];
            foreach ($key_ships as $key => $ship) {
                if (isset($$key) && $$key > 0) {
                    $vivant_att = true;
                    $ship_type .= "\t" . $ship;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.'); ;
                    $ship_protection .= "\t" . number_format(round(($base_ships[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_ships[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_ships[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            if ($vivant_att == true) {
                $template .= ' [' . $coordinates . ']';
                if ($round['numround'] == 1) {
                                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                }
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else {
                            $template .= ' détruit.' . "\n\n";
            }
        } // Fin récupération de chaque attaquant du RC

        // Récupération de chaque défenseur du RC
        $query = 'SELECT player, coordinates, Armes, Bouclier, Protection, PT, GT, CLE, CLO, CR, VB, VC, REC, 
      SE, BMD, SAT, DST, EDLM, TRA, LM, LLE, LLO, CG, AI, LP, PB, GB FROM ' .
            TABLE_ROUND_DEFENSE . ' WHERE 
      id_rcround = ' . $round['id_rcround'];
        $result_defense = $db->sql_query($query);
        while (list($player, $coordinates, $Armes, $Bouclier, $Protection, $PT, $GT, $CLE,
            $CLO, $CR, $VB, $VC, $REC, $SE, $BMD, $SAT, $DST, $EDLM, $TRA, $LM, $LLE, $LLO, $CG, $AI,
            $LP, $PB, $GB) = $db->sql_fetch_row($result_defense)) {
            $key = '';
            $ship = 0;
            $vivant_def = false;
            $template .= $lang['GAME_CREPORT_DEF'] . ' ' . $player;
            $ship_type = $lang['GAME_CREPORT_TYPE'];
            $ship_nombre = $lang['GAME_CREPORT_NB'];
            $ship_armes = $lang['GAME_CREPORT_WEAPONS'];
            $ship_bouclier = $lang['GAME_CREPORT_SHIELD'];
            $ship_protection = $lang['GAME_CREPORT_PROTECTION'];
            foreach ($key_ships as $key => $ship) {
                if (isset($$key) && $$key > 0) {
                    $vivant_def = true;
                    $ship_type .= "\t" . $ship;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.');
                    $ship_protection .= "\t" . number_format(round(($base_ships[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_ships[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_ships[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            foreach ($key_defs as $key => $def) {
                if (isset($$key) && $$key > 0) {
                    $vivant_def = true;
                    $ship_type .= "\t" . $def;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.');
                    $ship_protection .= "\t" . number_format(round(($base_defs[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_defs[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_defs[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            if ($vivant_def == true) {
                $template .= ' [' . $coordinates . ']';
                if ($round['numround'] == 1) {
                                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                }
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else {
                            $template .= ' ' . $lang['GAME_CREPORT_DESTROYED'] . ' ' . "\n\n";
            }
        } // Fin récupération de chaque défenseur du RC

        // Résultat du round
        if ($round['attaque_tir'] != 0 || $round['defense_tir'] != 0) {
            $template .= $lang['GAME_CREPORT_RESULT_FLEET'] . ' ' . $nf_attaque_tir .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_1'] . ' ' . $nf_attaque_puissance .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_2'] . ' ' . $nf_defense_bouclier .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_3'] . ' ' . "\n\n";
            $template .= $lang['GAME_CREPORT_RESULT_DEF'] . ' ' . $nf_defense_tir .
                ' ' . $lang['GAME_CREPORT_RESULT_DEF_1'] . ' ' . $nf_defense_puissance . '. ' . $lang['GAME_CREPORT_RESULT_DEF_2'] . ' ' .
                $nf_attaque_bouclier . ' ' . $lang['GAME_CREPORT_RESULT_DEF_3'] . '.' . "\n\n";
        }
    } // Fin récupération de chaque round du RC

    // Qui a remporté le combat ?
    switch ($report['victoire']) {
        case 'N':
            $template .= $lang['GAME_CREPORT_RESULT_EVEN'] . '.' .
                "\n\n";
            break;
        case 'A':
            $template .= $lang['GAME_CREPORT_RESULT_WIN'] . ' ' .
                $nf_gain_M . ' ' . $lang['GAME_CREPORT_RESULT_WIN_1'] . ', ' . $nf_gain_C . ' ' . $lang['GAME_CREPORT_RESULT_WIN_2'] . ' ' . $nf_gain_D .
                ' ' . $lang['GAME_CREPORT_RESULT_WIN_3'] . '.' . "\n\n";
            break;
        case 'D':
            $template .= $lang['GAME_CREPORT_RESULT_LOST'] . "\n\n";
            break;
    }

    // Pertes et champs de débris
    $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_A'] . ' ' . $nf_pertes_A . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
    $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_D'] . ' ' . $nf_pertes_D . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
    $template .= $lang['GAME_CREPORT_RESULT_DEBRIS'] . ' ' . $nf_debris_M .
        ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_M'] . ' ' . $nf_debris_C . ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_C'] .
        "\n";

    $lunePourcent = floor(($report['debris_M'] + $report['debris_C']) / 100000);
    $lunePourcent = ($lunePourcent < 0 ? 0 : ($lunePourcent > 20 ? 20 : $lunePourcent));
    if ($lunePourcent > 0) {
            $template .= $lang['GAME_CREPORT_RESULT_NO_MOON'] . ' ' . $lunePourcent . ' %';
    }

    if ($report['lune'] == 1) {
            $template .= "\n" . $lang['GAME_CREPORT_RESULT_MOON'] . ".";
    }

    return ($template);
}
