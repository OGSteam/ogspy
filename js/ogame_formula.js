/*eslint camelcase: ["error", {properties: "never"}]*/
/**
 *  @file	js/ogame_formula.js
 *  Bibliothèques des formules Ogame pour des modifications dynamiques.
 *  @author pitch314
 *  @version 2.0, 2021-01-22
 */
//uglifyjs js/ogame_formula.js -c -m -b -o js/ogame_formula.min.js
'use strict';
// const DEFAULT_ARRAY_RESSOURCE = {'M':0,'C':0,'D':0,'NRJ':0};
// Object.freeze(DEFAULT_ARRAY_RESSOURCE);
// var a = JSON.parse(JSON.stringify(DEFAULT_ARRAY_RESSOURCE));

function ogame_arrayRessource($metal, $cristal, $deut, $NRJ = 0) {
    return {'M':$metal, 'C':$cristal, 'D':$deut, 'NRJ':$NRJ};
}
function ogame_getElementNames() {
	const names = {'BAT' : ['M','C','D','CES','CEF','UdR','UdN','CSp','HM','HC','HD','Lab','Ter','DdR','Silo','Dock','BaLu','Pha','PoSa'],
				'RECH' : ['Esp','Ordi','Armes','Bouclier','Protection','NRJ','Hyp','RC','RI','PH','Laser','Ions','Plasma','RRI','Graviton','Astrophysique'],
				'VSO'  : ['PT','GT','CLE','CLO','CR','VB','VC','REC','SE','BMD','DST','EDLM','TRA','SAT','FOR','FAU','ECL'],
				'DEF'  : ['LM','LLE','LLO','CG','AI','LP','PB','GB','MIC','MIP'],
				'CLASS': ['none','COL','GEN','EXP'],
				'RESS' : ['M','C','D','NRJ'] };
	return names;
}
function ogame_isElement(nom) {
	var names = ogame_getElementNames();
	for (var type in names) {
		for (var elem in names[type]) {
			if (nom === names[type][elem]) {
				return type;
			}
		}
	}
	return false;
}
function ogame_findPlanetPosition(coordinates) {
	var position = ogame_findCoordinates(coordinates);
	return position['p'];
}
function ogame_findCoordinates(string_coord) {
	var result = {'g' : 0, 's' : 0, 'p' : 0};
	var coordinates_tmp = string_coord.split(':');
	if (coordinates_tmp.length === 3) {
		result['g'] = coordinates_tmp[0];
		result['s'] = coordinates_tmp[1];
		result['p'] = coordinates_tmp[2];
	}
	return result;
}

