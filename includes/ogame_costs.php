<?php
/** @file includes/ogame_costs.php
 * Cost and cumulate related functions extracted from ogame.php
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
// helpers are required centrally from common.php

/**
 * @see ogame_element_cout_base() in includes/ogame.php (original)
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

function ogame_all_cumulate($user, $type)
{
    $total = 0;

    if ($type === 'RECH') {
        $data = $user;  //1 seul array, les technos
    } else {
        $data = current($user); //plusieurs array, les planètes/lunes, donc juste la 1er
    }
    while ($data) {
        if (!is_array($data)) {
            break;
        }
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

function all_building_cumulate($user_building)
{
    return ogame_all_cumulate($user_building, 'BAT');
}

function all_defense_cumulate($user_defence)
{
    return ogame_all_cumulate($user_defence, 'DEF');
}

function all_fleet_cumulate($user_fleet)
{
    return ogame_all_cumulate($user_fleet, 'VSO');
}

function all_technology_cumulate($user_techno)
{
    return ogame_all_cumulate($user_techno, 'RECH');
}

function all_lune_cumulate($user_building, $user_defense)
{
    return all_defense_cumulate($user_defense) + all_building_cumulate($user_building);
}

function ogame_building_destroy($name, $level, $techno_ions = 0)
{
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
