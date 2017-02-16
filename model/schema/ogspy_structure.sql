#
# OGSpy version 3.4.0
# Novembre 2016
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
DROP TABLE IF EXISTS ogspy_mod_user_config;
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
DROP TABLE IF EXISTS ogspy_tokens;

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
  group_id                  INT             NOT NULL AUTO_INCREMENT,
  group_name                VARCHAR(255)    NOT NULL,
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
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)    NOT NULL
  COMMENT 'Nom du mod',
  `menu`       VARCHAR(255)    NOT NULL
  COMMENT 'Titre du lien dans le menu',
  `action`     VARCHAR(255)    NOT NULL
  COMMENT 'Action transmise en get et traitée dans index.php',
  `root`       VARCHAR(255)    NOT NULL
  COMMENT 'Répertoire où se situe le mod (relatif au répertoire mods)',
  `link`       VARCHAR(255)    NOT NULL
  COMMENT 'fichier principale du mod',
  `version`    VARCHAR(255)    NOT NULL
  COMMENT 'Version du mod',
  `position`   INT             NOT NULL,
  `active`     TINYINT(1)      NOT NULL
  COMMENT 'Permet de désactiver un mod sans le désinstaller, évite les mods#pirates',
  `admin_only` ENUM ('0', '1') NOT NULL DEFAULT '0'
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  ally          VARCHAR(255) NOT NULL,
  number_member INT UNSIGNED NOT NULL,
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate          INT UNSIGNED NOT NULL DEFAULT '0',
  rank              INT UNSIGNED NOT NULL DEFAULT '0',
  ally              VARCHAR(255) NOT NULL,
  number_member     INT UNSIGNED NOT NULL,
  points            INT UNSIGNED NOT NULL DEFAULT '0',
  points_per_member INT UNSIGNED NOT NULL,
  sender_id         INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate      INT UNSIGNED NOT NULL DEFAULT '0',
  rank          INT UNSIGNED NOT NULL DEFAULT '0',
  player        VARCHAR(255) NOT NULL DEFAULT '',
  ally          VARCHAR(255) NOT NULL DEFAULT '',
  points        INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id     INT UNSIGNED NOT NULL DEFAULT '0',
  nb_spacecraft INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  datadate  INT UNSIGNED NOT NULL DEFAULT '0',
  rank      INT UNSIGNED NOT NULL DEFAULT '0',
  player    VARCHAR(255) NOT NULL DEFAULT '',
  ally      VARCHAR(255) NOT NULL DEFAULT '',
  points    INT UNSIGNED NOT NULL DEFAULT '0',
  sender_id INT UNSIGNED NOT NULL DEFAULT '0',
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
  session_user_id   INT UNSIGNED    NOT NULL DEFAULT '0',
  session_start     INT UNSIGNED    NOT NULL DEFAULT '0',
  session_expire    INT UNSIGNED    NOT NULL DEFAULT '0',
  session_ip        CHAR(32)        NOT NULL DEFAULT '',
  session_ogs       ENUM ('0', '1') NOT NULL DEFAULT '0',
  session_lastvisit INT UNSIGNED    NOT NULL DEFAULT '0',
  UNIQUE KEY session_id (session_id, session_ip)
)
  DEFAULT CHARSET = utf8;

## ########################################################

##
## Structure de la table `ogspy_apitokens`
##

CREATE TABLE ogspy_tokens (
  token_id      CHAR(64)     NOT NULL DEFAULT '',
  token_user_id INT UNSIGNED NOT NULL DEFAULT '0',
  token_expire  INT UNSIGNED NOT NULL DEFAULT '0',
  token_type    VARCHAR(32)  NOT NULL DEFAULT '0',
  UNIQUE KEY api_session_id (token_id, token_user_id)
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
  player              VARCHAR(20)              DEFAULT NULL,
  `status`            VARCHAR(5)      NOT NULL,
  last_update         INT UNSIGNED    NOT NULL DEFAULT '0',
  last_update_moon    INT UNSIGNED    NOT NULL DEFAULT '0',
  last_update_user_id INT UNSIGNED    NOT NULL DEFAULT '0',
  UNIQUE KEY univers (galaxy, system, `row`),
  KEY player (player)
)
  DEFAULT CHARSET = utf8;

## ########################################################
## 
## Structure de la table `ogspy_user`
## 

