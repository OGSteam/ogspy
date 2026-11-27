<?php
/** @file includes/ogame_requirements.php
 * Requirement-related functions extracted from includes/ogame.php
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * @brief Calculates technical data of Ogame requirement.
 *
 * @param[in] string $name The name, like name in Database
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
            $current_tech_level = isset($user_technology_list[$reqName]) ? $user_technology_list[$reqName] : 0;
            if ($reqValue > $current_tech_level) {
                $log->debug("Requires $reqName technology for Tech $ogame_element_name");
                return false;
            }
        }
        // prerequis bat
        if (ogame_is_a_building($reqName) && $reqValue > 0) {
            $current_building_level = isset($user_building_list[$reqName]) ? $user_building_list[$reqName] : 0;
            if ($reqValue > $current_building_level) {
                $log->debug("Requires $reqName building for Tech $ogame_element_name");
                return false;
            }
        }
    }
    // tout autre cas, les prerequis sont bons
    return true;
}
