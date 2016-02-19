<?php
/***************************************************************************
 *    filename    : page_header.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 08/12/2005
 *    modified    : 08/04/2007 06:19:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="language" content="fr">
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"];?></title>
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formate.css"/>
    <link rel="stylesheet" href="js/jquery-ui/jquery-ui.css" >
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="icon" type="image/icon" href="favicon.ico">
    <link rel="alternate" type="application/rss+xml" title="Flux RSS OGSpy" href="rss.xml"/>

    <!-- Ajout des scripts Graphique (highcharts), jquery et prototype-->

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery-ui/jquery-ui.js"></script>
    <script type="text/javascript" src="js/highcharts.js"></script>
</head>
<body>
<table id="maintable">
    <tr>
        <td width="150" align="center" valign="top" rowspan="4"><?php require_once("menu.php");?></td>
        <td height="70">
            <div align="center"><img src="./skin/OGSpy_skin/<?php echo  $banner_selected; ?>"></div>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
