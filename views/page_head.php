<?php
/**
 * HTML Header
 */
// deprécié
if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}
use Ogsteam\Ogspy\Helper\ToolTip_Helper;
?>
<!DOCTYPE html>
<html lang="<?php echo($lang['HEAD_LANGUAGE']); ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo $server_config['servername'] . ' - OGSpy ' . $server_config['version']; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/icon" href="favicon.ico">
    <!-- old skin-->
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formate.css">
    <!-- sera remplacer par !-->
    <link rel="stylesheet" type="text/css" href="./skin/src/default/ogspy.css">

    <!-- Ajout des scripts Graphique (highcharts), jquery et prototype-->
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/tooltipster/dist/css/tooltipster.bundle.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/highcharts.js"></script>
    <script src="vendor/tooltipster/tooltipster/dist/js/tooltipster.bundle.min.js"></script>
<?php echo (new ToolTip_Helper())->activateJs(); ?>
</head>
<body>
<div class="main">
    <div class="ban"> <!--logo ogsteam !-->
        <!--<img src="./skin/OGSpy_skin/<?php echo  $banner_selected; ?>">-->
    </div>