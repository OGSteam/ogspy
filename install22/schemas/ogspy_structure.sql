#
# OGSpy version 3.3.1
# Juin 2016
# 

## ########################################################

DROP TABLE IF EXISTS ogspy_config;
DROP TABLE IF EXISTS ogspy_group;
DROP TABLE IF EXISTS ogspy_mod;
DROP TABLE IF EXISTS ogspy_rank_ally_economique;
DROP TABLE IF EXISTS ogspy_rank_ally_technology;
DROP TABLE IF EXISTS ogspy_rank_ally_military;
DROP TABLE IF EXISTS ogspy_rank_ally_military_built;
DROP TABLE IF EXISTS ogspy_rank_ally_military_loose;
DROP TABLE IF EXISTS ogspy_rank_ally_military_destruct;
DROP TABLE IF EXISTS ogspy_rank_ally_honor;
DROP TABLE IF EXISTS ogspy_rank_ally_points;
DROP TABLE IF EXISTS ogspy_rank_player_economique;
DROP TABLE IF EXISTS ogspy_rank_player_technology;
DROP TABLE IF EXISTS ogspy_rank_player_military;
DROP TABLE IF EXISTS ogspy_rank_player_military_built;
DROP TABLE IF EXISTS ogspy_rank_player_military_built;
DROP TABLE IF EXISTS ogspy_rank_player_military_destruct;
DROP TABLE IF EXISTS ogspy_rank_player_military_loose;
DROP TABLE IF EXISTS ogspy_rank_player_honor;
DROP TABLE IF EXISTS ogspy_rank_player_points;
DROP TABLE IF EXISTS ogspy_sessions;
DROP TABLE IF EXISTS ogspy_statistics;
DROP TABLE IF EXISTS ogspy_universe;
DROP TABLE IF EXISTS ogspy_user;
DROP TABLE IF EXISTS ogspy_user_building;
DROP TABLE IF EXISTS ogspy_user_defence;
DROP TABLE IF EXISTS ogspy_user_favorite;
DROP TABLE IF EXISTS ogspy_user_group;
DROP TABLE IF EXISTS ogspy_user_spy;
DROP TABLE IF EXISTS ogspy_user_technology;
DROP TABLE IF EXISTS ogspy_mod_config;
DROP TABLE IF EXISTS ogspy_parsedspy;
DROP TABLE IF EXISTS ogspy_parsedRC;
DROP TABLE IF EXISTS ogspy_parsedRCRound;
DROP TABLE IF EXISTS ogspy_round_attack;
DROP TABLE IF EXISTS ogspy_round_defense;
DROP TABLE IF EXISTS ogspy_gcm_users;

## 
## Structure de la table `ogspy_config`
## 

