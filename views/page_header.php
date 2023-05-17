<?php

/**
 * HTML Header
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Helper\ToolTip_Helper;
?>
<!DOCTYPE html>
<html lang="<?php echo ($lang['HEAD_LANGUAGE']); ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"]; ?></title>
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formateredesign.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/tooltipster/dist/css/tooltipster.bundle.min.css" />
    <link rel="icon" type="image/icon" href="favicon.ico">

    <!-- Ajout des scripts Graphique (highcharts), jquery et prototype-->

    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="js/highcharts.js"></script>
    <script src="js/autocomplete.js"></script>
    <script src="vendor/tooltipster/tooltipster/dist/js/tooltipster.bundle.min.js"></script>
    <script src="js/ogame_formula.js"></script>
    <script src="js/ogspy.js"></script>
    <?php echo (new ToolTip_Helper())->activateJs(); ?>
</head>

<body onload="ogspy_run();">
    
    <header id="ban">
        <img alt="Logo OGSpy" src="./skin/OGSpy_skin/<?php echo  $banner_selected; ?>">
    </header> <!-- fin header Banniere ogspy -->
    <nav id="navbar">
        <?php require_once("menu.php"); ?>
    </nav><!-- fin partie navigation principale -->
    
<section id="content"> <!-- Contenu principal Attention, fermeture dans le footer / compat legacy -->
