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


// <li class="OgameClock">21.12.2020 <span>06:34:22</span>
// var serverTime = new Date(2020, 11, 21, 6, 32, 34);
// var serverTimeZoneOffsetInMinutes = -60;
// var localTime = new Date();
// var localTimeZoneOffsetInMinutes = localTime.getTimezoneOffset();
// var timeDiff = serverTime - localTime;
// var timeZoneDiffSeconds = (serverTimeZoneOffsetInMinutes - localTimeZoneOffsetInMinutes) * 60;
// var timerHandler = new TimerHandler();
// $(document).ready(
	// function(){
	  // initOverlays();
	// }
  // );
// timerHandler.appendCallback(function () {
	// localTime = new Date();
	// serverTime = new Date(localTime.valueOf() + timeDiff);
	// $(".OGameClock").html(getFormatedDate(serverTime.getTime(), "[d].[m].[Y] <span>[H]:[i]:[s]</span>"));
// });
	
// Global debut
function ogspy_run() {
	
}