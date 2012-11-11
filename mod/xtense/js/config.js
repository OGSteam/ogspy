var auth = ['system', 'ranking', 'empire', 'messages'];


function check_col (type, El) {
	var status = El.checked;
	for (i in groups_id) {
		document.getElementById(type+'_'+groups_id[i]).checked = status;
	}
	El.checked = status;
}

function check_row(id, El) {
	var status = El.checked;
	for (var i = 0; i < auth.length; i++) {
		document.getElementById(auth[i]+'_'+id).checked = status;
	}
	El.checked = status;
}

function set_all (status) {
	for (var i in groups_id) {
		for (var a = 0; a < auth.length; a++) {
			document.getElementById(auth[a]+'_'+groups_id[i]).checked = status;
		}
	}
}

function winOpen (El) {
	try {window.opener.open(El.href); return false;} catch (e) {}
}