<?php
/** @file includes/ogame_elements.php
 * Element-related functions extracted from includes/ogame.php
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
// helpers are required centrally from common.php

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

function ogame_is_a_defence($nom)
{
    return ogame_is_element($nom) === 'DEF';
}

function ogame_is_a_fleet($nom)
{
    return ogame_is_element($nom) === 'VSO';
}

function ogame_is_a_building($nom)
{
    return ogame_is_element($nom) === 'BAT';
}

function ogame_is_a_research($nom)
{
    return ogame_is_element($nom) === 'RECH';
}

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

function ogame_all_details($user_techno = null, $classe = 0)
{
    $result = array();
    $names = ogame_get_element_names();

    foreach (array_merge($names['VSO'], $names['DEF']) as $element) {
        $result[$element] = ogame_elements_details($element, $user_techno, $classe);
    }

    return $result;
}
