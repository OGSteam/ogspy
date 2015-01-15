<?php

/***************************************************************************
 * filename : login.php
 * desc.    :
 * Author   : Kyser - http://ogsteam.fr/
 * created  : 15/11/2005
 * modified : 22/08/2006 00:00:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

require_once("views/page_header_2.php");

if (!isset($goto)) {
	$goto = "";
}

$enable_register_view = isset ($server_config['enable_register_view']) ? $server_config['enable_register_view'] : 0;

?>

<form method='post' action=''>
	<input type='hidden' name='action' value='login_web' />
	<input type='hidden' name='goto' value='<?php echo $goto;?>' />
	
	<table align='center' cellpadding="0" cellspacing="1">
		<tr>
			<td class="c" colspan="2">Paramètres de connexion</td>
		</tr>
		<tr>
			<th width="150">Login :</th>
			<th width="150"><input type='text' name='login' /></th>
		</tr>
		<tr>
			<th>Mot de passe :</th>
			<th><input type='password' name='password' /></th>
		</tr>
		<tr>
			<th colspan='2' align='right'><input type='submit' value='Connexion' /></th>
		</tr>
		
		<?php
		
		if ($enable_register_view == 1 ) {
		
		?>
		
		<tr>
			<td class="c" colspan="2">Demande de compte OGSpy</td>
		</tr>
		<tr>
			<th colspan='2' align='right'>Si vous ne disposez pas d'un compte, il faut <font color='red'>obligatoirement</font> en demander un sur le forum de <?php echo $server_config['register_alliance']; ?>.</th>
		</tr>
		<tr>
			<th colspan='2' align='right'><input type="button" value="Demander un compte" onclick="window.open('<?php echo $server_config['register_forum']; ?>');" /></th>
		</tr>
		
		<?php
		
		}
		
		?>
	</table>
</form>

<?php require_once("views/page_tail_2.php"); ?>
