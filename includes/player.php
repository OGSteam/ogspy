<?php

use Ogsteam\Ogspy\Model\Combat_Report_Model;
use Ogsteam\Ogspy\Model\Player_Building_Model;
use Ogsteam\Ogspy\Model\Player_Defense_Model;
use Ogsteam\Ogspy\Model\Player_Technology_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Récupération des données empire de l'utilisateur loggé.
 * @comment On pourrait mettre un paramètre $user_id optionnel
 * @param $player_id
 * @return array
 */
function player_get_empire($player_id)
{
    $planet = array(
        false, "user_id" => "", "planet_name" => "", "coordinates" => "",
        "fields" => 0, "fields_used" => 0, "boosters" => booster_encode(),
        "temperature_min" => 0, "temperature_max" => 0,
        "Sat" => 0, "Sat_percentage" => 100, "FOR" => 0, "FOR_percentage" => 100,
        "M" => 0, "M_percentage" => 100, "C" => 0, "C_percentage" => 100, "D" => 0, "D_percentage" => 100,
        "CES" => 0, "CES_percentage" => 100, "CEF" => 0, "CEF_percentage" => 100,
        "UdR" => 0, "UdN" => 0, "CSp" => 0, "HM" => 0, "HC" => 0, "HD" => 0, "Lab" => 0,
        "Ter" => 0, "Silo" => 0, "Dock" => 0, "BaLu" => 0, "Pha" => 0, "PoSa" => 0, "DdR" => 0
    );

    $defense = ["LM" => 0, "LLE" => 0, "LLO" => 0, "CG" => 0, "AI" => 0, "LP" =>
        0, "PB" => 0, "GB" => 0, "MIC" => 0, "MIP" => 0];


    $tBuildingList = (new Player_Building_Model())->select_player_building_list($player_id);

    // mise en forme des valeurs
    foreach ($tBuildingList as $BuildingList) {
        //préparationpct
        $pct = [];
        $pct["M_percentage"] = $BuildingList["M_percentage"];
        $pct["C_percentage"] = $BuildingList["C_percentage"];
        $pct["D_percentage"] = $BuildingList["D_percentage"];
        $pct["CES_percentage"] = $BuildingList["CES_percentage"];
        $pct["CEF_percentage"] = $BuildingList["CEF_percentage"];
        $pct["Sat_percentage"] = $BuildingList["Sat_percentage"];
        $pct["FOR_percentage"] = $BuildingList["FOR_percentage"];


        //calcul des cases utilisées
        $arr = $BuildingList;
        unset($arr["planet_id"]);
        unset($arr["planet_name"]);
        unset($arr["coordinates"]);
        unset($arr["fields"]);
        unset($arr["boosters"]);
        unset($arr["temperature_min"]);
        unset($arr["temperature_max"]);
        unset($arr["Sat"]);
        unset($arr["Sat_percentage"]);
        unset($arr["FOR"]);
        unset($arr["FOR_percentage"]);
        unset($arr["M_percentage"]);
        unset($arr["C_percentage"]);
        unset($arr["D_percentage"]);
        unset($arr["CES_percentage"]);
        unset($arr["CEF_percentage"]);
        unset($arr["Dock"]);
        $fields_used = array_sum(array_values($arr));

        $BuildingList["fields_used"] = $fields_used;

        // modification Booster
        $BuildingList["boosters"] = booster_verify_str($BuildingList["boosters"]); //Correction et mise à jour booster from date
        $BuildingList["booster_tab"] = booster_decode($BuildingList["boosters"]); // ajout booster dans get_empire
        // incrémentation field
        if (isset($BuildingList["moon_id"])) {
            $BuildingList["fields"] += $BuildingList["booster_tab"]["extention_m"];
        } else {
            $BuildingList["fields"] += $BuildingList["booster_tab"]["extention_p"];
        }

        $player_building[$BuildingList["id"]] = $BuildingList;
        $player_building[$BuildingList["id"]][0] = true;
    }

    $player_technology = (new Player_Technology_Model())->select_user_technologies($player_id);
    $technology_list = array("Esp","Ordi","Armes","Bouclier","Protection","NRJ","Hyp","RC","RI","PH","Laser","Ions","Plasma","RRI","Graviton","Astrophysique");
    foreach ($technology_list as $technologyName) {
        if(!isset($player_technology[$technologyName]))
        {
            $player_technology[$technologyName]=""; // alimentation des technology tel qu'attendu dans page empire ("" et non 0)
        }
    }


    $tDefenseList = (new Player_Defense_Model())->select_player_defense($player_id);
    $player_defense = [];
    foreach ($tDefenseList as $tmpDefense) {
        $planet_id = $tmpDefense["id"];
        unset($tmpDefense["id"]);
        $player_defense[$planet_id] = $tmpDefense;
    }

    return array(
        "building" => $player_building, "technology" => $player_technology, "defense" => $player_defense, "user_percentage" => $pct
    );
}

