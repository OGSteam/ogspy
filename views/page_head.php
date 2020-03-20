<?php
/**
 * HTML Header
 */
// deprécié
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="language" content="<?php echo($lang['HEAD_LANGUAGE']); ?>">
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"]; ?></title>
    <!-- old skin-->
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formate.css"/>
    <!-- sera remplacer par !-->
    <link rel="stylesheet" type="text/css" href="./skin/src/default/ogspy.css"/>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="vendor/tooltipster/tooltipster/dist/css/tooltipster.bundle.min.css"/>
    <link rel="icon" type="image/icon" href="favicon.ico">

    <!-- Ajout des scripts Graphique (highcharts), jquery et prototype-->

    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/highcharts.js"></script>
    <script type="text/javascript" src="vendor/tooltipster/tooltipster/dist/js/tooltipster.bundle.min.js"></script>
</head>
<body>
    <div class="main">
        <div class="ban"> <!--logo ogsteam !-->
            <!--<img src="./skin/OGSpy_skin/<?php echo  $banner_selected; ?>">-->
        </div>

