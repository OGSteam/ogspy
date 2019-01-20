#
# OGSpy version 3.3.5
# Decembre 2018
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
  config_name  VARCHAR(255) NOT NULL DEFAULT '',
  config_value VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (config_name)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_group`
## 

CREATE TABLE ogspy_group (
  group_id                  MEDIUMINT(8)    NOT NULL AUTO_INCREMENT,
  group_name                VARCHAR(30)     NOT NULL,
  server_set_system         ENUM ('0', '1') NOT NULL DEFAULT '0',
  server_set_spy            ENUM ('0', '1') NOT NULL DEFAULT '0',
  server_set_rc             ENUM ('0', '1') NOT NULL DEFAULT '0',
  server_set_ranking        ENUM ('0', '1') NOT NULL DEFAULT '0',
  server_show_positionhided ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_connection            ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_set_system            ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_get_system            ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_set_spy               ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_get_spy               ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_set_ranking           ENUM ('0', '1') NOT NULL DEFAULT '0',
  ogs_get_ranking           ENUM ('0', '1') NOT NULL DEFAULT '0',
  PRIMARY KEY (group_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_mod`
## 

CREATE TABLE ogspy_mod (
  id         INT(11)         NOT NULL AUTO_INCREMENT,
  title      VARCHAR(255)    NOT NULL
  COMMENT 'Nom du mod',
  menu       VARCHAR(255)    NOT NULL
  COMMENT 'Titre du lien dans le menu',
  `action`   VARCHAR(255)    NOT NULL
  COMMENT 'Action transmise en get et traitée dans index.php',
  root       VARCHAR(255)    NOT NULL
  COMMENT 'Répertoire où se situe le mod (relatif au répertoire mods)',
  link       VARCHAR(255)    NOT NULL
  COMMENT 'fichier principale du mod',
  version    VARCHAR(10)     NOT NULL
  COMMENT 'Version du mod',
  position   INT(11)         NOT NULL DEFAULT '-1',
  active     TINYINT(1)      NOT NULL
  COMMENT 'Permet de désactiver un mod sans le désinstaller, évite les mods#pirates',
  admin_only ENUM ('0', '1') NOT NULL DEFAULT '0'
  COMMENT 'Affichage des mods de l utilisateur',
  PRIMARY KEY (id),
  UNIQUE KEY `action` (`action`),
  UNIQUE KEY title (title),
  UNIQUE KEY menu (menu),
  UNIQUE KEY root (root)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_economique`
## 

CREATE TABLE ogspy_rank_ally_economique (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT     NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_technology`
## 

CREATE TABLE ogspy_rank_ally_technology (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military`
## 

CREATE TABLE ogspy_rank_ally_military (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_built`
## 

CREATE TABLE ogspy_rank_ally_military_built (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_loose`
## 

CREATE TABLE ogspy_rank_ally_military_loose (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_military_destruct`
## 

CREATE TABLE ogspy_rank_ally_military_destruct (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_honor`
## 

CREATE TABLE ogspy_rank_ally_honor (
  datadate      INT(11)     NOT NULL DEFAULT '0',
  rank          INT(11)     NOT NULL DEFAULT '0',
  ally          VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member INT(11)     NOT NULL,
  points        BIGINT      NOT NULL DEFAULT '0',
  sender_id     INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_ally_points`
## 

CREATE TABLE ogspy_rank_ally_points (
  datadate          INT(11)     NOT NULL DEFAULT '0',
  rank              INT(11)     NOT NULL DEFAULT '0',
  ally              VARCHAR(30) NOT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  number_member     INT(11)     NOT NULL,
  points            BIGINT      NOT NULL DEFAULT '0',
  points_per_member BIGINT      NOT NULL,
  sender_id         INT(11)     NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, ally),
  KEY ally (ally)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_economique`
## 

CREATE TABLE ogspy_rank_player_economique (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_technology`
## 

CREATE TABLE ogspy_rank_player_technology (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military`
## 

CREATE TABLE ogspy_rank_player_military (
  datadate      INT(11)      NOT NULL DEFAULT '0',
  rank          INT(11)      NOT NULL DEFAULT '0',
  player        VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally          VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points        BIGINT       NOT NULL DEFAULT '0',
  sender_id     INT(11)      NOT NULL DEFAULT '0',
  nb_spacecraft INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_built`
## 

CREATE TABLE ogspy_rank_player_military_built (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_loose`
## 

CREATE TABLE ogspy_rank_player_military_loose (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_military_destruct`
## 

CREATE TABLE ogspy_rank_player_military_destruct (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_rank_player_honor`
## 

CREATE TABLE ogspy_rank_player_honor (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################


## 
## Structure de la table `ogspy_rank_player_points`
## 

CREATE TABLE ogspy_rank_player_points (
  datadate  INT(11)      NOT NULL DEFAULT '0',
  rank      INT(11)      NOT NULL DEFAULT '0',
  player    VARCHAR(30)  NOT NULL DEFAULT '',
  player_id               INT(6)     NOT NULL DEFAULT '-1',
  ally      VARCHAR(100) NOT NULL DEFAULT '',
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  points    BIGINT       NOT NULL DEFAULT '0',
  sender_id INT(11)      NOT NULL DEFAULT '0',
  PRIMARY KEY (rank, datadate),
  KEY datadate (datadate, player),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_sessions`
## 

CREATE TABLE ogspy_sessions (
  session_id        CHAR(32)        NOT NULL DEFAULT '',
  session_user_id   INT(11)         NOT NULL DEFAULT '0',
  session_start     INT(11)         NOT NULL DEFAULT '0',
  session_expire    INT(11)         NOT NULL DEFAULT '0',
  session_ip        CHAR(32)        NOT NULL DEFAULT '',
  session_ogs       ENUM ('0', '1') NOT NULL DEFAULT '0',
  session_lastvisit INT(11)         NOT NULL DEFAULT '0',
  UNIQUE KEY session_id (session_id, session_ip)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_statistics`
## 

CREATE TABLE ogspy_statistics (
  statistic_name  VARCHAR(255) NOT NULL DEFAULT '',
  statistic_value VARCHAR(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (statistic_name)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_universe`
## 

CREATE TABLE ogspy_universe (
  galaxy              SMALLINT(2)     NOT NULL DEFAULT '1',
  system              SMALLINT(3)     NOT NULL DEFAULT '1',
  `row`               SMALLINT(2)     NOT NULL DEFAULT '1',
  moon                ENUM ('0', '1') NOT NULL DEFAULT '0',
  phalanx             TINYINT(1)      NOT NULL DEFAULT '0',
  gate                ENUM ('0', '1') NOT NULL DEFAULT '0',
  `name`              VARCHAR(20)     NOT NULL DEFAULT '',
  ally                VARCHAR(20)              DEFAULT NULL,
  ally_id              INT(6)     NOT NULL DEFAULT '-1',
  player              VARCHAR(20)              DEFAULT NULL,
  player_id              INT(6)     NOT NULL DEFAULT '-1',
  `status`            VARCHAR(5)      NOT NULL,
  last_update         INT(11)         NOT NULL DEFAULT '0',
  last_update_moon    INT(11)         NOT NULL DEFAULT '0',
  last_update_user_id INT(11)         NOT NULL DEFAULT '0',
  UNIQUE KEY univers (galaxy, system, `row`),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################
## 
## Structure de la table `ogspy_user`
## 

CREATE TABLE ogspy_user (
  user_id            INT(11)         NOT NULL AUTO_INCREMENT,
  user_name          VARCHAR(20)     NOT NULL DEFAULT '',
  user_password      VARCHAR(32)     NOT NULL DEFAULT '',
  user_email         VARCHAR(50)     NOT NULL DEFAULT '',
  user_admin         ENUM ('0', '1') NOT NULL DEFAULT '0',
  user_coadmin       ENUM ('0', '1') NOT NULL DEFAULT '0',
  user_active        ENUM ('0', '1') NOT NULL DEFAULT '0',
  user_regdate       INT(11)         NOT NULL DEFAULT '0',
  user_lastvisit     INT(11)         NOT NULL DEFAULT '0',
  user_galaxy        SMALLINT(2)     NOT NULL DEFAULT '1',
  user_system        SMALLINT(3)     NOT NULL DEFAULT '1',
  planet_added_web   INT(11)         NOT NULL DEFAULT '0',
  planet_added_ogs   INT(11)         NOT NULL DEFAULT '0',
  planet_exported    INT(11)         NOT NULL DEFAULT '0',
  search             INT(11)         NOT NULL DEFAULT '0',
  spy_added_web      INT(11)         NOT NULL DEFAULT '0',
  spy_added_ogs      INT(11)         NOT NULL DEFAULT '0',
  spy_exported       INT(11)         NOT NULL DEFAULT '0',
  rank_added_web     INT(11)         NOT NULL DEFAULT '0',
  rank_added_ogs     INT(11)         NOT NULL DEFAULT '0',
  xtense_type        ENUM ('FF', 'GM-FF', 'GM-GC', 'ANDROID'),
  xtense_version     VARCHAR(10),
  rank_exported      INT(11)         NOT NULL DEFAULT '0',
  user_skin          VARCHAR(255)    NOT NULL DEFAULT '',
  user_stat_name     VARCHAR(50)     NOT NULL DEFAULT '',
  management_user    ENUM ('0', '1') NOT NULL DEFAULT '0',
  management_ranking ENUM ('0', '1') NOT NULL DEFAULT '0',
  disable_ip_check   ENUM ('0', '1') NOT NULL DEFAULT '0',
  off_commandant     ENUM ('0', '1') NOT NULL DEFAULT '0',
  off_amiral         ENUM ('0', '1') NOT NULL DEFAULT '0',
  off_ingenieur      ENUM ('0', '1') NOT NULL DEFAULT '0',
  off_geologue       ENUM ('0', '1') NOT NULL DEFAULT '0',
  off_technocrate    ENUM ('0', '1') NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  UNIQUE KEY user_name (user_name)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_building`
## 

CREATE TABLE ogspy_user_building (
  user_id         INT(11)     NOT NULL DEFAULT '0',
  planet_id       INT(11)     NOT NULL DEFAULT '0',
  planet_name     VARCHAR(20) NOT NULL DEFAULT '',
  coordinates     VARCHAR(10) NOT NULL DEFAULT '',
  `fields`        SMALLINT(3) NOT NULL DEFAULT '0',
  `boosters`      VARCHAR(64) NOT NULL DEFAULT 'm:0:0_c:0:0_d:0:0_p:0_m:0',
  temperature_min SMALLINT(2) NOT NULL DEFAULT '0',
  temperature_max SMALLINT(2) NOT NULL DEFAULT '0',
  Sat             SMALLINT(5) NOT NULL DEFAULT '0',
  Sat_percentage  SMALLINT(3) NOT NULL DEFAULT '100',
  M               SMALLINT(2) NOT NULL DEFAULT '0',
  M_percentage    SMALLINT(3) NOT NULL DEFAULT '100',
  C               SMALLINT(2) NOT NULL DEFAULT '0',
  C_Percentage    SMALLINT(3) NOT NULL DEFAULT '100',
  D               SMALLINT(2) NOT NULL DEFAULT '0',
  D_percentage    SMALLINT(3) NOT NULL DEFAULT '100',
  CES             SMALLINT(2) NOT NULL DEFAULT '0',
  CES_percentage  SMALLINT(3) NOT NULL DEFAULT '100',
  CEF             SMALLINT(2) NOT NULL DEFAULT '0',
  CEF_percentage  SMALLINT(3) NOT NULL DEFAULT '100',
  UdR             SMALLINT(2) NOT NULL DEFAULT '0',
  UdN             SMALLINT(2) NOT NULL DEFAULT '0',
  CSp             SMALLINT(2) NOT NULL DEFAULT '0',
  HM              SMALLINT(2) NOT NULL DEFAULT '0',
  HC              SMALLINT(2) NOT NULL DEFAULT '0',
  HD              SMALLINT(2) NOT NULL DEFAULT '0',
  Lab             SMALLINT(2) NOT NULL DEFAULT '0',
  Ter             SMALLINT(2) NOT NULL DEFAULT '0',
  DdR             SMALLINT(2) NOT NULL DEFAULT '0',
  Silo            SMALLINT(2) NOT NULL DEFAULT '0',
  Dock            SMALLINT(2) NOT NULL DEFAULT '0',
  BaLu            SMALLINT(2) NOT NULL DEFAULT '0',
  Pha             SMALLINT(2) NOT NULL DEFAULT '0',
  PoSa            SMALLINT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, planet_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_defence`
## 

CREATE TABLE ogspy_user_defence (
  user_id   INT(11)     NOT NULL DEFAULT '0',
  planet_id INT(11)     NOT NULL DEFAULT '0',
  LM        INT(11)     NOT NULL DEFAULT '0',
  LLE       INT(11)     NOT NULL DEFAULT '0',
  LLO       INT(11)     NOT NULL DEFAULT '0',
  CG        INT(11)     NOT NULL DEFAULT '0',
  AI        INT(11)     NOT NULL DEFAULT '0',
  LP        INT(11)     NOT NULL DEFAULT '0',
  PB        SMALLINT(1) NOT NULL DEFAULT '0',
  GB        SMALLINT(1) NOT NULL DEFAULT '0',
  MIC       SMALLINT(3) NOT NULL DEFAULT '0',
  MIP       SMALLINT(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, planet_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_favorite`
## 

CREATE TABLE ogspy_user_favorite (
  user_id INT(11)     NOT NULL DEFAULT '0',
  galaxy  SMALLINT(2) NOT NULL DEFAULT '1',
  system  SMALLINT(3) NOT NULL DEFAULT '0',
  UNIQUE KEY user_id (user_id, galaxy, system)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_group`
## 

CREATE TABLE ogspy_user_group (
  group_id MEDIUMINT(8) NOT NULL DEFAULT '0',
  user_id  MEDIUMINT(8) NOT NULL DEFAULT '0',
  UNIQUE KEY group_id (group_id, user_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_spy`
## 

CREATE TABLE ogspy_user_spy (
  user_id INT(11) NOT NULL DEFAULT '0',
  spy_id  INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, spy_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################
## 
## Structure de la table `ogspy_user_technology`
## 

CREATE TABLE ogspy_user_technology (
  user_id       INT(11)     NOT NULL DEFAULT '0',
  Esp           SMALLINT(2) NOT NULL DEFAULT '0',
  Ordi          SMALLINT(2) NOT NULL DEFAULT '0',
  Armes         SMALLINT(2) NOT NULL DEFAULT '0',
  Bouclier      SMALLINT(2) NOT NULL DEFAULT '0',
  Protection    SMALLINT(2) NOT NULL DEFAULT '0',
  NRJ           SMALLINT(2) NOT NULL DEFAULT '0',
  Hyp           SMALLINT(2) NOT NULL DEFAULT '0',
  RC            SMALLINT(2) NOT NULL DEFAULT '0',
  RI            SMALLINT(2) NOT NULL DEFAULT '0',
  PH            SMALLINT(2) NOT NULL DEFAULT '0',
  Laser         SMALLINT(2) NOT NULL DEFAULT '0',
  Ions          SMALLINT(2) NOT NULL DEFAULT '0',
  Plasma        SMALLINT(2) NOT NULL DEFAULT '0',
  RRI           SMALLINT(2) NOT NULL DEFAULT '0',
  Graviton      SMALLINT(2) NOT NULL DEFAULT '0',
  Astrophysique SMALLINT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################
##
## Structure de la table `ogspy_mod_config`
##

CREATE TABLE `ogspy_mod_config` (
  `mod`    VARCHAR(50)  NOT NULL DEFAULT '',
  `config` VARCHAR(255) NOT NULL DEFAULT '',
  `value`  VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`mod`, `config`)
)
  DEFAULT CHARSET = utf8;
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
INSERT INTO `ogspy_config` VALUES ('url_forum', 'https://forum.ogsteam.fr/');
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

## Partie mail
INSERT INTO `ogspy_config` VALUES ('mail_active', '0');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_use', '0');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_server', '');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_secure', '0');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_host', '');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_port', '');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_username', '');
INSERT INTO `ogspy_config` VALUES ('mail_smtp_password', '');


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
  `id_spy`        INT(11)         NOT NULL AUTO_INCREMENT,
  `planet_name`   VARCHAR(20)     NOT NULL DEFAULT '',
  `coordinates`   VARCHAR(9)      NOT NULL DEFAULT '',
  `metal`         INT(7)          NOT NULL DEFAULT '-1',
  `cristal`       INT(7)          NOT NULL DEFAULT '-1',
  `deuterium`     INT(7)          NOT NULL DEFAULT '-1',
  `energie`       INT(7)          NOT NULL DEFAULT '-1',
  `activite`      INT(2)          NOT NULL DEFAULT '-1',
  `M`             SMALLINT(2)     NOT NULL DEFAULT '-1',
  `C`             SMALLINT(2)     NOT NULL DEFAULT '-1',
  `D`             SMALLINT(2)     NOT NULL DEFAULT '-1',
  `CES`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `CEF`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `UdR`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `UdN`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `CSp`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `HM`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `HC`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `HD`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Lab`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Ter`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `DdR`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Silo`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Dock`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `BaLu`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Pha`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `PoSa`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `LM`            INT(11)         NOT NULL DEFAULT '-1',
  `LLE`           INT(11)         NOT NULL DEFAULT '-1',
  `LLO`           INT(11)         NOT NULL DEFAULT '-1',
  `CG`            INT(11)         NOT NULL DEFAULT '-1',
  `AI`            INT(11)         NOT NULL DEFAULT '-1',
  `LP`            INT(11)         NOT NULL DEFAULT '-1',
  `PB`            SMALLINT(1)     NOT NULL DEFAULT '-1',
  `GB`            SMALLINT(1)     NOT NULL DEFAULT '-1',
  `MIC`           SMALLINT(3)     NOT NULL DEFAULT '-1',
  `MIP`           SMALLINT(3)     NOT NULL DEFAULT '-1',
  `PT`            INT(11)         NOT NULL DEFAULT '-1',
  `GT`            INT(11)         NOT NULL DEFAULT '-1',
  `CLE`           INT(11)         NOT NULL DEFAULT '-1',
  `CLO`           INT(11)         NOT NULL DEFAULT '-1',
  `CR`            INT(11)         NOT NULL DEFAULT '-1',
  `VB`            INT(11)         NOT NULL DEFAULT '-1',
  `VC`            INT(11)         NOT NULL DEFAULT '-1',
  `REC`           INT(11)         NOT NULL DEFAULT '-1',
  `SE`            INT(11)         NOT NULL DEFAULT '-1',
  `BMD`           INT(11)         NOT NULL DEFAULT '-1',
  `DST`           INT(11)         NOT NULL DEFAULT '-1',
  `EDLM`          INT(11)         NOT NULL DEFAULT '-1',
  `SAT`           INT(11)                  DEFAULT '-1',
  `TRA`           INT(11)         NOT NULL DEFAULT '-1',
  `Esp`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Ordi`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Armes`         SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Bouclier`      SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Protection`    SMALLINT(2)     NOT NULL DEFAULT '-1',
  `NRJ`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Hyp`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `RC`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `RI`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `PH`            SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Laser`         SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Ions`          SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Plasma`        SMALLINT(2)     NOT NULL DEFAULT '-1',
  `RRI`           SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Graviton`      SMALLINT(2)     NOT NULL DEFAULT '-1',
  `Astrophysique` SMALLINT(2)     NOT NULL DEFAULT '-1',
  `dateRE`        INT(11)         NOT NULL DEFAULT '0',
  `proba`         SMALLINT(2)     NOT NULL DEFAULT '0',
  `active`        ENUM ('0', '1') NOT NULL DEFAULT '1',
  `sender_id`     INT(11)         NOT NULL,
  PRIMARY KEY (`id_spy`),
  KEY `coordinates` (`coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRC`
##

CREATE TABLE `ogspy_parsedRC` (
  `id_rc`       INT(11)    NOT NULL AUTO_INCREMENT,
  `dateRC`      INT(11)    NOT NULL DEFAULT '0',
  `coordinates` VARCHAR(9) NOT NULL DEFAULT '',
  `nb_rounds`   INT(2)     NOT NULL DEFAULT '0',
  `victoire`    CHAR       NOT NULL DEFAULT 'A',
  `pertes_A`    BIGINT     NOT NULL DEFAULT '0',
  `pertes_D`    BIGINT     NOT NULL DEFAULT '0',
  `gain_M`      BIGINT     NOT NULL DEFAULT '-1',
  `gain_C`      BIGINT     NOT NULL DEFAULT '-1',
  `gain_D`      BIGINT     NOT NULL DEFAULT '-1',
  `debris_M`    BIGINT     NOT NULL DEFAULT '-1',
  `debris_C`    BIGINT     NOT NULL DEFAULT '-1',
  `lune`        INT(2)     NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rc`),
  KEY `coordinatesrc` (`coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRCRound`
##

CREATE TABLE `ogspy_parsedRCRound` (
  `id_rcround`        INT(11) NOT NULL AUTO_INCREMENT,
  `id_rc`             INT(11) NOT NULL,
  `numround`          INT(2)  NOT NULL,
  `attaque_tir`       INT(11) NOT NULL DEFAULT '-1',
  `attaque_puissance` INT(11) NOT NULL DEFAULT '-1',
  `defense_bouclier`  INT(11) NOT NULL DEFAULT '-1',
  `attaque_bouclier`  INT(11) NOT NULL DEFAULT '-1',
  `defense_tir`       INT(11) NOT NULL DEFAULT '-1',
  `defense_puissance` INT(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (id_rcround),
  KEY `rcround` (`id_rc`, `numround`),
  KEY `id_rc` (`id_rc`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_round_attack`
##

CREATE TABLE `ogspy_round_attack` (
  `id_roundattack` INT(11)     NOT NULL AUTO_INCREMENT,
  `id_rcround`     INT(11)     NOT NULL,
  `player`         VARCHAR(30) NOT NULL DEFAULT '',
  `coordinates`    VARCHAR(9)  NOT NULL DEFAULT '',
  `Armes`          SMALLINT(2) NOT NULL DEFAULT '-1',
  `Bouclier`       SMALLINT(2) NOT NULL DEFAULT '-1',
  `Protection`     SMALLINT(2) NOT NULL DEFAULT '-1',
  `PT`             INT(11)     NOT NULL DEFAULT '-1',
  `GT`             INT(11)     NOT NULL DEFAULT '-1',
  `CLE`            INT(11)     NOT NULL DEFAULT '-1',
  `CLO`            INT(11)     NOT NULL DEFAULT '-1',
  `CR`             INT(11)     NOT NULL DEFAULT '-1',
  `VB`             INT(11)     NOT NULL DEFAULT '-1',
  `VC`             INT(11)     NOT NULL DEFAULT '-1',
  `REC`            INT(11)     NOT NULL DEFAULT '-1',
  `SE`             INT(11)     NOT NULL DEFAULT '-1',
  `BMD`            INT(11)     NOT NULL DEFAULT '-1',
  `DST`            INT(11)     NOT NULL DEFAULT '-1',
  `EDLM`           INT(11)     NOT NULL DEFAULT '-1',
  `TRA`            INT(11)     NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id_roundattack`),
  KEY `id_rcround` (`id_rcround`),
  KEY `player` (`player`, `coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_round_defense`
##

CREATE TABLE `ogspy_round_defense` (
  `id_rounddefense` INT(11)     NOT NULL AUTO_INCREMENT,
  `id_rcround`      INT(11)     NOT NULL,
  `player`          VARCHAR(30) NOT NULL DEFAULT '',
  `coordinates`     VARCHAR(9)  NOT NULL DEFAULT '',
  `Armes`           SMALLINT(2) NOT NULL DEFAULT '-1',
  `Bouclier`        SMALLINT(2) NOT NULL DEFAULT '-1',
  `Protection`      SMALLINT(2) NOT NULL DEFAULT '-1',
  `PT`              INT(11)     NOT NULL DEFAULT '-1',
  `GT`              INT(11)     NOT NULL DEFAULT '-1',
  `CLE`             INT(11)     NOT NULL DEFAULT '-1',
  `CLO`             INT(11)     NOT NULL DEFAULT '-1',
  `CR`              INT(11)     NOT NULL DEFAULT '-1',
  `VB`              INT(11)     NOT NULL DEFAULT '-1',
  `VC`              INT(11)     NOT NULL DEFAULT '-1',
  `REC`             INT(11)     NOT NULL DEFAULT '-1',
  `SE`              INT(11)     NOT NULL DEFAULT '-1',
  `BMD`             INT(11)     NOT NULL DEFAULT '-1',
  `DST`             INT(11)     NOT NULL DEFAULT '-1',
  `EDLM`            INT(11)     NOT NULL DEFAULT '-1',
  `SAT`             INT(11)     NOT NULL DEFAULT '-1',
  `TRA`             INT(11)     NOT NULL DEFAULT '-1',
  `LM`              INT(11)     NOT NULL DEFAULT '-1',
  `LLE`             INT(11)     NOT NULL DEFAULT '-1',
  `LLO`             INT(11)     NOT NULL DEFAULT '-1',
  `CG`              INT(11)     NOT NULL DEFAULT '-1',
  `AI`              INT(11)     NOT NULL DEFAULT '-1',
  `LP`              INT(11)     NOT NULL DEFAULT '-1',
  `PB`              SMALLINT(1) NOT NULL DEFAULT '-1',
  `GB`              SMALLINT(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id_rounddefense`),
  KEY `id_rcround` (`id_rcround`),
  KEY `player` (`player`, `coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################
##
## Structure de la table `ogspy_gcm_users`
##
CREATE TABLE IF NOT EXISTS `ogspy_gcm_users` (
  `user_id`         INT(11)      NOT NULL DEFAULT '0',
  `gcm_regid`       VARCHAR(128) NOT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `version_android` VARCHAR(50),
  `version_ogspy`   VARCHAR(50),
  `device`          VARCHAR(50),
  PRIMARY KEY (`gcm_regid`)
)
  DEFAULT CHARSET = utf8;

