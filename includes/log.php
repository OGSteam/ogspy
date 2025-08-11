<?php

/**
 * OGSpy Log Functions
 * @package OGSpy
 * @subpackage Log
 * @author Kyser
 * @copyright Copyright &copy; 2012, https://www.ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.1.1 ($Rev: 7690 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * File Log size on the Server
 * @return Array tableau [type] and [size]
 */
function log_size_info()
{
    $logSize = 0;
    $res = opendir(PATH_LOG);
    $directory = array();
    //Récupération de la liste des fichiers présents dans les répertoires répertoriés
    while ($file = readdir($res)) {
        if ($file != "." && $file != "..") {
            if (is_dir(PATH_LOG . $file)) {
                $directory[] = PATH_LOG . $file;
            }
        }
    }
    closedir($res);

    foreach ($directory as $v) {
        $res = opendir($v);
        $directory = array();
        //Récupération de la liste des fichiers présents dans les répertoires répertoriés
        while ($file = readdir($res)) {
            if ($file != "." && $file != "..") {
                $logSize += @filesize($v . "/" . $file);
            }
        }
        closedir($res);
    }

    $bytes = array('Octets', 'Ko', 'Mo', 'Go', 'To');

    if ($logSize < 1024) {
        $logSize = 1;
    }

    for ($i = 0; $logSize > 1024; $i++) {
        $logSize /= 1024;
    }

    $log_size_info['size'] = round($logSize, 2);
    $log_size_info['type'] = $bytes[$i];

    return $log_size_info;
}

/**
 * Checks if a log file exists for a specified date in the defined log directory.
 *
 * @param string $date The date to search for log files. Expected format is either AAAAMM or AAAAMMJJ.
 * @return bool Returns true if a corresponding log file is found, otherwise false.
 */
function log_check_exist(string $date)
{
    global $log;

    // Valide le format AAAAMM ou AAAAMMJJ
    if (empty($date) || !preg_match('/^(\d{6}|\d{8})$/', $date)) {
        $log->warning("Format de date invalide pour log_check_exist", ['date' => $date]);
        return false;
    }

    $root = PATH_LOG;
    if (!is_dir($root) || !($path = opendir($root))) {
        $log->error('Log directory not found or cannot be read', ['path' => $root]);
        return false;
    }

    $log_found = false;
    // Extrait l'année et le mois
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);

    // Construit le pattern en fonction du format de date (mois ou jour spécifique)
    if (strlen($date) === 8) {
        // Format AAAAMMJJ : recherche un fichier pour un jour précis
        $day = substr($date, 6, 2);
        $pattern = '/^OGSpy-' . $year . '-' . $month . '-' . $day . '\.log$/';
    } else {
        // Format AAAAMM : recherche n'importe quel fichier du mois
        $pattern = '/^OGSpy-' . $year . '-' . $month . '-\d{2}\.log$/';
    }

    // Recherche de fichiers de log correspondant au format OGSpy-AAAA-MM-JJ.log
    while (($file = readdir($path)) !== false) {
        if (preg_match($pattern, $file)) {
            $log_found = true;
            break;
        }
    }
    closedir($path);
    return $log_found;
}
