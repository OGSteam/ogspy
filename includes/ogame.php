<?php
/**
* OGame Games Formulas and Data
* @package OGSpy
* @subpackage Ogame Data
* @author Kyser
* @created 15/11/2005
* @copyright Copyright &copy; 2012, http://ogsteam.fr/
* @version 3.04b ($Rev: 7697 $)
* @modified $Date: 2012-08-25 19:27:55 +0200 (Sat, 25 Aug 2012) $
* @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/ogame.php $
* $Id: ogame.php 7697 2012-08-25 17:27:55Z darknoon $
*/
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
* @param int $NRJ Current value of Energy available on the planet
* @param int $Plasma Current value of the user Plasma Technology
* @return the result of the production on the specified building.
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
        $geo = 0.10;    //+10%
    } elseif ($officier == 2) {
        $geo = 0.12;    //+12%
    } else {
        $geo = 0;
    }
    $ing = $geo;
    switch ($building) {
        case "M":
            $prod_base = 30 * $server_config['speed_uni'];
            $result =  30 * $level * pow(1.1, $level); // formule de base
            $result = $result * (1 + $geo + 0.01 * $Plasma);
            $result = round($result); // arrondi 
            $result = $result + $prod_base; // prod de base
            $result = $server_config['speed_uni'] * $result; // vitesse uni
            break;

        case "C":
            $prod_base = 15 * $server_config['speed_uni'];    
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
            $result = floor($result);   // troncature inférieure
            //$result = $server_config['speed_uni'] * $result; // vitesste uni ne change pas la prod d E
            break;

        case "CEF":
            $result = 30 * $level * pow((1.05 + $NRJ * 0.01), $level);
            $result = $result * (1 + $ing); // ingenieur
            $result = floor($result);   // troncature inférieure
            //$result = $server_config['speed_uni'] * $result; // vitesste uni ne change pas la prod d E
            break;

        default:
            $result = 0;
            break;
    }

    return ($result);
}

/**
* Gets the energy production of satellites.
* @param int $temperature_min Min temprature of the current planet
* @param int $temperature_max Max temprature of the current planet
* @param int $officier Officer option enabled (=1) or not(=0) or full Officer(=2)
* @return the result of the production on the specified building.
*/
function production_sat($temperature_min, $temperature_max, $officier = 0)
{
    if ($officier == 0) {
        $ing = 1;
    } elseif ($officier == 1) {
        $ing = 1.10;    //110%
    } elseif ($officier == 2) {
        $ing = 1.12;    //112%
    } else {
        $ing = 1;
    }
    return floor($ing * (((($temperature_min + $temperature_max) / 2) + 160) / 6));
}

