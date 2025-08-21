/*eslint camelcase: ["error", {properties: "never"}]*/
/**
 *  @file   js/ogame_formula.js
 *  Bibliothèques des formules Ogame pour des modifications dynamiques.
 *  @author pitch314
 *  @version 2.0, 2021-01-22
 */

function ogame_arrayRessource(metal, cristal, deut, NRJ = 0) {
  return { 'M': metal, 'C': cristal, 'D': deut, 'NRJ': NRJ };
}

function ogame_arraySubDetail(vitesse = 0, fret = 0, conso = 0, civil = true) {
  return { 'vitesse': vitesse, 'fret': fret, 'conso': conso, 'civil': civil };
}

function ogame_getElementNames() {
  const names = {
    'BAT': ['M', 'C', 'D', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'HM', 'HC', 'HD', 'Lab', 'Ter', 'DdR', 'Silo', 'Dock', 'BaLu', 'Pha', 'PoSa'],
    'RECH': ['Esp', 'Ordi', 'Armes', 'Bouclier', 'Protection', 'NRJ', 'Hyp', 'RC', 'RI', 'PH', 'Laser', 'Ions', 'Plasma', 'RRI', 'Graviton', 'Astrophysique'],
    'VSO': ['PT', 'GT', 'CLE', 'CLO', 'CR', 'VB', 'VC', 'REC', 'SE', 'BMD', 'DST', 'EDLM', 'TRA', 'SAT', 'FOR', 'FAU', 'ECL'],
    'DEF': ['LM', 'LLE', 'LLO', 'CG', 'AI', 'LP', 'PB', 'GB', 'MIC', 'MIP'],
    'CLASS': ['none', 'COL', 'GEN', 'EXP'],
    'RESS': ['M', 'C', 'D', 'NRJ']
  };
  return names;
}
function ogame_isElement(nom) {
  const names = ogame_getElementNames();
  for (const type in names) {
    if (names[type].includes(nom)) {
      return type;
    }
  }
  return false;
}
function ogame_findPlanetPosition(coordinates) {
  let position = ogame_findCoordinates(coordinates);
  return position['p'];
}
function ogame_findCoordinates(string_coord) {
  let result = { 'g': 0, 's': 0, 'p': 0 };
  let coordinates_tmp = string_coord.split(':');
  if (coordinates_tmp.length === 3) {
    result['g'] = coordinates_tmp[0];
    result['s'] = coordinates_tmp[1];
    result['p'] = coordinates_tmp[2];
  }
  return result;
}