// Production
function ogame_productionPosition(position) {
	const prod = {'M':[0,0,0,0,0,0,0.17,0.23,0.35,0.23,0.17,0,0,0,0,0,0],
				'C':[0,0.4,0.3,0.2,0,0,0,0,0,0,0,0,0,0,0,0,0]};
	if (position < 1 || position > prod.length) {
		position = 0;
	}
	return {'M' : prod['M'][position], 'C' : prod['C'][position], 'D' : 0, 'NRJ' : 0};
}
function ogame_productionForeuseMax(mine_M, mine_C, mine_D, user_data) {
	if (typeof(user_data) === 'undefined') { user_data = new Array(); }
	if (typeof(user_data['off_geologue']) === 'undefined') { user_data['off_geologue'] = 0; }
	if (typeof(user_data['off_full']) === 'undefined')     { user_data['off_full'] = 0; }
	if (typeof(user_data['user_class']) === 'undefined')   { user_data['user_class'] = 'none'; }

	var FOR_BONUS_COL_GEO = 0.1;    //+10% de foreuse pour COL+GEO
	var nb_foreuse_max = 8 * (mine_M + mine_C + mine_D);
	if (user_data['user_class'] === 'COL' && (user_data['off_geologue'] !== 0 || user_data['off_full'] !== 0)) {
		nb_foreuse_max = nb_foreuse_max * (1 + FOR_BONUS_COL_GEO);
	}
	return Math.floor(nb_foreuse_max);
}
function ogame_productionForeuseBonus(user_building, user_data) {
	var FOR_COEF       = 2e-4; //0.02% / foreuse
	var FOR_BONUS_COL  = 0.5;    //+50% pour COL
//Valeurs OUT par défaut :
	var result = {'bonus':0, 'nb_FOR_maxed':0};
//Valeurs IN par défaut :
	if (typeof(user_building) === 'undefined') { user_building = new Array(); }
	if (typeof(user_data) === 'undefined')     { user_data = new Array(); }
	if (typeof(user_building['M']) === 'undefined')   { user_building['M'] = 0; }
	if (typeof(user_building['C']) === 'undefined')   { user_building['C'] = 0; }
	if (typeof(user_building['D']) === 'undefined')   { user_building['D'] = 0; }
	if (typeof(user_building['FOR']) === 'undefined') { user_building['FOR'] = 0; }
	if (typeof(user_data['user_class']) === 'undefined') { user_data['user_class'] = 'none'; }

	var bonus_foreuse  = FOR_COEF;
	var nb_foreuse_max = ogame_productionForeuseMax(user_building['M'], user_building['C'], user_building['D'], user_data);
	var nb_foreuse     = user_building['FOR'];

	if (user_data['user_class'] === 'COL') {
		bonus_foreuse = bonus_foreuse * (1 + FOR_BONUS_COL);
	}
	if (nb_foreuse > nb_foreuse_max) {
		nb_foreuse = nb_foreuse_max;
	}
	result['bonus'] = Math.min(0.5, bonus_foreuse * nb_foreuse);
	result['nb_FOR_maxed'] = nb_foreuse;

	return result;
}
function ogame_productionBuilding(building, user_building=null, user_technology=null, user_data=null, server_config=null) {
	var BASE_M = 30;
	var BASE_C = 15;
//Valeurs OUT par défaut :
	var result = ogame_arrayRessource(0,0,0);
//Valeurs IN par défaut :
	if (user_building   === null) { user_building = new Array(); }
	if (user_technology === null) { user_technology = new Array(); }
	if (user_data       === null) { user_data = new Array(); }
	if (server_config   === null) { server_config = new Array(); }
	if (typeof(user_technology['NRJ']) === 'undefined') { user_technology['NRJ'] = 0; }
	if (typeof(user_building['M'])     === 'undefined') { user_building['M'] = 0; }
	if (typeof(user_building['C'])     === 'undefined') { user_building['C'] = 0; }
	if (typeof(user_building['D'])     === 'undefined') { user_building['D'] = 0; }
	if (typeof(user_building['CES'])   === 'undefined') { user_building['CES'] = 0; }
	if (typeof(user_building['CEF'])   === 'undefined') { user_building['CEF'] = 0; }
	if (typeof(user_building['FOR'])   === 'undefined') { user_building['FOR'] = 0; }
	if (typeof(user_building['SAT'])   === 'undefined') { user_building['SAT'] = 0; }
	if (typeof(user_building['temperature_max']) === 'undefined') { user_building['temperature_max'] = 0; }
	if (typeof(user_building['coordinates'])     === 'undefined') { user_building['coordinates'] = ''; }
	if (typeof(server_config['speed_uni'])       === 'undefined') { server_config['speed_uni'] = 1; }
	if (typeof(server_config['final_calcul'])    === 'undefined') { server_config['final_calcul'] = true; }

	user_building['position'] = ogame_findPlanetPosition(user_building['coordinates']);
	var bonus_position = ogame_productionPosition(user_building['position']);
	var level = 0, coef_base = 0, bonus_for = 0, number = 0;
	var production_mine_base = [];

	switch (building) {
		case 'base':
			result['M'] = Math.floor(BASE_M * (1 + bonus_position['M']) * server_config['speed_uni']);
			result['C'] = Math.floor(BASE_C * (1 + bonus_position['C']) * server_config['speed_uni']);
			break;
		case 'M':
			level = user_building['M'];
			coef_base = (1 + bonus_position['M']) * server_config['speed_uni'];
			result['M']   = 30 * level * Math.pow(1.1, level) * coef_base;
			result['NRJ'] = - Math.floor( 10 * level * Math.pow(1.1, level) );
			break;
		case 'C':
			level = user_building['C'];
			coef_base = (1 + bonus_position['C']) * server_config['speed_uni'];
			result['C']   = 20 * level * Math.pow(1.1, level) * coef_base;
			result['NRJ'] = - Math.floor( 10 * level * Math.pow(1.1, level) );
			break;
		case 'D':
			level = user_building['D'];
			coef_base = (1 + bonus_position['D']) * server_config['speed_uni'];
			result['D']   = 10 * level * Math.pow(1.1, level) * (1.44 - 0.004 * user_building['temperature_max']) * coef_base;
			result['NRJ'] = - Math.floor( 20 * level * Math.pow(1.1, level) );
			break;
		case 'CES':
			level = user_building['CES'];
			result['NRJ'] = Math.floor( 20 * level * Math.pow(1.1, level) );
			break;
		case 'CEF':
			level = user_building['CEF'];
			result['NRJ'] = Math.floor( 30 * level * Math.pow((1.05 + user_technology['NRJ'] * 0.01), level) );
			result['D']   = - Math.floor( 10 * level * Math.pow(1.1, level) ) * server_config['speed_uni'];
			break;
		case 'SAT':
			number = user_building['SAT'];
			result['NRJ'] = Math.floor( (user_building['temperature_max'] + 140) / 6 ) * number;
			break;
		case 'FOR':
			number = user_building['FOR'];
			production_mine_base['M'] = ogame_productionBuilding('M', user_building, null, null, server_config)['M'];
			production_mine_base['C'] = ogame_productionBuilding('C', user_building, null, null, server_config)['C'];
			production_mine_base['D'] = ogame_productionBuilding('D', user_building, null, null, server_config)['D'];
			bonus_for = ogame_productionForeuseBonus(user_building, user_data);

			result['M'] = Math.round( production_mine_base['M'] * bonus_for['bonus'] );
			result['C'] = Math.round( production_mine_base['C'] * bonus_for['bonus'] );
			result['D'] = Math.round( production_mine_base['D'] * bonus_for['bonus'] );
			result['NRJ'] = - 50 * bonus_for['nb_FOR_maxed'];
			break;
		default:
			break;
	}
	if (server_config['final_calcul']) {
		result['M'] = Math.floor(result['M']);
		result['C'] = Math.floor(result['C']);
		result['D'] = Math.floor(result['D']);
	}

	return result;
}
// console.log(ogame_productionBuilding('base',{M:38,CES:38,coordinates:'::8'},null,null,{speed_uni:8}))
function ogame_productionPlanet(user_building, user_technology=null, user_data=null, server_config=null) {
	var NRJ_BONUS_COL     = 0.1;   //+10% pour COL
	var NRJ_BONUS_ING     = 0.1;   //+10% pour ingénieur
	var NRJ_BONUS_FULL    = 0.02;  //+2% pour full officier
	var RESS_BONUS_COL    = 0.25;  //+25% pour COL
	var RESS_BONUS_GEO    = 0.1;   //+10% pour géologue
	var RESS_BONUS_FULL   = 0.02;  //+2% pour full officier
	var RESS_PLASMA_M     = 0.01;
	var RESS_PLASMA_C     = 6.6e-4;
	var RESS_PLASMA_D     = 3.3e-4;
	var names = ogame_getElementNames();
//Valeurs OUT par défaut :
	var result = {'prod_reel':0, 'prod_theorique':0, 'ratio':0, 'conso_E':0, 'prod_E':0,   //Production totale
		'prod_CES':0, 'prod_CEF':0, 'prod_SAT':0, 'prod_FOR':0, //production et conso de chaque unité
		'prod_M':0, 'prod_C':0, 'prod_D':0, 'prod_base':0,  //production et conso de chaque unité
		'prod_booster':0, 'prod_off':0, 'prod_Plasma':0, 'prod_classe':0,   //production des bonus
	'nb_FOR_maxed':0, 'M':0,'C':0,'D':0,'NRJ':0};
	result['prod_reel']      = ogame_arrayRessource(0,0,0);
	result['prod_theorique'] = ogame_arrayRessource(0,0,0);
	result['prod_booster']   = ogame_arrayRessource(0,0,0);
	result['prod_off']       = ogame_arrayRessource(0,0,0);
	result['prod_Plasma']    = ogame_arrayRessource(0,0,0);
	result['prod_classe']    = ogame_arrayRessource(0,0,0);
//Valeurs IN par défaut :
	if (typeof(user_building) === 'undefined') { user_building = new Array(); }
	if (user_technology === null) { user_technology = new Array(); }
	if (user_data       === null) { user_data = new Array(); }
	if (server_config   === null) { server_config = new Array(); }
	if (typeof(user_technology['Plasma']) === 'undefined') { user_technology['Plasma'] = 0; }
	if (typeof(user_technology['NRJ']) === 'undefined')    { user_technology['NRJ'] = 0; }
	if (typeof(user_data['off_commandant']) === 'undefined')  { user_data['off_commandant'] = 0; }
	if (typeof(user_data['off_amiral']) === 'undefined')      { user_data['off_amiral'] = 0; }
	if (typeof(user_data['off_ingenieur']) === 'undefined')   { user_data['off_ingenieur'] = 0; }
	if (typeof(user_data['off_geologue']) === 'undefined')    { user_data['off_geologue'] = 0; }
	if (typeof(user_data['off_full']) === 'undefined')        { user_data['off_full'] = 0; }
	if (typeof(user_data['user_class']) === 'undefined')      { user_data['user_class'] = 'none'; }
	if (typeof(user_building['M_percentage']) === 'undefined')   { user_building['M_percentage'] = 100; }
	if (typeof(user_building['C_percentage']) === 'undefined')   { user_building['C_percentage'] = 100; }
	if (typeof(user_building['D_percentage']) === 'undefined')   { user_building['D_percentage'] = 100; }
	if (typeof(user_building['CES_percentage']) === 'undefined') { user_building['CES_percentage'] = 100; }
	if (typeof(user_building['CEF_percentage']) === 'undefined') { user_building['CEF_percentage'] = 100; }
	if (typeof(user_building['Sat_percentage']) === 'undefined') { user_building['Sat_percentage'] = 100; }
	if (typeof(user_building['FOR_percentage']) === 'undefined') { user_building['FOR_percentage'] = 100; }
	if (typeof(user_building['booster_tab']) === 'undefined') { user_building['booster_tab'] = new Array(); }
	if (typeof(user_building['booster_tab']['booster_e_val']) === 'undefined') { user_building['booster_tab']['booster_e_val'] = 0; }
	if (typeof(user_building['booster_tab']['booster_m_val']) === 'undefined') { user_building['booster_tab']['booster_m_val'] = 0; }
	if (typeof(user_building['booster_tab']['booster_c_val']) === 'undefined') { user_building['booster_tab']['booster_c_val'] = 0; }
	if (typeof(user_building['booster_tab']['booster_d_val']) === 'undefined') { user_building['booster_tab']['booster_d_val'] = 0; }
	if (typeof(user_data['production_theorique']) === 'undefined') { user_data['production_theorique'] = false; }

	if (user_data['off_full'] !== 0) {
		user_data['off_ingenieur'] = 1;
		user_data['off_geologue']  = 1;
	}
	if (user_data['off_commandant'] !== 0 && user_data['off_amiral'] !== 0 && user_data['off_ingenieur'] !== 0 && user_data['off_geologue'] !== 0) {
		user_data['off_full'] = 1;
	}    
	if (user_data['user_class'] !== 'COL' && user_building['FOR_percentage'] > 100) {
		user_building['FOR_percentage'] = 100;
	}
	
	var M_per   = user_building['M_percentage'];
	var C_per   = user_building['C_percentage'];
	var D_per   = user_building['D_percentage'];
	var FOR_per = user_building['FOR_percentage'];
//Calcul valeurs de base
	var tmp = [];
	var ratio = 1;
	var prod_base    = ogame_arrayRessource(0,0,0);
	var prod_mine_M  = ogame_arrayRessource(0,0,0);
	var prod_mine_C  = ogame_arrayRessource(0,0,0);
	var prod_mine_D  = ogame_arrayRessource(0,0,0);
	var prod_bat_CES = ogame_arrayRessource(0,0,0);
	var prod_bat_CEF = ogame_arrayRessource(0,0,0);
	var prod_vso_SAT = ogame_arrayRessource(0,0,0);
	var prod_vso_FOR = ogame_arrayRessource(0,0,0);
	tmp = ogame_productionBuilding('base', user_building, null, null, server_config);
	prod_base['M'] = tmp['M']
	prod_base['C'] = tmp['C']
	server_config['final_calcul'] = false;
	tmp  = ogame_productionBuilding('M', user_building, null, user_data, server_config);
	prod_mine_M['M'] = tmp['M']
	prod_mine_M['NRJ'] = tmp['NRJ']
	tmp  = ogame_productionBuilding('C', user_building, null, user_data, server_config);
	prod_mine_C['C'] = tmp['C']
	prod_mine_C['NRJ'] = tmp['NRJ']
	tmp  = ogame_productionBuilding('D', user_building, null, user_data, server_config);
	prod_mine_D['D'] = tmp['D']
	prod_mine_D['NRJ'] = tmp['NRJ']
	tmp = ogame_productionBuilding('CES', user_building, null, user_data, server_config);
	prod_bat_CES['NRJ'] = tmp['NRJ']
	tmp = ogame_productionBuilding('CEF', user_building, user_technology, user_data, server_config);
	prod_bat_CEF['NRJ'] = tmp['NRJ']
	prod_bat_CEF['D'] = tmp['D']
	tmp = ogame_productionBuilding('SAT', user_building, null, user_data, server_config);
	prod_vso_SAT['NRJ'] = tmp['NRJ']
	tmp = ogame_productionBuilding('FOR', user_building, null, user_data, server_config);
	prod_vso_FOR['M'] = tmp['M']
	prod_vso_FOR['C'] = tmp['C']
	prod_vso_FOR['D'] = tmp['D']
	prod_vso_FOR['NRJ'] = tmp['NRJ']
	result['prod_base'] = prod_base;
	result['prod_M']    = prod_mine_M;
	result['prod_C']    = prod_mine_C;
	result['prod_D']    = prod_mine_D;
	result['prod_CES']  = prod_bat_CES;
	result['prod_CEF']  = prod_bat_CEF;
	result['prod_SAT']  = prod_vso_SAT;
	result['prod_FOR']  = prod_vso_FOR;

//Calcul de la consommation d'énergie théorique
	var conso_M   = Math.round( prod_mine_M['NRJ'] * user_building['M_percentage'] / 100 );
	var conso_C   = Math.round( prod_mine_C['NRJ'] * user_building['C_percentage'] / 100 );
	var conso_D   = Math.round( prod_mine_D['NRJ'] * user_building['D_percentage'] / 100 );
	var conso_FOR = Math.round( prod_vso_FOR['NRJ'] * Math.max(1, user_building['FOR_percentage'] * 2 / 100 - 1) );
	var consommation_E = conso_M + conso_C + conso_D + conso_FOR;
	result['conso_E']         = consommation_E;
	result['prod_M']['NRJ']   = conso_M;
	result['prod_C']['NRJ']   = conso_C;
	result['prod_D']['NRJ']   = conso_D;
	result['prod_FOR']['NRJ'] = conso_FOR;
	if (!user_data['production_theorique']) {
	//Calcul de la production d'énergie
		var prod_CES = prod_bat_CES['NRJ'] * user_building['CES_percentage'] / 100;
		var prod_CEF = prod_bat_CEF['NRJ'] * user_building['CEF_percentage'] / 100;
		var prod_SAT = prod_vso_SAT['NRJ'] * user_building['Sat_percentage'] / 100;
		var production_E = prod_CES + prod_CEF + prod_SAT;
		result['prod_booster']['NRJ']    = Math.round( production_E * user_building['booster_tab']['booster_e_val'] / 100 );
		if (user_data['user_class'] === 'COL') {
			result['prod_classe']['NRJ'] = Math.round( production_E * NRJ_BONUS_COL );
		}
		if (user_data['off_ingenieur'] !== 0) {
			result['prod_off']['NRJ']    = Math.round( production_E * NRJ_BONUS_ING );
		}
		if (user_data['off_full'] !== 0) {
			result['prod_off']['NRJ']   += Math.round( production_E * NRJ_BONUS_FULL );
		}
		result['prod_CES']['NRJ'] = Math.round( prod_CES );
		result['prod_CEF']['NRJ'] = Math.round( prod_CEF );
		result['prod_SAT']['NRJ'] = Math.round( prod_SAT );
		production_E  = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];
		production_E += result['prod_booster']['NRJ'] + result['prod_off']['NRJ'] + result['prod_classe']['NRJ'];

	//Calcul ratio
		ratio = 1; // indique le pourcentage à appliquer sur la prod
		var ratio_temp = 1;
		ratio_temp = (consommation_E == 0) ? 0 : (- production_E * 100 / consommation_E) / 100; // fix division par 0
		if (ratio_temp > 1) {
			ratio = 1;
		}  else {
			ratio = ratio_temp;
		}
		result['ratio'] = ratio;
	} else { //Pour le cas d'un calcul théorique
		M_per   = 100;
		C_per   = 100;
		D_per   = 100;
		FOR_per = 100;
		if (user_building['FOR_percentage'] < 100) {
			FOR_per = 100;
		}
		result['prod_FOR']['NRJ'] = Math.round( prod_vso_FOR['NRJ'] * Math.max(1, FOR_per * 2 / 100 - 1) );
		production_E = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];
		
		result['prod_booster']['NRJ']    = Math.round( production_E * user_building['booster_tab']['booster_e_val'] / 100 );
		if (user_data['user_class'] === 'COL') {
			result['prod_classe']['NRJ'] = Math.round( production_E * NRJ_BONUS_COL );
		}
		if (user_data['off_ingenieur'] !== 0) {
			result['prod_off']['NRJ']    = Math.round( production_E * NRJ_BONUS_ING );
		}
		if (user_data['off_full'] !== 0) {
			result['prod_off']['NRJ']   += Math.round( production_E * NRJ_BONUS_FULL );
		}
		result['prod_CES']['NRJ'] = Math.round( result['prod_CES']['NRJ'] );
		result['prod_CEF']['NRJ'] = Math.round( result['prod_CEF']['NRJ'] );
		result['prod_SAT']['NRJ'] = Math.round( result['prod_SAT']['NRJ'] );
		production_E  = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];
		production_E += result['prod_booster']['NRJ'] + result['prod_off']['NRJ'] + result['prod_classe']['NRJ'];
		ratio = 1;
	}
	result['prod_E'] = production_E;

