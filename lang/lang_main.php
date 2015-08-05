<?php

//ui lang à récupérer dans parameters.php
$ui_lang="french";

if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {
	require_once ("../lang/".$ui_lang."/lang_install.php");
}else
{
	require_once ("./lang/".$ui_lang."/lang_install.php");
}






?>
