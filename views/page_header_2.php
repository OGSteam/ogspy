<?php global $server_config, $lang;
/**
 * HTML Header Light
 * @package OGSpy
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?= $lang['HEAD_LANGUAGE']; ?>">
<head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8"/>
    <title><?php echo $server_config["servername"] . " - OGSpy " . $server_config["version"]; ?></title>
    <link rel="stylesheet" type="text/css" href="./skin/OGSpy_skin/formateredesign.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
    <link rel="icon" type="image/icon" href="favicon.ico"/>
</head>
<body>

    <section id="content"> <!-- Contenu principal Attention, fermeture dans le footer / compat legacy -->