/**
* Gets the power consumption of the current building
* @param string $building The building type
* @param int $level Min The building Level
* @return the building power consumption
*/
function consumption($building, $level)
{
    global $server_config;
    switch ($building) {
        case "M":   //no break
        case "C":
            $result = 10 * $level * pow(1.1, $level);
            $result = ceil($result);    //troncature supérieure
            break;

        case "D":
            $result = 20 * $level * pow(1.1, $level);
            $result = ceil($result);    //troncature supérieure
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
* @param int $D Deuterieum Mine Level
* @param int $CES Solar Plant Level
* @param int $CEF Fusion Plant Level
* @param int $ingenieur Ingenieur option enabled or not
* @param int $SAT Number of sattelites
* @param int $temperature_min Min temprature of the current planet
* @param int $temperature_max Max temprature of the current planet
* @param int $NRJ NRJ available on the current planet
* @return the ratio for the selected planet
*/
function ratio($M, $C, $D, $CES, $CEF, $ingenieur, $SAT, $temperature_min, $temperature_max,$NRJ)
{
    $consommation_E = 0; // la consommation
    $conso_M = consumption("M", $M);
    $conso_C = consumption("C", $C);
    $conso_D = consumption("D", $D);
    $consommation_E += $conso_M;
    $consommation_E += $conso_C;
    $consommation_E += $conso_D;

    $production_E = 0; // la production
    $prod_CES = production("CES", $CES, $ingenieur);
    $prod_CEF = production("CEF", $CEF, $ingenieur, $temperature_max, $NRJ);
    $prod_SAT = $SAT * production_sat($temperature_min, $temperature_max, $ingenieur);
    $production_E += $prod_CES;
    $production_E += $prod_CEF;
    $production_E += $prod_SAT;

    $ratio = 1; // indique le pourcentage a appliquer sur la prod
    $ratio_temp = 1;
    $ratio_temp = ($consommation_E == 0) ? 0 : ($production_E * 100 / $consommation_E) / 100; // fix division par 0
    $ratio = ($ratio_temp >= 1) ? 1 : $ratio_temp;

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
* @param int $temperature_min Min temprature of the current planet
* @param int $temperature_max Max temprature of the current planet
* @param int $NRJ NRJ available on the current planet
* @param int $ingenieur Ingenieur option enabled or not
* @param int $geologue Geologue option enabled or not
* @return the production for each building for the selected planet according to the current ratio
*/
function bilan_production_ratio($M, $C, $D, $CES, $CEF, $SAT, $temperature_min, $temperature_max, $NRJ = 0, $ingenieur = 0, $geologue = 0, $Plasma = 0)
{

    $tmp = ratio($M, $C, $D, $CES, $CEF, $ingenieur, $SAT, $temperature_min, $temperature_max,$NRJ);
    $ratio = $tmp["ratio"];
    $consommation_E = $tmp["conso_E"];
    $production_E = $tmp["prod_E"];
    $prod_CES = $tmp["prod_CES"];
    $prod_CEF = $tmp["prod_CEF"];
    $prod_SAT = $tmp["prod_SAT"];
    $conso_M = $tmp["conso_M"];
    $conso_C = $tmp["conso_C"];
    $conso_D = $tmp["conso_D"];

    //production de metal avec ratio
    $prod_M = production("M", $M, $geologue,$temperature_max,$NRJ, $Plasma);
    $prod_M *= $ratio;

    //production de cristal avec ratio
    $prod_C = production("C", $C, $geologue,$temperature_max,$NRJ, $Plasma);
    $prod_C *= $ratio;

    //production de deut avec ratio
    $prod_D = production("D", $D, $geologue, $temperature_max);
    $prod_D *= $ratio;
    $prod_D -= consumption("CEF", $CEF); //on soustrait la conso de deut de la cef


    return array("M" => $prod_M, "C" => $prod_C, "D" => $prod_D, "ratio" => $ratio,
        "conso_E" => $consommation_E, "prod_E" => $production_E, "prod_CES" => $prod_CES,
        "prod_CEF" => $prod_CEF, "prod_SAT" => $prod_SAT, "conso_M" => $conso_M,
        "conso_C" => $conso_C, "conso_D" => $conso_D);
}


/**
* Calculates the Planet storage capacity (Taille Hangar)
* @param int $level Storage building Level
* @return the capacity
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
            
        case "CM":
        	$M = 2645 * pow(2.3, ($level - 1));
        	$C = 0;
        	$D = 0;
        	$NRJ = 0;
        	break;
        	
        case "CC":
        	$M = 2645 * pow(2.3, ($level - 1));
        	$C = 1322 * pow(2.3, ($level - 1));
        	$D = 0;
        	$NRJ = 0;
        	break;
        	
        case "CD":
        	$M = 2645 * pow(2.3, ($level - 1));
        	$C = 2645 * pow(2.3, ($level - 1));
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
* @return the bild :-)
*/
function all_building_cumulate($user_building)
{

    $total = 0;

    while ($data = current($user_building)) {

        $bats = array_keys($data);

        foreach ($bats as $key) {

            $level = $data[$key];
            if ($level == "")
                $level = 0;

            if ($key == "M" || $key == "C" || $key == "D" || $key == "CES" || $key == "CEF" ||
                $key == "UdR" || $key == "UdN" || $key == "CSp" || $key == "HM" || $key == "HC" ||
                $key == "HD" || $key == "CM" ||$key == "CC" ||$key == "CD" ||$key == "Lab" || 
                $key == "Ter" || $key == "DdR" || $key == "Silo" ||	$key == "BaLu" || $key == "Pha" ||
                $key == "PoSa") {
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
        if (sizeof($init_d_prix) != sizeof($keys))
            continue;

        for ($i = 0; $i < sizeof($init_d_prix); $i++) {
            $total += $init_d_prix[$keys[$i]] * ($data[$keys[$i]] != "" ? $data[$keys[$i]] :
                0);
        }

        next($user_defence);
    }

    return $total;
}

/**
* Calculates the price of all lunas
* @param string $user_defence The list of buildings with corresponding levels on the luna
* @return the bild :-)
*/
function all_lune_cumulate($user_building, $user_defence)
{

    $total = all_defence_cumulate($user_defence) + all_building_cumulate($user_building);

    return $total;
}

/**
* Calculates the price of all researches
* @param string $user_defence The list of technologies with corresponding levels
* @return the bild :-)
*/
function all_technology_cumulate($user_technology)
{

    $total = 0;
    $init_t_prix = array("Esp" => 1400, "Ordi" => 1000, "Armes" => 1000, "Bouclier" =>
        800, "Protection" => 1000, "NRJ" => 1200, "Hyp" => 6000, "RC" => 1000, "RI" =>
        6600, "PH" => 36000, "Laser" => 300, "Ions" => 1400, "Plasma" => 7000, "RRI" =>
        800000, "Graviton" => 0, "Astrophysique" => 16000);
    $keys = array_keys($init_t_prix);

    if (sizeof($init_t_prix) != sizeof($user_technology))
        return 0;

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
?>