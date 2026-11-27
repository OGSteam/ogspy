<?php
/** @file includes/ogame_structs.php
 * Small helpers extracted from the original ogame.php file (array builders).
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Builds an OGame resource array.
 *
 * @param int $metal
 * @param int $cristal
 * @param int $deut
 * @param int $NRJ
 * @param int $AM
 * @return array('M','C','D','NRJ','AM')
 */
function ogame_array_ressource($metal, $cristal, $deut, $NRJ = 0, $AM = 0)
{
    return array('M' => $metal, 'C' => $cristal, 'D' => $deut, 'NRJ' => $NRJ, 'AM' => $AM);
}

/**
 * Builds an OGame detail array for fleet/defense.
 *
 * @param int $structure
 * @param int $bouclier
 * @param int $attaque
 * @param int $vitesse
 * @param int $fret
 * @param int $conso
 * @param bool $civil
 * @return array
 */
function ogame_array_detail($structure, $bouclier, $attaque, $vitesse = 0, $fret = 0, $conso = 0, $civil = true)
{
    return array(
        'structure' => $structure, 'bouclier' => $bouclier, 'attaque' => $attaque,
        'vitesse' => $vitesse, 'fret' => $fret, 'conso' => $conso,
        'rapidfire' => array(), 'civil' => $civil
    );
}

/**
 * Returns coordinates as an array.
 *
 * @param string $string_coord Coordinates, in string like in Database ('2:3:4')
 * @return array('g','s','p') of int, default is 0 ('::6' give planet position of 6)
 */
function ogame_find_coordinates($string_coord)
{
    $result = array('g' => 0, 's' => 0, 'p' => 0);

    $coordinates_tmp = explode(':', $string_coord);
    if (count($coordinates_tmp) === 3) {
        $result['g'] = (int)$coordinates_tmp[0];
        $result['s'] = (int)$coordinates_tmp[1];
        $result['p'] = (int)$coordinates_tmp[2];
    }

    return $result;
}

/**
 * Returns the planet position from coordinates.
 * @param string $coordinates planet coordinates (galaxy:system:position)
 * @return int planet position
 */
function ogame_find_planet_position($coordinates)
{
    $position = ogame_find_coordinates($coordinates);

    return $position['p'];
}
