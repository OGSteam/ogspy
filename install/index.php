<?php
/**
 * Fichier d'installation d'ogspy : ROOT/install/index.php
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04
 * @since 3.04 - 26 sept. 07
 */

define("IN_SPYOGAME", true);
define("INSTALL_IN_PROGRESS", true);

require_once("../common.php");
require_once("version.php");
if (isset($pub_redirection)) {
	switch ($pub_redirection) {
		case "install";
			redirection("install.php?lang=".$ui_lang);
			break;

		case "upgrade";
			redirection("upgrade_to_latest.php?lang=".$ui_lang);
			break;
	}
}

?>
<html>
<head>
	<title>OGSpy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="fr">
	<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<table width="100%" align="center" cellpadding="20">
	<tr>
		<td height="70"><div align="center"><img src="../images/OgameSpy2.jpg"></div></td>
	</tr>
	<tr>
		<td height="50"><div align="center">
                <a href="index.php?lang=fr"><img src="../images/i18n/France.png"></a>
                <a href="index.php?lang=en"><img src="../images/i18n/United_Kingdom.png"></a>
				<a href="index.php?lang=pt_BR"><img src="../images/i18n/Brazil.png"></a>
                <a href="index.php?lang=es"><img src="../images/i18n/Spain.png"></a>
            </div>
        </td>
	</tr>
	<tr>
		<td align="center">
			<table>
				<tr>
					<td align="center"><span
							style="font-size: small; "><b><?php echo $lang['INSTALL_WELCOME']; ?></b></span></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<span style="font-size: x-small; ">
							<ul>
								<li><?php echo $lang['INSTALL_PROJECT_L1']; ?></li>
								<li><?php echo $lang['INSTALL_PROJECT_L2']; ?></li><br>
                                <ul>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L3']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L4']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L5']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L6']; ?></li>
								</ul>
                            </ul>
							<div style="text-align: center;"><?php echo $lang['INSTALL_FORUM']; ?></div>
						</span>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<?php
				if(!(version_compare(PHP_VERSION, "5.2.0") >= 0)){
					echo "<tr><td style='font-size: 24px;'><span style=\"color: red; \">" .$lang['INSTALL_PHPERROR']. "</span></td></tr>";
					echo "<tr><td><span style=\"color: red; \">" .$lang['INSTALL_PHP_ADVISE'];
					echo "<br/><br/>".$lang['INSTALL_PHPVERSION'].PHP_VERSION;
					echo "</font></td></tr>";
				}else{
				?>
				<tr><td>&nbsp;</td></tr>
				<form action="index.php?lang=<?php echo($ui_lang); ?>" method="POST">
					<tr>
						<td align="center"><span style="color: orange; "><b><?php echo $lang['INSTALL_ACTION']; ?></b></span>
							<select name="redirection" onchange="this.form.submit();" onkeyup="this.form.submit();">
								<option></option>
								<option value="install"><?php echo $lang['INSTALL_ACTION_FULL']; ?></option>
								<option value="upgrade"><?php echo $lang['INSTALL_ACTION_UPGRADE']; ?></option>
							</select>
						</td>
					</tr>
				</form>
			</table>
		</td>
	</tr>
	<?php
	} // Fin Version compare
	?>
	<tr align="center">
		<td>
			<div style="text-align: center;font-size: x-small;"><i><b>OGSpy</b> is an <b>OGSteam Software</b> (c) 2005-2015</i><br>v <?php echo $install_version ;?></div>
		</td>
	</tr>
</table>
</body>
</html>
