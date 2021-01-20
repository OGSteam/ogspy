<?php
/** @file includes/ogame.php
 * OGame games formulas and data.
 * @package OGSpy
 * @subpackage Ogame formula library
 * @author Kyser
 * @copyright Copyright &copy; 2012, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7697 $)
 * @created 15/11/2005
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 *  @brief Get an Ogame ressources array.
 *  
 *  @param[in] int $metal,$cristal,$deut The needed ressources
 *  @param[in] int $NRJ,$AM              Optional ressources (0 default)
 *  @return array('M','C','D','NRJ','AM'), default is 0
 */
function ogame_array_ressource($metal, $cristal, $deut, $NRJ = 0, $AM = 0)
{
    return array('M'=>$metal, 'C'=>$cristal, 'D'=>$deut, 'NRJ'=>$NRJ, 'AM'=>$AM);
}

/**
 *  @brief Return position ressources bonus in Ogame.
 *  
 *  @param[in] int $position The wanted position
 *  @return array('M','C','D','NRJ','AM') of bonus, default is 0%
 */
function ogame_production_position($position)
{
    $result = array('M'=>0, 'C'=>0, 'D'=>0, 'NRJ'=>0, 'AM'=>0);

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
 *  @brief Calculates foreuse coefficient on base production.
 *  
 *  @param[in] array $user_building array of mines level and FOR number (array('M','C','D','FOR'))
 *  @param[in] array $user_data     array with class and officiers infos (array('user_class'=>'COL'/...,'off_geologue' or 'off_full'))
 *  @return array('bonus', 'nb_FOR_maxed') bonus=foreuse bonus coefficient ; nb_FOR_maxed=limit nb if too much
 */
function ogame_production_bonus_foreuse($user_building, $user_data)
{
    static $FOR_COEF          = 0.0002; //0.02% / foreuse
    static $FOR_BONUS_COL     = 0.5;    //+50% pour COL
    static $FOR_BONUS_COL_GEO = 0.1;    //+10% de foreuse pour COL+GEO
    $names = ogame_get_element_names();
//Valeurs OUT par défaut :
    $result = array('bonus'=>0, 'nb_FOR_maxed'=>0);
//Valeurs IN par défaut :
    if (!isset($user_building['M'])   || !is_numeric($user_building['M']))   { $user_building['M'] = 0; }
    if (!isset($user_building['C'])   || !is_numeric($user_building['C']))   { $user_building['C'] = 0; }
    if (!isset($user_building['D'])   || !is_numeric($user_building['D']))   { $user_building['D'] = 0; }
    if (!isset($user_building['FOR']) || !is_numeric($user_building['FOR'])) { $user_building['FOR'] = 0; }
    if (!isset($user_data['off_geologue'])) { $user_data['off_geologue'] = 0; }
    if (!isset($user_data['off_full']))     { $user_data['off_full'] = 0; }
    if (!isset($user_data['user_class']))   { $user_data['user_class'] = 'none'; }
    if (!in_array($user_data['user_class'], $names['CLASS'], true)) { $user_data['user_class'] = $names['CLASS'][0]; }

    $bonus_foreuse = $FOR_COEF;
    if ($user_data['user_class'] === 'COL') {
        $bonus_foreuse = $bonus_foreuse * (1 + $FOR_BONUS_COL);
    }
    $nb_foreuse_max = 8 * ($user_building['M'] + $user_building['C'] + $user_building['D']);
    if ($user_data['user_class'] === 'COL' && ($user_data['off_geologue'] != 0 || $user_data['off_full'] != 0)) {
        $nb_foreuse_max = $nb_foreuse_max * (1 + $FOR_BONUS_COL_GEO);
    }
    $nb_foreuse_max = floor($nb_foreuse_max);
    if ($user_building['FOR'] > $nb_foreuse_max) {
        $user_building['FOR'] = $nb_foreuse_max;
    }

    $result['bonus'] = min(0.5, $bonus_foreuse * $user_building['FOR']);
    $result['nb_FOR_maxed'] = $user_building['FOR'];

    return $result;
}

/**
 *  @brief Calculates building/sat/for or base production and consumption.
 *  
 *  @param[in] string $building        The wanted building/sat/for ('base','M','C','D','CES','CEF','SAT','FOR')
 *  @param[in] array  $user_building   Planet info ('M','C','D','CES','CEF','SAT','FOR','temperature_max','coordinates') 0 as default value
 *  @param[in] array  $user_technology Techno info ('NRJ','Plasma') 0 as default value
 *  @param[in] array  $user_data       User info (array('user_class'=>'COL'/...,'off_geologue' or 'off_full')
 *  @param[in] array  $server_config   Ogame univers info ('speed_uni',(bool)'final_calcul') / final_calcul permet de déterminer si les valeurs retournées seront manipulées avec les % de production ressources, et donc sans arrondi.
 *  @return array('M','C','D','NRJ','AM') of production
 *  
 *  @details remplace les fonctions consumption et partiellement production,production_sat,production_foreuse
 */
function ogame_production_building($building, $user_building = null, $user_technology = null, $user_data = null, $server_config = null)
{
    static $BASE_M = 30;
    static $BASE_C = 15;
//Valeurs OUT par défaut :
    $result = array('M'=>0, 'C'=>0, 'D'=>0, 'NRJ'=>0, 'AM'=>0);
//Valeurs IN par défaut :
    if (!isset($user_technology['NRJ']) || !is_numeric($user_technology['NRJ'])) { $user_technology['NRJ'] = 0; }
    if (!isset($user_building['M'])     || !is_numeric($user_building['M']))     { $user_building['M'] = 0; }
    if (!isset($user_building['C'])     || !is_numeric($user_building['C']))     { $user_building['C'] = 0; }
    if (!isset($user_building['D'])     || !is_numeric($user_building['D']))     { $user_building['D'] = 0; }
    if (!isset($user_building['CES'])   || !is_numeric($user_building['CES']))   { $user_building['CES'] = 0; }
    if (!isset($user_building['CEF'])   || !is_numeric($user_building['CEF']))   { $user_building['CEF'] = 0; }
    if (!isset($user_building['FOR'])   || !is_numeric($user_building['FOR']))   { $user_building['FOR'] = 0; }
    if (!isset($user_building['SAT'])) {
        if (!isset($user_building['Sat']) || !is_numeric($user_building['Sat'])) {
            $user_building['SAT'] = 0;
        } else {
            $user_building['SAT'] = $user_building['Sat'];
        }
    }
    if (!isset($user_building['temperature_max']) || !is_numeric($user_building['temperature_max'])) { $user_building['temperature_max'] = 0; }
    if (!isset($user_building['coordinates']))  { $user_building['coordinates'] = 0; }
    if (!isset($server_config['speed_uni']))    { $server_config['speed_uni'] = 1; }
    if (!isset($server_config['final_calcul'])) { $server_config['final_calcul'] = true; }

    $user_building['position'] = ogame_find_planet_position($user_building['coordinates']);
    $bonus_position = ogame_production_position($user_building['position']);

    switch ($building) {
        case 'base':
            $result['M'] = $BASE_M * (1 + $bonus_position['M']) * $server_config['speed_uni'];
            $result['C'] = $BASE_C * (1 + $bonus_position['C']) * $server_config['speed_uni'];
            break;
        case 'M':
            $level = $user_building['M'];
            $coef_base = (1 + $bonus_position['M']) * $server_config['speed_uni'];
            $result['M']   = 30 * $level * pow(1.1, $level) * $coef_base;
            $result['NRJ'] = - floor( 10 * $level * pow(1.1, $level) );
            break;
        case 'C':
            $level = $user_building['C'];
            $coef_base = (1 + $bonus_position['C']) * $server_config['speed_uni'];
            $result['C']   = 20 * $level * pow(1.1, $level) * $coef_base;
            $result['NRJ'] = - floor( 10 * $level * pow(1.1, $level) );
            break;
        case 'D':
            $level = $user_building['D'];
            $coef_base = (1 + $bonus_position['D']) * $server_config['speed_uni'];
            $result['D']   = 10 * $level * pow(1.1, $level) * (1.44 - 0.004 * $user_building['temperature_max']) * $coef_base;
            $result['NRJ'] = - floor( 20 * $level * pow(1.1, $level) );
            break;
        case 'CES':
            $level = $user_building['CES'];
            $result['NRJ'] = floor( 20 * $level * pow(1.1, $level) );
            break;
        case 'CEF':
            $level = $user_building['CEF'];
            $result['NRJ'] = floor( 30 * $level * pow((1.05 + $user_technology['NRJ'] * 0.01), $level) );
            $result['D']   = - floor( 10 * $level * pow(1.1, $level) ) * $server_config['speed_uni'];
            break;
        case 'SAT':
            $number = $user_building['SAT'];
            $result['NRJ'] = floor( ($user_building['temperature_max'] + 140) / 6 ) * $number;
            break;
        case 'FOR':
            $number = $user_building['FOR'];
            $production_mine_base['M'] = ogame_production_building('M', $user_building, null, null, $server_config)['M'];
            $production_mine_base['C'] = ogame_production_building('C', $user_building, null, null, $server_config)['C'];
            $production_mine_base['D'] = ogame_production_building('D', $user_building, null, null, $server_config)['D'];
            $bonus_for = ogame_production_bonus_foreuse($user_building, $user_data);

            $result['M'] = round( $production_mine_base['M'] * $bonus_for['bonus'] );
            $result['C'] = round( $production_mine_base['C'] * $bonus_for['bonus'] );
            $result['D'] = round( $production_mine_base['D'] * $bonus_for['bonus'] );
            $result['NRJ'] = - 50 * $bonus_for['nb_FOR_maxed'];
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
 *  @brief Calculates planet production and consumption.
 *  
 *  @param[in] array $user_building   Planet info ('M','C','D','CES','CEF','SAT','FOR','temperature_max','coordinates','M_percentage','C_percentage','D_percentage','CES_percentage','CEF_percentage','Sat_percentage','FOR_percentage',array 'booster_tab') 0 as default value
 *  @param[in] array $user_technology Techno info ('NRJ','Plasma') 
 *  @param[in] array $user_data       User info (array('user_class'=>'COL'/...,'off_commandant','off_amiral','off_ingenieur','off_geologue', or 'off_full')
 *  @param[in] array $server_config   Ogame univers info ('speed_uni')
 *  @return array('prod_reel,'prod_theorique','ratio','conso_E','prod_E',  //Production totale
 *      'prod_CES','prod_CEF','prod_SAT','prod_FOR',   //production énergie de chaque unité
 *      'prod_M','prod_C','prod_D','prod_base', //production ressources de chaque mine
 *      'prod_booster','prod_off','prod_Plasma','prod_classe',   //production des bonus
 *      'M','C','D','NRJ','AM', =>héritage du type ressource pour les valeurs retournées.
 *      'nb_FOR_maxed',
 *      ) à part conso_E/prod_E (float) les autres sont array('M','C','D','NRJ','AM')
 *  
 *  @details remplace ratio() et bilan_production_ratio()
 */
function ogame_production_planet($user_building, $user_technology = null, $user_data = null, $server_config = null)
{
    static $DEFAULT_TYPE_RESS = array('M'=>0, 'C'=>0, 'D'=>0, 'NRJ'=>0, 'AM'=>0);
    static $NRJ_BONUS_COL     = 0.1;   //+10% pour COL
    static $NRJ_BONUS_ING     = 0.1;   //+10% pour ingénieur
    static $NRJ_BONUS_FULL    = 0.02;  //+2% pour full officier
    static $RESS_BONUS_COL    = 0.25;  //+25% pour COL
    static $RESS_BONUS_GEO    = 0.1;   //+10% pour géologue
    static $RESS_BONUS_FULL   = 0.02; //+2% pour full officier
    static $RESS_PLASMA_M     = 0.01;
    static $RESS_PLASMA_C     = 0.0066;
    static $RESS_PLASMA_D     = 0.0033;
    $names = ogame_get_element_names();
//Valeurs OUT par défaut :
    $result = array('prod_reel'=>0, 'prod_theorique'=>0, 'ratio'=>0, 'conso_E'=>0, 'prod_E'=>0,   //Production totale
        'prod_CES'=>0, 'prod_CEF'=>0, 'prod_SAT'=>0, 'prod_FOR'=>0, //production et conso de chaque unité
        'prod_M'=>0, 'prod_C'=>0, 'prod_D'=>0, 'prod_base'=>0,  //production et conso de chaque unité
        'prod_booster'=>0, 'prod_off'=>0, 'prod_Plasma'=>0, 'prod_classe'=>0,   //production des bonus
        'nb_FOR_maxed'=>0,
        );
    $result['prod_reel']      = $DEFAULT_TYPE_RESS;
    $result['prod_theorique'] = $DEFAULT_TYPE_RESS;
    $result['prod_booster']   = $DEFAULT_TYPE_RESS;
    $result['prod_off']       = $DEFAULT_TYPE_RESS;
    $result['prod_Plasma']    = $DEFAULT_TYPE_RESS;
    $result['prod_classe']    = $DEFAULT_TYPE_RESS;
    $result = array_merge($result, $DEFAULT_TYPE_RESS); //Compatibilité par héritage !
//Valeurs IN par défaut :
    if (!isset($user_technology['Plasma']) || !is_numeric($user_technology['Plasma'])) { $user_technology['Plasma'] = 0; }
    if (!isset($user_data['off_commandant']))  { $user_data['off_commandant'] = 0; }
    if (!isset($user_data['off_amiral']))      { $user_data['off_amiral'] = 0; }
    if (!isset($user_data['off_ingenieur']))   { $user_data['off_ingenieur'] = 0; }
    if (!isset($user_data['off_geologue']))    { $user_data['off_geologue'] = 0; }
    if (!isset($user_data['off_full']))        { $user_data['off_full'] = 0; }
    if (!isset($user_data['user_class']))      { $user_data['user_class'] = 'none'; }
    if (!in_array($user_data['user_class'], $names['CLASS'], true)) { $user_data['user_class'] = $names['CLASS'][0]; }
    if (!isset($user_building['M_percentage'])   || !is_numeric($user_building['M_percentage']))   { $user_building['M_percentage'] = 100; }
    if (!isset($user_building['C_percentage'])   || !is_numeric($user_building['C_percentage']))   { $user_building['C_percentage'] = 100; }
    if (!isset($user_building['D_percentage'])   || !is_numeric($user_building['D_percentage']))   { $user_building['D_percentage'] = 100; }
    if (!isset($user_building['CES_percentage']) || !is_numeric($user_building['CES_percentage'])) { $user_building['CES_percentage'] = 100; }
    if (!isset($user_building['CEF_percentage']) || !is_numeric($user_building['CEF_percentage'])) { $user_building['CEF_percentage'] = 100; }
    if (!isset($user_building['Sat_percentage']) || !is_numeric($user_building['Sat_percentage'])) { $user_building['Sat_percentage'] = 100; }
    if (!isset($user_building['FOR_percentage']) || !is_numeric($user_building['FOR_percentage'])) { $user_building['FOR_percentage'] = 100; }
    if (!isset($user_building['booster_tab']['booster_e_val'])) { $user_building['booster_tab']['booster_e_val'] = 0; }
    if (!isset($user_building['booster_tab']['booster_m_val'])) { $user_building['booster_tab']['booster_m_val'] = 0; }
    if (!isset($user_building['booster_tab']['booster_c_val'])) { $user_building['booster_tab']['booster_c_val'] = 0; }
    if (!isset($user_building['booster_tab']['booster_d_val'])) { $user_building['booster_tab']['booster_d_val'] = 0; }
    if (!isset($user_data['production_theorique']) || !is_bool($user_data['production_theorique'])) { $user_data['production_theorique'] = false; }
    
    // $user_empire = user_get_empire($user_data['user_id']);
    // $user_production = user_empire_production($user_empire, $user_data, $server_config['speed_uni']);

    if ($user_data['off_full'] != 0) {
        $user_data['off_ingenieur'] = 1;
        $user_data['off_geologue']  = 1;
    }
    if ($user_data['off_commandant'] != 0 && $user_data['off_amiral'] != 0 && $user_data['off_ingenieur'] != 0 && $user_data['off_geologue'] != 0) {
        $user_data['off_full'] = 1;
    }    
    if ($user_data['user_class'] !== 'COL' && $user_building['FOR_percentage'] > 100) {
        $user_building['FOR_percentage'] = 100;
    }

//Calcul valeurs de base
    $prod_base    = ogame_production_building('base', $user_building, null, null, $server_config);
    $server_config['final_calcul'] = false;
    $prod_mine_M  = ogame_production_building('M', $user_building, $user_technology, $user_data, $server_config);
    $prod_mine_C  = ogame_production_building('C', $user_building, $user_technology, $user_data, $server_config);
    $prod_mine_D  = ogame_production_building('D', $user_building, $user_technology, $user_data, $server_config);
    $prod_bat_CES = ogame_production_building('CES', $user_building, $user_technology, $user_data, $server_config);
    $prod_bat_CEF = ogame_production_building('CEF', $user_building, $user_technology, $user_data, $server_config);
    $prod_vso_SAT = ogame_production_building('SAT', $user_building, $user_technology, $user_data, $server_config);
    $prod_vso_FOR = ogame_production_building('FOR', $user_building, $user_technology, $user_data, $server_config);
    $result['prod_base'] = $prod_base;
    $result['prod_M']    = $prod_mine_M;
    $result['prod_C']    = $prod_mine_C;
    $result['prod_D']    = $prod_mine_D;
    $result['prod_CES']  = $prod_bat_CES;
    $result['prod_CEF']  = $prod_bat_CEF;
    $result['prod_SAT']  = $prod_vso_SAT;
    $result['prod_FOR']  = $prod_vso_FOR;

//Calcul de la consommation d'énergie théorique
    $conso_M   = round( $prod_mine_M['NRJ'] * $user_building['M_percentage'] / 100 );
    $conso_C   = round( $prod_mine_C['NRJ'] * $user_building['C_percentage'] / 100 );
    $conso_D   = round( $prod_mine_D['NRJ'] * $user_building['D_percentage'] / 100 );
    $conso_FOR = round( $prod_vso_FOR['NRJ'] * max(1, $user_building['FOR_percentage'] * 2 / 100 - 1) ); // [50 * max( 1 ; 1 + (pourcentage_production - 100%) * %_malus_overload / 10% ) ]
    $consommation_E = $conso_M + $conso_C + $conso_D + $conso_FOR;
    $result['conso_E']         = $consommation_E;
    $result['prod_M']['NRJ']   = $conso_M;
    $result['prod_C']['NRJ']   = $conso_C;
    $result['prod_D']['NRJ']   = $conso_D;
    $result['prod_FOR']['NRJ'] = $conso_FOR;
    if (!$user_data['production_theorique']) {
    //Calcul de la production d'énergie
        $prod_CES = $prod_bat_CES['NRJ'] * $user_building['CES_percentage'] / 100;
        $prod_CEF = $prod_bat_CEF['NRJ'] * $user_building['CEF_percentage'] / 100;
        $prod_SAT = $prod_vso_SAT['NRJ'] * $user_building['Sat_percentage'] / 100;
        $production_E = $prod_CES + $prod_CEF + $prod_SAT;
        $result['prod_booster']['NRJ']    = round( $production_E * $user_building['booster_tab']['booster_e_val'] / 100 );
        if ($user_data['user_class'] === 'COL') {
            $result['prod_classe']['NRJ'] = round( $production_E * $NRJ_BONUS_COL );
        }
        if ($user_data['off_ingenieur'] != 0) {
            $result['prod_off']['NRJ']    = round( $production_E * $NRJ_BONUS_ING );
        }
        if ($user_data['off_full'] != 0) {
            $result['prod_off']['NRJ']   += round( $production_E * $NRJ_BONUS_FULL );
        }
        $result['prod_CES']['NRJ'] = round( $prod_CES );
        $result['prod_CEF']['NRJ'] = round( $prod_CEF );
        $result['prod_SAT']['NRJ'] = round( $prod_SAT );
        $production_E  = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];
        $production_E += $result['prod_booster']['NRJ'] + $result['prod_off']['NRJ'] + $result['prod_classe']['NRJ'];

    //Calcul ratio
        $ratio = 1; // indique le pourcentage à appliquer sur la prod
        $ratio_temp = 1;
        $ratio_temp = ($consommation_E == 0) ? 0 : (- $production_E * 100 / $consommation_E) / 100; // fix division par 0
        if ($ratio_temp > 1) {
            $ratio = 1;
        }  else {
            $ratio = $ratio_temp;
        }
        $result['ratio'] = $ratio;
    } else { //Pour le cas d'un calcul théorique
        $user_building['M_percentage'] = 100;
        $user_building['C_percentage'] = 100;
        $user_building['D_percentage'] = 100;
        $user_building['CES_percentage'] = 100;
        $user_building['CEF_percentage'] = 100;
        $user_building['Sat_percentage'] = 100;
        if ($user_building['FOR_percentage'] < 100) {
            $user_building['FOR_percentage'] = 100;
        }
        $result['prod_FOR']['NRJ'] = round( $prod_vso_FOR['NRJ'] * max(1, $user_building['FOR_percentage'] * 2 / 100 - 1) );
        $production_E = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];
        
        $result['prod_booster']['NRJ']    = round( $production_E * $user_building['booster_tab']['booster_e_val'] / 100 );
        if ($user_data['user_class'] === 'COL') {
            $result['prod_classe']['NRJ'] = round( $production_E * $NRJ_BONUS_COL );
        }
        if ($user_data['off_ingenieur'] != 0) {
            $result['prod_off']['NRJ']    = round( $production_E * $NRJ_BONUS_ING );
        }
        if ($user_data['off_full'] != 0) {
            $result['prod_off']['NRJ']   += round( $production_E * $NRJ_BONUS_FULL );
        }
        $result['prod_CES']['NRJ'] = round( $result['prod_CES']['NRJ'] );
        $result['prod_CEF']['NRJ'] = round( $result['prod_CEF']['NRJ'] );
        $result['prod_SAT']['NRJ'] = round( $result['prod_SAT']['NRJ'] );
        $production_E  = $result['prod_CES']['NRJ'] + $result['prod_CEF']['NRJ'] + $result['prod_SAT']['NRJ'];
        $production_E += $result['prod_booster']['NRJ'] + $result['prod_off']['NRJ'] + $result['prod_classe']['NRJ'];
        $ratio = 1;
    }
    $result['prod_E'] = $production_E;

//Calcul de la production
    $bonus_off_geo  = ($user_data['off_geologue'] != 0)    ? $RESS_BONUS_GEO  : 0;
    $bonus_off_full = ($user_data['off_full'] != 0)        ? $RESS_BONUS_FULL : 0;
    $bonus_class    = ($user_data['user_class'] === 'COL') ? $RESS_BONUS_COL  : 0;
    $bonus_for      = ogame_production_bonus_foreuse($user_building, $user_data);
    $result['nb_FOR_maxed'] = $bonus_for['nb_FOR_maxed'];

//*Métal :
    $production_mine_base = floor( $prod_mine_M['M'] * ($user_building['M_percentage'] / 100) * $ratio );

    $prod_off     = round( $production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full );
    $prod_Plasma  = round( $production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_M );
    $prod_booster = round( $production_mine_base * $user_building['booster_tab']['booster_m_val'] / 100 );
    $prod_FOR     = round( $production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100) );
    $prod_classe  = round( $production_mine_base * $bonus_class );

    $result['M'] = $prod_base['M'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['prod_off']['M']     = $prod_off;
    $result['prod_Plasma']['M']  = $prod_Plasma;
    $result['prod_booster']['M'] = $prod_booster;
    $result['prod_FOR']['M']     = $prod_FOR;
    $result['prod_classe']['M']  = $prod_classe;
    $result['prod_M']['M']       = $production_mine_base;

//*Cristal :
    $production_mine_base = floor( $prod_mine_C['C'] * ($user_building['C_percentage'] / 100) * $ratio );

    $prod_off     = round( $production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full );
    $prod_Plasma  = round( $production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_C );
    $prod_booster = round( $production_mine_base * $user_building['booster_tab']['booster_c_val'] / 100 );
    $prod_FOR     = round( $production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100) );
    $prod_classe  = round( $production_mine_base * $bonus_class );

    $result['C'] = $prod_base['C'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['prod_off']['C']     = $prod_off;
    $result['prod_Plasma']['C']  = $prod_Plasma;
    $result['prod_booster']['C'] = $prod_booster;
    $result['prod_FOR']['C']     = $prod_FOR;
    $result['prod_classe']['C']  = $prod_classe;
    $result['prod_C']['C']       = $production_mine_base;

//*Deutérium :
    $production_mine_base = floor( $prod_mine_D['D'] * ($user_building['D_percentage'] / 100) * $ratio );

    $prod_off     = round( $production_mine_base * $bonus_off_geo) + round($production_mine_base * $bonus_off_full );
    $prod_Plasma  = round( $production_mine_base * $user_technology['Plasma'] * $RESS_PLASMA_D );
    $prod_booster = round( $production_mine_base * $user_building['booster_tab']['booster_d_val'] / 100 );
    $prod_FOR     = round( $production_mine_base * $bonus_for['bonus'] * ($user_building['FOR_percentage'] / 100) );
    $prod_classe  = round( $production_mine_base * $bonus_class );
    $conso_CEF    = ceil( $prod_bat_CEF['D'] * $user_building['CEF_percentage'] / 100 );

    $result['D'] = $prod_base['D'] + $production_mine_base + $prod_FOR + $prod_Plasma + $prod_booster + $prod_off + $prod_classe;
    $result['D'] = $result['D'] + $conso_CEF;
    $result['prod_off']['D']     = $prod_off;
    $result['prod_Plasma']['D']  = $prod_Plasma;
    $result['prod_booster']['D'] = $prod_booster;
    $result['prod_FOR']['D']     = $prod_FOR;
    $result['prod_classe']['D']  = $prod_classe;
    $result['prod_CEF']['D']     = $conso_CEF;
    $result['prod_D']['D']       = $production_mine_base;

    foreach ($names['RESS'] as $ress) {
        $result['prod_reel'][$ress]  = floor($result['prod_base'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_M'][$ress])   + floor($result['prod_C'][$ress]) + floor($result['prod_D'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_CES'][$ress]) + floor($result['prod_CEF'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_SAT'][$ress]) + floor($result['prod_FOR'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_Plasma'][$ress]) + floor($result['prod_booster'][$ress]);
        $result['prod_reel'][$ress] += floor($result['prod_off'][$ress]) + floor($result['prod_classe'][$ress]);
    }
    if (!$user_data['production_theorique']) {
        $user_data['production_theorique'] = true;
        $tmp = ogame_production_planet($user_building, $user_technology, $user_data, $server_config);
        $result['prod_theorique'] = $tmp['prod_reel'];
    }
    $result['NRJ'] = $result['prod_reel']['NRJ'];

    return $result;
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
    $bonus_foreuse     = 0.0002; //0.02% / foreuse
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
               $per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $FOR = 0, $per_FOR = 0, $classe = 0, $booster = null)
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
$per_M = 1, $per_C = 1, $per_D = 1, $per_CES = 1, $per_CEF = 1, $per_SAT = 1, $booster = null,  $FOR = 0, $per_FOR = 0, $classe = 0, $position = 0, $speed_uni = 1)
{
    trigger_error("Les fonctions bilan_production_ratio,ratio,production,consumption,production_sat,production_foreuse dépréciées depuis 3.3.8, préférer les fonctions ogame_*. Ici ogame_production_planet.", E_USER_NOTICE);

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
        $boost_C = ($booster['booster_c_val'] / 100) * (production('C', $C, 0, $temperature_max, 0, 0, 0, $position, $speed_uni) - floor(15 * (1 + $bonus_position_C) * $speed_uni)) * $per_C * $ratio;
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
 *  
 *  @param[in] string $coordinates planet coordinates (galaxy:system:position)
 *  @return int planet position
 */
function ogame_find_planet_position($coordinates) {
    $position = 0;

    $coordinates_tmp = explode(':', $coordinates);
    if (count($coordinates_tmp) === 3) {
        $position = (int) $coordinates_tmp[2];
    }

    return $position;
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
 *  @param[in] string $name  Name of building or research, like in name in database
 *  @param[in] int    $level The wanted level
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
            $M = round( 4000 * pow(1.75, ($level - 1)), -2 );
            $C = round( 8000 * pow(1.75, ($level - 1)), -2 );
            $D = round( 4000 * pow(1.75, ($level - 1)), -2 );
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
 *  @param[in] string $name  Name of building/research/fleet/defence, like in name in database
 *  @param[in] int    $level The current level or the number of fleet/defence
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
 *  @param[in] string $building Name of building, like in name in database
 *  @param[in] int    $level The current level
 *  @return array('M', 'C','D, 'NRJ') ressources used to it current level
 */
function building_cumulate($building, $level) { return ogame_element_cumulate($building, $level); }
/**
 *  @brief Calculates the price of a number of defence.
 *  @param[in] string $defence Name of defence, like in name in database
 *  @param[in] int    $number The current number
 *  @return array('M', 'C','D, 'NRJ') ressources used to have this defence number
 */
function defence_cumulate($defence, $number)  { return ogame_element_cumulate($defence, $number); }
/**
 *  @brief Calculates the price of a number of fleet.
 *  @param[in] string $fleet Name of fleet, like in name in database
 *  @param[in] int    $number The current number
 *  @return array('M', 'C','D, 'NRJ') ressources used to have this fleet number
 */
function fleet_cumulate($fleet, $number)      { return ogame_element_cumulate($fleet, $number); }
/**
 *  @brief Calculates the price of a research to it current level.
 *  @param[in] string $research Name of research, like in name in database
 *  @param[in] int    $level The current level
 *  @return array('M', 'C','D, 'NRJ') ressources used to it current level
 */
function research_cumulate($research, $level) { return ogame_element_cumulate($research, $level); }

/**
 *  @brief Give database names of a buiding/research/fleet/defence/class/ressources.
 *  
 *  @return array('BAT'=>array, 'RECH'=>array, 'VSO'=>array, 'DEF'=>array, 'CLASS'=>array, 'RESS'=>array)
 */
function ogame_get_element_names()
{
    $names = array();

    $names['BAT'] = array(  // Bâtiments :
        'M',    //Mine de métal
        'C',    //Mine de cristal
        'D',    //Synthétiseur de deutérium
        'CES',  //Centrale électrique solaire
        'CEF',  //Centrale électrique de fusion
        'UdR',  //Usine de robots
        'UdN',  //Usine de nanites
        'CSp',  //Chantier spatial
        'HM',   //Hangar de métal
        'HC',   //Hangar de cristal
        'HD',   //Réservoir de deutérium
        'Lab',  //Laboratoire
        'Ter',  //Terraformeur
        'DdR',  //Dépot de ravitaillement
        'Silo', //Silo de missiles
        'Dock', //Dock spatial
        'BaLu', //Base lunaire
        'Pha',  //Phalange de capteur
        'PoSa', //Porte de saut spatial
        );
    $names['RECH'] = array( // Recherches :
        'Esp',           //Technologie espionage
        'Ordi',          //Technologie ordinateur
        'Armes',         //Technologie armes
        'Bouclier',      //Technologie bouclier
        'Protection',    //Technologie protection des vaisseaux spatiaux
        'NRJ',           //Technologie énergie
        'Hyp',           //Technologie hyperespace
        'RC',            //Réacteur à combustion
        'RI',            //Réacteur à impulsion
        'PH',            //Propulsion hyperespace
        'Laser',         //Technologie laser
        'Ions',          //Technologie à ions
        'Plasma',        //Technologie plasma
        'RRI',           //Réseau de recherche intergalactique
        'Graviton',      //Technologie graviton
        'Astrophysique', //Astrophysique
        );
    $names['VSO'] = array(  // Flottes :
        'PT',   //Petit transporteur
        'GT',   //Grand transporteur
        'CLE',  //Chasseur léger
        'CLO',  //Chasseur lourd
        'CR',   //Croiseur
        'VB',   //Vaisseau de bataille
        'VC',   //Vaisseau de colonisation
        'REC',  //Recycleur
        'SE',   //Sonde d'espionnage
        'BMD',  //Bombardier
        'DST',  //Destructeur
        'EDLM', //Étoile de la mort
        'TRA',  //Traqueur
        'SAT',  //Satellite solaire
        'FOR',  //Foreuse
        'FAU',  //Faucheur
        'ECL',  //Éclaireur
        );
    $names['DEF'] = array(  // Défenses :
        'LM',  //Lanceur de missiles
        'LLE', //Artillerie laser légère
        'LLO', //Artillerie laser lourde
        'CG',  //Canon de Gauss
        'AI',  //Artillerie à ions
        'LP',  //Lanceur de plasma
        'PB',  //Petit bouclier
        'GB',  //Grand bouclier
        'MIC', //Missile d'interception
        'MIP', //Missile interplanétaire
        );
    $names['CLASS'] = array(
        'none', //Aucune classe
        'COL',  //Classe collecteur
        'GEN',  //Classe général
        'EXP',  //Classe explorateur
        );
    $names['RESS'] = array(
        'M',   //métal
        'C',   //cristal
        'D',   //deutérium
        'NRJ', //énergie
        'AM',  //AM
        );

    return $names;
}

/**
 *  @brief Détermine si c'est un bâtiment, une recherche, un vaisseau, une défense ou une classe.
 *  
 *  @param[in] string $nom Nom à rechercher, correspond au nom en BDD
 *  @return false|string 'BAT' bâtiment, 'RECH' recherche, 'DEF' défense, 'VSO' vaisseau, 'CLASS' classe et false sinon
 */
function ogame_is_element($nom)
{
    $names = ogame_get_element_names();
    foreach ($names as $label=>$name) {
        if (in_array($nom, $name, true)) {
            return $label;
        }
    }

    return false;
}

/**
 *  @brief Is an Ogame defence ?
 *  @param[in] string $nom Name to look, like name in database
 *  @return bool
 */
function ogame_is_a_defence($nom)  { return ogame_is_element($nom) === 'DEF'; }
/**
 *  @brief Is an Ogame fleet ?
 *  @param[in] string $nom Name to look, like name in database
 *  @return bool
 */
function ogame_is_a_fleet($nom)    { return ogame_is_element($nom) === 'VSO'; }
/**
 *  @brief Is an Ogame building ?
 *  @param[in] string $nom Name to look, like name in database
 *  @return bool
 */
function ogame_is_a_building($nom) { return ogame_is_element($nom) === 'BAT'; }
/**
 *  @brief Is an Ogame research ?
 *  @param[in] string $nom Name to look, like name in database
 *  @return bool
 */
function ogame_is_a_research($nom) { return ogame_is_element($nom) === 'RECH'; }

/**
 *  @brief Calculates the price of all element of type (building,defence,fleet,research).
 *  
 *  @param[in] array  $user Array of element each planet or moon
 *  @param[in] string $type Type of element ('BAT' pour bâtiment, 'RECH' pour recherche, 'DEF' pour défense, 'VSO' pour vaisseau)
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
 *  @param[in] $user_building Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_building_cumulate($user_building) { return ogame_all_cumulate($user_building, 'BAT'); }
/**
 *  @brief Calculates the price of all defence.
 *  @param[in] $user_defence Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_defence_cumulate($user_defence)   { return ogame_all_cumulate($user_defence, 'DEF'); }
/**
 *  @brief Calculates the price of all fleet.
 *  @param[in] $user_fleet Info of planet or moon
 *  @return float Total price (M+C+D)
 */
function all_fleet_cumulate($user_fleet)       { return ogame_all_cumulate($user_fleet, 'VSO'); }
/**
 *  @brief Calculates the price of all research.
 *  @param[in] $user_techno Info of technologies
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
 *  @param[in] int $conso The conso of the fleet
 *  @param[in] int $hour  Number of hours in parking
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
 *  @param[in] string     $nom         The name, like name in Database
 *  @param[in] array      $user_techno The array of technologies
 *  @param[in] string|int $classe      The user class //array('none','COL','GEN','EXP') - (1=Collectionneur)[0=aucune, 2=général, 3=explorateur])
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
    $names = ogame_get_element_names();
//Valeurs IN par défaut :
    if (!isset($user_techno['Armes'])      || !is_numeric($user_techno['Armes']))      { $user_techno['Armes'] = 0; }
    if (!isset($user_techno['Bouclier'])   || !is_numeric($user_techno['Bouclier']))   { $user_techno['Bouclier'] = 0; }
    if (!isset($user_techno['Protection']) || !is_numeric($user_techno['Protection'])) { $user_techno['Protection'] = 0; }
    if (!isset($user_techno['RC'])         || !is_numeric($user_techno['RC']))         { $user_techno['RC'] = 0; }
    if (!isset($user_techno['RI'])         || !is_numeric($user_techno['RI']))         { $user_techno['RI'] = 0; }
    if (!isset($user_techno['PH'])         || !is_numeric($user_techno['PH']))         { $user_techno['PH'] = 0; }
    if (!isset($user_techno['Hyp'])        || !is_numeric($user_techno['Hyp']))        { $user_techno['Hyp'] = 0; }
    if (isset($names['CLASS'][$classe])) { $classe = $names['CLASS'][$classe]; }
    if (!in_array($classe, $names['CLASS'], true)) { $classe = $names['CLASS'][0]; }
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

    $user_techno['speed'] = 0;  //local variable pour la vitesse
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
    EXP : none
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
 *  @brief Calculates technical data of all fleet/defence.
 *  
 *  @param[in] array      $user_techno The array of technologies
 *  @param[in] string|int $classe The user class //array('none','COL','GEN','EXP') - (1=Collectionneur)[0=aucune, 2=général, 3=explorateur])
 *  @return array of all fleet/defence with are array of details from ogame_elements_details()
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
 *  @brief Calculates technical data of Ogame requirement.
 *  
 *  @param[in] string $nom The name, like name in Database
 *  @return array('none','COL','GEN','EXP' : bool for class, 'CES',etc. : int for all bat/rech name in database)
 */
function ogame_elements_requirement($nom)
{
    $result = array();
    $names = ogame_get_element_names();
    foreach ($names['CLASS'] as $element) {
        $result[$element] = false;
    }
    $result['none'] = true;

    switch ($nom) {
// Bâtiments :
        case 'M': //no break;
        case 'C': //no break;
        case 'D': //no break;
        case 'CES': //no break;
        case 'HM': //no break;
        case 'HC': //no break;
        case 'HD': //no break;
        case 'UdR': //no break;
        case 'Lab': //no break;
        case 'DdR': //no break;
        case 'BaLu': //no break;
            break; //no requirement
        case 'CEF':
            $result['D']   = 5;
            $result['NRJ'] = 3;
            break;
        case 'UdN':
            $result['UdR']  = 10;
            $result['Ordi'] = 10;
            break;
        case 'CSp':
            $result['UdR'] = 2;
            break;
        case 'Ter':
            $result['UdN'] = 1;
            $result['NRJ'] = 12;
            break;
        case 'Silo':
            $result['CSp'] = 1;
            break;
        case 'Dock':
            $result['CSp'] = 2;
            break;
        case 'Pha':
            $result['BaLu'] = 1;
            break;  
        case 'PoSa':
            $result['BaLu'] = 1;
            $result['Hyp']  = 7;
            break;
// Recherches :
        case 'Esp':
            $result['Lab'] = 3;
            break;
        case 'Ordi':
            $result['Lab'] = 1;
            break;
        case 'Armes':
            $result['Lab'] = 4;
            break;
        case 'Bouclier':
            $result['Lab'] = 6;
            $result['NRJ'] = 3;
            break;
        case 'Protection':
            $result['Lab'] = 2;
            break;
        case 'NRJ':
            $result['Lab'] = 1;
            break;
        case 'Hyp':
            $result['Lab']      = 7;
            $result['NRJ']      = 5;
            $result['Bouclier'] = 5;
            break;
        case 'RC':
            $result['Lab'] = 1;
            $result['NRJ'] = 1;
            break;
        case 'RI':
            $result['Lab'] = 2;
            $result['NRJ'] = 1;
            break;
        case 'PH':
            $result['Lab'] = 7;
            $result['Hyp'] = 3;
            break;
        case 'Laser':
            $result['Lab'] = 1;
            $result['NRJ'] = 2;
            break;
        case 'Ions':
            $result['Lab']   = 4;
            $result['NRJ']   = 4;
            $result['Laser'] = 5;
            break;
        case 'Plasma':
            $result['Lab']   = 4;
            $result['NRJ']   = 8;
            $result['Laser'] = 10;
            $result['Ions']  = 5;
            break;
        case 'RRI':
            $result['Lab']  = 10;
            $result['Ordi'] = 8;
            $result['Hyp']  = 8;
            break;
        case 'Graviton':
            $result['Lab'] = 12;
            break;
        case 'Astrophysique':
            $result['Lab'] = 3;
            $result['Esp'] = 4;
            $result['RI']  = 3;
            break;
// Flottes :
        case 'PT':
            $result['CSp'] = 2;
            $result['RC']  = 2;
            break;
        case 'GT':
            $result['CSp'] = 4;
            $result['RC']  = 6;
            break;
        case 'CLE':
            $result['CSp'] = 1;
            $result['RC']  = 1;
            break;
        case 'CLO':
            $result['CSp']        = 3;
            $result['Protection'] = 2;
            $result['RI']         = 2;
            break;
        case 'CR':
            $result['CSp']  = 5;
            $result['RI']   = 4;
            $result['Ions'] = 2;
            break;
        case 'VB':
            $result['CSp'] = 7;
            $result['PH']  = 4;
            break;
        case 'VC':
            $result['CSp'] = 4;
            $result['RI']  = 3;
            break;
        case 'REC':
            $result['CSp']      = 4;
            $result['RC']       = 6;
            $result['Bouclier'] = 2;
            break;
        case 'SE':
            $result['CSp'] = 3;
            $result['RC']  = 3;
            $result['Esp'] = 2;
            break;
        case 'BMD':
            $result['CSp']    = 8;
            $result['RI']     = 6;
            $result['Plasma'] = 5;
            break;
        case 'DST':
            $result['CSp'] = 9;
            $result['Hyp'] = 5;
            $result['PH']  = 6;
            break;
        case 'EDLM':
            $result['CSp']      = 12;
            $result['Hyp']      = 6;
            $result['PH']       = 7;
            $result['Graviton'] = 1;
            break;
        case 'TRA':
            $result['CSp']   = 8;
            $result['Hyp']   = 5;
            $result['PH']    = 5;
            $result['Laser'] = 12;
            break;
        case 'SAT':
            $result['CSp'] = 1;
            break;
        case 'FOR':
            $result['CSp']        = 5;
            $result['RC']         = 4;
            $result['Protection'] = 4;
            $result['Laser']      = 4;
            $result['COL']        = true;
            break;
        case 'FAU':
            $result['CSp']      = 10;
            $result['Hyp']      = 6;
            $result['PH']       = 7;
            $result['Bouclier'] = 6;
            $result['GEN']      = true;
            break;
        case 'ECL':
            $result['CSp'] = 5;
            $result['PH']  = 2;
            $result['EXP'] = true;
            break;
// Défenses :
        case 'LM':
            $result['CSp'] = 1;
            break;
        case 'LLE':
            $result['CSp']   = 2;
            $result['Laser'] = 3;
            break;
        case 'LLO':
            $result['CSp']   = 4;
            $result['NRJ']   = 3;
            $result['Laser'] = 6;
            break;
        case 'CG':
            $result['CSp']      = 6;
            $result['NRJ']      = 6;
            $result['Armes']    = 3;
            $result['Bouclier'] = 1;
            break;
        case 'AI':
            $result['CSp']  = 4;
            $result['Ions'] = 4;
            break;
        case 'LP':
            $result['CSp']    = 8;
            $result['Plasma'] = 7;
            break;
        case 'PB':
            $result['CSp']      = 1;
            $result['Bouclier'] = 2;
            break;
        case 'GB':
            $result['CSp']      = 6;
            $result['Bouclier'] = 6;
            break;
        case 'MIC':
            $result['CSp']  = 1;
            $result['Silo'] = 1;
            break;
        case 'MIP':
            $result['CSp']  = 1;
            $result['Silo'] = 4;
            $result['RI']   = 1;
            break;
        default:
            break;
    }
    //fill with other building/research/fleet/defence
    foreach (array_merge($names['BAT'], $names['RECH']) as $element) {
        if (!isset($result[$element])) {
            $result[$element] = 0;
        }
    }
    if ($result['COL'] || $result['GEN'] || $result['EXP']) {
        $result['none'] = false;
    }

    return $result;
}

/**
 *  @brief Calculates technical data of Ogame requirement of all building/research/fleet/defence.
 *  
 *  @return array of all building/research/fleet/defence with are array of requirement from ogame_elements_requirement()
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
 *  @brief Calculates cumulate lab network.
 *  
 *  @param[in] array $user_empire       From user_get_empire()
 *  @param[in] int   $current_planet_id Current planet to run a research, if not best lab (theory).
 *  @return int Number of cumulate lab network
 */
function ogame_labo_cumulate($user_empire, $current_planet_id = -1)
{
    $result = 0;
//Valeurs IN par défaut :
    if (!isset($user_empire['technology']['RRI']) || !is_numeric($user_empire['technology']['RRI'])) { $user_empire['technology']['RRI'] = 0; }

    $labs        = array();
    $current_lab = -1;
    $nb_labo     = 1 + $user_empire['technology']['RRI'];

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
    for ($i = 0 ; $i < $nb_labo ; $i++) {
        $result += $labs[$i];
    }
    
    return $result;
}

/**
 *  @brief Calculates construction time of a OGame element bat/vso/def/rech.
 *  
 *  @param[in] string $name          The name, like name in Database
 *  @param[in] int    $level         The level or number for def/vso
 *  @param[in] array  $user_building Array of bat level ('CSp','UdR','UdN','Lab')
 *  @param[in] int    $cumul_labo    Number of cumulate lab network (only for rech)
 *  @param[in] array  $user_class    User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 *  @return float Time in seconds
 */
function ogame_construction_time($name, $level, $user_building, $cumul_labo = 0, $user_class = 'none')
{
    static $RECH_BONUS_EXP = 0.25;   //-25% temps de recherche
//Valeurs OUT par défaut :
    $result = 0;
//Valeurs IN par défaut :
    if (!isset($user_building['CSp']) || !is_numeric($user_building['CSp'])) { $user_building['CSp'] = 0; }
    if (!isset($user_building['UdR']) || !is_numeric($user_building['UdR'])) { $user_building['UdR'] = 0; }
    if (!isset($user_building['UdN']) || !is_numeric($user_building['UdN'])) { $user_building['UdN'] = 0; }
    if (!isset($user_building['Lab']) || !is_numeric($user_building['Lab'])) { $user_building['Lab'] = 0; }

    if ($cumul_labo === 0) {
        $cumul_labo = $user_building['Lab'];
    }
    $type = ogame_is_element($name);
    $cout = ogame_element_upgrade($name, $level);
    switch ($type) {
        case 'BAT':
            //(Métal + Cristal) / (2500 * MAX(4 - niveau / 2; 1) * (1 + niveau Usine de robots) * 2^niveau Usine de Nanites )
            $result = ($cout['M'] + $cout['C']);
            $tmp    = 2500 * max(4 - $level / 2, 1);
            $tmp   *= (1 + $user_building['UdR']) * pow(2, $user_building['UdN']);
            $result = $result / $tmp;
            break;
        case 'VSO': //no break
        case 'DEF':
            $cout = ogame_element_cumulate($name, $level);
            //(cristal + métal)/5000 * 2/(1 + niveau chantier spatial) * 0,5^niveau nanites
            $result  = ($cout['M'] + $cout['C']) / 5000;
            $result *= 2 / (1 + $user_building['CSp']);
            $result *= pow(0.5, $user_building['UdN']);
            break;
        case 'RECH':
            //(métal + cristal) / (1000 * (1 + niveau labo + n meilleurs niveaux des labos autres que le labo de la planète effectuant la recherche))
            $result = ($cout['M'] + $cout['C']) / (1000 * (1 + $cumul_labo));
            if ($user_class === 'EXP') {
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
//php8 -r "define('IN_SPYOGAME',true);include('includes/ogame.php');var_dump($a=ogame_construction_time('NRJ',21,array('Lab'=>18,'CSp'=>12,'UdR'=>10,'UdN'=>7),234));echo gmdate('z:H:m:s',$a);"

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

/**
 *  @brief Calculates phalanx range.
 *  
 *  @param[in] int   $level         Level of the phalanx
 *  @param[in] array $user_class    User class ($user_data['user_class']=array('user_class'=>'COL'/GEN/EXP/none))
 *  @return Range in system
 */
function ogame_phalanx_range($level, $user_class = 'none')
{
    static $PHA_BONUS_EXP = 0.2;   //-20%

    $bonus_class = 0;
    if ($user_class === 'EXP') {
        $bonus_class = $PHA_BONUS_EXP;
    }

    return floor( (pow($level, 2) - 1) * (1 + $bonus_class) );
}

/**
 *  @brief Calculates MIP range.
 *  
 *  @param[in] int $impulsion Techno impulsion (RI)
 *  @return int Range in system
 */
function ogame_missile_range($impulsion = 1)
{
    return 5 * $impulsion - 1;
}

/**
 *  @brief Calculates MIP speed.
 *  
 *  @param[in] int $nb_system Number of sub-system from current planet
 *  @param[in] int $speed_uni Univers speed
 *  @return int Speed in seconds
 */
function ogame_missile_speed($nb_system, $speed_uni = 1)
{
    return (30 + 60 * $nb_system) * $speed_uni;
}




