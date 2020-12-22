/* JS OGSpy global */
// includes/admin_members.php
function _admin_visible(byId) {
	document.getElementById(byId).style.visibility = 'visible';
	document.getElementById(byId).style.display = 'block';
}
function _admin_unvisible(byId) {
	document.getElementById(byId).style.visibility = 'hidden';
	document.getElementById(byId).style.display = 'none';
}
function ogspy_beginCreateUser() {
	_admin_visible("createNewPlayer");
	_admin_unvisible("creatingNewPlayer");
}
function ogspy_endCreateUser() {
	_admin_visible("creatingNewPlayer");
	_admin_unvisible("createNewPlayer");
}
// includes/profile.php
function ogspy_check_password(form, message) {
	var old_password = form.old_password.value;
	var new_password = form.new_password.value;
	var new_password2 = form.new_password2.value;
	
	if (typeof(message) == 'undefined') {
        message['PROFILE_ERROR_RETRY'] = "Saisissez le nouveau mot de passe et sa confirmation.";
		message['PROFILE_ERROR_OLDPWD'] = "Saisissez l'ancien mot de passe.";
		message['PROFILE_ERROR_ERROR'] = "Le mot de passe saisi est différent de la confirmation !";
		message['PROFILE_ERROR_ILLEGAL'] = "Le mot de passe doit contenir entre 6 et 15 caractères !";
    }
	if (old_password !== "" && (new_password === "" || new_password2 === "")) {
		alert(message['PROFILE_ERROR_RETRY']);
		return false;
	}
	if (old_password === "" && (new_password !== "" || new_password2 !== "")) {
		alert(message['PROFILE_ERROR_OLDPWD']);
		return false;
	}
	if (old_password !== "" && new_password !== new_password2) {
		alert(message['PROFILE_ERROR_ERROR']);
		return false;
	}
	if (old_password !== "" && new_password !== "" && new_password2 !== "") {
		if (new_password.length < 6 || new_password.length > 64) {
			alert(message['PROFILE_ERROR_ILLEGAL']);
			return false;
		}
	}
	return true;
}
// includes/menu.php
function ogspy_run_timer() {
	if (document.getElementById('datetime') !== null) {
		var date = new Date();
		var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
		
		document.getElementById('datetime').innerHTML = date.toLocaleString('fr-FR', options) + " " + date.toLocaleTimeString();
		
		setTimeout("ogspy_run_timer()", 1000);
	}
}
	
// Global debut
function ogspy_run() {
	ogspy_run_timer();
}