// Production par heure
function production (building, level, temperatureMax, energy, plasma = 0) {

    var speed = document.getElementById('vitesse_uni').value,
        ingenieur = document.getElementById('off_ingenieur').value == 1 ? 1.1 : 1,
        geologue = document.getElementById('off_geologue').value == 1 ? 1.1 : 1;

    switch (building) {
        case 'M': return speed * (30 + Math.floor(30 * level * Math.pow(1.1, level) * geologue *(1 + (0.01 * plasma))));
        case 'C': return speed * (15 + Math.floor(20 * level * Math.pow(1.1, level) * geologue *(1 + (0.0066 * plasma))));
        case 'D': return speed * Math.floor(10 * level * Math.pow(1.1, level) * (1.44 - 0.004 * temperatureMax) * geologue);
        case 'CES': return 20 * level * Math.pow(1.1, level) * ingenieur;
        case 'CEF': return 30 * level * Math.pow(1.05 + 0.01 * energy, level) * ingenieur;
        default: return 0;
    }

}

// Production des satellites
function production_sat (temperatureMin, temperatureMax) {
    var ingenieur = document.getElementById('off_ingenieur').value == 1 ? 1.1 : 1;
    return Math.floor(ingenieur * ((((parseInt(temperatureMin) + parseInt(temperatureMax)) / 2) + 160) / 6));
}

// Consommation d"énergie
function consumption (building, level) {
    
    switch (building) {
        case 'M': return Math.ceil(10 * level * Math.pow(1.1, level));
        case 'C': return Math.ceil(10 * level * Math.pow(1.1, level));
        case 'D': return Math.ceil(20 * level * Math.pow(1.1, level));
        case 'CEF': return Math.ceil((10 * level * Math.pow(1.1, level)) * document.getElementById('vitesse_uni').value);
        default: return 0;
    }

}

