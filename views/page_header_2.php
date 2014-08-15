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
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset="UTF-8" />
		<meta name="language" content="fr" />
		<title><?php echo $server_config["servername"]." - OGSpy ".$server_config["version"];?></title>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $link_css;?>formate.css" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="icon" href="favicon.ico" type="image/icon" />
	</head>
	
	<body>
		<table width="100%" align="center" cellpadding="20">
			<tr>
				<td height="70">
					<div align="center">
						<img src="<?php echo $server_config["default_skin"].$banner_selected;?>" />
					</div>
				</td>
			</tr>
			
			<tr>
				<td>