CREATE TABLE ogspy_config (
  config_name varchar(255) NOT NULL default '',
  config_value varchar(255) NOT NULL default '',
  PRIMARY KEY  (config_name)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_group`
## 

CREATE TABLE ogspy_group (
  group_id mediumint(8) NOT NULL auto_increment,
  group_name varchar(30) NOT NULL,
  server_set_system enum('0','1') NOT NULL default '0',
  server_set_spy enum('0','1') NOT NULL default '0',
  server_set_rc enum('0','1') NOT NULL default '0',
  server_set_ranking enum('0','1') NOT NULL default '0',
  server_show_positionhided enum('0','1') NOT NULL default '0',
  ogs_connection enum('0','1') NOT NULL default '0',
  ogs_set_system enum('0','1') NOT NULL default '0',
  ogs_get_system enum('0','1') NOT NULL default '0',
  ogs_set_spy enum('0','1') NOT NULL default '0',
  ogs_get_spy enum('0','1') NOT NULL default '0',
  ogs_set_ranking enum('0','1') NOT NULL default '0',
  ogs_get_ranking enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (group_id)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_mod`
## 

CREATE TABLE ogspy_mod (
  id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL COMMENT 'Nom du mod',
  menu varchar(255) NOT NULL COMMENT 'Titre du lien dans le menu',
  `action` varchar(255) NOT NULL COMMENT 'Action transmise en get et traitée dans index.php',
  root varchar(255) NOT NULL COMMENT 'Répertoire où se situe le mod (relatif au répertoire mods)',
  link varchar(255) NOT NULL COMMENT 'fichier principale du mod',
  version varchar(10) NOT NULL COMMENT 'Version du mod',
  position int(11) NOT NULL default '-1',
  active tinyint(1) NOT NULL COMMENT 'Permet de désactiver un mod sans le désinstaller, évite les mods#pirates',
  admin_only enum('0','1') NOT NULL default '0' COMMENT 'Affichage des mods de l utilisateur',
  PRIMARY KEY  (id),
  UNIQUE KEY `action` (`action`),
  UNIQUE KEY title (title),
  UNIQUE KEY menu (menu),
  UNIQUE KEY root (root)
) DEFAULT CHARSET=utf8;


## ########################################################

## 
## Structure de la table `ogspy_rank_ally_economique`
## 
      
CREATE TABLE ogspy_rank_ally_economique (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_technology`
## 
      
CREATE TABLE ogspy_rank_ally_technology (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military`
## 
      
CREATE TABLE ogspy_rank_ally_military (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_built`
## 
      
CREATE TABLE ogspy_rank_ally_military_built (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_loose`
## 
      
CREATE TABLE ogspy_rank_ally_military_loose (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_destruct`
## 
      
CREATE TABLE ogspy_rank_ally_military_destruct (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_honor`
## 
      
CREATE TABLE ogspy_rank_ally_honor (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_points`
## 

CREATE TABLE ogspy_rank_ally_points (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  ally varchar(30) NOT NULL,
  number_member int(11) NOT NULL,
  points int(11) NOT NULL default '0',
  points_per_member int(11) NOT NULL,
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,ally),
  KEY ally (ally)
) DEFAULT CHARSET=utf8;


## ########################################################

## 
## Structure de la table `ogspy_rank_player_economique`
## 

CREATE TABLE ogspy_rank_player_economique (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_technology`
## 

CREATE TABLE ogspy_rank_player_technology (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military`
## 

CREATE TABLE ogspy_rank_player_military (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  nb_spacecraft int(11) NOT NULL default '0',
  PRIMARY KEY (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_built`
## 

CREATE TABLE ogspy_rank_player_military_built (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;


## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_loose`
## 

CREATE TABLE ogspy_rank_player_military_loose (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_destruct`
## 

CREATE TABLE ogspy_rank_player_military_destruct (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_honor`
## 

CREATE TABLE ogspy_rank_player_honor (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################


## 
## Structure de la table `ogspy_rank_player_points`
## 

CREATE TABLE ogspy_rank_player_points (
  datadate int(11) NOT NULL default '0',
  rank int(11) NOT NULL default '0',
  player varchar(30) NOT NULL default '',
  ally varchar(100) NOT NULL default '',
  points int(11) NOT NULL default '0',
  sender_id int(11) NOT NULL default '0',
  PRIMARY KEY  (rank,datadate),
  KEY datadate (datadate,player),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_sessions`
## 

CREATE TABLE ogspy_sessions (
  session_id char(32) NOT NULL default '',
  session_user_id int(11) NOT NULL default '0',
  session_start int(11) NOT NULL default '0',
  session_expire int(11) NOT NULL default '0',
  session_ip char(32) NOT NULL default '',
  session_ogs enum('0','1') NOT NULL default '0',
  session_lastvisit int(11) NOT NULL default '0',
  UNIQUE KEY session_id (session_id,session_ip)
) DEFAULT CHARSET=utf8;



## ########################################################

## 
## Structure de la table `ogspy_statistics`
## 

CREATE TABLE ogspy_statistics (
  statistic_name varchar(255) NOT NULL default '',
  statistic_value varchar(255) NOT NULL default '0',
  PRIMARY KEY  (statistic_name)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_universe`
## 

CREATE TABLE ogspy_universe (
  galaxy smallint(2) NOT NULL default '1',
  system smallint(3) NOT NULL default '1',
  `row` smallint(2) NOT NULL default '1',
  moon enum('0','1') NOT NULL default '0',
  phalanx tinyint(1) NOT NULL default '0',
  gate enum('0','1') NOT NULL default '0',
  `name` varchar(20) NOT NULL default '',
  ally varchar(20) default NULL,
  player varchar(20) default NULL,
  `status` varchar(5) NOT NULL,
  last_update int(11) NOT NULL default '0',
  last_update_moon int(11) NOT NULL default '0',
  last_update_user_id int(11) NOT NULL default '0',
  UNIQUE KEY univers (galaxy,system,`row`),
  KEY player (player)
) DEFAULT CHARSET=utf8;

## ########################################################
## 
## Structure de la table `ogspy_user`
## 

CREATE TABLE ogspy_user (
  user_id int(11) NOT NULL auto_increment,
  user_name varchar(20) NOT NULL default '',
  user_password varchar(32) NOT NULL default '',
  user_email varchar(50) NOT NULL default '',
  user_admin enum('0','1') NOT NULL default '0',
  user_coadmin enum('0','1') NOT NULL default '0',
  user_active enum('0','1') NOT NULL default '0',
  user_regdate int(11) NOT NULL default '0',
  user_lastvisit int(11) NOT NULL default '0',
  user_galaxy smallint(2) NOT NULL default '1',
  user_system smallint(3) NOT NULL default '1',
  planet_added_web int(11) NOT NULL default '0',
  planet_added_ogs int(11) NOT NULL default '0',
  planet_exported int(11) NOT NULL default '0',
  search int(11) NOT NULL default '0',
  spy_added_web int(11) NOT NULL default '0',
  spy_added_ogs int(11) NOT NULL default '0',
  spy_exported int(11) NOT NULL default '0',
  rank_added_web int(11) NOT NULL default '0',
  rank_added_ogs int(11) NOT NULL default '0',
  xtense_type enum('FF','GMFF','GMGC','ANDROID'),
  xtense_version varchar(10),
  rank_exported int(11) NOT NULL default '0',
  user_skin varchar(255) NOT NULL default '',
  user_stat_name varchar(50) NOT NULL default '',
  management_user enum('0','1') NOT NULL default '0',
  management_ranking enum('0','1') NOT NULL default '0',
  disable_ip_check enum('0','1') NOT NULL default '0',
  off_commandant enum('0','1') NOT NULL default '0',
  off_amiral enum('0','1') NOT NULL default '0',
  off_ingenieur enum('0','1') NOT NULL default '0',
  off_geologue enum('0','1') NOT NULL default '0',
  off_technocrate enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (user_id),
  UNIQUE KEY user_name (user_name)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_building`
## 

CREATE TABLE ogspy_user_building (
  user_id int(11) NOT NULL default '0',
  planet_id int(11) NOT NULL default '0',
  planet_name varchar(20) NOT NULL default '',
  coordinates varchar(10) NOT NULL default '',
  `fields` smallint(3) NOT NULL default '0',
  `boosters` varchar(64) NOT NULL default 'm:0:0_c:0:0_d:0:0_p:0_m:0',
  temperature_min smallint(2) NOT NULL default '0',
  temperature_max smallint(2) NOT NULL default '0',
  Sat smallint(5) NOT NULL default '0',
  Sat_percentage smallint(3) NOT NULL default '100',
  M smallint(2) NOT NULL default '0',
  M_percentage smallint(3) NOT NULL default '100',
  C smallint(2) NOT NULL default '0',
  C_Percentage smallint(3) NOT NULL default '100',
  D smallint(2) NOT NULL default '0',
  D_percentage smallint(3) NOT NULL default '100',
  CES smallint(2) NOT NULL default '0',
  CES_percentage smallint(3) NOT NULL default '100',
  CEF smallint(2) NOT NULL default '0',
  CEF_percentage smallint(3) NOT NULL default '100',
  UdR smallint(2) NOT NULL default '0',
  UdN smallint(2) NOT NULL default '0',
  CSp smallint(2) NOT NULL default '0',
  HM smallint(2) NOT NULL default '0',
  HC smallint(2) NOT NULL default '0',
  HD smallint(2) NOT NULL default '0',
  Lab smallint(2) NOT NULL default '0',
  Ter smallint(2) NOT NULL default '0',
  DdR smallint(2) NOT NULL default '0',
  Silo smallint(2) NOT NULL default '0',
  BaLu smallint(2) NOT NULL default '0',
  Pha smallint(2) NOT NULL default '0',
  PoSa smallint(2) NOT NULL default '0',
  PRIMARY KEY  (user_id,planet_id)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_defence`
## 

CREATE TABLE ogspy_user_defence (
  user_id int(11) NOT NULL default '0',
  planet_id int(11) NOT NULL default '0',
  LM int(11) NOT NULL default '0',
  LLE int(11) NOT NULL default '0',
  LLO int(11) NOT NULL default '0',
  CG int(11) NOT NULL default '0',
  AI int(11) NOT NULL default '0',
  LP int(11) NOT NULL default '0',
  PB smallint(1) NOT NULL default '0',
  GB smallint(1) NOT NULL default '0',
  MIC smallint(3) NOT NULL default '0',
  MIP smallint(3) NOT NULL default '0',
  PRIMARY KEY  (user_id,planet_id)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_favorite`
## 

CREATE TABLE ogspy_user_favorite (
  user_id int(11) NOT NULL default '0',
  galaxy smallint(2) NOT NULL default '1',
  system smallint(3) NOT NULL default '0',
  UNIQUE KEY user_id (user_id,galaxy,system)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_group`
## 

CREATE TABLE ogspy_user_group (
  group_id mediumint(8) NOT NULL default '0',
  user_id mediumint(8) NOT NULL default '0',
  UNIQUE KEY group_id (group_id,user_id)
) DEFAULT CHARSET=utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_spy`
## 

CREATE TABLE ogspy_user_spy (
  user_id int(11) NOT NULL default '0',
  spy_id int(11) NOT NULL default '0',
  PRIMARY KEY  (user_id,spy_id)
) DEFAULT CHARSET=utf8;

## ########################################################
## 
## Structure de la table `ogspy_user_technology`
## 

CREATE TABLE ogspy_user_technology (
  user_id int(11) NOT NULL default '0',
  Esp smallint(2) NOT NULL default '0',
  Ordi smallint(2) NOT NULL default '0',
  Armes smallint(2) NOT NULL default '0',
  Bouclier smallint(2) NOT NULL default '0',
  Protection smallint(2) NOT NULL default '0',
  NRJ smallint(2) NOT NULL default '0',
  Hyp smallint(2) NOT NULL default '0',
  RC smallint(2) NOT NULL default '0',
  RI smallint(2) NOT NULL default '0',
  PH smallint(2) NOT NULL default '0',
  Laser smallint(2) NOT NULL default '0',
  Ions smallint(2) NOT NULL default '0',
  Plasma smallint(2) NOT NULL default '0',
  RRI smallint(2) NOT NULL default '0',
  Graviton smallint(2) NOT NULL default '0',
  Astrophysique smallint(2) NOT NULL default '0',
  PRIMARY KEY  (user_id)
) DEFAULT CHARSET=utf8;

## ########################################################
##
## Structure de la table `ogspy_mod_config`
##

CREATE TABLE `ogspy_mod_config` (
  `mod` varchar(50) NOT NULL DEFAULT '',
  `config` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`mod`,`config`)
) DEFAULT CHARSET=utf8;
## ########################################################

## 
## Contenu de la table `ogspy_config`
## 

INSERT INTO `ogspy_config` VALUES ('allied', '');
INSERT INTO `ogspy_config` VALUES ('ally_protection', '');
INSERT INTO `ogspy_config` VALUES ('debug_log', '0');
INSERT INTO `ogspy_config` VALUES ('default_skin', 'skin/OGSpy_skin/');
INSERT INTO `ogspy_config` VALUES ('disable_ip_check', '1');
INSERT INTO `ogspy_config` VALUES ('keeprank_criterion', 'day');
INSERT INTO `ogspy_config` VALUES ('last_maintenance_action', '0');
INSERT INTO `ogspy_config` VALUES ('max_battlereport', '10');
INSERT INTO `ogspy_config` VALUES ('max_favorites', '20');
INSERT INTO `ogspy_config` VALUES ('max_favorites_spy', '10');
INSERT INTO `ogspy_config` VALUES ('max_keeplog', '7');
INSERT INTO `ogspy_config` VALUES ('max_keeprank', '30');
INSERT INTO `ogspy_config` VALUES ('max_keepspyreport', '30');
INSERT INTO `ogspy_config` VALUES ('max_spyreport', '10');
INSERT INTO `ogspy_config` VALUES ('reason', '');
INSERT INTO `ogspy_config` VALUES ('servername', 'Cartographie');
INSERT INTO `ogspy_config` VALUES ('server_active', '1');
INSERT INTO `ogspy_config` VALUES ('session_time', '30');
INSERT INTO `ogspy_config` VALUES ('url_forum', 'http://www.ogsteam.fr/');
INSERT INTO `ogspy_config` VALUES ('log_phperror', '0');
INSERT INTO `ogspy_config` VALUES ('block_ratio', '0');
INSERT INTO `ogspy_config` VALUES ('ratio_limit', '0');
INSERT INTO `ogspy_config` VALUES ('config_cache', '3600');
INSERT INTO `ogspy_config` VALUES ('mod_cache', '604800');

## Partie affichage

INSERT INTO `ogspy_config` VALUES ('enable_stat_view', '1');
INSERT INTO `ogspy_config` VALUES ('enable_members_view', '0');
INSERT INTO `ogspy_config` VALUES ('portee_missil', '1');
INSERT INTO `ogspy_config` VALUES ('enable_register_view', '0');
INSERT INTO `ogspy_config` VALUES ('register_forum', '');
INSERT INTO `ogspy_config` VALUES ('register_alliance', '');
INSERT INTO `ogspy_config` VALUES ('galaxy_by_line_stat', '9');
INSERT INTO `ogspy_config` VALUES ('system_by_line_stat', '10');
INSERT INTO `ogspy_config` VALUES ('galaxy_by_line_ally', '9');
INSERT INTO `ogspy_config` VALUES ('system_by_line_ally', '10');
INSERT INTO `ogspy_config` VALUES ('color_ally', 'Magenta_Yellow_Red');
INSERT INTO `ogspy_config` VALUES ('nb_colonnes_ally', '3');
INSERT INTO `ogspy_config` VALUES ('open_user', '');
INSERT INTO `ogspy_config` VALUES ('open_admin', '');

## ########################################################

## 
## Contenu de la table `ogspy_group`
## 

INSERT INTO `ogspy_group` VALUES (1, 'Standard', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');

## ########################################################

##
## Structure de la table `ogspy_parsedspy`
##

CREATE TABLE `ogspy_parsedspy` (
  `id_spy` int(11) NOT NULL auto_increment,
  `planet_name` varchar(20) NOT NULL default '',
  `coordinates` varchar(9) NOT NULL default '',
  `metal` int(7) NOT NULL default '-1',
  `cristal` int(7) NOT NULL default '-1',
  `deuterium` int(7) NOT NULL default '-1',
  `energie` int(7) NOT NULL default '-1',
  `activite` int(2) NOT NULL default '-1',
  `M` smallint(2) NOT NULL default '-1',
  `C` smallint(2) NOT NULL default '-1',
  `D` smallint(2) NOT NULL default '-1',
  `CES` smallint(2) NOT NULL default '-1',
  `CEF` smallint(2) NOT NULL default '-1',
  `UdR` smallint(2) NOT NULL default '-1',
  `UdN` smallint(2) NOT NULL default '-1',
  `CSp` smallint(2) NOT NULL default '-1',
  `HM` smallint(2) NOT NULL default '-1',
  `HC` smallint(2) NOT NULL default '-1',
  `HD` smallint(2) NOT NULL default '-1',
  `Lab` smallint(2) NOT NULL default '-1',
  `Ter` smallint(2) NOT NULL default '-1',
  `DdR` smallint(2) NOT NULL default '-1',
  `Silo` smallint(2) NOT NULL default '-1',
  `BaLu` smallint(2) NOT NULL default '-1',
  `Pha` smallint(2) NOT NULL default '-1',
  `PoSa` smallint(2) NOT NULL default '-1',
  `LM` int(11) NOT NULL default '-1',
  `LLE` int(11) NOT NULL default '-1',
  `LLO` int(11) NOT NULL default '-1',
  `CG` int(11) NOT NULL default '-1',
  `AI` int(11) NOT NULL default '-1',
  `LP` int(11) NOT NULL default '-1',
  `PB` smallint(1) NOT NULL default '-1',
  `GB` smallint(1) NOT NULL default '-1',
  `MIC` smallint(3) NOT NULL default '-1',
  `MIP` smallint(3) NOT NULL default '-1',
  `PT` int(11) NOT NULL default '-1',
  `GT` int(11) NOT NULL default '-1',
  `CLE` int(11) NOT NULL default '-1',
  `CLO` int(11) NOT NULL default '-1',
  `CR` int(11) NOT NULL default '-1',
  `VB` int(11) NOT NULL default '-1',
  `VC` int(11) NOT NULL default '-1',
  `REC` int(11) NOT NULL default '-1',
  `SE` int(11) NOT NULL default '-1',
  `BMD` int(11) NOT NULL default '-1',
  `DST` int(11) NOT NULL default '-1',
  `EDLM` int(11) NOT NULL default '-1',
  `SAT` int(11) default '-1',
  `TRA` int(11) NOT NULL default '-1',
  `Esp` smallint(2) NOT NULL default '-1',
  `Ordi` smallint(2) NOT NULL default '-1',
  `Armes` smallint(2) NOT NULL default '-1',
  `Bouclier` smallint(2) NOT NULL default '-1',
  `Protection` smallint(2) NOT NULL default '-1',
  `NRJ` smallint(2) NOT NULL default '-1',
  `Hyp` smallint(2) NOT NULL default '-1',
  `RC` smallint(2) NOT NULL default '-1',
  `RI` smallint(2) NOT NULL default '-1',
  `PH` smallint(2) NOT NULL default '-1',
  `Laser` smallint(2) NOT NULL default '-1',
  `Ions` smallint(2) NOT NULL default '-1',
  `Plasma` smallint(2) NOT NULL default '-1',
  `RRI` smallint(2) NOT NULL default '-1',
  `Graviton` smallint(2) NOT NULL default '-1',
  `Astrophysique` smallint(2) NOT NULL default '-1',
  `dateRE` int(11) NOT NULL default '0',
  `proba` smallint (2) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `sender_id` int(11) NOT NULL,
  PRIMARY KEY  (`id_spy`),
  KEY `coordinates` (`coordinates`)
) DEFAULT CHARSET=utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRC`
##

CREATE TABLE `ogspy_parsedRC` (
  `id_rc` int(11) NOT NULL auto_increment,
  `dateRC` int(11) NOT NULL default '0',
  `coordinates` varchar(9) NOT NULL default '',
  `nb_rounds` int(2) NOT NULL default '0',
  `victoire` char NOT NULL default 'A',
  `pertes_A` BIGINT NOT NULL default '0',
  `pertes_D` BIGINT NOT NULL default '0',
  `gain_M` BIGINT NOT NULL default '-1',
  `gain_C` BIGINT NOT NULL default '-1',
  `gain_D` BIGINT NOT NULL default '-1',
  `debris_M` BIGINT NOT NULL default '-1',
  `debris_C` BIGINT NOT NULL default '-1',
  `lune` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id_rc`),
  KEY `coordinatesrc` (`coordinates`)
) DEFAULT CHARSET=utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRCRound`
##

CREATE TABLE `ogspy_parsedRCRound` (
  `id_rcround` int(11) NOT NULL auto_increment,
  `id_rc` int(11) NOT NULL ,
  `numround` int(2) NOT NULL ,
  `attaque_tir` int(11) NOT NULL default '-1',
  `attaque_puissance` int(11) NOT NULL default '-1',
  `defense_bouclier` int(11) NOT NULL default '-1',
  `attaque_bouclier` int(11) NOT NULL default '-1',
  `defense_tir` int(11) NOT NULL default '-1',
  `defense_puissance` int(11) NOT NULL default '-1',
  PRIMARY KEY (id_rcround),
  KEY `rcround` (`id_rc`,`numround`),
  KEY `id_rc` (`id_rc`)
) DEFAULT CHARSET=utf8;
## ########################################################

##
## Structure de la table `ogspy_round_attack`
##

CREATE TABLE `ogspy_round_attack` (
  `id_roundattack` int(11) NOT NULL auto_increment,
  `id_rcround` int(11) NOT NULL ,
  `player` varchar(30) NOT NULL default '',
  `coordinates` varchar(9) NOT NULL default '',
  `Armes` smallint(2) NOT NULL default '-1',
  `Bouclier` smallint(2) NOT NULL default '-1',
  `Protection` smallint(2) NOT NULL default '-1',
  `PT` int(11) NOT NULL default '-1',
  `GT` int(11) NOT NULL default '-1',
  `CLE` int(11) NOT NULL default '-1',
  `CLO` int(11) NOT NULL default '-1',
  `CR` int(11) NOT NULL default '-1',
  `VB` int(11) NOT NULL default '-1',
  `VC` int(11) NOT NULL default '-1',
  `REC` int(11) NOT NULL default '-1',
  `SE` int(11) NOT NULL default '-1',
  `BMD` int(11) NOT NULL default '-1',
  `DST` int(11) NOT NULL default '-1',
  `EDLM` int(11) NOT NULL default '-1',
  `TRA` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`id_roundattack`),
  KEY `id_rcround` (`id_rcround`),
  KEY `player` (`player`,`coordinates`)
) DEFAULT CHARSET=utf8;
## ########################################################

##
## Structure de la table `ogspy_round_defense`
##

CREATE TABLE `ogspy_round_defense` (
  `id_rounddefense` int(11) NOT NULL auto_increment,
  `id_rcround` int(11) NOT NULL ,
  `player` varchar(30) NOT NULL default '',
  `coordinates` varchar(9) NOT NULL default '',
  `Armes` smallint(2) NOT NULL default '-1',
  `Bouclier` smallint(2) NOT NULL default '-1',
  `Protection` smallint(2) NOT NULL default '-1',
  `PT` int(11) NOT NULL default '-1',
  `GT` int(11) NOT NULL default '-1',
  `CLE` int(11) NOT NULL default '-1',
  `CLO` int(11) NOT NULL default '-1',
  `CR` int(11) NOT NULL default '-1',
  `VB` int(11) NOT NULL default '-1',
  `VC` int(11) NOT NULL default '-1',
  `REC` int(11) NOT NULL default '-1',
  `SE` int(11) NOT NULL default '-1',
  `BMD` int(11) NOT NULL default '-1',
  `DST` int(11) NOT NULL default '-1',
  `EDLM` int(11) NOT NULL default '-1',
  `SAT` int(11) NOT NULL default '-1',
  `TRA` int(11) NOT NULL default '-1',
  `LM` int(11) NOT NULL default '-1',
  `LLE` int(11) NOT NULL default '-1',
  `LLO` int(11) NOT NULL default '-1',
  `CG` int(11) NOT NULL default '-1',
  `AI` int(11) NOT NULL default '-1',
  `LP` int(11) NOT NULL default '-1',
  `PB` smallint(1) NOT NULL default '-1',
  `GB` smallint(1) NOT NULL default '-1',
  PRIMARY KEY  (`id_rounddefense`),
  KEY `id_rcround` (`id_rcround`),
  KEY `player` (`player`,`coordinates`)
) DEFAULT CHARSET=utf8;
## ########################################################
##
## Structure de la table `ogspy_gcm_users`
##
CREATE TABLE IF NOT EXISTS `ogspy_gcm_users` (
	`user_id` int(11) NOT NULL default '0',
  	`gcm_regid` varchar(128) NOT NULL,
  	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`version_android` varchar(50),
  	`version_ogspy` varchar(50),
  	`device` varchar(50),
  	PRIMARY KEY (`gcm_regid`)
) DEFAULT CHARSET=utf8;

