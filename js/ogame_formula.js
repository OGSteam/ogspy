// Production par heure
function production(building, level, temperatureMax, energy, plasma, position) {
    if (typeof(plasma) == 'undefined') {
        plasma = 0;
    }
	if (typeof(position) == 'undefined') {
        position = 0;
    }
    var speed = document.getElementById('vitesse_uni').value,
        ingenieur = document.getElementById('off_ingenieur').value == 1 ? 0.1 : 0,
        geologue = document.getElementById('off_geologue').value == 1 ? 0.1 : 0;
    var bonus_class_mine = 0,
        bonus_class_energie = 0;
    
    if (document.getElementById('off_full').value == 1) {
        ingenieur = 0.12;
        geologue = 0.12;
    }
    if (document.getElementById('class_collect').value == 1) {
        bonus_class_mine = 0.25; //+25%
        bonus_class_energie = 0.10; //+10%
    }
	//Bonus position
    bonus_position = 0;
    if (building == 'C') {
        if (position == 1) {
            bonus_position = 0.4;
        } else if (position == 2) {
            bonus_position = 0.3;
        } else if (position == 3) {
            bonus_position = 0.2;
        }
    } else if (building == 'M') {
        if (position == 8) {
            bonus_position = 0.35;
        } else if (position == 9 || position == 7) {
            $bonus_position = 0.23;
        } else if (position == 10 || position == 6) {
            bonus_position = 0.17;
        }
    }
    switch (building) {
        case 'M':
            return Math.floor(speed * 30 * level * Math.pow(1.1, level) * (1 + bonus_position) * (1 + geologue + 0.01 * plasma + bonus_class_mine)) + Math.floor(speed * 30  * (1 + bonus_position));
        case 'C':
            return Math.floor(speed * 20 * level * Math.pow(1.1, level) * (1 + bonus_position) * (1 + geologue + 0.0066 * plasma + bonus_class_mine)) + Math.floor(speed * 15  * (1 + bonus_position));
        case 'D':
            // return Math.floor(speed * 10 * level * Math.pow(1.1, level) * Math.floor((1.44 - 0.004 * temperatureMax)*10)/10 * (1 + geologue + 0.0033 * plasma + bonus_class_mine));
            return Math.round(speed * 10 * level * Math.pow(1.1, level) * (1.44 - 0.004 * temperatureMax) * (1 + geologue + 0.0033 * plasma + bonus_class_mine));
        case 'CES':
            return Math.floor(20 * level * Math.pow(1.1, level) * (1 + ingenieur + bonus_class_energie));
        case 'CEF':
            return Math.floor(30 * level * Math.pow(1.05 + 0.01 * energy, level) * (1 + ingenieur + bonus_class_energie));
        case 'SAT':
            return Math.floor(Math.floor((parseInt(temperatureMax) + 140) / 6) * level * (1 + ingenieur + bonus_class_energie));
        default:
            return 0;
    }
}

// Production des satellites
function production_sat(temperatureMax, nbSat) {
    return production('SAT', nbSat, temperatureMax, 0, 0, 0);
}

