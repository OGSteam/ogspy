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
			redirection("install.php");
			break;

		case "upgrade";
			redirection("upgrade_to_latest.php");
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
		<td align="center">
			<table>
				<tr>
					<td align="center"><font size="3"><b><?php echo $lang['INSTALL_WELCOME']; ?></b></font></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<font size="2">
							<ul>
								<li><?php echo $lang['INSTALL_PROJECT_L1']; ?></li>
								<li><?php echo $lang['INSTALL_PROJECT_L2']; ?></li><br />
                                <ul>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L3']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L4']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L5']; ?></li>
									<li type="disc"><?php echo $lang['INSTALL_PROJECT_L6']; ?></li>
								</ul>
                            </ul>
							<center><?php echo $lang['INSTALL_FORUM']; ?></center>
						</font>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<?php
				if(!(version_compare(PHP_VERSION, "5.2.0") >= 0)){
					echo "<tr><td style='font-size: 24px;'><font color='red'>".$lang['INSTALL_PHPERROR']."</font></td></tr>";
					echo "<tr><td><font color='red'>".$lang['INSTALL_PHP_ADVISE'];
					echo "<br/><br/>".$lang['INSTALL_PHPVERSION'].PHP_VERSION;
					echo "</font></td></tr>";
				}else{
				?>
				<tr><td>&nbsp;</td></tr>
				<form action="index.php" method="POST">
					<tr>
						<td align="center"><font color="orange"><b><?php echo $lang['INSTALL_ACTION']; ?></b></font>
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
			<center><font size="2"><i><b>OGSpy</b> is an <b>OGSteam Software</b> (c) 2005-2015</i><br />v <?php echo $install_version ;?></font></center>
		</td>
	</tr>
</table>
</body>
</html>
