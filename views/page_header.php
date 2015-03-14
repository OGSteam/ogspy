<?php
/***************************************************************************
*	filename	: page_header.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 08/12/2005
*	modified	: 08/04/2007 06:19:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

if ($link_css == "") {
	$link_css = $server_config["default_skin"];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="fr">
<title><?php echo $server_config["servername"]." - OGSpy ".$server_config["version"];?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $link_css;?>formate.css" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/icon">
<link rel="alternate" type="application/rss+xml" title="Flux RSS OGSpy" href="rss.xml" />  

<!-- Ajout des scripts Graphique (highcharts), jquery et prototype-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"> </script>
<script language="JavaScript" src="js/highcharts.js"></script>
</head>
<body>
<table id="maintable">
<tr>
	<td width="150" align="center" valign="top" rowspan="4"><?php require_once("menu.php");?></td>
	<td height="70"><div align="center"><img src="<?php echo $server_config["default_skin"].$banner_selected;?>"></div></td>
</tr>
<tr>
	<td align="center" valign="top">
