<?php
/**
 * HTML Header Light
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo($lang['HEAD_LANGUAGE']); ?>" lang="<?php echo($lang['HEAD_LANGUAGE']); ?>">
<head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"]; ?></title>
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formate.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
    <link rel="icon" type="image/icon" href="favicon.ico"/>
</head>
<body>
<table style="text-align:center; width:100%; padding:20px;">
    <tr>
        <td>
            <img style="margin-bottom:30px;" alt="Logo OGSpy" src="./skin/OGSpy_skin/<?php echo $banner_selected; ?>"/>
        </td>
    </tr>
    <tr>
        <td>