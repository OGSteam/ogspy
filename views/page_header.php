<?php
/**
 * HTML Header
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="language" content="<?php echo($lang['HEAD_LANGUAGE']); ?>">
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"];?></title>
    <link rel="stylesheet" type="text/css" href="./assets/css/formate.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/icon" href="favicon.ico">

    <!-- Ajout des scripts Graphique (highcharts), jquery-->

    <script type="text/javascript" src="vendor/components/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/highcharts.js"></script>
</head>
<body>
<table id="maintable">
    <tr>
        <td width="180" align="center" valign="top" rowspan="4"><?php require_once("menu.php");?></td>
        <td height="70">
            <div align="center"><img src="./assets/default_skin/<?php echo  $banner_selected; ?>"></div>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