// Met à jour la page Espace Personel > Simulation
function update_page () {
	var NRJ = document.getElementById("NRJ").value;
    var Plasma = document.getElementById("Plasma").value;
	
	//
	// Planètes
	//
	var M_1_conso = new Array();
	var M_1_prod = new Array();
	var C_1_conso = new Array();
	var C_1_prod = new Array();
	var D_1_conso = new Array();
	var D_1_prod = new Array();
	
	var NRJ_1 = new Array();
	
	var M_1 = new Array();
	var C_1 = new Array();
	var D_1 = new Array();
	
	var CES_1 = new Array();
	var CEF_1 = new Array();
	var Sat_1 = new Array();
	
	var nombrePlanete = parseInt(document.getElementById('simu').title); // on passe par le titre du tableau pour recuperer le nombre de planetes, la recuperation du nb de colonne ne marchant pas ...
	
	for(i=101; i <= nombrePlanete+100; i++) {
		var temperature_min_1 = document.getElementById("temperature_min_" + i).value;
		var temperature_max_1 = document.getElementById("temperature_max_" + i).value;
	
		//Métal - Planète 1
		M_1[i] = document.getElementById("M_" + i).value;
		var M_1_percentage = document.getElementById("M_" + i + "_percentage").value;
	
		M_1_conso[i] = Math.round(consumption("M", M_1[i]) * M_1_percentage / 100);
		M_1_prod[i] = Math.round(production("M", M_1[i], temperature_max_1, NRJ, Plasma) * M_1_percentage / 100);
	
		document.getElementById("M_" + i + "_conso").innerHTML = M_1_conso[i];
		document.getElementById("M_" + i + "_prod").innerHTML = M_1_prod[i];
	
		//Cristal - Planète 1
		C_1[i] = document.getElementById("C_" + i).value;
		var C_1_percentage = document.getElementById("C_" + i + "_percentage").value;
	
		C_1_conso[i] = Math.round(consumption("C", C_1[i]) * C_1_percentage / 100);
		C_1_prod[i] = Math.round(production("C", C_1[i], temperature_max_1, NRJ, Plasma) * C_1_percentage / 100);
	
		document.getElementById("C_" + i + "_conso").innerHTML = C_1_conso[i];
		document.getElementById("C_" + i + "_prod").innerHTML = C_1_prod[i];
	
		//CES - Planète 1
		CES_1[i] = document.getElementById("CES_" + i).value;
		var CES_1_percentage = document.getElementById("CES_" + i + "_percentage").value;
		var CES_1_production = production("CES", CES_1[i], temperature_max_1, NRJ) * CES_1_percentage / 100;
	
		//CEF - Planète 1
		CEF_1[i] = document.getElementById("CEF_" + i).value;
		var CEF_1_percentage = document.getElementById("CEF_" + i + "_percentage").value;
		var CEF_1_production = production("CEF", CEF_1[i], temperature_max_1, NRJ) * CEF_1_percentage / 100;
	
		//Sat - Planète 1
		Sat_1[i] = document.getElementById("Sat_" + i).value;
		var Sat_1_percentage = document.getElementById("Sat_" + i + "_percentage").value;
		var Sat_1_production = production_sat(temperature_min_1, temperature_max_1) * Sat_1[i] * Sat_1_percentage / 100;

	
		//Deutérium - Planète 1
		D_1[i] = document.getElementById("D_" + i).value;
		var D_1_percentage = document.getElementById("D_" + i + "_percentage").value;
	
		D_1_conso[i] = Math.round(consumption("D", D_1[i]) * D_1_percentage / 100);
		D_1_prod[i] = Math.round(production("D", D_1[i], temperature_max_1, NRJ) * D_1_percentage / 100) - Math.round(consumption("CEF", CEF_1[i]) * CEF_1_percentage / 100);

		
		document.getElementById("D_" + i + "_conso").innerHTML = D_1_conso[i];
		document.getElementById("D_" + i + "_prod").innerHTML = D_1_prod[i];
	
		//Energie

		NRJ_1[i] = Math.round(CES_1_production + CEF_1_production + Sat_1_production);
		var NRJ_1_delta = NRJ_1[i] - (M_1_conso[i] + C_1_conso[i] + D_1_conso[i]);
		if (NRJ_1_delta < 0) NRJ_1_delta = "<font color='red'>" + NRJ_1_delta + "</font>";
		document.getElementById("NRJ_" + i).innerHTML = NRJ_1_delta + " / " + NRJ_1[i];
	
		//Ratio de consommation d'énergie
		var ratio_conso = NRJ_1[i] / (M_1_conso[i] + C_1_conso[i] + D_1_conso[i]);
		if (ratio_conso < 1) {
			M_1_prod[i] = Math.round(M_1_prod[i] * ratio_conso);
			document.getElementById("M_" + i + "_prod").innerHTML = M_1_prod[i];
	
			C_1_prod[i] = Math.round(C_1_prod[i] * ratio_conso);
			document.getElementById("C_" + i + "_prod").innerHTML = C_1_prod[i];
	
			D_1_prod[i] = Math.round(D_1_prod[i] * ratio_conso);
			document.getElementById("D_" + i + "_prod").innerHTML = D_1_prod[i];
		}
	}

	//
	// Totaux
	//

	var M_conso = 0;
	var M_prod = 0;
	var C_conso = 0;
	var C_prod = 0;
	var D_conso = 0;
	var D_prod = 0;
	var NRJ = 0;
	
	for(i=101; i <= nombrePlanete+100; i++) {
		M_conso = M_conso + M_1_conso[i];
		M_prod = M_prod + M_1_prod[i];
		C_conso = C_conso + C_1_conso[i];
		C_prod = C_prod + C_1_prod[i];
		D_conso = D_conso + D_1_conso[i];
		D_prod = D_prod + D_1_prod[i];
		NRJ = NRJ + NRJ_1[i];
	}
	
	//Metal
	document.getElementById("M_conso").innerHTML = M_conso;
	document.getElementById("M_prod").innerHTML = M_prod;

	//Cristal
	document.getElementById("C_conso").innerHTML = C_conso;
	document.getElementById("C_prod").innerHTML = C_prod;

	//Deutérium
	document.getElementById("D_conso").innerHTML = D_conso;
	document.getElementById("D_prod").innerHTML = D_prod;

	//Energie
	var Delta_NRJ = NRJ - (M_conso + C_conso + D_conso);

	if (Delta_NRJ < 0) Delta_NRJ = "<font color='red'>"+Delta_NRJ+"</font>";
	else Delta_NRJ = "<font color='lime'>"+Delta_NRJ+"</font>";
	NRJ = "<font color='lime'>"+NRJ+"</font>"
	document.getElementById("NRJ").innerHTML = Delta_NRJ + " / " + NRJ;

	//
	// Points
	//
	//                      UdR, Nanites, CSp,   HM,   HC,   HD,    CM,    CC,    CD, Lab, TeraF, DepotR,  Silo, BaseL, Phalang, PdS
	init_b_prix = new Array(720, 1600000, 700, 1000, 1500, 2000,  2645,  3967,  5290, 800, 150000, 41000, 80000, 80000, 8000000, 60000);

	// Batiments planetes
	var total_b_pts = 0;
	var total_pts_1 = new Array();
	
	for (i = 101; i <= nombrePlanete + 100; i++) {
	    
		var building_1 = document.getElementById("building_" + i).value;
		building_1 = building_1.split('<>');
		
		var b_pts_1 = Math.floor(((60 + 15) * (1 - Math.pow(1.5, M_1[i])) / (-0.5)) + ((48 + 24) * (1 - Math.pow(1.6, C_1[i])) / (-0.6)) + ((225 +75) * (1 - Math.pow(1.5, D_1[i])) / (-0.5)) + ((75 + 30) * (1 - Math.pow(1.5, CES_1[i])) / (-0.5)) + ((900 + 360 + 180) * (1 - Math.pow(1.8, CEF_1[i])) / (-0.8)));
		
		for (j = 0; j < building_1.length; j++) {
		    
			if (building_1[j] != 0) {
				
				if (j == 6 || j == 7 || j == 8) {
					b_pts_1 += init_b_prix[j] * (1 - Math.pow(2.3, building_1[j])) / -1.3; //Formules Cachette En attendant de faire du propre en V5 et les Formules Officielles...
				}
				else {
					b_pts_1 += init_b_prix[j] * Math.pow(2, building_1[j] - 1);
				}
			}
		}
		
		total_pts_1[i] = b_pts_1;
		total_b_pts += b_pts_1;
		
		document.getElementById("building_pts_" + i).innerHTML = Math.round(b_pts_1/1000);
	}

	document.getElementById("total_b_pts").innerHTML = Math.round(total_b_pts/1000);

	init_d_prix = new Array(2000, 2000, 8000, 37000, 8000, 130000, 20000, 100000, 10000, 25000);

	// Defences planetes
	var total_d_pts = 0;
	for(i=101; i <= nombrePlanete+100; i++) {
		var defence_1 = document.getElementById("defence_" + i).value;
		defence_1 = defence_1.split('<>');
		var d_pts_1 = 0;
		for(j=0; j<defence_1.length; j++) {
			d_pts_1 = d_pts_1 + init_d_prix[j] * defence_1[j];
		}
		total_pts_1[i] += d_pts_1;
		total_d_pts += d_pts_1;
		document.getElementById("defence_pts_" + i).innerHTML = Math.round(d_pts_1/1000);
	}

	document.getElementById("total_d_pts").innerHTML = Math.round(total_d_pts/1000);

	// Lunes de planetes
	var total_lune_pts = 0;
	var lune_pts_1 = new Array();
	for(i=201; i <= nombrePlanete+200; i++) {
		var lune_b_1 = document.getElementById("lune_b_" + (i )).value;
		lune_b_1 = lune_b_1.split('<>');
		var lune_defence_1 = document.getElementById("lune_d_" + (i )).value;
		lune_defence_1 = lune_defence_1.split('<>');
		lune_pts_1[i] = 0;
		for(j=0; j<(lune_b_1.length); j++) { // pk ? for(j=0; j<(lune_b_1.length-2); j++) ne prennait pas en compte ddr 
			lune_pts_1[i] += init_b_prix[j] * (Math.pow(2, lune_b_1[j]) - 1);
		}
		for(j=0; j<lune_defence_1.length; j++) {
			lune_pts_1[i] += init_d_prix[j] * lune_defence_1[j];
		}
		total_lune_pts += lune_pts_1[i];
		document.getElementById("lune_pts_" + (i )).innerHTML = Math.round(lune_pts_1[i]/1000);
	}

	document.getElementById("total_lune_pts").innerHTML = Math.round(total_lune_pts/1000);

	// Sat planetes
	var total_sat_pts = 0;
	var sat_pts_1 = new Array();
	for(i=101; i <= nombrePlanete+100; i++) {
		var sat_lune_1 = document.getElementById("sat_lune_" + i).value;
		sat_pts_1[i] = Math.round(Sat_1[i]*2.5+sat_lune_1*2.5);
		total_sat_pts += sat_pts_1[i];
		document.getElementById("sat_pts_" + i).innerHTML = "<font color='lime'>" + sat_pts_1[i] + "</font>";
	}
	
	document.getElementById("total_sat_pts").innerHTML = total_sat_pts;
	
	for(i=101; i <= nombrePlanete+100; i++) {
	    var j= i+100;
		document.getElementById("total_pts_" + i).innerHTML = Math.round((total_pts_1[i] + lune_pts_1[j])/1000)+sat_pts_1[i];
	}
	
	// Technologies planete avec le labo de plus au niveau

	init_t_prix = new Array(1400, 1000, 1000, 800, 1000, 1200, 6000, 1000, 6600, 36000, 300, 1400, 7000, 800000, 0, 16000);

	var techno = document.getElementById("techno").value;
	techno = techno.split('<>');
	var techno_pts = 0;
	for(i=0; i<(techno.length-1); i++) {
		techno_pts = techno_pts + init_t_prix[i] * (Math.pow(2, techno[i]) - 1);
	}
	
	// Calcul du cout de la techno astrophysique.
	var techno_astro_pts = 0;
	var techno_astro_pts_prec = 0;
	if (techno[15] > 0) {
		techno_astro_pts = init_t_prix[15];
		techno_astro_pts_prec = init_t_prix[15];
	}
	for (i=1; i < techno[15]; i++) {
		techno_astro_pts = techno_astro_pts + techno_astro_pts_prec * 1.75;
		techno_astro_pts_prec = techno_astro_pts_prec * 1.75;
	}
	techno_pts = techno_pts + techno_astro_pts;
	// Cout Total Techno
	document.getElementById("techno_pts").innerHTML = Math.round(techno_pts/1000);

	// Cout Total
	document.getElementById("total_pts").innerHTML = Math.round((total_b_pts + total_d_pts + total_lune_pts + techno_pts)/1000)+total_sat_pts;
}