//Calcul de la production
	var production_mine_base=0, conso_CEF=0, prod_off=0, prod_Plasma=0, prod_booster=0, prod_FOR=0, prod_Classe=0;
	var bonus_off_geo  = (user_data['off_geologue'] !== 0)    ? RESS_BONUS_GEO  : 0;
	var bonus_off_full = (user_data['off_full'] !== 0)        ? RESS_BONUS_FULL : 0;
	var bonus_class    = (user_data['user_class'] === 'COL')  ? RESS_BONUS_COL  : 0;
	var bonus_for      = ogame_productionForeuseBonus(user_building, user_data);
	result['nb_FOR_maxed'] = bonus_for['nb_FOR_maxed'];

//*Métal :
	production_mine_base = Math.floor( prod_mine_M['M'] * (M_per / 100) * ratio );
	prod_off     = Math.round( production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full );
	prod_Plasma  = Math.round( production_mine_base * user_technology['Plasma'] * RESS_PLASMA_M );
	prod_booster = Math.round( production_mine_base * user_building['booster_tab']['booster_m_val'] / 100 );
	prod_FOR     = Math.round( production_mine_base * bonus_for['bonus'] * (FOR_per / 100) );
	prod_Classe  = Math.round( production_mine_base * bonus_class );

	result['M'] = prod_base['M'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
	result['prod_off']['M']     = prod_off;
	result['prod_Plasma']['M']  = prod_Plasma;
	result['prod_booster']['M'] = prod_booster;
	result['prod_FOR']['M']     = prod_FOR;
	result['prod_classe']['M']  = prod_Classe;
	result['prod_M']['M']       = production_mine_base;

//*Cristal :
	production_mine_base = Math.floor( prod_mine_C['C'] * (C_per / 100) * ratio );

	prod_off     = Math.round( production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full );
	prod_Plasma  = Math.round( production_mine_base * user_technology['Plasma'] * RESS_PLASMA_C );
	prod_booster = Math.round( production_mine_base * user_building['booster_tab']['booster_c_val'] / 100 );
	prod_FOR     = Math.round( production_mine_base * bonus_for['bonus'] * (FOR_per / 100) );
	prod_Classe  = Math.round( production_mine_base * bonus_class );

	result['C'] = prod_base['C'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
	result['prod_off']['C']     = prod_off;
	result['prod_Plasma']['C']  = prod_Plasma;
	result['prod_booster']['C'] = prod_booster;
	result['prod_FOR']['C']     = prod_FOR;
	result['prod_classe']['C']  = prod_Classe;
	result['prod_C']['C']       = production_mine_base;

//*Deutérium :
	production_mine_base = Math.floor( prod_mine_D['D'] * (D_per / 100) * ratio );

	prod_off     = Math.round( production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full );
	prod_Plasma  = Math.round( production_mine_base * user_technology['Plasma'] * RESS_PLASMA_D );
	prod_booster = Math.round( production_mine_base * user_building['booster_tab']['booster_d_val'] / 100 );
	prod_FOR     = Math.round( production_mine_base * bonus_for['bonus'] * (FOR_per / 100) );
	prod_Classe  = Math.round( production_mine_base * bonus_class );
	conso_CEF    = Math.ceil( prod_bat_CEF['D'] * user_building['CEF_percentage'] / 100 );

	result['D'] = prod_base['D'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
	result['D'] = result['D'] + conso_CEF;
	result['prod_off']['D']     = prod_off;
	result['prod_Plasma']['D']  = prod_Plasma;
	result['prod_booster']['D'] = prod_booster;
	result['prod_FOR']['D']     = prod_FOR;
	result['prod_classe']['D']  = prod_Classe;
	result['prod_CEF']['D']     = conso_CEF;
	result['prod_D']['D']       = production_mine_base;

	for(var RESS in names['RESS']) {
		RESS = names['RESS'][RESS]
		result['prod_reel'][RESS]  = Math.floor(result['prod_base'][RESS]);
		result['prod_reel'][RESS] += Math.floor(result['prod_M'][RESS])   + Math.floor(result['prod_C'][RESS]) + Math.floor(result['prod_D'][RESS]);
		result['prod_reel'][RESS] += Math.floor(result['prod_CES'][RESS]) + Math.floor(result['prod_CEF'][RESS]);
		result['prod_reel'][RESS] += Math.floor(result['prod_SAT'][RESS]) + Math.floor(result['prod_FOR'][RESS]);
		result['prod_reel'][RESS] += Math.floor(result['prod_Plasma'][RESS]) + Math.floor(result['prod_booster'][RESS]);
		result['prod_reel'][RESS] += Math.floor(result['prod_off'][RESS]) + Math.floor(result['prod_classe'][RESS]);
	}
	if (!user_data['production_theorique']) {
		user_data['production_theorique'] = true;
		tmp = []
		tmp = ogame_productionPlanet(user_building, user_technology, user_data, server_config);
		result['prod_theorique'] = tmp['prod_reel'];
	}
	result['NRJ'] = result['prod_reel']['NRJ'];

	return result;
}
// console.log(ogame_productionPlanet({M:38,C:34,D:34,CES:28,CEF:20,CEF_percentage:80,SAT:2200,FOR:858,FOR_percentage:150,coordinates:'::8',temperature_max:47},{Plasma:19,NRJ:20},{user_class:'COL'}))

//Flotte


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
		bonus_class_energie = 0,
		bonus_position = 0;
	
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
	if (building === 'C') {
		if (position == 1) {
			bonus_position = 0.4;
		} else if (position == 2) {
			bonus_position = 0.3;
		} else if (position == 3) {
			bonus_position = 0.2;
		}
	} else if (building === 'M') {
		if (position == 8) {
			bonus_position = 0.35;
		} else if (position == 9 || position == 7) {
			bonus_position = 0.23;
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
			return Math.floor(Math.floor((parseInt(temperatureMax, 10) + 140) / 6) * level * (1 + ingenieur + bonus_class_energie));
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
	nb_max = (parseInt(levelM,10) + parseInt(levelC,10) + parseInt(levelD,10)) * 8 * (1 + bonus_foreuse_max);
	if(nbForeuse > nb_max) {
		nbForeuse = nb_max;
	}
	//Bonus position
	var bonus_position_M = 0;
	var bonus_position_C = 0;
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
	result_M = Math.round(final_bonus_foreuse * (production('M', levelM, temperatureMax, 0, 0, position) - Math.floor(speed * 30  * (1 + bonus_position_M))));
	result_C = Math.round(final_bonus_foreuse * (production('C', levelC, temperatureMax, 0, 0, position) - Math.floor(speed * 15  * (1 + bonus_position_C))));
	result_D = Math.round(final_bonus_foreuse *  production('D', levelD, temperatureMax, 0, 0, position));
	
	document.getElementById('class_collect').value = tmp_class;
	document.getElementById('off_geologue').value = tmp_geo;
	document.getElementById('off_full').value = tmp_off_full;
	
	
	return {'M':result_M, 'C':result_C, 'D':result_D};
}

//Max foreuses
function foreuse_max(levelM, levelC, levelD) {
	var bonus_foreuse_max = 0;
	if (document.getElementById('class_collect').value == 1 && document.getElementById('off_geologue').value == 1) {
			bonus_foreuse_max = 0.1; //+10%
	}
	return (parseInt(levelM,10) + parseInt(levelC,10) + parseInt(levelD,10)) * 8 * (1 + bonus_foreuse_max);
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
	var NRJ = document.getElementById('NRJ').value;
	var Plasma = document.getElementById('Plasma').value;

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

	var nombrePlanete = parseInt(document.getElementById('simu').title,10); // on passe par le titre du tableau pour recuperer le nombre de planetes, la recuperation du nb de colonne ne marchant pas ...

	var i = 0;
	for (j = 101; j <= nombrePlanete + 100; j++) {
		var temperature_max_1 = document.getElementById('temperature_max_' + j).value;
		var M_1_percentage = document.getElementById('M_' + j + '_percentage').value;
		var C_1_percentage = document.getElementById('C_' + j + '_percentage').value;
		var D_1_percentage = document.getElementById('D_' + j + '_percentage').value;
		var CES_1_percentage = document.getElementById('CES_' + j + '_percentage').value;
		var CEF_1_percentage = document.getElementById('CEF_' + j + '_percentage').value;
		var Sat_1_percentage = document.getElementById('Sat_' + j + '_percentage').value;
		var For_1_percentage = document.getElementById('For_' + j + '_percentage').value;
		var M_1_booster = document.getElementById('M_' + j + '_booster').value;
		var C_1_booster = document.getElementById('C_' + j + '_booster').value;
		var D_1_booster = document.getElementById('D_' + j + '_booster').value;
		var E_1_booster = document.getElementById('E_' + j + '_booster').value;
		
		var position = document.getElementById('position_' + j).value;

		M_1[i] = document.getElementById('M_' + j).value;
		C_1[i] = document.getElementById('C_' + j).value;
		D_1[i] = document.getElementById('D_' + j).value;
		CES_1[i] = document.getElementById('CES_' + j).value;
		CEF_1[i] = document.getElementById('CEF_' + j).value;
		Sat_1[i] = document.getElementById('Sat_' + j).value;
		For_1[i] = document.getElementById('For_' + j).value;

		M_1_conso[i] = Math.round(consumption('M', M_1[i]) * M_1_percentage / 100);
		C_1_conso[i] = Math.round(consumption('C', C_1[i]) * C_1_percentage / 100);
		D_1_conso[i] = Math.round(consumption('D', D_1[i]) * D_1_percentage / 100);
		FOR_1_conso[i] = Math.round(consumption('FOR', For_1[i]) * For_1_percentage / 100);
		var energie_conso = M_1_conso[i] + C_1_conso[i] + D_1_conso[i] + FOR_1_conso[i];

		var CES_1_production = production('CES', CES_1[i], temperature_max_1, NRJ) * CES_1_percentage / 100;
		var CEF_1_production = production('CEF', CEF_1[i], temperature_max_1, NRJ) * CEF_1_percentage / 100;
		var Sat_1_production = production_sat(temperature_max_1, Sat_1[i]) * Sat_1_percentage / 100;
		NRJ_1[i] = Math.round((CES_1_production + CEF_1_production + Sat_1_production) * (1 + E_1_booster / 100));

		var NRJ_1_delta = NRJ_1[i] - energie_conso;
		if (NRJ_1_delta < 0) {
			document.getElementById('NRJ_' + j).innerHTML = '<span style="color: red; ">' + format(NRJ_1_delta) + '</span>' + ' / ' + format(NRJ_1[i]);
		} else {
			document.getElementById('NRJ_' + j).innerHTML = format(NRJ_1_delta) + ' / ' + format(NRJ_1[i]);
		}
		if (isNaN(NRJ_1[i])) NRJ_1[i] = 0;

		//Ratio de consommation d'énergie
		var ratio_conso = 0;
		if (energie_conso !== 0) {
			ratio_conso = NRJ_1[i] / energie_conso;
			if (ratio_conso > 1) ratio_conso = 1;
		}
		if (ratio_conso > 0) {
			M_1_prod[i] = Math.round(ratio_conso * production('M', M_1[i], temperature_max_1, NRJ, Plasma, position) * M_1_percentage / 100);
			C_1_prod[i] = Math.round(ratio_conso * production('C', C_1[i], temperature_max_1, NRJ, Plasma, position) * C_1_percentage / 100);
			D_1_prod[i] = Math.round(ratio_conso * production('D', D_1[i], temperature_max_1, NRJ, Plasma, position) * D_1_percentage / 100) - Math.round(consumption('CEF', CEF_1[i]) * CEF_1_percentage / 100);
			prod_for_tmp = production_foreuse(For_1[i], M_1[i], C_1[i], D_1[i], temperature_max_1, position);
			prod_for_tmp['M'] = Math.round(ratio_conso * prod_for_tmp['M'] * For_1_percentage / 100);
			prod_for_tmp['C'] = Math.round(ratio_conso * prod_for_tmp['C'] * For_1_percentage / 100);
			prod_for_tmp['D'] = Math.round(ratio_conso * prod_for_tmp['D'] * For_1_percentage / 100);
			FOR_1_prod[i] = prod_for_tmp;
			
			M_1_prod[i] = M_1_prod[i] + Math.round((ratio_conso * production('M', M_1[i], temperature_max_1, NRJ, 0, position) * M_1_percentage / 100) * (M_1_booster / 100));
			C_1_prod[i] = C_1_prod[i] + Math.round((ratio_conso * production('C', C_1[i], temperature_max_1, NRJ, 0, position) * C_1_percentage / 100) * (C_1_booster / 100));
			D_1_prod[i] = D_1_prod[i] + Math.round((ratio_conso * production('D', D_1[i], temperature_max_1, NRJ, 0, position) * D_1_percentage / 100) * (D_1_booster / 100));
		} else {
			M_1_prod[i] = Math.round(production('M', 0, 0, 0, 0, position));
			C_1_prod[i] = Math.round(production('C', 0, 0, 0, 0, position));
			D_1_prod[i] = Math.round(production('D', 0, 0, 0, 0, position));
			prod_for_tmp = production_foreuse(0, 0, 0, 0, 0, position);
			prod_for_tmp['M'] = Math.round(ratio_conso * prod_for_tmp['M'] * For_1_percentage / 100);
			prod_for_tmp['C'] = Math.round(ratio_conso * prod_for_tmp['C'] * For_1_percentage / 100);
			prod_for_tmp['D'] = Math.round(ratio_conso * prod_for_tmp['D'] * For_1_percentage / 100);
			FOR_1_prod[i] = prod_for_tmp;
		}
		document.getElementById('M_' + j + '_conso').innerHTML = format(M_1_conso[i]);
		document.getElementById('M_' + j + '_prod').innerHTML = format(M_1_prod[i]);
		document.getElementById('C_' + j + '_conso').innerHTML = format(C_1_conso[i]);
		document.getElementById('C_' + j + '_prod').innerHTML = format(C_1_prod[i]);
		document.getElementById('D_' + j + '_conso').innerHTML = format(D_1_conso[i]);
		document.getElementById('D_' + j + '_prod').innerHTML = format(D_1_prod[i]);
		document.getElementById('FOR_' + j + '_conso').innerHTML = format(FOR_1_conso[i]);
		document.getElementById('FOR_' + j + '_prod').innerHTML = format(FOR_1_prod[i]['M']) + ' / ' + format(FOR_1_prod[i]['C']) + ' / ' + format(FOR_1_prod[i]['D']);
		
		document.getElementById('FOR_' + j + '_max').innerHTML = format(foreuse_max(M_1[i], C_1[i], D_1[i]));
		
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
	document.getElementById('M_conso').innerHTML = format(M_conso);
	document.getElementById('M_prod').innerHTML = format(M_prod);
	document.getElementById('C_conso').innerHTML = format(C_conso);
	document.getElementById('C_prod').innerHTML = format(C_prod);
	document.getElementById('D_conso').innerHTML = format(D_conso);
	document.getElementById('D_prod').innerHTML = format(D_prod);
	document.getElementById('FOR_conso').innerHTML = format(FOR_conso);

	//Energie
	var Delta_NRJ = NRJ - (M_conso + C_conso + D_conso + FOR_conso);
	var s_delta = "-";
	if (Delta_NRJ < 0 || isNaN(Delta_NRJ)) s_delta = '<span style="color: red;">' + format(Delta_NRJ) + '</span>';
	else s_delta = '<span style="color: lime;">'+ format(Delta_NRJ) + '</span>';
	document.getElementById('E_NRJ').innerHTML = s_delta + ' / ' + '<span style="color: lime;">' + format(NRJ) + '</span>';

	//
	// Points
	//				 UdR, Nanites, CSp,   HM,   HC,   HD, Lab,  TeraF,DepotR,  Silo,Dock, BaseL, Phalg, PdS
	var init_b_prix = [720, 1600000, 700, 1000, 1500, 2000, 800, 150000, 60000, 41000, 250, 80000, 80000, 8000000];

	// Batiments planetes
	var total_b_pts = 0;
	var total_pts_1 = [];

	j = 101;
	for (i = 0; i < nombrePlanete; i++) {
		var building_1 = document.getElementById('building_' + j).value;
		var b_pts_1 = Math.floor(((60 + 15) * (1 - Math.pow(1.5, M_1[i])) / (-0.5)) + ((48 + 24) * (1 - Math.pow(1.6, C_1[i])) / (-0.6)) + ((225 + 75) * (1 - Math.pow(1.5, D_1[i])) / (-0.5)) + ((75 + 30) * (1 - Math.pow(1.5, CES_1[i])) / (-0.5)) + ((900 + 360 + 180) * (1 - Math.pow(1.8, CEF_1[i])) / (-0.8)));

		building_1 = building_1.split('<>');
		for (k = 0; k < building_1.length; k++) {
			if (building_1[k] !== 0 || building_1[k] !== 100) {
				b_pts_1 += init_b_prix[k] * Math.pow(2, building_1[k] - 1);
			}
		}
		total_pts_1[j] = b_pts_1;
		total_b_pts += b_pts_1;

		document.getElementById('building_pts_' + j).innerHTML = format(Math.round(total_pts_1[j] / 1000));
		j++;
	}
	document.getElementById('total_b_pts').innerHTML = format(Math.round(total_b_pts / 1000));

	var init_d_prix = [2000, 2000, 8000, 37000, 8000, 130000, 20000, 100000, 10000, 25000];

	// Defences planetes
	var total_d_pts = 0;
	j = 101;
	for (i = 0; i < nombrePlanete; i++) {
		var defence_1 = document.getElementById('defence_' + j).value;
		defence_1 = defence_1.split('<>');
		var d_pts_1 = 0;
		for (k = 0; k < defence_1.length; k++) {
			d_pts_1 = d_pts_1 + init_d_prix[k] * defence_1[k];
		}
		total_pts_1[j] += d_pts_1;
		total_d_pts += d_pts_1;
		document.getElementById('defence_pts_'+ j).innerHTML = format(Math.round(total_pts_1[j] / 1000));
		j++;
	}
	document.getElementById('total_d_pts').innerHTML = format(Math.round(total_d_pts / 1000));

	var total_lune_pts = 0;
	var lune_pts_1 = [];
	var t = 201;
	for (i = 0; i < nombrePlanete; i++) {
		var lune_b_1 = document.getElementById('lune_b_' + t).value;
		var lune_defence_1 = document.getElementById('lune_d_' + t).value;

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


		document.getElementById('lune_pts_' + t).innerHTML = format(Math.round(lune_pts_1[i] / 1000));
		t++;
	}
	document.getElementById('total_lune_pts').innerHTML = format(Math.round(total_lune_pts / 1000));

	// Sat planetes
	var total_sat_pts = 0;
	var sat_pts_1 = [];
	j = 101;
	for (i = 0; i < nombrePlanete; i++) {
		var sat_lune_1 = document.getElementById('sat_lune_' + j).value;
		sat_pts_1[i] = Math.round(Sat_1[i] * 2.5 + sat_lune_1 * 2.5);
		total_sat_pts += sat_pts_1[i];
		document.getElementById('sat_pts_' + j).innerHTML = '<span style="color: lime;">' + format(sat_pts_1[i]) + '</span>';
		j++;
	}
	document.getElementById('total_sat_pts').innerHTML = format(total_sat_pts);

	t = 101;
	for (i = 0; i < nombrePlanete; i++) {
		j = i + 100;
		document.getElementById('total_pts_' + t).innerHTML = format(Math.round((total_pts_1[i] + lune_pts_1[j]) / 1000) + sat_pts_1[i]);
		t++;
	}

	// Technologies planete avec le labo de plus au niveau
	var init_t_prix = [1400, 1000, 1000, 800, 1000, 1200, 6000, 1000, 6600, 36000, 300, 1400, 7000, 800000, 0, 16000];

	var techno = document.getElementById('techno').value;
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
	document.getElementById('techno_pts').innerHTML = format(Math.round(techno_pts / 1000));// Cout Total Techno
	document.getElementById('total_pts').innerHTML = format(Math.round((total_b_pts + total_d_pts + total_lune_pts + techno_pts) / 1000) + total_sat_pts);// Cout Total
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
 *	  0 : Galaxie
 *	  1 : Système
 *	  2 : Planète
 * Math.max_type = représente la valeur Math.maximale pour le type donnée (ex. Galaxie=9; Système=499 ...)
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