//Production des foreuses
function production_foreuse(nbForeuse, levelM, levelC, levelD, temperatureMax, position) {
	if (typeof(position) == 'undefined') {
        position = 0;
    }
	var speed = document.getElementById('vitesse_uni').value;
    var bonus_foreuse = 0.0002,
		bonus_foreuse_max = 0;
    var nb_max;
    if (document.getElementById('class_collect').value == 1) {
        bonus_foreuse = bonus_foreuse * 1.5; //+50%
		if (document.getElementById('off_geologue').value == 1) {
			bonus_foreuse_max = 0.1; //+10%
		}
    }
    nb_max = (parseInt(levelM) + parseInt(levelC) + parseInt(levelD)) * 8 * (1 + bonus_foreuse_max);
    if(nbForeuse > nb_max) {
        nbForeuse = nb_max;
    }
	//Bonus position
    bonus_position_M = 0;
    bonus_position_C = 0;
    if (position == 1) {
        bonus_position_C = 0.4;
    } else if (position == 2) {
        bonus_position_C = 0.3;
    } else if (position == 3) {
        bonus_position_C = 0.2;
    } else if (position == 8) {
        bonus_position_M = 0.35;
    } else if (position == 9 || position == 7) {
        bonus_position_M = 0.23;
    } else if (position == 10 || position == 6) {
        bonus_position_M = 0.17;
    }
    //paypass lien externe !!
    var tmp_class = document.getElementById('class_collect').value;
    var tmp_geo = document.getElementById('off_geologue').value;
    var tmp_off_full = document.getElementById('off_full').value;
    document.getElementById('class_collect').value = 0;
    document.getElementById('off_geologue').value = 0;
    document.getElementById('off_full').value = 0;
    
	final_bonus_foreuse = Math.min(0.5, bonus_foreuse * nbForeuse);
    result_M = Math.round(final_bonus_foreuse * (production('M', levelM, temperatureMax, 0, 0, position) - Math.floor(speed * 30  * (1 + bonus_position))));
    result_C = Math.round(final_bonus_foreuse * (production('C', levelC, temperatureMax, 0, 0, position) - Math.floor(speed * 15  * (1 + bonus_position))));
    result_D = Math.round(final_bonus_foreuse *  production('D', levelD, temperatureMax, 0, 0, position));
    
    document.getElementById('class_collect').value = tmp_class;
    document.getElementById('off_geologue').value = tmp_geo;
    document.getElementById('off_full').value = tmp_off_full;
    
    
    return {"M":result_M, "C":result_C, "D":result_D};
}

//Max foreuses
function foreuse_max(levelM, levelC, levelD) {
    var bonus_foreuse_max = 0;
    if (document.getElementById('class_collect').value == 1 && document.getElementById('off_geologue').value == 1) {
			bonus_foreuse_max = 0.1; //+10%
    }
    return (parseInt(levelM) + parseInt(levelC) + parseInt(levelD)) * 8 * (1 + bonus_foreuse_max);
}

// Consommation d"énergie
function consumption(building, level) {
    switch (building) {
        case 'M':
            return Math.floor(10 * level * Math.pow(1.1, level));
        case 'C':
            return Math.floor(10 * level * Math.pow(1.1, level));
        case 'D':
            return Math.floor(20 * level * Math.pow(1.1, level));
        case 'CEF':
            return Math.round((10 * level * Math.pow(1.1, level)) * document.getElementById('vitesse_uni').value);
        case 'FOR':
            return level * 50;
        default:
            return 0;
    }
}