CREATE TABLE ogspy_user (
  `user_id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_name`           VARCHAR(255)    NOT NULL DEFAULT '',
  `user_password`       VARCHAR(255)    NOT NULL DEFAULT '',
  `user_email`          VARCHAR(255)    NOT NULL DEFAULT '',
  `user_admin`          ENUM ('0', '1') NOT NULL DEFAULT '0',
  `user_coadmin`        ENUM ('0', '1') NOT NULL DEFAULT '0',
  `user_active`         ENUM ('0', '1') NOT NULL DEFAULT '0',
  `user_regdate`        INT UNSIGNED    NOT NULL DEFAULT '0',
  `user_lastvisit`      INT UNSIGNED    NOT NULL DEFAULT '0',
  `user_galaxy`         SMALLINT(2)     NOT NULL DEFAULT '1',
  `user_system`         SMALLINT(3)     NOT NULL DEFAULT '1',
  `planet_added_xtense` INT UNSIGNED    NOT NULL DEFAULT '0',
  `search`              INT UNSIGNED    NOT NULL DEFAULT '0',
  `spy_added_xtense`    INT UNSIGNED    NOT NULL DEFAULT '0',
  `rank_added_xtense`   INT UNSIGNED    NOT NULL DEFAULT '0',
  `xtense_type`         ENUM ('FF', 'GM-FF', 'GM-GC', 'ANDROID'),
  `xtense_version`      VARCHAR(255),
  `user_skin`           VARCHAR(255)    NOT NULL DEFAULT '',
  `user_stat_name`      VARCHAR(255)    NOT NULL DEFAULT '',
  `management_user`     ENUM ('0', '1') NOT NULL DEFAULT '0',
  `management_ranking`  ENUM ('0', '1') NOT NULL DEFAULT '0',
  `disable_ip_check`    ENUM ('0', '1') NOT NULL DEFAULT '0',
  `off_commandant`      ENUM ('0', '1') NOT NULL DEFAULT '0',
  `off_amiral`          ENUM ('0', '1') NOT NULL DEFAULT '0',
  `off_ingenieur`       ENUM ('0', '1') NOT NULL DEFAULT '0',
  `off_geologue`        ENUM ('0', '1') NOT NULL DEFAULT '0',
  `off_technocrate`     ENUM ('0', '1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY user_name (`user_name`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_building`
## 

CREATE TABLE ogspy_user_building (
  user_id         INT UNSIGNED NOT NULL DEFAULT '0',
  planet_id       INT UNSIGNED NOT NULL DEFAULT '0',
  planet_name     VARCHAR(20)  NOT NULL DEFAULT '',
  coordinates     VARCHAR(10)  NOT NULL DEFAULT '',
  `fields`        SMALLINT(3)  NOT NULL DEFAULT '0',
  `boosters`      VARCHAR(64)  NOT NULL DEFAULT 'm:0:0_c:0:0_d:0:0_p:0_m:0',
  temperature_min SMALLINT(2)  NOT NULL DEFAULT '0',
  temperature_max SMALLINT(2)  NOT NULL DEFAULT '0',
  Sat             SMALLINT(5)  NOT NULL DEFAULT '0',
  Sat_percentage  SMALLINT(3)  NOT NULL DEFAULT '100',
  M               SMALLINT(2)  NOT NULL DEFAULT '0',
  M_percentage    SMALLINT(3)  NOT NULL DEFAULT '100',
  C               SMALLINT(2)  NOT NULL DEFAULT '0',
  C_Percentage    SMALLINT(3)  NOT NULL DEFAULT '100',
  D               SMALLINT(2)  NOT NULL DEFAULT '0',
  D_percentage    SMALLINT(3)  NOT NULL DEFAULT '100',
  CES             SMALLINT(2)  NOT NULL DEFAULT '0',
  CES_percentage  SMALLINT(3)  NOT NULL DEFAULT '100',
  CEF             SMALLINT(2)  NOT NULL DEFAULT '0',
  CEF_percentage  SMALLINT(3)  NOT NULL DEFAULT '100',
  UdR             SMALLINT(2)  NOT NULL DEFAULT '0',
  UdN             SMALLINT(2)  NOT NULL DEFAULT '0',
  CSp             SMALLINT(2)  NOT NULL DEFAULT '0',
  HM              SMALLINT(2)  NOT NULL DEFAULT '0',
  HC              SMALLINT(2)  NOT NULL DEFAULT '0',
  HD              SMALLINT(2)  NOT NULL DEFAULT '0',
  Lab             SMALLINT(2)  NOT NULL DEFAULT '0',
  Ter             SMALLINT(2)  NOT NULL DEFAULT '0',
  DdR             SMALLINT(2)  NOT NULL DEFAULT '0',
  Silo            SMALLINT(2)  NOT NULL DEFAULT '0',
  BaLu            SMALLINT(2)  NOT NULL DEFAULT '0',
  Pha             SMALLINT(2)  NOT NULL DEFAULT '0',
  PoSa            SMALLINT(2)  NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, planet_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_defence`
## 

CREATE TABLE ogspy_user_defence (
  user_id   INT UNSIGNED NOT NULL DEFAULT '0',
  planet_id INT UNSIGNED NOT NULL DEFAULT '0',
  LM        INT UNSIGNED NOT NULL DEFAULT '0',
  LLE       INT UNSIGNED NOT NULL DEFAULT '0',
  LLO       INT UNSIGNED NOT NULL DEFAULT '0',
  CG        INT UNSIGNED NOT NULL DEFAULT '0',
  AI        INT UNSIGNED NOT NULL DEFAULT '0',
  LP        INT UNSIGNED NOT NULL DEFAULT '0',
  PB        SMALLINT(1)  NOT NULL DEFAULT '0',
  GB        SMALLINT(1)  NOT NULL DEFAULT '0',
  MIC       SMALLINT(3)  NOT NULL DEFAULT '0',
  MIP       SMALLINT(3)  NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, planet_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################

## 
## Structure de la table `ogspy_user_favorite`
## 

CREATE TABLE ogspy_user_favorite (
  user_id INT UNSIGNED NOT NULL DEFAULT '0',
  galaxy  SMALLINT(2)  NOT NULL DEFAULT '1',
  system  SMALLINT(3)  NOT NULL DEFAULT '0',
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
  user_id INT UNSIGNED NOT NULL DEFAULT '0',
  spy_id  INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id, spy_id)
)
  DEFAULT CHARSET = utf8;

## ########################################################
## 
## Structure de la table `ogspy_user_technology`
## 

CREATE TABLE ogspy_user_technology (
  user_id       INT UNSIGNED NOT NULL DEFAULT '0',
  Esp           SMALLINT(2)  NOT NULL DEFAULT '0',
  Ordi          SMALLINT(2)  NOT NULL DEFAULT '0',
  Armes         SMALLINT(2)  NOT NULL DEFAULT '0',
  Bouclier      SMALLINT(2)  NOT NULL DEFAULT '0',
  Protection    SMALLINT(2)  NOT NULL DEFAULT '0',
  NRJ           SMALLINT(2)  NOT NULL DEFAULT '0',
  Hyp           SMALLINT(2)  NOT NULL DEFAULT '0',
  RC            SMALLINT(2)  NOT NULL DEFAULT '0',
  RI            SMALLINT(2)  NOT NULL DEFAULT '0',
  PH            SMALLINT(2)  NOT NULL DEFAULT '0',
  Laser         SMALLINT(2)  NOT NULL DEFAULT '0',
  Ions          SMALLINT(2)  NOT NULL DEFAULT '0',
  Plasma        SMALLINT(2)  NOT NULL DEFAULT '0',
  RRI           SMALLINT(2)  NOT NULL DEFAULT '0',
  Graviton      SMALLINT(2)  NOT NULL DEFAULT '0',
  Astrophysique SMALLINT(2)  NOT NULL DEFAULT '0',
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
## Structure de la table `ogspy_mod_user_config
##
CREATE TABLE `ogspy_mod_user_config` (
  `mod`     VARCHAR(50)  NOT NULL,
  `config`  VARCHAR(255) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `value`   VARCHAR(255) NOT NULL,
  PRIMARY KEY (`mod`, `config`, `user_id`),
  INDEX `fk_user_userid` (`user_id`),
  CONSTRAINT `fk_user_userid` FOREIGN KEY (`user_id`) REFERENCES ogspy_user (`user_id`)
)
  DEFAULT CHARSET = utf8
  ENGINE = InnoDB;

## 
## Contenu de la table `ogspy_config`
## 

INSERT INTO `ogspy_config` VALUES ('allied', '');
INSERT INTO `ogspy_config` VALUES ('ally_protection', '');
INSERT INTO `ogspy_config` VALUES ('debug_log', '0');
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
  `id_spy`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `planet_name`   VARCHAR(20)  NOT NULL DEFAULT '',
  `coordinates`   VARCHAR(9)   NOT NULL DEFAULT '',
  `is_moon`       ENUM ('0', '1') NOT NULL DEFAULT '0',
  `metal`         INT(7)                DEFAULT NULL,
  `cristal`       INT(7)                DEFAULT NULL,
  `deuterium`     INT(7)                DEFAULT NULL,
  `energie`       INT(7)                DEFAULT NULL,
  `activite`      INT(2)                DEFAULT NULL,
  `M`             TINYINT(2) UNSIGNED   DEFAULT NULL,
  `C`             TINYINT(2) UNSIGNED   DEFAULT NULL,
  `D`             TINYINT(2) UNSIGNED   DEFAULT NULL,
  `CES`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `CEF`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `UdR`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `UdN`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `CSp`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `HM`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `HC`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `HD`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Lab`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Ter`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `DdR`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Silo`          TINYINT(2) UNSIGNED   DEFAULT NULL,
  `BaLu`          TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Pha`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `PoSa`          TINYINT(2) UNSIGNED   DEFAULT NULL,
  `LM`            INT UNSIGNED          DEFAULT NULL,
  `LLE`           INT UNSIGNED          DEFAULT NULL,
  `LLO`           INT UNSIGNED          DEFAULT NULL,
  `CG`            INT UNSIGNED          DEFAULT NULL,
  `AI`            INT UNSIGNED          DEFAULT NULL,
  `LP`            INT UNSIGNED          DEFAULT NULL,
  `PB`            SMALLINT(1)           DEFAULT NULL,
  `GB`            SMALLINT(1)           DEFAULT NULL,
  `MIC`           SMALLINT(3)           DEFAULT NULL,
  `MIP`           SMALLINT(3)           DEFAULT NULL,
  `PT`            INT UNSIGNED          DEFAULT NULL,
  `GT`            INT UNSIGNED          DEFAULT NULL,
  `CLE`           INT UNSIGNED          DEFAULT NULL,
  `CLO`           INT UNSIGNED          DEFAULT NULL,
  `CR`            INT UNSIGNED          DEFAULT NULL,
  `VB`            INT UNSIGNED          DEFAULT NULL,
  `VC`            INT UNSIGNED          DEFAULT NULL,
  `REC`           INT UNSIGNED          DEFAULT NULL,
  `SE`            INT UNSIGNED          DEFAULT NULL,
  `BMD`           INT UNSIGNED          DEFAULT NULL,
  `DST`           INT UNSIGNED          DEFAULT NULL,
  `EDLM`          INT UNSIGNED          DEFAULT NULL,
  `SAT`           INT UNSIGNED          DEFAULT NULL,
  `TRA`           INT UNSIGNED          DEFAULT NULL,
  `Esp`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Ordi`          TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Armes`         TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Bouclier`      TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Protection`    TINYINT(2) UNSIGNED   DEFAULT NULL,
  `NRJ`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Hyp`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `RC`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `RI`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `PH`            TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Laser`         TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Ions`          TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Plasma`        TINYINT(2) UNSIGNED   DEFAULT NULL,
  `RRI`           TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Graviton`      TINYINT(2) UNSIGNED   DEFAULT NULL,
  `Astrophysique` TINYINT(2) UNSIGNED   DEFAULT NULL,
  `dateRE`        INT UNSIGNED NOT NULL,
  `proba`         TINYINT(2)   NOT NULL,
  `active`        ENUM ('0', '1')       DEFAULT '1',
  `sender_id`     INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_spy`),
  KEY `coordinates` (`coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRC`
##

CREATE TABLE `ogspy_parsedRC` (
  `id_rc`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateRC`      INT UNSIGNED NOT NULL DEFAULT '0',
  `coordinates` VARCHAR(255) NOT NULL DEFAULT '',
  `nb_rounds`   TINYINT(2)   NOT NULL DEFAULT '0',
  `victoire`    CHAR         NOT NULL DEFAULT 'A',
  `pertes_A`    BIGINT       NOT NULL DEFAULT '0',
  `pertes_D`    BIGINT       NOT NULL DEFAULT '0',
  `gain_M`      BIGINT                DEFAULT NULL,
  `gain_C`      BIGINT                DEFAULT NULL,
  `gain_D`      BIGINT                DEFAULT NULL,
  `debris_M`    BIGINT                DEFAULT NULL,
  `debris_C`    BIGINT                DEFAULT NULL,
  `lune`        TINYINT(2)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rc`),
  KEY `coordinatesrc` (`coordinates`)
)
  DEFAULT CHARSET = utf8;
## ########################################################

##
## Structure de la table `ogspy_parsedRCRound`
##

CREATE TABLE `ogspy_parsedRCRound` (
  `id_rcround`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rc`             INT UNSIGNED NOT NULL,
  `numround`          TINYINT(2)   NOT NULL,
  `attaque_tir`       INT UNSIGNED NOT NULL,
  `attaque_puissance` INT UNSIGNED NOT NULL,
  `defense_bouclier`  INT UNSIGNED NOT NULL,
  `attaque_bouclier`  INT UNSIGNED NOT NULL,
  `defense_tir`       INT UNSIGNED NOT NULL,
  `defense_puissance` INT UNSIGNED NOT NULL,
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
  `id_roundattack` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rcround`     INT UNSIGNED NOT NULL,
  `player`         VARCHAR(255) NOT NULL,
  `coordinates`    VARCHAR(9)   NOT NULL,
  `Armes`          SMALLINT(2)  NOT NULL,
  `Bouclier`       SMALLINT(2)  NOT NULL,
  `Protection`     SMALLINT(2)  NOT NULL,
  `PT`             INT UNSIGNED          DEFAULT NULL,
  `GT`             INT UNSIGNED          DEFAULT NULL,
  `CLE`            INT UNSIGNED          DEFAULT NULL,
  `CLO`            INT UNSIGNED          DEFAULT NULL,
  `CR`             INT UNSIGNED          DEFAULT NULL,
  `VB`             INT UNSIGNED          DEFAULT NULL,
  `VC`             INT UNSIGNED          DEFAULT NULL,
  `REC`            INT UNSIGNED          DEFAULT NULL,
  `SE`             INT UNSIGNED          DEFAULT NULL,
  `BMD`            INT UNSIGNED          DEFAULT NULL,
  `DST`            INT UNSIGNED          DEFAULT NULL,
  `EDLM`           INT UNSIGNED          DEFAULT NULL,
  `TRA`            INT UNSIGNED          DEFAULT NULL,
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
  `id_rounddefense` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rcround`      INT UNSIGNED NOT NULL,
  `player`          VARCHAR(255) NOT NULL,
  `coordinates`     VARCHAR(9)   NOT NULL,
  `Armes`           SMALLINT(2)  NOT NULL,
  `Bouclier`        SMALLINT(2)  NOT NULL,
  `Protection`      SMALLINT(2)  NOT NULL,
  `PT`              INT UNSIGNED          DEFAULT NULL,
  `GT`              INT UNSIGNED          DEFAULT NULL,
  `CLE`             INT UNSIGNED          DEFAULT NULL,
  `CLO`             INT UNSIGNED          DEFAULT NULL,
  `CR`              INT UNSIGNED          DEFAULT NULL,
  `VB`              INT UNSIGNED          DEFAULT NULL,
  `VC`              INT UNSIGNED          DEFAULT NULL,
  `REC`             INT UNSIGNED          DEFAULT NULL,
  `SE`              INT UNSIGNED          DEFAULT NULL,
  `BMD`             INT UNSIGNED          DEFAULT NULL,
  `DST`             INT UNSIGNED          DEFAULT NULL,
  `EDLM`            INT UNSIGNED          DEFAULT NULL,
  `SAT`             INT UNSIGNED          DEFAULT NULL,
  `TRA`             INT UNSIGNED          DEFAULT NULL,
  `LM`              INT UNSIGNED          DEFAULT NULL,
  `LLE`             INT UNSIGNED          DEFAULT NULL,
  `LLO`             INT UNSIGNED          DEFAULT NULL,
  `CG`              INT UNSIGNED          DEFAULT NULL,
  `AI`              INT UNSIGNED          DEFAULT NULL,
  `LP`              INT UNSIGNED          DEFAULT NULL,
  `PB`              SMALLINT(1)           DEFAULT NULL,
  `GB`              SMALLINT(1)           DEFAULT NULL,
  PRIMARY KEY (`id_rounddefense`),
  KEY `id_rcround` (`id_rcround`),
  KEY `player` (`player`, `coordinates`)
)
  DEFAULT CHARSET = utf8;