// Production
function ogame_productionPosition(position) {
  const prod = {
    'M': [0, 0, 0, 0, 0, 0, 0.17, 0.23, 0.35, 0.23, 0.17, 0, 0, 0, 0, 0, 0],
    'C': [0, 0.4, 0.3, 0.2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
  };
  if (position < 1 || position > prod.length) {
    position = 0;
  }
  return { 'M': prod['M'][position], 'C': prod['C'][position], 'D': 0, 'NRJ': 0 };
}
function ogame_productionForeuseMax(mine_M, mine_C, mine_D, user_data = {}) {
  const { off_geologue = 0, off_full = 0, user_class = 'none' } = user_data;
  const FOR_BONUS_COL_GEO = 0.1; // +10% foreuse bonus for COL+GEO
  let nb_foreuse_max = 8 * (mine_M + mine_C + mine_D);

  if (user_class === 'COL' && (off_geologue || off_full)) {
    nb_foreuse_max *= (1 + FOR_BONUS_COL_GEO);
  }

  return Math.floor(nb_foreuse_max);
}
function ogame_productionForeuseBonus(user_building, user_data) {
  let FOR_COEF = 2e-4; //0.02% / foreuse
  let FOR_BONUS_COL = 0.5;    //+50% pour COL
  //Valeurs OUT par défaut :
  let result = { 'bonus': 0, 'nb_FOR_maxed': 0 };
  //Valeurs IN par défaut :
  if (typeof (user_building) === 'undefined') { user_building = []; }
  if (typeof (user_data) === 'undefined') { user_data = []; }
  if (typeof (user_building['M']) === 'undefined') { user_building['M'] = 0; }
  if (typeof (user_building['C']) === 'undefined') { user_building['C'] = 0; }
  if (typeof (user_building['D']) === 'undefined') { user_building['D'] = 0; }
  if (typeof (user_building['FOR']) === 'undefined') { user_building['FOR'] = 0; }
  if (typeof (user_data['user_class']) === 'undefined') { user_data['user_class'] = 'none'; }

  let bonus_foreuse = FOR_COEF;
  let nb_foreuse_max = ogame_productionForeuseMax(user_building['M'], user_building['C'], user_building['D'], user_data);
  let nb_foreuse = user_building['FOR'];

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
function ogame_productionBuilding(building, user_building = null, user_technology = null, user_data = null, server_config = null) {
  const BASE_M = 30;
  const BASE_C = 15;
  //Valeurs OUT par défaut :
  let result = ogame_arrayRessource(0, 0, 0);
  //Valeurs IN par défaut :
  if (user_building === null) { user_building = []; }
  if (user_technology === null) { user_technology = []; }
  if (user_data === null) { user_data = []; }
  if (server_config === null) { server_config = []; }
  if (typeof (user_technology['NRJ']) === 'undefined') { user_technology['NRJ'] = 0; }
  if (typeof (user_building['M']) === 'undefined') { user_building['M'] = 0; }
  if (typeof (user_building['C']) === 'undefined') { user_building['C'] = 0; }
  if (typeof (user_building['D']) === 'undefined') { user_building['D'] = 0; }
  if (typeof (user_building['CES']) === 'undefined') { user_building['CES'] = 0; }
  if (typeof (user_building['CEF']) === 'undefined') { user_building['CEF'] = 0; }
  if (typeof (user_building['FOR']) === 'undefined') { user_building['FOR'] = 0; }
  if (typeof (user_building['SAT']) === 'undefined') { user_building['SAT'] = 0; }
  if (typeof (user_building['temperature_max']) === 'undefined') { user_building['temperature_max'] = 0; }
  if (typeof (user_building['coordinates']) === 'undefined') { user_building['coordinates'] = ''; }
  if (typeof (server_config['speed_uni']) === 'undefined') { server_config['speed_uni'] = 1; }
  if (typeof (server_config['final_calcul']) === 'undefined') { server_config['final_calcul'] = true; }

  user_building['position'] = ogame_findPlanetPosition(user_building['coordinates']);
  let bonus_position = ogame_productionPosition(user_building['position']);
  let level = 0, coef_base = 0, bonus_for = 0, number = 0;
  let production_mine_base = [];

  switch (building) {
    case 'base':
      result['M'] = Math.floor(BASE_M * (1 + bonus_position['M']) * server_config['speed_uni']);
      result['C'] = Math.floor(BASE_C * (1 + bonus_position['C']) * server_config['speed_uni']);
      break;
    case 'M':
      level = user_building['M'];
      coef_base = (1 + bonus_position['M']) * server_config['speed_uni'];
      result['M'] = 30 * level * Math.pow(1.1, level) * coef_base;
      result['NRJ'] = - Math.floor(10 * level * Math.pow(1.1, level));
      break;
    case 'C':
      level = user_building['C'];
      coef_base = (1 + bonus_position['C']) * server_config['speed_uni'];
      result['C'] = 20 * level * Math.pow(1.1, level) * coef_base;
      result['NRJ'] = - Math.floor(10 * level * Math.pow(1.1, level));
      break;
    case 'D':
      level = user_building['D'];
      coef_base = (1 + bonus_position['D']) * server_config['speed_uni'];
      result['D'] = 10 * level * Math.pow(1.1, level) * (1.44 - 0.004 * user_building['temperature_max']) * coef_base;
      result['NRJ'] = - Math.floor(20 * level * Math.pow(1.1, level));
      break;
    case 'CES':
      level = user_building['CES'];
      result['NRJ'] = Math.floor(20 * level * Math.pow(1.1, level));
      break;
    case 'CEF':
      level = user_building['CEF'];
      result['NRJ'] = Math.floor(30 * level * Math.pow((1.05 + user_technology['NRJ'] * 0.01), level));
      result['D'] = - Math.floor(10 * level * Math.pow(1.1, level)) * server_config['speed_uni'];
      break;
    case 'SAT':
      number = user_building['SAT'];
      result['NRJ'] = Math.floor((user_building['temperature_max'] + 140) / 6) * number;
      break;
    case 'FOR':
      number = user_building['FOR'];
      production_mine_base['M'] = ogame_productionBuilding('M', user_building, null, null, server_config)['M'];
      production_mine_base['C'] = ogame_productionBuilding('C', user_building, null, null, server_config)['C'];
      production_mine_base['D'] = ogame_productionBuilding('D', user_building, null, null, server_config)['D'];
      bonus_for = ogame_productionForeuseBonus(user_building, user_data);

      result['M'] = Math.round(production_mine_base['M'] * bonus_for['bonus']);
      result['C'] = Math.round(production_mine_base['C'] * bonus_for['bonus']);
      result['D'] = Math.round(production_mine_base['D'] * bonus_for['bonus']);
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
function ogame_productionPlanet(user_building, user_technology = null, user_data = null, server_config = null) {
  const NRJ_BONUS_COL = 0.1;   //+10% pour COL
  const NRJ_BONUS_ING = 0.1;   //+10% pour ingénieur
  const NRJ_BONUS_FULL = 0.02;  //+2% pour full officier
  const RESS_BONUS_COL = 0.25;  //+25% pour COL
  const RESS_BONUS_GEO = 0.1;   //+10% pour géologue
  const RESS_BONUS_FULL = 0.02;  //+2% pour full officier
  const RESS_PLASMA_M = 0.01;
  const RESS_PLASMA_C = 6.6e-4;
  const RESS_PLASMA_D = 3.3e-4;
  let names = ogame_getElementNames();
  //Valeurs OUT par défaut :
  let result = {
    'prod_reel': 0, 'prod_theorique': 0, 'ratio': 0, 'conso_E': 0, 'prod_E': 0,   //Production totale
    'prod_CES': 0, 'prod_CEF': 0, 'prod_SAT': 0, 'prod_FOR': 0, //production et conso de chaque unité
    'prod_M': 0, 'prod_C': 0, 'prod_D': 0, 'prod_base': 0,  //production et conso de chaque unité
    'prod_booster': 0, 'prod_off': 0, 'prod_Plasma': 0, 'prod_classe': 0,   //production des bonus
    'nb_FOR_maxed': 0, 'M': 0, 'C': 0, 'D': 0, 'NRJ': 0
  };
  result['prod_reel'] = ogame_arrayRessource(0, 0, 0);
  result['prod_theorique'] = ogame_arrayRessource(0, 0, 0);
  result['prod_booster'] = ogame_arrayRessource(0, 0, 0);
  result['prod_off'] = ogame_arrayRessource(0, 0, 0);
  result['prod_Plasma'] = ogame_arrayRessource(0, 0, 0);
  result['prod_classe'] = ogame_arrayRessource(0, 0, 0);
  //Valeurs IN par défaut :
  if (typeof (user_building) === 'undefined') { user_building = []; }
  if (user_technology === null) { user_technology = []; }
  if (user_data === null) { user_data = []; }
  if (server_config === null) { server_config = []; }
  if (typeof (user_technology['Plasma']) === 'undefined') { user_technology['Plasma'] = 0; }
  if (typeof (user_technology['NRJ']) === 'undefined') { user_technology['NRJ'] = 0; }
  if (typeof (user_data['off_commandant']) === 'undefined') { user_data['off_commandant'] = 0; }
  if (typeof (user_data['off_amiral']) === 'undefined') { user_data['off_amiral'] = 0; }
  if (typeof (user_data['off_ingenieur']) === 'undefined') { user_data['off_ingenieur'] = 0; }
  if (typeof (user_data['off_geologue']) === 'undefined') { user_data['off_geologue'] = 0; }
  if (typeof (user_data['off_full']) === 'undefined') { user_data['off_full'] = 0; }
  if (typeof (user_data['user_class']) === 'undefined') { user_data['user_class'] = 'none'; }
  if (typeof (user_building['M_percentage']) === 'undefined') { user_building['M_percentage'] = 100; }
  if (typeof (user_building['C_percentage']) === 'undefined') { user_building['C_percentage'] = 100; }
  if (typeof (user_building['D_percentage']) === 'undefined') { user_building['D_percentage'] = 100; }
  if (typeof (user_building['CES_percentage']) === 'undefined') { user_building['CES_percentage'] = 100; }
  if (typeof (user_building['CEF_percentage']) === 'undefined') { user_building['CEF_percentage'] = 100; }
  if (typeof (user_building['Sat_percentage']) === 'undefined') { user_building['Sat_percentage'] = 100; }
  if (typeof (user_building['FOR_percentage']) === 'undefined') { user_building['FOR_percentage'] = 100; }
  if (typeof (user_building['booster_tab']) === 'undefined') { user_building['booster_tab'] = []; }
  if (typeof (user_building['booster_tab']['booster_e_val']) === 'undefined') { user_building['booster_tab']['booster_e_val'] = 0; }
  if (typeof (user_building['booster_tab']['booster_m_val']) === 'undefined') { user_building['booster_tab']['booster_m_val'] = 0; }
  if (typeof (user_building['booster_tab']['booster_c_val']) === 'undefined') { user_building['booster_tab']['booster_c_val'] = 0; }
  if (typeof (user_building['booster_tab']['booster_d_val']) === 'undefined') { user_building['booster_tab']['booster_d_val'] = 0; }
  if (typeof (user_data['production_theorique']) === 'undefined') { user_data['production_theorique'] = false; }

  if (user_data['off_full'] !== 0) {
    user_data['off_ingenieur'] = 1;
    user_data['off_geologue'] = 1;
  }
  if (user_data['off_commandant'] !== 0 && user_data['off_amiral'] !== 0 && user_data['off_ingenieur'] !== 0 && user_data['off_geologue'] !== 0) {
    user_data['off_full'] = 1;
  }
  if (user_data['user_class'] !== 'COL' && user_building['FOR_percentage'] > 100) {
    user_building['FOR_percentage'] = 100;
  }

  let M_per = user_building['M_percentage'];
  let C_per = user_building['C_percentage'];
  let D_per = user_building['D_percentage'];
  let FOR_per = user_building['FOR_percentage'];
  //Calcul valeurs de base
  let tmp = [];
  let ratio = 1;
  let prod_base = ogame_arrayRessource(0, 0, 0);
  let prod_mine_M = ogame_arrayRessource(0, 0, 0);
  let prod_mine_C = ogame_arrayRessource(0, 0, 0);
  let prod_mine_D = ogame_arrayRessource(0, 0, 0);
  let prod_bat_CES = ogame_arrayRessource(0, 0, 0);
  let prod_bat_CEF = ogame_arrayRessource(0, 0, 0);
  let prod_vso_SAT = ogame_arrayRessource(0, 0, 0);
  let prod_vso_FOR = ogame_arrayRessource(0, 0, 0);
  tmp = ogame_productionBuilding('base', user_building, null, null, server_config);
  prod_base['M'] = tmp['M']
  prod_base['C'] = tmp['C']
  server_config['final_calcul'] = false;
  tmp = ogame_productionBuilding('M', user_building, null, user_data, server_config);
  prod_mine_M['M'] = tmp['M']
  prod_mine_M['NRJ'] = tmp['NRJ']
  tmp = ogame_productionBuilding('C', user_building, null, user_data, server_config);
  prod_mine_C['C'] = tmp['C']
  prod_mine_C['NRJ'] = tmp['NRJ']
  tmp = ogame_productionBuilding('D', user_building, null, user_data, server_config);
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
  result['prod_M'] = prod_mine_M;
  result['prod_C'] = prod_mine_C;
  result['prod_D'] = prod_mine_D;
  result['prod_CES'] = prod_bat_CES;
  result['prod_CEF'] = prod_bat_CEF;
  result['prod_SAT'] = prod_vso_SAT;
  result['prod_FOR'] = prod_vso_FOR;

  //Calcul de la consommation d'énergie théorique
  let conso_M = Math.round(prod_mine_M['NRJ'] * user_building['M_percentage'] / 100);
  let conso_C = Math.round(prod_mine_C['NRJ'] * user_building['C_percentage'] / 100);
  let conso_D = Math.round(prod_mine_D['NRJ'] * user_building['D_percentage'] / 100);
  let conso_FOR = Math.round(prod_vso_FOR['NRJ'] * Math.max(1, user_building['FOR_percentage'] * 2 / 100 - 1));
  let consommation_E = conso_M + conso_C + conso_D + conso_FOR;
  result['conso_E'] = consommation_E;
  result['prod_M']['NRJ'] = conso_M;
  result['prod_C']['NRJ'] = conso_C;
  result['prod_D']['NRJ'] = conso_D;
  result['prod_FOR']['NRJ'] = conso_FOR;
  if (!user_data['production_theorique']) {
    //Calcul de la production d'énergie
    let prod_CES = prod_bat_CES['NRJ'] * user_building['CES_percentage'] / 100;
    let prod_CEF = prod_bat_CEF['NRJ'] * user_building['CEF_percentage'] / 100;
    let prod_SAT = prod_vso_SAT['NRJ'] * user_building['Sat_percentage'] / 100;
    let production_E = prod_CES + prod_CEF + prod_SAT;
    result['prod_booster']['NRJ'] = Math.round(production_E * user_building['booster_tab']['booster_e_val'] / 100);
    if (user_data['user_class'] === 'COL') {
      result['prod_classe']['NRJ'] = Math.round(production_E * NRJ_BONUS_COL);
    }
    if (user_data['off_ingenieur'] !== 0) {
      result['prod_off']['NRJ'] = Math.round(production_E * NRJ_BONUS_ING);
    }
    if (user_data['off_full'] !== 0) {
      result['prod_off']['NRJ'] += Math.round(production_E * NRJ_BONUS_FULL);
    }
    result['prod_CES']['NRJ'] = Math.round(prod_CES);
    result['prod_CEF']['NRJ'] = Math.round(prod_CEF);
    result['prod_SAT']['NRJ'] = Math.round(prod_SAT);
    production_E = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];
    production_E += result['prod_booster']['NRJ'] + result['prod_off']['NRJ'] + result['prod_classe']['NRJ'];

    //Calcul ratio
    ratio = 1; // indique le pourcentage à appliquer sur la prod
    let ratio_temp = 1;
    ratio_temp = (consommation_E === 0) ? 0 : (- production_E * 100 / consommation_E) / 100; // fix division par 0
    if (ratio_temp > 1) {
      ratio = 1;
    } else {
      ratio = ratio_temp;
    }
    result['ratio'] = ratio;
  } else { //Pour le cas d'un calcul théorique
    M_per = 100;
    C_per = 100;
    D_per = 100;
    FOR_per = 100;
    if (user_building['FOR_percentage'] < 100) {
      FOR_per = 100;
    }
    result['prod_FOR']['NRJ'] = Math.round(prod_vso_FOR['NRJ'] * Math.max(1, FOR_per * 2 / 100 - 1));
    let production_E = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];

    result['prod_booster']['NRJ'] = Math.round(production_E * user_building['booster_tab']['booster_e_val'] / 100);
    if (user_data['user_class'] === 'COL') {
      result['prod_classe']['NRJ'] = Math.round(production_E * NRJ_BONUS_COL);
    }
    if (user_data['off_ingenieur'] !== 0) {
      result['prod_off']['NRJ'] = Math.round(production_E * NRJ_BONUS_ING);
    }
    if (user_data['off_full'] !== 0) {
      result['prod_off']['NRJ'] += Math.round(production_E * NRJ_BONUS_FULL);
    }
    result['prod_CES']['NRJ'] = Math.round(result['prod_CES']['NRJ']);
    result['prod_CEF']['NRJ'] = Math.round(result['prod_CEF']['NRJ']);
    result['prod_SAT']['NRJ'] = Math.round(result['prod_SAT']['NRJ']);
    production_E = result['prod_CES']['NRJ'] + result['prod_CEF']['NRJ'] + result['prod_SAT']['NRJ'];
    production_E += result['prod_booster']['NRJ'] + result['prod_off']['NRJ'] + result['prod_classe']['NRJ'];
    ratio = 1;
  }
  result['prod_E'] = production_E;

  //Calcul de la production
  let production_mine_base = 0, conso_CEF = 0, prod_off = 0, prod_Plasma = 0, prod_booster = 0, prod_FOR = 0, prod_Classe = 0;
  let bonus_off_geo = (user_data['off_geologue'] !== 0) ? RESS_BONUS_GEO : 0;
  let bonus_off_full = (user_data['off_full'] !== 0) ? RESS_BONUS_FULL : 0;
  let bonus_class = (user_data['user_class'] === 'COL') ? RESS_BONUS_COL : 0;
  let bonus_for = ogame_productionForeuseBonus(user_building, user_data);
  result['nb_FOR_maxed'] = bonus_for['nb_FOR_maxed'];

  //*Métal :
  production_mine_base = Math.floor(prod_mine_M['M'] * (M_per / 100) * ratio);
  prod_off = Math.round(production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full);
  prod_Plasma = Math.round(production_mine_base * user_technology['Plasma'] * RESS_PLASMA_M);
  prod_booster = Math.round(production_mine_base * user_building['booster_tab']['booster_m_val'] / 100);
  prod_FOR = Math.round(production_mine_base * bonus_for['bonus'] * (FOR_per / 100));
  prod_Classe = Math.round(production_mine_base * bonus_class);

  result['M'] = prod_base['M'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
  result['prod_off']['M'] = prod_off;
  result['prod_Plasma']['M'] = prod_Plasma;
  result['prod_booster']['M'] = prod_booster;
  result['prod_FOR']['M'] = prod_FOR;
  result['prod_classe']['M'] = prod_Classe;
  result['prod_M']['M'] = production_mine_base;

  //*Cristal :
  production_mine_base = Math.floor(prod_mine_C['C'] * (C_per / 100) * ratio);

  prod_off = Math.round(production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full);
  prod_Plasma = Math.round(production_mine_base * user_technology['Plasma'] * RESS_PLASMA_C);
  prod_booster = Math.round(production_mine_base * user_building['booster_tab']['booster_c_val'] / 100);
  prod_FOR = Math.round(production_mine_base * bonus_for['bonus'] * (FOR_per / 100));
  prod_Classe = Math.round(production_mine_base * bonus_class);

  result['C'] = prod_base['C'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
  result['prod_off']['C'] = prod_off;
  result['prod_Plasma']['C'] = prod_Plasma;
  result['prod_booster']['C'] = prod_booster;
  result['prod_FOR']['C'] = prod_FOR;
  result['prod_classe']['C'] = prod_Classe;
  result['prod_C']['C'] = production_mine_base;

  //*Deutérium :
  production_mine_base = Math.floor(prod_mine_D['D'] * (D_per / 100) * ratio);

  prod_off = Math.round(production_mine_base * bonus_off_geo) + Math.round(production_mine_base * bonus_off_full);
  prod_Plasma = Math.round(production_mine_base * user_technology['Plasma'] * RESS_PLASMA_D);
  prod_booster = Math.round(production_mine_base * user_building['booster_tab']['booster_d_val'] / 100);
  prod_FOR = Math.round(production_mine_base * bonus_for['bonus'] * (FOR_per / 100));
  prod_Classe = Math.round(production_mine_base * bonus_class);
  conso_CEF = Math.ceil(prod_bat_CEF['D'] * user_building['CEF_percentage'] / 100);

  result['D'] = prod_base['D'] + production_mine_base + prod_FOR + prod_Plasma + prod_booster + prod_off + prod_Classe;
  result['D'] = result['D'] + conso_CEF;
  result['prod_off']['D'] = prod_off;
  result['prod_Plasma']['D'] = prod_Plasma;
  result['prod_booster']['D'] = prod_booster;
  result['prod_FOR']['D'] = prod_FOR;
  result['prod_classe']['D'] = prod_Classe;
  result['prod_CEF']['D'] = conso_CEF;
  result['prod_D']['D'] = production_mine_base;

  for (let RESS in names['RESS']) {
    RESS = names['RESS'][RESS]
    result['prod_reel'][RESS] = Math.floor(result['prod_base'][RESS]);
    result['prod_reel'][RESS] += Math.floor(result['prod_M'][RESS]) + Math.floor(result['prod_C'][RESS]) + Math.floor(result['prod_D'][RESS]);
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
/**
 *  @brief Return fleet moving details of Ogame vso.
 *
 *  @param[in] string name The name as in Database, all for all element
 *  @return array('vitesse','fret','conso',(bool)'civil')
 */
function ogame_fleetSubDetailsBase(name = 'all') {
  let details_base = {};
  let names = ogame_getElementNames();
  //Coût de base des vaisseaux                   vitesse   ,fret    ,conso,civil)
  details_base['PT'] = ogame_arraySubDetail(5000, 5000, 10);
  details_base['GT'] = ogame_arraySubDetail(7500, 25000, 50);
  details_base['CLE'] = ogame_arraySubDetail(12500, 50, 20, false);
  details_base['CLO'] = ogame_arraySubDetail(10000, 100, 75, false);
  details_base['CR'] = ogame_arraySubDetail(15000, 800, 300, false);
  details_base['VB'] = ogame_arraySubDetail(10000, 1500, 500, false);
  details_base['VC'] = ogame_arraySubDetail(2500, 7500, 1000);
  details_base['REC'] = ogame_arraySubDetail(2000, 20000, 300);
  details_base['SE'] = ogame_arraySubDetail(100000000, 0, 1);
  details_base['BMD'] = ogame_arraySubDetail(400, 500, 700, false);
  details_base['DST'] = ogame_arraySubDetail(5000, 2000, 1000, false);
  details_base['TRA'] = ogame_arraySubDetail(10000, 750, 250, false);
  details_base['EDLM'] = ogame_arraySubDetail(100, 1000000, 1, false);
  details_base['FOR'] = ogame_arraySubDetail();
  details_base['ECL'] = ogame_arraySubDetail(12000, 10000, 300, false);
  details_base['FAU'] = ogame_arraySubDetail(7000, 10000, 1100, false);
  details_base['SAT'] = ogame_arraySubDetail();
  if (name === 'all') {
    return details_base;
  }
  if (typeof (details_base[name]) === 'undefined') {
    return ogame_arraySubDetail();
  }
  return details_base[name];
}
// console.log(ogame_fleetSubDetailsBase())
/**
 *  @brief Calculates technical data of a fleet or defence.
 *  @return array('nom','vitesse','fret','conso',(bool)'civil') of the wanted fleet.
 */
function ogame_fleetSubDetails(name, user_techno = null, classe = 'none') {
  const RC_COEF = 0.1;
  const RI_COEF = 0.2;
  const PH_COEF = 0.3;
  const HYP_COEF = 0.05;
  //Valeurs IN par défaut :
  if (user_techno === null) { user_techno = []; }
  if (typeof (user_techno['RC']) === 'undefined') { user_techno['RC'] = 0; }
  if (typeof (user_techno['RI']) === 'undefined') { user_techno['RI'] = 0; }
  if (typeof (user_techno['PH']) === 'undefined') { user_techno['PH'] = 0; }
  if (typeof (user_techno['Hyp']) === 'undefined') { user_techno['Hyp'] = 0; }

  user_techno['speed'] = 0;  //local variable pour la vitesse
  let base_detail = ogame_fleetSubDetailsBase(name);
  let techno_RC_coef = user_techno['RC'] * RC_COEF;
  let techno_RI_coef = user_techno['RI'] * RI_COEF;
  let techno_PH_coef = user_techno['PH'] * PH_COEF;
  let techno_Hyp_coef = user_techno['Hyp'] * HYP_COEF;
  if (name === 'PT' || name === 'GT' || name === 'CLE' || name === 'SE' || name === 'REC') { //vso avec le réacteur à combustion.
    user_techno['speed'] = techno_RC_coef;
  } else if (name === 'CLO' || name === 'CR' || name === 'VC' || name === 'BMD') {
    user_techno['speed'] = techno_RI_coef;
  } else if (name === 'VB' || name === 'DST' || name === 'TRA' || name === 'EDLM' || name === 'ECL' || name === 'FAU') {
    user_techno['speed'] = techno_PH_coef;
  }
  if (name === 'PT' && user_techno['RI'] >= 5) {
    base_detail['vitesse'] = 10000;
    base_detail['conso'] = 20;
    user_techno['speed'] = techno_RI_coef;
  }
  if (name === 'REC') {
    if (user_techno['RI'] >= 17) {
      base_detail['vitesse'] = 4000;
      base_detail['conso'] = 600;
      user_techno['speed'] = techno_RI_coef;
    }
    if (user_techno['PH'] >= 15) {
      base_detail['vitesse'] = 6000;
      base_detail['conso'] = 900;
      user_techno['speed'] = techno_PH_coef;
    }
  }
  if (name === 'BMD' && user_techno['PH'] >= 8) {
    base_detail['vitesse'] = 500;
    user_techno['speed'] = techno_PH_coef;
  }

  let vitesse = base_detail['vitesse'];
  let fret = base_detail['fret'];
  let conso = base_detail['conso'];
  let bonus_class = 0;
  //Vitesse
  if (classe === 'COL') {
    if (name === 'PT' || name === 'GT') {
      bonus_class = 1; //+100%
    }
  } else if (classe === 'GEN') {
    if (!base_detail['civil'] && name !== 'EDLM' || name === 'REC') {
      bonus_class = 1; //+100%
    }
  }
  vitesse = Math.round(vitesse * (1 + user_techno['speed'] + bonus_class));
  //fret
  bonus_class = 0;
  if (classe === 'COL') {
    if (name === 'PT' || name === 'GT') {
      bonus_class = 0.25; //+25%
    }
  } else if (classe === 'GEN') {
    if (name === 'REC' || name === 'ECL') {
      bonus_class = 0.2; //+20%
    }
  }
  fret = Math.round(fret * (1 + techno_Hyp_coef + bonus_class));
  //conso
  bonus_class = 0;
  if (classe === 'GEN') {
    bonus_class = -0.25;    //-25%
  }
  conso = Math.round(conso * (1 + bonus_class));
  if (conso < 1) {
    conso = 1;
  }
  base_detail['vitesse'] = vitesse;
  base_detail['fret'] = fret;
  base_detail['conso'] = conso;
  base_detail['nom'] = name;
  return base_detail;
}
// console.log(ogame_fleetSubDetails('PT',{'Hyp':18,'RC':21,'RI':17,'PH':16},'GEN'));
function ogame_fleetConsoStatio(conso, hour) {
  let result = hour * conso / 10;
  if (result < 1) {
    result = 1;
  }
  if (hour === 0) {
    result = 0;
  }
  return Math.floor(result);
}
function ogame_fleetSlowestSpeed(fleet, user_techno = null, classe = 'none') {
  let names = ogame_getElementNames();
  let details = {};
  let max_speed = ogame_fleetSubDetails('SE', user_techno, classe);   //The fastest fleet
  let min_speed = max_speed['vitesse'];
  for (let elem in names['VSO']) {
    elem = names['VSO'][elem];
    if (typeof (fleet[elem]) !== 'undefined' && fleet[elem] !== 0) {
      details = ogame_fleetSubDetails(elem, user_techno, classe);
      if (min_speed > details['vitesse']) {
        min_speed = details['vitesse'];
      }
    }
  }
  return min_speed;
}
function ogame_fleetDistance(a, b, user_techno = null, classe = 'none', server_config = null) {
  let result = { 'distance': 0, 'type': 'p' };
  if (user_techno === null) { user_techno = []; }
  if (server_config === null) { server_config = []; }
  if (typeof (user_techno['RC']) === 'undefined') { user_techno['RC'] = 0; }
  if (typeof (server_config['num_of_galaxies']) === 'undefined') { server_config['num_of_galaxies'] = 9; }
  if (typeof (server_config['num_of_systems']) === 'undefined') { server_config['num_of_systems'] = 499; }
  if (typeof (server_config['donutGalaxy']) === 'undefined') { server_config['donutGalaxy'] = 1; }
  if (typeof (server_config['donutSystem']) === 'undefined') { server_config['donutSystem'] = 1; }

  let dist_abs = 0;
  let max_type = { 'g': server_config['num_of_galaxies'], 's': server_config['num_of_systems'], 'p': 0 };
  let uni_arrondi = { 'g': true, 's': true, 'p': false }; //Par défaut
  if (server_config['donutGalaxy'] === 0) {
    max_type['g'] = 0;
    uni_arrondi['g'] = false;
  }
  if (server_config['donutSystem'] === 0) {
    max_type['s'] = 0;
    uni_arrondi['s'] = false;
  }
  let coord_a = ogame_findCoordinates(a);
  let coord_b = ogame_findCoordinates(b);
  let key = 'p';
  for (let key in coord_a) {    //On ne calcule la distance qu'entre des vraies coordonnées.
    if (coord_a[key] === 0 || coord_b[key] === 0) {
      coord_a[key] = 0;
      coord_b[key] = 0;
    }
    dist_abs = Math.abs(coord_a[key] - coord_b[key]);   //|a-b|
    if (dist_abs !== 0) {
      break;
    }
  }
  result['type'] = key;
  result['distance'] = dist_abs;    //|a-b|
  if (uni_arrondi[key] && (dist_abs > max_type[key] / 2)) {
    result['distance'] = Math.abs(dist_abs - max_type[key]); //||a-b| - base|
  }
  return result;
}

function ogame_fleetSend(coord_from, coord_to, fleet, speed_per = 100, user_techno = null, classe = 'none', server_config = null, type = '', hour_mission = 0) {
  let result = { 'conso': 0, 'time': 0 };
  let names = ogame_getElementNames();
  let details = {};
  let consos = {};
  let max_speed = ogame_fleetSubDetails('SE', user_techno, classe);   //The fastest fleet
  let min_speed = max_speed['vitesse'];
  let conso_sum = 0;
  for (let elem in names['VSO']) {
    elem = names['VSO'][elem];
    consos[elem] = 0;
    if (typeof (fleet[elem]) !== 'undefined' && fleet[elem] !== 0) {
      details = ogame_fleetSubDetails(elem, user_techno, classe);
      if (min_speed > details['vitesse']) {
        min_speed = details['vitesse'];
      }
      consos[elem] = details['conso'] * fleet[elem];
      conso_sum += consos[elem];
    }
  }
  if (min_speed === 0) { //Ne devrait jamais arriver mais pour éviter une div/0.
    return $result;
  }
  let distance = ogame_fleetDistance(coord_from, coord_to, server_config);
  if (type === 'fuite') {
    distance['type'] = type;
  }
  switch (distance['type']) {
    case 'g':   //between galaxy
      result['time'] = (10 + (35000 / speed_per * Math.sqrt(distance['distance'] * 20000000 / min_speed)));
      result['conso'] = 1 + (conso_sum * ((4 * distance['distance']) / 7) * Math.pow(speed_per / 100 + 1, 2));
      break;
    case 's':   //between system (so inside same galaxy)
      result['time'] = (10 + (35000 / speed_per * Math.sqrt((2700000 + distance['distance'] * 95000) / min_speed)));
      result['conso'] = 1 + (conso_sum * ((2700 + 95 * distance['distance']) / 35000) * Math.pow(speed_per / 100 + 1, 2));
      break;
    case 'p':   //between sub-system (so in same galaxy and same system)
      if (distance['distance'] === 0) { // to moon/cdr
        result['time'] = (10 + (35000 / speed_per * Math.sqrt(5000 / min_speed)));
        result['conso'] = 1 + (conso_sum * (5 / 35000) * Math.pow(speed_per / 100 + 1, 2));
      } else { //to other planet in same system
        result['time'] = (10 + (35000 / speed_per * Math.sqrt((1000000 + distance['distance'] * 5000) / min_speed)));
        result['conso'] = 1 + (conso_sum * ((1000 + 5 * distance['distance']) / 35000) * Math.pow(speed_per / 100 + 1, 2));
      }
      break;
    case 'fuite':
      distance['distance'] = 1 * 1.5;
      result['conso'] = (conso_sum * distance['distance']);  //???
    default:
      break;
  }
  if (type === 'statio' || type === 'expe') {
    result['conso'] += ogame_fleetConsoStatio(conso_sum, hour_mission);
    // result['time']  += hour_mission * 3600;
  }
  result['time'] = Math.round(result['time']);
  result['conso'] = Math.ceil(result['conso']);
  return result;
}

function ogame_elementCoutBase(name = 'all') {
  let cout_base = {};
  //Coût de base des bâtiments                           métal , cristal, deutérium, NRJ
  cout_base['M'] = ogame_arrayRessource(60, 15, 0);
  cout_base['C'] = ogame_arrayRessource(48, 24, 0);
  cout_base['D'] = ogame_arrayRessource(225, 75, 0);
  cout_base['CES'] = ogame_arrayRessource(75, 30, 0);
  cout_base['CEF'] = ogame_arrayRessource(900, 360, 180);
  cout_base['UdR'] = ogame_arrayRessource(400, 120, 200);
  cout_base['UdN'] = ogame_arrayRessource(1000000, 500000, 100000);
  cout_base['CSp'] = ogame_arrayRessource(400, 200, 100);
  cout_base['HM'] = ogame_arrayRessource(1000, 0, 0);
  cout_base['HC'] = ogame_arrayRessource(1000, 500, 0);
  cout_base['HD'] = ogame_arrayRessource(1000, 1000, 0);
  cout_base['Lab'] = ogame_arrayRessource(200, 400, 200);
  cout_base['Ter'] = ogame_arrayRessource(0, 50000, 100000, 1000);
  cout_base['DdR'] = ogame_arrayRessource(20000, 40000, 0);
  cout_base['Silo'] = ogame_arrayRessource(20000, 20000, 1000);
  cout_base['Dock'] = ogame_arrayRessource(200, 0, 50, 50);
  cout_base['BaLu'] = ogame_arrayRessource(20000, 40000, 20000);
  cout_base['Pha'] = ogame_arrayRessource(20000, 40000, 20000);
  cout_base['PoSa'] = ogame_arrayRessource(2000000, 4000000, 2000000);
  //Coût de base des recherches
  cout_base['Esp'] = ogame_arrayRessource(200, 1000, 200);
  cout_base['Ordi'] = ogame_arrayRessource(0, 400, 600);
  cout_base['Armes'] = ogame_arrayRessource(800, 200, 0);
  cout_base['Bouclier'] = ogame_arrayRessource(200, 600, 0);
  cout_base['Protection'] = ogame_arrayRessource(1000, 0, 0);
  cout_base['NRJ'] = ogame_arrayRessource(0, 800, 400);
  cout_base['Hyp'] = ogame_arrayRessource(0, 4000, 2000);
  cout_base['RC'] = ogame_arrayRessource(400, 0, 600);
  cout_base['RI'] = ogame_arrayRessource(2000, 4000, 600);
  cout_base['PH'] = ogame_arrayRessource(10000, 20000, 6000);
  cout_base['Laser'] = ogame_arrayRessource(200, 100, 0);
  cout_base['Ions'] = ogame_arrayRessource(1000, 300, 100);
  cout_base['Plasma'] = ogame_arrayRessource(2000, 4000, 1000);
  cout_base['RRI'] = ogame_arrayRessource(240000, 400000, 160000);
  cout_base['Graviton'] = ogame_arrayRessource(0, 0, 0, 300000);
  cout_base['Astrophysique'] = ogame_arrayRessource(4000, 8000, 4000);
  //Coût de base des vaisseaux
  cout_base['PT'] = ogame_arrayRessource(2000, 2000, 0);
  cout_base['GT'] = ogame_arrayRessource(6000, 6000, 0);
  cout_base['CLE'] = ogame_arrayRessource(3000, 1000, 0);
  cout_base['CLO'] = ogame_arrayRessource(6000, 4000, 0);
  cout_base['CR'] = ogame_arrayRessource(20000, 7000, 2000);
  cout_base['VB'] = ogame_arrayRessource(45000, 15000, 0);
  cout_base['VC'] = ogame_arrayRessource(10000, 20000, 10000);
  cout_base['REC'] = ogame_arrayRessource(10000, 6000, 2000);
  cout_base['SE'] = ogame_arrayRessource(0, 1000, 0);
  cout_base['BMD'] = ogame_arrayRessource(50000, 25000, 15000);
  cout_base['DST'] = ogame_arrayRessource(60000, 50000, 15000);
  cout_base['TRA'] = ogame_arrayRessource(30000, 40000, 15000);
  cout_base['EDLM'] = ogame_arrayRessource(5000000, 4000000, 1000000);
  cout_base['FOR'] = ogame_arrayRessource(2000, 2000, 1000);
  cout_base['ECL'] = ogame_arrayRessource(8000, 15000, 8000);
  cout_base['FAU'] = ogame_arrayRessource(85000, 55000, 20000);
  cout_base['SAT'] = ogame_arrayRessource(0, 2000, 500);
  //Coût de base des défenses
  cout_base['LM'] = ogame_arrayRessource(2000, 0, 0);
  cout_base['LLE'] = ogame_arrayRessource(1500, 500, 0);
  cout_base['LLO'] = ogame_arrayRessource(6000, 2000, 0);
  cout_base['CG'] = ogame_arrayRessource(20000, 15000, 2000);
  cout_base['AI'] = ogame_arrayRessource(5000, 3000, 0);
  cout_base['LP'] = ogame_arrayRessource(50000, 50000, 30000);
  cout_base['PB'] = ogame_arrayRessource(10000, 10000, 0);
  cout_base['GB'] = ogame_arrayRessource(50000, 50000, 0);
  cout_base['MIC'] = ogame_arrayRessource(8000, 0, 2000);
  cout_base['MIP'] = ogame_arrayRessource(12500, 2500, 10000);
  if (name === 'all') {
    return cout_base;
  }
  if (typeof (cout_base[$name]) === 'undefined') {
    return ogame_arrayRessource(0, 0, 0);
  }
  return cout_base[name];
}
// console.log(ogame_elementCoutBase())

// Production par heure
function production(building, level, temperatureMax, energy, plasma, position) {
  if (typeof (plasma) == 'undefined') {
    plasma = 0;
  }
  if (typeof (position) == 'undefined') {
    position = 0;
  }
  // Convert position to integer to avoid type coercion issues
  position = parseInt(position, 10) || 0;

  let speed = Number(document.getElementById('vitesse_uni').value),
    ingenieur = Number(document.getElementById('off_ingenieur').value) === 1 ? 0.1 : 0,
    geologue = Number(document.getElementById('off_geologue').value) === 1 ? 0.1 : 0;
  let bonus_class_mine = 0,
    bonus_class_energie = 0,
    bonus_position = 0;

  if (Number(document.getElementById('off_full').value) === 1) {
    ingenieur = 0.12;
    geologue = 0.12;
  }
  if (Number(document.getElementById('class_collect').value) === 1) {
    bonus_class_mine = 0.25; //+25%
    bonus_class_energie = 0.10; //+10%
  }
  //Bonus position
  bonus_position = 0;
  if (building === 'C') {
    if (position === 1) {
      bonus_position = 0.4;
    } else if (position === 2) {
      bonus_position = 0.3;
    } else if (position === 3) {
      bonus_position = 0.2;
    }
  } else if (building === 'M') {
    if (position === 8) {
      bonus_position = 0.35;
    } else if (position === 9 || position === 7) {
      bonus_position = 0.23;
    } else if (position === 10 || position === 6) {
      bonus_position = 0.17;
    }
  }
  switch (building) {
    case 'M':
      return Math.floor(speed * 30 * level * Math.pow(1.1, level) * (1 + bonus_position) * (1 + geologue + 0.01 * plasma + bonus_class_mine)) + Math.floor(speed * 30 * (1 + bonus_position));
    case 'C':
      return Math.floor(speed * 20 * level * Math.pow(1.1, level) * (1 + bonus_position) * (1 + geologue + 0.0066 * plasma + bonus_class_mine)) + Math.floor(speed * 15 * (1 + bonus_position));
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
  if (typeof (position) == 'undefined') {
    position = 0;
  }
  let speed = document.getElementById('vitesse_uni').value;
  let bonus_foreuse = 0.0002,
    bonus_foreuse_max = 0;
  let nb_max;
  if (document.getElementById('class_collect').value == 1) {
    bonus_foreuse = bonus_foreuse * 1.5; //+50%
    if (document.getElementById('off_geologue').value == 1) {
      bonus_foreuse_max = 0.1; //+10%
    }
  }
  nb_max = (parseInt(levelM, 10) + parseInt(levelC, 10) + parseInt(levelD, 10)) * 8 * (1 + bonus_foreuse_max);
  if (nbForeuse > nb_max) {
    nbForeuse = nb_max;
  }
  //Bonus position
  let bonus_position_M = 0;
  let bonus_position_C = 0;
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
  let tmp_class = document.getElementById('class_collect').value;
  let tmp_geo = document.getElementById('off_geologue').value;
  let tmp_off_full = document.getElementById('off_full').value;
  document.getElementById('class_collect').value = 0;
  document.getElementById('off_geologue').value = 0;
  document.getElementById('off_full').value = 0;

  let final_bonus_foreuse = Math.min(0.5, bonus_foreuse * nbForeuse);
  let result_M = Math.round(final_bonus_foreuse * (production('M', levelM, temperatureMax, 0, 0, position) - Math.floor(speed * 30 * (1 + bonus_position_M))));
  let result_C = Math.round(final_bonus_foreuse * (production('C', levelC, temperatureMax, 0, 0, position) - Math.floor(speed * 15 * (1 + bonus_position_C))));
  let result_D = Math.round(final_bonus_foreuse * production('D', levelD, temperatureMax, 0, 0, position));

  document.getElementById('class_collect').value = tmp_class;
  document.getElementById('off_geologue').value = tmp_geo;
  document.getElementById('off_full').value = tmp_off_full;


  return { 'M': result_M, 'C': result_C, 'D': result_D };
}

//Max foreuses
function foreuse_max(levelM, levelC, levelD) {
  let bonus_foreuse_max = 0;
  if (document.getElementById('class_collect').value == 1 && document.getElementById('off_geologue').value == 1) {
    bonus_foreuse_max = 0.1; //+10%
  }
  return (parseInt(levelM, 10) + parseInt(levelC, 10) + parseInt(levelD, 10)) * 8 * (1 + bonus_foreuse_max);
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
function update_page(planetsIdList, planetBuildings, technologies, planetDefenses) {
  console.log(planetsIdList);
  console.log(planetBuildings);
  console.log(technologies);
  console.log(planetDefenses);

  let j = 0;
  let NRJ = document.getElementById('NRJ').value;
  let Plasma = document.getElementById('Plasma').value;

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
  let M_1_conso = [];
  let M_1_prod = [];
  let C_1_conso = [];
  let C_1_prod = [];
  let D_1_conso = [];
  let D_1_prod = [];
  let FOR_1_conso = [];
  let FOR_1_prod = [];

  let NRJ_1 = [];

  let M_1 = [];
  let C_1 = [];
  let D_1 = [];

  let CES_1 = [];
  let CEF_1 = [];
  let Sat_1 = [];
  let For_1 = [];

  planetsIdList.forEach(planetId => {
    let temperature_max_1 = document.getElementById('temperature_max_' + planetId).value;
    let M_1_percentage = document.getElementById('M_' + planetId + '_percentage').value;
    let C_1_percentage = document.getElementById('C_' + planetId + '_percentage').value;
    let D_1_percentage = document.getElementById('D_' + planetId + '_percentage').value;
    let CES_1_percentage = document.getElementById('CES_' + planetId + '_percentage').value;
    let CEF_1_percentage = document.getElementById('CEF_' + planetId + '_percentage').value;
    let Sat_1_percentage = document.getElementById('Sat_' + planetId + '_percentage').value;
    let For_1_percentage = document.getElementById('For_' + planetId + '_percentage').value;
    let M_1_booster = document.getElementById('M_' + planetId + '_booster').value;
    let C_1_booster = document.getElementById('C_' + planetId + '_booster').value;
    let D_1_booster = document.getElementById('D_' + planetId + '_booster').value;
    let E_1_booster = document.getElementById('E_' + planetId + '_booster').value;

    let position = document.getElementById('position_' + planetId).value;

    M_1[planetId] = document.getElementById('M_' + planetId).value;
    C_1[planetId] = document.getElementById('C_' + planetId).value;
    D_1[planetId] = document.getElementById('D_' + planetId).value;
    CES_1[planetId] = document.getElementById('CES_' + planetId).value;
    CEF_1[planetId] = document.getElementById('CEF_' + planetId).value;
    Sat_1[planetId] = document.getElementById('Sat_' + planetId).value;
    For_1[planetId] = document.getElementById('For_' + planetId).value;

    M_1_conso[planetId] = Math.round(consumption('M', M_1[planetId]) * M_1_percentage / 100);
    C_1_conso[planetId] = Math.round(consumption('C', C_1[planetId]) * C_1_percentage / 100);
    D_1_conso[planetId] = Math.round(consumption('D', D_1[planetId]) * D_1_percentage / 100);
    FOR_1_conso[planetId] = Math.round(consumption('FOR', For_1[planetId]) * For_1_percentage / 100);
    let energie_conso = M_1_conso[planetId] + C_1_conso[planetId] + D_1_conso[planetId] + FOR_1_conso[planetId];

    let CES_1_production = production('CES', CES_1[planetId], temperature_max_1, NRJ) * CES_1_percentage / 100;
    let CEF_1_production = production('CEF', CEF_1[planetId], temperature_max_1, NRJ) * CEF_1_percentage / 100;
    let Sat_1_production = production_sat(temperature_max_1, Sat_1[planetId]) * Sat_1_percentage / 100;
    NRJ_1[planetId] = Math.round((CES_1_production + CEF_1_production + Sat_1_production) * (1 + E_1_booster / 100));

    let NRJ_1_delta = NRJ_1[planetId] - energie_conso;
    if (NRJ_1_delta < 0) {
      document.getElementById('NRJ_' + planetId).innerHTML = '<span class="og-alert">' + format(NRJ_1_delta) + '</span>' + ' / ' + format(NRJ_1[planetId]);
    } else {
      document.getElementById('NRJ_' + planetId).innerHTML = format(NRJ_1_delta) + ' / ' + format(NRJ_1[planetId]);
    }
    if (isNaN(NRJ_1[planetId])) NRJ_1[planetId] = 0;

    //Ratio de consommation d'énergie
    let ratio_conso = 0;
    if (energie_conso !== 0) {
      ratio_conso = NRJ_1[planetId] / energie_conso;
      if (ratio_conso > 1) ratio_conso = 1;
    }
    if (ratio_conso > 0) {
      M_1_prod[planetId] = Math.round(ratio_conso * production('M', M_1[planetId], temperature_max_1, NRJ, Plasma, position) * M_1_percentage / 100);
      C_1_prod[planetId] = Math.round(ratio_conso * production('C', C_1[planetId], temperature_max_1, NRJ, Plasma, position) * C_1_percentage / 100);
      D_1_prod[planetId] = Math.round(ratio_conso * production('D', D_1[planetId], temperature_max_1, NRJ, Plasma, position) * D_1_percentage / 100) - Math.round(consumption('CEF', CEF_1[planetId]) * CEF_1_percentage / 100);
      let prod_for_tmp = production_foreuse(For_1[planetId], M_1[planetId], C_1[planetId], D_1[planetId], temperature_max_1, position);
      prod_for_tmp['M'] = Math.round(ratio_conso * prod_for_tmp['M'] * For_1_percentage / 100);
      prod_for_tmp['C'] = Math.round(ratio_conso * prod_for_tmp['C'] * For_1_percentage / 100);
      prod_for_tmp['D'] = Math.round(ratio_conso * prod_for_tmp['D'] * For_1_percentage / 100);
      FOR_1_prod[planetId] = prod_for_tmp;

      M_1_prod[planetId] = M_1_prod[planetId] + Math.round((ratio_conso * production('M', M_1[planetId], temperature_max_1, NRJ, 0, position) * M_1_percentage / 100) * (M_1_booster / 100));
      C_1_prod[planetId] = C_1_prod[planetId] + Math.round((ratio_conso * production('C', C_1[planetId], temperature_max_1, NRJ, 0, position) * C_1_percentage / 100) * (C_1_booster / 100));
      D_1_prod[planetId] = D_1_prod[planetId] + Math.round((ratio_conso * production('D', D_1[planetId], temperature_max_1, NRJ, 0, position) * D_1_percentage / 100) * (D_1_booster / 100));
    } else {
      M_1_prod[planetId] = Math.round(production('M', 0, 0, 0, 0, position));
      C_1_prod[planetId] = Math.round(production('C', 0, 0, 0, 0, position));
      D_1_prod[planetId] = Math.round(production('D', 0, 0, 0, 0, position));
      let prod_for_tmp = production_foreuse(0, 0, 0, 0, 0, position);
      prod_for_tmp['M'] = Math.round(ratio_conso * prod_for_tmp['M'] * For_1_percentage / 100);
      prod_for_tmp['C'] = Math.round(ratio_conso * prod_for_tmp['C'] * For_1_percentage / 100);
      prod_for_tmp['D'] = Math.round(ratio_conso * prod_for_tmp['D'] * For_1_percentage / 100);
      FOR_1_prod[planetId] = prod_for_tmp;
    }
    document.getElementById('M_' + planetId + '_conso').innerHTML = format(M_1_conso[planetId]);
    document.getElementById('M_' + planetId + '_prod').innerHTML = format(M_1_prod[planetId]);
    document.getElementById('C_' + planetId + '_conso').innerHTML = format(C_1_conso[planetId]);
    document.getElementById('C_' + planetId + '_prod').innerHTML = format(C_1_prod[planetId]);
    document.getElementById('D_' + planetId + '_conso').innerHTML = format(D_1_conso[planetId]);
    document.getElementById('D_' + planetId + '_prod').innerHTML = format(D_1_prod[planetId]);
    document.getElementById('FOR_' + planetId + '_conso').innerHTML = format(FOR_1_conso[planetId]);
    document.getElementById('FOR_' + planetId + '_prod').innerHTML = format(FOR_1_prod[planetId]['M']) + ' / ' + format(FOR_1_prod[planetId]['C']) + ' / ' + format(FOR_1_prod[planetId]['D']);

    document.getElementById('FOR_' + planetId + '_max').innerHTML = format(foreuse_max(M_1[planetId], C_1[planetId], D_1[planetId]));
  });

  //
  // Totaux
  //
  let M_conso = 0;
  let M_prod = 0;
  let C_conso = 0;
  let C_prod = 0;
  let D_conso = 0;
  let D_prod = 0;
  let FOR_conso = 0;
  NRJ = 0;

  planetsIdList.forEach(planetId => {
    M_conso = M_conso + M_1_conso[planetId];
    M_prod = M_prod + M_1_prod[planetId] + FOR_1_prod[planetId]["M"];
    C_conso = C_conso + C_1_conso[planetId];
    C_prod = C_prod + C_1_prod[planetId] + FOR_1_prod[planetId]["C"];
    D_conso = D_conso + D_1_conso[planetId];
    D_prod = D_prod + D_1_prod[planetId] + FOR_1_prod[planetId]["D"];
    FOR_conso = FOR_conso + FOR_1_conso[planetId];
    NRJ += NRJ_1[planetId];
  });
  document.getElementById('M_conso').innerHTML = format(M_conso);
  document.getElementById('M_prod').innerHTML = format(M_prod);
  document.getElementById('C_conso').innerHTML = format(C_conso);
  document.getElementById('C_prod').innerHTML = format(C_prod);
  document.getElementById('D_conso').innerHTML = format(D_conso);
  document.getElementById('D_prod').innerHTML = format(D_prod);
  document.getElementById('FOR_conso').innerHTML = format(FOR_conso);

  //Energie
  let Delta_NRJ = NRJ - (M_conso + C_conso + D_conso + FOR_conso);
  let s_delta = "-";
  if (Delta_NRJ < 0 || isNaN(Delta_NRJ)) s_delta = '<span class="og-alert">' + format(Delta_NRJ) + '</span>';
  else s_delta = '<span>' + format(Delta_NRJ) + '</span>';
  document.getElementById('E_NRJ').innerHTML = s_delta + ' / ' + '<span>' + format(NRJ) + '</span>';

  //
  // Building Points
  //
  const init_b_prix = {
    'UdR': 720,
    'UdN': 1600000,
    'CSp': 700,
    'HM': 1000,
    'HC': 1500,
    'HD': 2000,
    'Lab': 800,
    'Ter': 150000,
    'DdR': 60000,
    'Silo': 41000,
    'Dock': 250,
    'BaLu': 80000,
    'Pha': 80000,
    'PoSa': 8000000
  };

  // Batiments planetes
  let total_b_pts = 0;
  let total_pts_1 = [];

  planetsIdList.forEach(planetId => {
    let b_pts_1 = Math.floor(((60 + 15) * (1 - Math.pow(1.5, M_1[planetId])) / (-0.5)) + ((48 + 24) * (1 - Math.pow(1.6, C_1[planetId])) / (-0.6)) + ((225 + 75) * (1 - Math.pow(1.5, D_1[planetId])) / (-0.5)) + ((75 + 30) * (1 - Math.pow(1.5, CES_1[planetId])) / (-0.5)) + ((900 + 360 + 180) * (1 - Math.pow(1.8, CEF_1[planetId])) / (-0.8)));

    for (const [key, value] of Object.entries(planetBuildings[planetId])) {
      if (key in init_b_prix) { // ne calculer que les entries avec un prix
        b_pts_1 += init_b_prix[key] * (Math.pow(2, value) - 1);
      }
    }
    total_pts_1[planetId] = b_pts_1;
    total_b_pts += b_pts_1;

    document.getElementById('building_pts_' + planetId).innerHTML = format(Math.round(total_pts_1[planetId] / 1000));

  });
  document.getElementById('total_b_pts').innerHTML = format(Math.round(total_b_pts / 1000));


  const init_d_prix = {
    'LM': 2000,
    'LLE': 2000,
    'LLO': 8000,
    'CG': 37000,
    'AI': 8000,
    'LP': 130000,
    'PB': 20000,
    'GB': 100000,
    'MIC': 10000,
    'MIP': 25000
  };


// Defenses planetes
  let total_d_pts = 0;

  planetsIdList.forEach(planetId => {
    let d_pts_1 = 0;
    for (const [key, value] of Object.entries(planetDefenses[planetId])) {
      if (key in init_d_prix) { // ne calculer que les entries avec un prix
        d_pts_1 += init_d_prix[key] * value;
      }
    }
    total_pts_1[planetId] += d_pts_1;
    total_d_pts += d_pts_1;
    document.getElementById('defence_pts_' + planetId).innerHTML = format(Math.round(total_pts_1[planetId] / 1000));
});
  document.getElementById('total_d_pts').innerHTML = format(Math.round(total_d_pts / 1000));

  // Lune
  /*var total_lune_pts = 0;
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
  document.getElementById('total_lune_pts').innerHTML = format(Math.round(total_lune_pts / 1000));*/

  // Sat planetes
  let total_sat_pts = 0;
  let sat_pts_1 = [];
  planetsIdList.forEach(planetId => {
    let sat_lune_1 = document.getElementById('sat_lune_' + planetId).value;
    sat_pts_1[planetId] = Math.round(Sat_1[planetId] * 2.5 + sat_lune_1 * 2.5);
    total_sat_pts += sat_pts_1[planetId];
    document.getElementById('sat_pts_' + planetId).innerHTML =  format(sat_pts_1[planetId]);
  });
  document.getElementById('total_sat_pts').innerHTML = format(total_sat_pts);


  planetsIdList.forEach(planetId => {
    document.getElementById('total_pts_' + planetId).innerHTML = format(Math.round((total_pts_1[planetId] /*+ lune_pts_1[planetId]*/) / 1000) + sat_pts_1[planetId]);
  });

  // Technologies planete avec le labo de plus au niveau
  const technoPrix = {
    'Esp': 1400,
    'Ordi': 1000,
    'Armes': 1000,
    'Bouclier': 800,
    'Protection': 1000,
    'NRJ': 1200,
    'Hyp': 6000,
    'RC': 1000,
    'RI': 6600,
    'PH': 36000,
    'Laser': 300,
    'Ions': 1400,
    'Plasma': 7000,
    'RRI': 800000,
    'Graviton': 0,
    'Astrophysique': 16000
  };

  let techno_pts = 0;

  for (const [key, value] of Object.entries(technologies)) {
    if (key !== 'player_id') { // Ignorer `player_id`
      techno_pts += technoPrix[key] * (Math.pow(2, value) - 1);
    }
  }

  // Calcul du cout de la techno astrophysique.
  let techno_astro_pts = 0;
  let techno_astro_pts_prec = 0;
  if (technologies['Astrophysique'] > 0) {
    techno_astro_pts = technoPrix['Astrophysique'];
    techno_astro_pts_prec = technoPrix['Astrophysique'];
  }
  for (let i = 1; i < technologies['Astrophysique']; i++) {
    techno_astro_pts = techno_astro_pts + techno_astro_pts_prec * 1.75;
    techno_astro_pts_prec = techno_astro_pts_prec * 1.75;
  }
  techno_pts = techno_pts + techno_astro_pts;
  console.log(`Points totaux des technologies : ${techno_pts}`);
  document.getElementById('techno_pts').innerHTML = format(Math.round(techno_pts / 1000));// Cout Total Techno
  document.getElementById('total_pts').innerHTML = format(Math.round((total_b_pts + total_d_pts /*+ total_lune_pts */+ techno_pts) / 1000) + total_sat_pts);// Cout Total
}

//Affiche les nombres sous format lisible (10 000 à la place de 10000)
function format(x) {
  let signe = '';
  if (isNaN(x)) return '-';
  if (x < 0) {
    x = Math.abs(x);
    signe = '-';
  }
  let str = x.toString(), n = str.length;
  if (n < 4) return (signe + x);
  else return (signe + ((n % 3) ? str.substring(0, n % 3) + '&nbsp;' : '')) + str.substring(n % 3).match(/\d{3}/g).join('&nbsp;');
}
/**
 * Calcule la distance entre a et b, a - b ; en tenant en compte des univers arrondis.
 * type = Représente le type de distance à calculer
 *    0 : Galaxie
 *    1 : Système
 *    2 : Planète
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
