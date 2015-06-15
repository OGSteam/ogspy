<?php

/***************************************************************************
 * filename : page_header_2.php
 * desc.    :
 * Author   : Kyser - http://ogsteam.fr/
 * created  : 15/12/2005
 * modified : 22/08/2006 00:00:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

if ($link_css == "") {
	$link_css = $server_config["default_skin"];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
		<meta name="language" content="fr" />
		<title><?php echo $server_config["servername"]." - OGSpy ".$server_config["version"];?></title>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $link_css;?>formate.css" />
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<link rel="icon" type="image/icon" href="favicon.ico" />
	</head>
	
	<body>
		<table style="text-align:center; width:100%; padding:20px;">
			<tr><td>
                <img style="margin-bottom:30px;" alt="Logo OGSpy" src="<?php echo $server_config["default_skin"].$banner_selected;?>" />
            </td></tr>
			
			<tr>
				<td>