/**
 * Suppression des données de batiments de l'utilisateur loggé
 */
function user_del_building()
{
    global $user_data;
    global $pub_planet_id, $pub_view;

    $User_Building_Model = new Player_Building_Model();
    $User_Defense_Model = new Player_Defense_Model();

    if (!check_var($pub_planet_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!isset($pub_planet_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $User_Building_Model->delete_user_aster($user_data["player_id"], intval($pub_planet_id)); //batiment
    $User_Defense_Model->delete_user_aster($user_data["player_id"], intval($pub_planet_id)); //defense


    // si on supprime une planete; la lune doit suivre
    if (intval($pub_planet_id) < 199) {
        $moon_id = (intval($pub_planet_id) + 100);
        $User_Building_Model->delete_user_aster($user_data["player_id"], $moon_id); //batiment
        $User_Defense_Model->delete_user_aster($user_data["player_id"], $moon_id); //defense
    }

    //si plus de planete
    $iNBPlanet = $User_Building_Model->get_nb_planets($user_data["player_id"]);
    if ($iNBPlanet == 0) {
        (new Player_Technology_Model())->delete_user_technologies($user_data["player_id"]);
    }

    if ($iNBPlanet != 0) {
        // remise en ordre des planetes :
        //uniquement s'il en reste
        user_set_all_empire_resync_id();

    }
    redirection("index.php?action=home&subaction=empire&view=" . $pub_view);
}

/**
 * Récuperation du nombre de  planete de l utilisateur.
 *
 * @param $player_id
 * @return int|the
 */
function find_nb_planete_user($player_id)
{
    return (new Player_Building_Model())->get_nb_planets($player_id);
}

/**
 * @param $user_id
 * @return int Nb of moons
 */
function find_nb_moon_user($player_id)
{
    return (new Player_Building_Model())->get_nb_moons($player_id);
}

/**
 * Reconstruction des RC
 * @param int $id_RC RC à reconstituer
 * @return string $template_RC reconstitué
 *
 * TODO : fonctionne-t-elle ? Pleins de variables non utilisées.
 */
function UNparseRC($id_RC)
{
    global $lang;

    $Combat_Report_Model = new Combat_Report_Model();

    $key_ships = array(
        'PT' => $lang['GAME_FLEET_PT_S'], 'GT' => $lang['GAME_FLEET_GT_S'], 'CLE' => $lang['GAME_FLEET_CLE_S'],
        'CLO' => $lang['GAME_FLEET_CLO_S'], 'CR' => $lang['GAME_FLEET_CR_S'], 'VB' => $lang['GAME_FLEET_VB_S'],
        'VC' => $lang['GAME_FLEET_VC_S'], 'REC' => $lang['GAME_FLEET_REC_S'], 'SE' => $lang['GAME_FLEET_SE_S'],
        'BMD' => $lang['GAME_FLEET_BMD_S'], 'DST' => $lang['GAME_FLEET_DST_S'], 'EDLM' => $lang['GAME_FLEET_EDLM_S'],
        'SAT' => $lang['GAME_FLEET_SAT_S'], 'TRA' => $lang['GAME_FLEET_TRA_S'], 'ECL' => $lang['GAME_FLEET_ECL'],
        'FAU' => $lang['GAME_FLEET_FAU'], 'FOR' => $lang['GAME_FLEET_FOR']
    );
    $key_defs = array(
        'LM' => $lang['GAME_DEF_LM_S'], 'LLE' => $lang['GAME_DEF_LLE_S'], 'LLO' => $lang['GAME_DEF_LLO_S'],
        'CG' => $lang['GAME_DEF_CG_S'], 'AI' => $lang['GAME_DEF_AI_S'], 'LP' => $lang['GAME_DEF_LP_S'], 'PB' =>
            $lang['GAME_DEF_PB_S'], 'GB' => $lang['GAME_DEF_GB_S']
    );
    $base_ships = array(
        'PT' => array(4000, 10, 5), 'GT' => array(12000, 25, 5),
        'CLE' => array(4000, 10, 50), 'CLO' => array(10000, 25, 150), 'CR' => array(
            27000,
            50, 400
        ), 'VB' => array(60000, 200, 1000), 'VC' => array(30000, 100, 50), 'REC' =>
            array(16000, 10, 1), 'SE' => array(1000, 0, 0), 'BMD' => array(75000, 500, 1000),
        'DST' => array(110000, 500, 2000), 'EDLM' => array(9000000, 50000, 200000),
        'SAT' => array(2000, 1, 1), 'TRA' => array(70000, 400, 700),
        'ECL' => array(23000, 100, 200), 'FAU' => array(140000, 700, 2800), 'FOR' => array(4000, 1, 1)
    );
    $base_defs = array(
        'LM' => array(2000, 20, 80), 'LLE' => array(2000, 25, 100),
        'LLO' => array(8000, 100, 250), 'CG' => array(35000, 200, 1100), 'AI' => array(
            8000,
            500, 150
        ), 'LP' => array(100000, 300, 3000), 'PB' => array(20000, 2000, 1), 'GB' =>
            array(100000, 10000, 1)
    );

    // Récupération des constantes du RC

    $RC = $Combat_Report_Model->get_combat_report($id_RC);

    // mise en forme des data pour affichage
    $dateRC = $RC["dateRC"];
    $coordinates = $RC["coordinates"];
    // $nb_rounds = $RC["nb_rounds"];
    $victoire = $RC["victoire"];
    $pertes_A = $RC["pertes_A"];
    $pertes_D = $RC["pertes_D"];
    $gain_M = $RC["gain_M"];
    $gain_C = $RC["gain_C"];
    $gain_D = $RC["gain_D"];
    $debris_M = $RC["debris_M"];
    $debris_C = $RC["debris_C"];
    $lune = $RC["lune"];
    $tRounds = $RC["rounds"];


    $dateRC = date($lang['GAME_CREPORT_DATE'], $dateRC);
    $template = $lang['GAME_CREPORT_FIGHT'] . ' (' . $dateRC . "):\n\n";

    // Récupération de chaque round du RC
    foreach ($tRounds as $round) {
        // round de Départ (0) inutile
        if ($round['numround'] === '0') continue;

        // mise en forme des data pour affichage
        // $id_rcround = $round["id_rcround"];
        $attaque_tir = $round["attaque_tir"];
        $attaque_puissance = $round["attaque_puissance"];
        $attaque_bouclier = $round["attaque_bouclier"];
        $defense_tir = $round["defense_tir"];
        $defense_puissance = $round["defense_puissance"];
        $defense_bouclier = $round["defense_bouclier"];

        // On formate les résultats
        $nf_gain_M = number_format($gain_M, 0, ',', '.');
        $nf_gain_C = number_format($gain_C, 0, ',', '.');
        $nf_gain_D = number_format($gain_D, 0, ',', '.');
        $nf_pertes_A = number_format($pertes_A, 0, ',', '.');
        $nf_pertes_D = number_format($pertes_D, 0, ',', '.');
        $nf_debris_M = number_format($debris_M, 0, ',', '.');
        $nf_debris_C = number_format($debris_C, 0, ',', '.');
        $nf_attaque_tir = number_format($attaque_tir, 0, ',', '.');
        $nf_attaque_puissance = number_format($attaque_puissance, 0, ',', '.');
        $nf_attaque_bouclier = number_format($attaque_bouclier, 0, ',', '.');
        $nf_defense_tir = number_format($defense_tir, 0, ',', '.');
        $nf_defense_puissance = number_format($defense_puissance, 0, ',', '.');
        $nf_defense_bouclier = number_format($defense_bouclier, 0, ',', '.');


        // Récupération de chaque attaquant du RC
        $idx = 1;
        foreach ($round["attacks"] as $attak) {
            $player = $attak["player"];
            $coordinates = $attak["coordinates"];
            $Armes = $attak["Armes"];
            $Bouclier = $attak["Bouclier"];
            $Protection = $attak["Protection"];
            // $PT = $attak["PT"];
            // $GT = $attak["GT"];
            // $CLE = $attak["CLE"];
            // $CLO = $attak["CLO"];
            // $CR = $attak["CR"];
            // $VB = $attak["VB"];
            // $VC = $attak["VC"];
            // $REC = $attak["REC"];
            // $SE = $attak["SE"];
            // $BMD = $attak["BMD"];
            // $DST = $attak["DST"];
            // $EDLM = $attak["EDLM"];
            // $TRA = $attak["TRA"];
            // $ECL = $attak["ECL"];
            // $FAU = $attak["FAU"];


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
                if (isset($key) && $key > 0) {
                    $vivant_att = true;
                    $ship_type .= "\t" . $ship;
                    $ship_nombre .= "\t" . number_format(intval($key), 0, ',', '.');;
                    $ship_protection .= "\t" . number_format(round(($base_ships[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_ships[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_ships[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            if ($vivant_att === true) {
                $template .= ' [' . $coordinates . ']';
                //Ligne Techno
                if ($idx === 1) {
                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                }
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else {
                $template .= ' détruit.' . "\n\n";
            }

            $idx++;
        }

        // Récupération de chaque defenseur du RC
        // Récupération de chaque attaquant du RC
        $idx = 1;
        foreach ($round["defenses"] as $defenses) {

            $player = $defenses["player"];
            $coordinates = $defenses["coordinates"];
            $Armes = $defenses["Armes"];
            $Bouclier = $defenses["Bouclier"];
            $Protection = $defenses["Protection"];
            // $PT = $defenses["PT"];
            // $GT = $defenses["GT"];
            // $CLE = $defenses["CLE"];
            // $CLO = $defenses["CLO"];
            // $CR = $defenses["CR"];
            // $VB = $defenses["VB"];
            // $VC = $defenses["VC"];
            // $REC = $defenses["REC"];
            // $SE = $defenses["SE"];
            // $BMD = $defenses["BMD"];
            // $DST = $defenses["DST"];
            // $EDLM = $defenses["EDLM"];
            // $TRA = $defenses["TRA"];
            // $ECL = $attak["ECL"];
            // $FOR = $attak["FOR"];
            // $FAU = $attak["FAU"];

            // $SAT = $defenses["SAT"];

            // $LM = $defenses["LM"];
            // $LLE = $defenses["LLE"];
            // $LLO = $defenses["LLO"];
            // $CG = $defenses["CG"];
            // $AI = $defenses["AI"];
            // $LP = $defenses["LP"];
            // $PB = $defenses["PB"];
            // $GB = $defenses["GB"];


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

            if ($vivant_def === true) {
                $template .= ' [' . $coordinates . ']';
                //Ligne Technos
                if ($idx == 1) {
                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                }
                //Ligne Vaisseaux
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else {
                $template .= ' ' . $lang['GAME_CREPORT_DESTROYED'] . ' ' . "\n\n";
            }
            $idx++;
        } // Fin récupération de chaque défenseur du RC


        // Résultat du round
        if ($attaque_tir !== 0 || $defense_tir !== 0) {
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
    switch ($victoire) {
        case 'N':
            $template .= $lang['GAME_CREPORT_RESULT_EVEN'] . '.' .
                "\n\n";
            break;
        case 'A':
            $template .= $lang['GAME_CREPORT_RESULT_WIN'] . ' ';
            if (isset($nf_gain_M) && isset($nf_gain_C) && isset($nf_gain_D)) {
                $template .= $nf_gain_M . ' ' . $lang['GAME_CREPORT_RESULT_WIN_1'] . ', ' . $nf_gain_C . ' ' . $lang['GAME_CREPORT_RESULT_WIN_2'] . ' ' . $nf_gain_D .
                    ' ' . $lang['GAME_CREPORT_RESULT_WIN_3'] . '.' . "\n\n";
            }
            break;
        case 'D':
            $template .= $lang['GAME_CREPORT_RESULT_LOST'] . "\n\n";
            break;
    }

    // Pertes et champs de débris
    if (isset($nf_pertes_A) && isset($nf_pertes_D)) {
        $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_A'] . ' ' . $nf_pertes_A . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
        $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_D'] . ' ' . $nf_pertes_D . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
    }
    if (isset($nf_debris_M) && isset($nf_debris_C)) {
        $template .= $lang['GAME_CREPORT_RESULT_DEBRIS'] . ' ' . $nf_debris_M .
            ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_M'] . ' ' . $nf_debris_C . ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_C'] .
            "\n";
    }
    $lunePourcent = floor(($debris_M + $debris_C) / 100000);
    $lunePourcent = ($lunePourcent < 0 ? 0 : ($lunePourcent > 20 ? 20 : $lunePourcent));
    if ($lunePourcent > 0) {
        $template .= $lang['GAME_CREPORT_RESULT_NO_MOON'] . ' ' . $lunePourcent . ' %';
    }

    if ($lune === 1) {
        $template .= "\n" . $lang['GAME_CREPORT_RESULT_MOON'] . ".";
    }

    return ($template);
}
