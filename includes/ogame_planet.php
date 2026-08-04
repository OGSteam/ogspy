<?php
/**
 * @file includes/ogame_planet.php
 * Planet production and calculation logic extracted from ogame.php
 * @package OGSpy
 * @subpackage Ogame formula library
 * @author OGSteam
 * @copyright Copyright &copy; 2025, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.0
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * @brief Calculates planet production and consumption.
 *
 * @param[in] array $user_building   Planet info ('M','C','D','CES','CEF','SAT','FOR','temperature_max','coordinates','M_percentage','C_percentage','D_percentage','CES_percentage','CEF_percentage','Sat_percentage','FOR_percentage',array 'booster_tab') 0 as default value
 * @param[in] array $user_technology Techno info ('NRJ','Plasma')
 * @param[in] array $user_data       User info (array('user_class'=>'COL'/...,'off_commandant','off_amiral','off_ingenieur','off_geologue', or 'off_full')
 * @param[in] array $server_config   Ogame universe info ('speed_uni')
 * @return array('prod_reel,'prod_theorique','ratio','conso_E','prod_E',  //Production totale
 *      'prod_CES','prod_CEF','prod_SAT','prod_FOR',   //production énergie of chaque unité
 *      'prod_M','prod_C','prod_D','prod_base', //production ressources of chaque mine
 *      'prod_booster','prod_off','prod_Plasma','prod_classe',   //production des bonus
 *      'M','C','D','NRJ','AM', =>héritage of the type ressource for the values retournées.
 *      'nb_FOR_maxed',
 *      ) à part conso_E/prod_E (float) the autres sont array('M','C','D','NRJ','AM')
 *
 * @details remplace the fonctions ratio and bilan_production_ratio
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

/* Legacy production/consumption helpers were previously defined here.
   They have been moved to `includes/ogame_legacy.php` to centralize deprecated wrappers
   and avoid duplication. Use the modern `ogame_production_building()` and
   `ogame_production_planet()` APIs instead.
*/

// Legacy satellite helper moved to `includes/ogame_legacy.php`.

// Legacy foreuse helper moved to `includes/ogame_legacy.php`.

/* Legacy helpers (foreuse_max, consumption, ratio, bilan_production_ratio)
   were moved to `includes/ogame_legacy.php` to centralize deprecated wrappers
   and avoid duplication. Use `ogame_production_*` APIs in this file for new code.
*/

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
/* Legacy helper `ratio()` moved to `includes/ogame_legacy.php`. Use ogame_production_planet() instead. */

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
/* Legacy helper `bilan_production_ratio()` moved to `includes/ogame_legacy.php`.
   The legacy implementation block that previously executed on include has been removed
   to avoid runtime calls to deprecated helpers during file inclusion. If you need
   the old behavior, use the wrappers provided in `includes/ogame_legacy.php` or
   migrate callers to `ogame_production_planet()` / `ogame_production_building()`.
*/