// Met à jour la page Espace Personel > Simulation
function update_page() {
    var j = 0;
    var NRJ = document.getElementById("NRJ").value;
    var Plasma = document.getElementById("Plasma").value;

    if (document.getElementById('c_off_ingenieur').checked) {
        document.getElementById('off_ingenieur').value = '1';
    } else {
        document.getElementById('off_ingenieur').value = '0';
    }
    if (document.getElementById('c_off_geologue').checked) {
        document.getElementById('off_geologue').value = '1';
    } else {
        document.getElementById('off_geologue').value = '0';
    }
    if (document.getElementById('c_off_full').checked) {
        document.getElementById('c_off_geologue').checked = true;
        document.getElementById('c_off_ingenieur').checked = true;
        document.getElementById('off_ingenieur').value = '1';
        document.getElementById('off_geologue').value = '1';
        document.getElementById('off_full').value = '1';
    } else {
        document.getElementById('off_full').value = '0';
    }
    if (document.getElementById('c_class_collect').checked) {
        document.getElementById('class_collect').value = '1';
    } else {
        document.getElementById('class_collect').value = '0';
    }

    //
    // Planètes
    //
    var M_1_conso = [];
    var M_1_prod = [];
    var C_1_conso = [];
    var C_1_prod = [];
    var D_1_conso = [];
    var D_1_prod = [];
    var FOR_1_conso = [];
    var FOR_1_prod = [];

    var NRJ_1 = [];

    var M_1 = [];
    var C_1 = [];
    var D_1 = [];

    var CES_1 = [];
    var CEF_1 = [];
    var Sat_1 = [];
    var For_1 = [];

    var nombrePlanete = parseInt(document.getElementById('simu').title); // on passe par le titre du tableau pour recuperer le nombre de planetes, la recuperation du nb de colonne ne marchant pas ...

    var i = 0;
    for (j = 101; j <= nombrePlanete + 100; j++) {
        var temperature_max_1 = document.getElementById("temperature_max_" + j).value;
        var M_1_percentage = document.getElementById("M_" + j + "_percentage").value;
        var C_1_percentage = document.getElementById("C_" + j + "_percentage").value;
        var D_1_percentage = document.getElementById("D_" + j + "_percentage").value;
        var CES_1_percentage = document.getElementById("CES_" + j + "_percentage").value;
        var CEF_1_percentage = document.getElementById("CEF_" + j + "_percentage").value;
        var Sat_1_percentage = document.getElementById("Sat_" + j + "_percentage").value;
        var For_1_percentage = document.getElementById("For_" + j + "_percentage").value;
        var M_1_booster = document.getElementById("M_" + j + "_booster").value;
        var C_1_booster = document.getElementById("C_" + j + "_booster").value;
        var D_1_booster = document.getElementById("D_" + j + "_booster").value;
		var E_1_booster = document.getElementById("E_" + j + "_booster").value;
		
		var position = document.getElementById("position_" + j).value;

        M_1[i] = document.getElementById("M_" + j).value;
        C_1[i] = document.getElementById("C_" + j).value;
        D_1[i] = document.getElementById("D_" + j).value;
        CES_1[i] = document.getElementById("CES_" + j).value;
        CEF_1[i] = document.getElementById("CEF_" + j).value;
        Sat_1[i] = document.getElementById("Sat_" + j).value;
        For_1[i] = document.getElementById("For_" + j).value;

        M_1_conso[i] = Math.round(consumption("M", M_1[i]) * M_1_percentage / 100);
        C_1_conso[i] = Math.round(consumption("C", C_1[i]) * C_1_percentage / 100);
        D_1_conso[i] = Math.round(consumption("D", D_1[i]) * D_1_percentage / 100);
        FOR_1_conso[i] = Math.round(consumption("FOR", For_1[i]) * For_1_percentage / 100);
        var energie_conso = M_1_conso[i] + C_1_conso[i] + D_1_conso[i] + FOR_1_conso[i];

        var CES_1_production = production("CES", CES_1[i], temperature_max_1, NRJ) * CES_1_percentage / 100;
        var CEF_1_production = production("CEF", CEF_1[i], temperature_max_1, NRJ) * CEF_1_percentage / 100;
        var Sat_1_production = production_sat(temperature_max_1, Sat_1[i]) * Sat_1_percentage / 100;
        NRJ_1[i] = Math.round((CES_1_production + CEF_1_production + Sat_1_production) * (1 + E_1_booster / 100));

        var NRJ_1_delta = NRJ_1[i] - energie_conso;
        if (NRJ_1_delta < 0) {
            document.getElementById("NRJ_" + j).innerHTML = "<span style=\"color: red; \">" + format(NRJ_1_delta) + "</span>" + " / " + format(NRJ_1[i]);
        } else {
            document.getElementById("NRJ_" + j).innerHTML = format(NRJ_1_delta) + " / " + format(NRJ_1[i]);
        }
        if (isNaN(NRJ_1[i])) NRJ_1[i] = 0;

        //Ratio de consommation d'énergie
        var ratio_conso = 0;
        if (energie_conso != 0) {
            var ratio_conso = NRJ_1[i] / energie_conso;
            if (ratio_conso > 1) ratio_conso = 1;
        }
        if (ratio_conso > 0) {
            M_1_prod[i] = Math.round(ratio_conso * production("M", M_1[i], temperature_max_1, NRJ, Plasma, position) * M_1_percentage / 100);
            C_1_prod[i] = Math.round(ratio_conso * production("C", C_1[i], temperature_max_1, NRJ, Plasma, position) * C_1_percentage / 100);
            D_1_prod[i] = Math.round(ratio_conso * production("D", D_1[i], temperature_max_1, NRJ, Plasma, position) * D_1_percentage / 100) - Math.round(consumption("CEF", CEF_1[i]) * CEF_1_percentage / 100);
            prod_for_tmp = production_foreuse(For_1[i], M_1[i], C_1[i], D_1[i], temperature_max_1, position);
			prod_for_tmp["M"] = Math.round(ratio_conso * prod_for_tmp["M"] * C_1_percentage / 100);
			prod_for_tmp["C"] = Math.round(ratio_conso * prod_for_tmp["C"] * C_1_percentage / 100);
			prod_for_tmp["D"] = Math.round(ratio_conso * prod_for_tmp["D"] * C_1_percentage / 100);
			FOR_1_prod[i] = prod_for_tmp;
            
			M_1_prod[i] = M_1_prod[i] + Math.round((ratio_conso * production("M", M_1[i], temperature_max_1, NRJ, 0, position) * M_1_percentage / 100) * (M_1_booster / 100));
			C_1_prod[i] = C_1_prod[i] + Math.round((ratio_conso * production("C", C_1[i], temperature_max_1, NRJ, 0, position) * C_1_percentage / 100) * (C_1_booster / 100));
			D_1_prod[i] = D_1_prod[i] + Math.round((ratio_conso * production("D", D_1[i], temperature_max_1, NRJ, 0, position) * D_1_percentage / 100) * (D_1_booster / 100));
        } else {
            M_1_prod[i] = Math.round(production("M", 0, 0, 0, 0, position));
            C_1_prod[i] = Math.round(production("C", 0, 0, 0, 0, position));
            D_1_prod[i] = Math.round(production("D", 0, 0, 0, 0, position));
			prod_for_tmp = production_foreuse(0, 0, 0, 0, 0, position);
			prod_for_tmp["M"] = Math.round(ratio_conso * prod_for_tmp["M"] * C_1_percentage / 100);
			prod_for_tmp["C"] = Math.round(ratio_conso * prod_for_tmp["C"] * C_1_percentage / 100);
			prod_for_tmp["D"] = Math.round(ratio_conso * prod_for_tmp["D"] * C_1_percentage / 100);
			FOR_1_prod[i] = prod_for_tmp;
        }
        document.getElementById("M_" + j + "_conso").innerHTML = format(M_1_conso[i]);
        document.getElementById("M_" + j + "_prod").innerHTML = format(M_1_prod[i]);
        document.getElementById("C_" + j + "_conso").innerHTML = format(C_1_conso[i]);
        document.getElementById("C_" + j + "_prod").innerHTML = format(C_1_prod[i]);
        document.getElementById("D_" + j + "_conso").innerHTML = format(D_1_conso[i]);
        document.getElementById("D_" + j + "_prod").innerHTML = format(D_1_prod[i]);
        document.getElementById("FOR_" + j + "_conso").innerHTML = format(FOR_1_conso[i]);
        document.getElementById("FOR_" + j + "_prod").innerHTML = format(FOR_1_prod[i]["M"]) + " / " + format(FOR_1_prod[i]["C"]) + " / " + format(FOR_1_prod[i]["D"]);
		
		document.getElementById("FOR_" + j + "_max").innerHTML = format(foreuse_max(M_1[i], C_1[i], D_1[i]));
		
        i++;
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
    var FOR_conso = 0;
    var NRJ = 0;

    for (i = 0; i < nombrePlanete; i++) {
        M_conso = M_conso + M_1_conso[i];
        M_prod = M_prod + M_1_prod[i] + FOR_1_prod[i]["M"];
        C_conso = C_conso + C_1_conso[i];
        C_prod = C_prod + C_1_prod[i] + FOR_1_prod[i]["C"];
        D_conso = D_conso + D_1_conso[i];
        D_prod = D_prod + D_1_prod[i] + FOR_1_prod[i]["D"];
        FOR_conso = FOR_conso + FOR_1_conso[i];
        NRJ += NRJ_1[i];
    }
    document.getElementById("M_conso").innerHTML = format(M_conso);
    document.getElementById("M_prod").innerHTML = format(M_prod);
    document.getElementById("C_conso").innerHTML = format(C_conso);
    document.getElementById("C_prod").innerHTML = format(C_prod);
    document.getElementById("D_conso").innerHTML = format(D_conso);
    document.getElementById("D_prod").innerHTML = format(D_prod);
    document.getElementById("FOR_conso").innerHTML = format(FOR_conso);

    //Energie
    var Delta_NRJ = NRJ - (M_conso + C_conso + D_conso + FOR_conso);
    var s_delta = "-";
    if (Delta_NRJ < 0 || isNaN(Delta_NRJ)) s_delta = "<span style=\"color: red; \">" + format(Delta_NRJ) + "</span>";
    else s_delta = "<span style=\"color: lime; \">" + format(Delta_NRJ) + "</span>";
    document.getElementById("E_NRJ").innerHTML = s_delta + " / " + "<span style=\"color: lime; \">" + format(NRJ) + "</span>";

    //
    // Points
    //                 UdR, Nanites, CSp,   HM,   HC,   HD, Lab,  TeraF,DepotR,  Silo,Dock, BaseL, Phalg, PdS
    var init_b_prix = [720, 1600000, 700, 1000, 1500, 2000, 800, 150000, 60000, 41000, 250, 80000, 80000, 8000000];

    // Batiments planetes
    var total_b_pts = 0;
    var total_pts_1 = [];

    j = 101;
    for (i = 0; i < nombrePlanete; i++) {
        var building_1 = document.getElementById("building_" + j).value;
        var b_pts_1 = Math.floor(((60 + 15) * (1 - Math.pow(1.5, M_1[i])) / (-0.5)) + ((48 + 24) * (1 - Math.pow(1.6, C_1[i])) / (-0.6)) + ((225 + 75) * (1 - Math.pow(1.5, D_1[i])) / (-0.5)) + ((75 + 30) * (1 - Math.pow(1.5, CES_1[i])) / (-0.5)) + ((900 + 360 + 180) * (1 - Math.pow(1.8, CEF_1[i])) / (-0.8)));

        building_1 = building_1.split('<>');
        for (k = 0; k < building_1.length; k++) {
            if (building_1[k] !== 0 || building_1[k] !== 100) {
                b_pts_1 += init_b_prix[k] * Math.pow(2, building_1[k] - 1);
            }
        }
        total_pts_1[j] = b_pts_1;
        total_b_pts += b_pts_1;

        document.getElementById("building_pts_" + j).innerHTML = format(Math.round(total_pts_1[j] / 1000));
        j++;
    }
    document.getElementById("total_b_pts").innerHTML = format(Math.round(total_b_pts / 1000));

    var init_d_prix = [2000, 2000, 8000, 37000, 8000, 130000, 20000, 100000, 10000, 25000];

    // Defences planetes
    var total_d_pts = 0;
    j = 101;
    for (i = 0; i < nombrePlanete; i++) {
        var defence_1 = document.getElementById("defence_" + j).value;
        defence_1 = defence_1.split('<>');
        var d_pts_1 = 0;
        for (k = 0; k < defence_1.length; k++) {
            d_pts_1 = d_pts_1 + init_d_prix[k] * defence_1[k];
        }
        total_pts_1[j] += d_pts_1;
        total_d_pts += d_pts_1;
        document.getElementById("defence_pts_" + j).innerHTML = format(Math.round(total_pts_1[j] / 1000));
        j++;
    }
    document.getElementById("total_d_pts").innerHTML = format(Math.round(total_d_pts / 1000));

    var total_lune_pts = 0;
    var lune_pts_1 = [];
    var t = 201;
    for (i = 0; i < nombrePlanete; i++) {
        var lune_b_1 = document.getElementById("lune_b_" + t).value;
        var lune_defence_1 = document.getElementById("lune_d_" + t).value;

        lune_b_1 = lune_b_1.split('<>');
        lune_defence_1 = lune_defence_1.split('<>');
        lune_pts_1[i] = 0;

        for (k = 0; k < (lune_b_1.length); k++) {
            lune_pts_1[i] += init_b_prix[k] * (Math.pow(2, lune_b_1[k]) - 1);
        }

        for (kk = 0; kk < lune_defence_1.length; kk++) {
            lune_pts_1[i] += init_d_prix[kk] * lune_defence_1[kk];
        }
        total_lune_pts += lune_pts_1[i];


        document.getElementById("lune_pts_" + t).innerHTML = format(Math.round(lune_pts_1[i] / 1000));
        t++;
    }
    document.getElementById("total_lune_pts").innerHTML = format(Math.round(total_lune_pts / 1000));

    // Sat planetes
    var total_sat_pts = 0;
    var sat_pts_1 = [];
    j = 101;
    for (i = 0; i < nombrePlanete; i++) {
        var sat_lune_1 = document.getElementById("sat_lune_" + j).value;
        sat_pts_1[i] = Math.round(Sat_1[i] * 2.5 + sat_lune_1 * 2.5);
        total_sat_pts += sat_pts_1[i];
        document.getElementById("sat_pts_" + j).innerHTML = "<span style=\"color: lime; \">" + format(sat_pts_1[i]) + "</span>";
        j++;
    }
    document.getElementById("total_sat_pts").innerHTML = format(total_sat_pts);

    t = 101;
    for (i = 0; i < nombrePlanete; i++) {
        j = i + 100;
        document.getElementById("total_pts_" + t).innerHTML = format(Math.round((total_pts_1[i] + lune_pts_1[j]) / 1000) + sat_pts_1[i]);
        t++;
    }

    // Technologies planete avec le labo de plus au niveau
    var init_t_prix = [1400, 1000, 1000, 800, 1000, 1200, 6000, 1000, 6600, 36000, 300, 1400, 7000, 800000, 0, 16000];

    var techno = document.getElementById("techno").value;
    techno = techno.split('<>');
    var techno_pts = 0;
    for (i = 0; i < (techno.length - 1); i++) {
        techno_pts = techno_pts + init_t_prix[i] * (Math.pow(2, techno[i]) - 1);
    }

    // Calcul du cout de la techno astrophysique.
    var techno_astro_pts = 0;
    var techno_astro_pts_prec = 0;
    if (techno[15] > 0) {
        techno_astro_pts = init_t_prix[15];
        techno_astro_pts_prec = init_t_prix[15];
    }
    for (i = 1; i < techno[15]; i++) {
        techno_astro_pts = techno_astro_pts + techno_astro_pts_prec * 1.75;
        techno_astro_pts_prec = techno_astro_pts_prec * 1.75;
    }
    techno_pts = techno_pts + techno_astro_pts;
    document.getElementById("techno_pts").innerHTML = format(Math.round(techno_pts / 1000));// Cout Total Techno
    document.getElementById("total_pts").innerHTML = format(Math.round((total_b_pts + total_d_pts + total_lune_pts + techno_pts) / 1000) + total_sat_pts);// Cout Total
}

//Affiche les nombres sous format lisible (10 000 à la place de 10000)
function format(x) {
    var signe = '';
    if (isNaN(x)) return '-';
    if (x < 0) {
        x = Math.abs(x);
        signe = '-';
    }
    var str = x.toString(), n = str.length;
    if (n < 4) return (signe + x);
    else return (signe + ((n % 3) ? str.substr(0, n % 3) + '&nbsp;' : '')) + str.substr(n % 3).match(new RegExp('[0-9]{3}', 'g')).join('&nbsp;');
}

/**
 * Calcule la distance entre a et b, a - b ; en tenant en compte des univers arrondis.
 * type = Représente le type de distance à calculer
 *      0 : Galaxie
 *      1 : Système
 *      2 : Planète
 * max_type = représente la valeur maximale pour le type donnée (ex. Galaxie=9; Système=499 ...)
 * typeArrondi = true pour un univers arrondi selon le type donnée
 */
function calc_distance(a, b, type, max_type, typeArrondi) {//a-b
    if (typeArrondi) {
        if (Math.abs(a - b) < max_type / 2) {
            return Math.abs(a - b);//|a-b|
        } else {
            return Math.abs(Math.abs(a - b) - max_type); //||a-b| - base|
        }
    } else {
        return Math.abs(a - b);//|a-b|
    }
}