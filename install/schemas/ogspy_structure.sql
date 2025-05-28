--
-- OGSpy version 4.0.0
-- Mai 2025
--
--

-- -----------------------------------------------------------------------------
-- Partie DROP
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- Suppression des tables existantes
DROP TABLE IF EXISTS
    `ogspy_user`,
    `ogspy_user_tokens`,
    `ogspy_config`,
    `ogspy_group`,
    `ogspy_user_group`,
    `ogspy_sessions`,
    `ogspy_statistics`,
    `ogspy_mod`,
    `ogspy_mod_config`,
    `ogspy_mod_user_config`,
    `ogspy_game_user_favorites`,
    `ogspy_game_player`,
    `ogspy_game_ally`,
    `ogspy_game_universe`,
    `ogspy_game_astro_object`,
    `ogspy_game_player_defense`,
    `ogspy_game_player_spy`,
    `ogspy_game_player_technology`,
    `ogspy_game_player_fleet`,
    `ogspy_game_parsedspy`,
    `ogspy_game_parsedRC`,
    `ogspy_game_parsedRCRound`,
    `ogspy_game_rc_round_attack`,
    `ogspy_game_rc_round_defense`,
    `ogspy_game_rank_ally_economics`,
    `ogspy_game_rank_ally_technology`,
    `ogspy_game_rank_ally_military`,
    `ogspy_game_rank_ally_military_built`,
    `ogspy_game_rank_ally_military_loose`,
    `ogspy_game_rank_ally_military_destruct`,
    `ogspy_game_rank_ally_honor`,
    `ogspy_game_rank_ally_points`,
    `ogspy_game_rank_player_economics`,
    `ogspy_game_rank_player_technology`,
    `ogspy_game_rank_player_military`,
    `ogspy_game_rank_player_military_built`,
    `ogspy_game_rank_player_military_loose`,
    `ogspy_game_rank_player_military_destruct`,
    `ogspy_game_rank_player_honor`,
    `ogspy_game_rank_player_points`;


-- -----------------------------------------------------------------------------
-- Partie OGSpy
-- -----------------------------------------------------------------------------

--
-- Structure de la table `ogspy_user`
--
CREATE TABLE `ogspy_user`
(
    `id`                 INT(11)      NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(20)  NOT NULL DEFAULT '',
    `password_s`         VARCHAR(255) NOT NULL DEFAULT '',
    `pwd_change`         TINYINT(1)   NOT NULL DEFAULT '1',
    `email`              VARCHAR(50)  NOT NULL DEFAULT '',
    `email_valid`        TINYINT(1)   NOT NULL DEFAULT '0',
    `admin`              TINYINT(1)   NOT NULL DEFAULT '0',
    `coadmin`            TINYINT(1)   NOT NULL DEFAULT '0',
    `active`             TINYINT(1)   NOT NULL DEFAULT '0',
    `regdate`            INT(11)      NOT NULL DEFAULT '0',
    `lastvisit`          INT(11)      NOT NULL DEFAULT '0',
    `default_galaxy`     SMALLINT(2)  NOT NULL DEFAULT '1',
    `default_system`     SMALLINT(3)  NOT NULL DEFAULT '1',
    `planet_imports`     INT(11)      NOT NULL DEFAULT '0',
    `spy_imports`        INT(11)      NOT NULL DEFAULT '0',
    `rank_imports`       INT(11)      NOT NULL DEFAULT '0',
    `search`             INT(11)      NOT NULL DEFAULT '0',
    `xtense_type`        ENUM ('FF', 'GM-FF', 'GM-GC', 'ANDROID'),
    `xtense_version`     VARCHAR(10),
    `management_user`    TINYINT(1)   NOT NULL DEFAULT '0',
    `management_ranking` TINYINT(1)   NOT NULL DEFAULT '0',
    `disable_ip_check`   TINYINT(1)   NOT NULL DEFAULT '0',
    `player_id`          INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_tokens`
--
CREATE TABLE `ogspy_user_tokens`
(
    `id`              INT          NOT NULL AUTO_INCREMENT,
    `user_id`         INT          NOT NULL,
    `name`            VARCHAR(100) NOT NULL,
    `token`           VARCHAR(64)  NOT NULL,
    `expiration_date` VARCHAR(15)  NOT NULL,
    PRIMARY KEY (`id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;


--
-- Structure de la table `ogspy_config`
--
CREATE TABLE `ogspy_config`
(
    `name`  VARCHAR(255) NOT NULL DEFAULT '',
    `value` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`name`),
    UNIQUE KEY `name` (`name`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_group`
--
CREATE TABLE `ogspy_group`
(
    `id`                        MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
    `name`                      VARCHAR(30)  NOT NULL,
    `server_set_system`         TINYINT(1)   NOT NULL DEFAULT '0',
    `server_set_spy`            TINYINT(1)   NOT NULL DEFAULT '0',
    `server_set_rc`             TINYINT(1)   NOT NULL DEFAULT '0',
    `server_set_ranking`        TINYINT(1)   NOT NULL DEFAULT '0',
    `server_show_positionhided` TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_connection`            TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_set_system`            TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_get_system`            TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_set_spy`               TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_get_spy`               TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_set_ranking`           TINYINT(1)   NOT NULL DEFAULT '0',
    `ogs_get_ranking`           TINYINT(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_group`
--
CREATE TABLE `ogspy_user_group`
(
    `group_id` MEDIUMINT(8) NOT NULL DEFAULT '0',
    `user_id`  MEDIUMINT(8) NOT NULL DEFAULT '0',
    UNIQUE KEY `group_id` (`group_id`, `user_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_sessions`
--
CREATE TABLE `ogspy_sessions`
(
    `id`                CHAR(32)   NOT NULL DEFAULT '',
    `user_id`           INT(11)    NOT NULL DEFAULT '0',
    `session_start`     INT(11)    NOT NULL DEFAULT '0',
    `session_expire`    INT(11)    NOT NULL DEFAULT '0',
    `session_ip`        CHAR(32)   NOT NULL DEFAULT '',
    `session_type`      TINYINT(1) NOT NULL DEFAULT '0',
    `session_lastvisit` INT(11)    NOT NULL DEFAULT '0',
    UNIQUE KEY `session_id` (`id`, `session_ip`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_statistics`
--
CREATE TABLE `ogspy_statistics`
(
    `statistic_name`  VARCHAR(255) NOT NULL DEFAULT '',
    `statistic_value` VARCHAR(255) NOT NULL DEFAULT '0',
    PRIMARY KEY (`statistic_name`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_mod`
--
CREATE TABLE `ogspy_mod`
(
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255) NOT NULL COMMENT 'Nom du mod',
    `menu`       VARCHAR(255) NOT NULL COMMENT 'Titre du lien dans le menu',
    `action`     VARCHAR(255) NOT NULL COMMENT 'Action transmise en get et traitée dans index.php',
    `root`       VARCHAR(255) NOT NULL COMMENT 'Répertoire où se situe le mod (relatif au répertoire mods)',
    `link`       VARCHAR(255) NOT NULL COMMENT 'fichier principale du mod',
    `version`    VARCHAR(100) NOT NULL COMMENT 'Version du mod',
    `position`   INT(11)      NOT NULL DEFAULT '-1',
    `active`     TINYINT(1)   NOT NULL COMMENT 'Permet de désactiver un mod sans le désinstaller, évite les mods#pirates',
    `admin_only` TINYINT(1)   NOT NULL DEFAULT '0' COMMENT 'Affichage des mods de l utilisateur',
    PRIMARY KEY (`id`),
    UNIQUE KEY `action` (`action`),
    UNIQUE KEY `title` (`title`),
    UNIQUE KEY `menu` (`menu`),
    UNIQUE KEY `root` (`root`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_mod_config`
--
CREATE TABLE `ogspy_mod_config`
(
    `mod`    VARCHAR(50)  NOT NULL DEFAULT '',
    `config` VARCHAR(255) NOT NULL DEFAULT '',
    `value`  VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`mod`, `config`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_mod_user_config`
--
CREATE TABLE `ogspy_mod_user_config`
(
    `mod`     VARCHAR(50)  NOT NULL,
    `user_id` INT(11)      NOT NULL,
    `config`  VARCHAR(255) NOT NULL,
    `value`   VARCHAR(255) NOT NULL,
    PRIMARY KEY (`mod`, `config`, `user_id`),
    UNIQUE KEY `config` (`config`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_favorite`
--
CREATE TABLE `ogspy_game_user_favorites`
(
    `user_id` INT(11)     NOT NULL DEFAULT '0',
    `galaxy`  SMALLINT(2) NOT NULL DEFAULT '1',
    `system`  SMALLINT(3) NOT NULL DEFAULT '0',
    UNIQUE KEY `user_id` (`user_id`, `galaxy`, `system`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;


-- ---------------------------------------------------------------------------
-- Partie Game
-- ---------------------------------------------------------------------------

--
-- Structure de la table `ogspy_player`
--
CREATE TABLE `ogspy_game_player`
(
    `id`              INT(6)                             NOT NULL,
    `name`            VARCHAR(65)                        NOT NULL COMMENT 'Nom du joueur',
    `status`          VARCHAR(6)                         NOT NULL DEFAULT '',
    `class`           ENUM ('none', 'COL', 'GEN', 'EXP') NOT NULL DEFAULT 'none',
    `ally_id`         INT(6)                             NOT NULL COMMENT 'Action transmise en get et traitée dans index.php',
    `off_commandant`  TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_amiral`      TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_ingenieur`   TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_geologue`    TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_technocrate` TINYINT(1)                         NOT NULL DEFAULT '0',
    `datadate`        INT(11)                            NOT NULL DEFAULT '0',
    `ogspy_user_id`   INT(11)                                     DEFAULT NULL,
    PRIMARY KEY (`id`)

)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_ally`
--
CREATE TABLE `ogspy_game_ally`
(
    `id`       INT(6)                             NOT NULL,
    `name`     VARCHAR(65) COMMENT 'Nom de l alliance',
    `tag`      VARCHAR(65)                        NOT NULL DEFAULT '',
    `class`    ENUM ('none', 'MAR', 'WAR', 'RES') NOT NULL DEFAULT 'none',
    `datadate` INT(11)                            NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_game_astro_object`
--
CREATE TABLE `ogspy_game_astro_object`
(
    `id`                  INT(11)     NOT NULL,
    `type`                VARCHAR(10) NOT NULL DEFAULT 'planet' COMMENT 'Type astre : planet, moon, debris,...',
    `galaxy`              SMALLINT(2) NOT NULL DEFAULT '1',
    `system`              SMALLINT(3) NOT NULL DEFAULT '1',
    `row`                 SMALLINT(2) NOT NULL DEFAULT '1',
    `name`                VARCHAR(50) NOT NULL DEFAULT '',
    `player_id`           INT(11)     NOT NULL DEFAULT '0',
    `ally_id`             INT(6)      NOT NULL DEFAULT '-1',
    `fields`              SMALLINT(3) NOT NULL DEFAULT '0',
    `boosters`            VARCHAR(64) NOT NULL DEFAULT 'm:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0',
    `temperature_min`     SMALLINT(2) NOT NULL DEFAULT '0',
    `temperature_max`     SMALLINT(2) NOT NULL DEFAULT '0',
    `Sat`                 SMALLINT(5) NOT NULL DEFAULT '0',
    `Sat_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `FOR`                 SMALLINT(5) NOT NULL DEFAULT '0',
    `FOR_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `M`                   SMALLINT(2) NOT NULL DEFAULT '0',
    `M_percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `C`                   SMALLINT(2) NOT NULL DEFAULT '0',
    `C_Percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `D`                   SMALLINT(2) NOT NULL DEFAULT '0',
    `D_percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `CES`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `CES_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `CEF`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `CEF_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `UdR`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `UdN`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `CSp`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `HM`                  SMALLINT(2) NOT NULL DEFAULT '0',
    `HC`                  SMALLINT(2) NOT NULL DEFAULT '0',
    `HD`                  SMALLINT(2) NOT NULL DEFAULT '0',
    `Lab`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `Ter`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `DdR`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `Silo`                SMALLINT(2) NOT NULL DEFAULT '0',
    `Dock`                SMALLINT(2) NOT NULL DEFAULT '0',
    `BaLu`                SMALLINT(2) NOT NULL DEFAULT '0',
    `Pha`                 SMALLINT(2) NOT NULL DEFAULT '0',
    `PoSa`                SMALLINT(2) NOT NULL DEFAULT '0',
    `last_update`         INT(11)     NOT NULL DEFAULT '0',
    `last_update_moon`    INT(11)     NOT NULL DEFAULT '0',
    `last_update_user_id` INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `univers` (`galaxy`, `system`, `row`)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;
--
-- Structure de la table `ogspy_game_player_defense`
--
CREATE TABLE `ogspy_game_player_defense`
(
    `id`              INT(11)     NOT NULL AUTO_INCREMENT,
    `astro_object_id` INT(11)     NOT NULL,
    `LM`              INT(11)     NOT NULL DEFAULT '0',
    `LLE`             INT(11)     NOT NULL DEFAULT '0',
    `LLO`             INT(11)     NOT NULL DEFAULT '0',
    `CG`              INT(11)     NOT NULL DEFAULT '0',
    `AI`              INT(11)     NOT NULL DEFAULT '0',
    `LP`              INT(11)     NOT NULL DEFAULT '0',
    `PB`              SMALLINT(1) NOT NULL DEFAULT '0',
    `GB`              SMALLINT(1) NOT NULL DEFAULT '0',
    `MIC`             SMALLINT(3) NOT NULL DEFAULT '0',
    `MIP`             SMALLINT(3) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `astro_object_id` (`astro_object_id`),
    UNIQUE KEY `unique_astro_object_id` (`astro_object_id`)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_spy`
--
CREATE TABLE `ogspy_game_player_spy`
(
    `user_id` INT(11) NOT NULL DEFAULT '0',
    `spy_id`  INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`, `spy_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_technology` // Linked to player
--
CREATE TABLE ogspy_game_player_technology
(
    `player_id`     INT(11)     NOT NULL DEFAULT '0',
    `Esp`           SMALLINT(2) NOT NULL DEFAULT '0',
    `Ordi`          SMALLINT(2) NOT NULL DEFAULT '0',
    `Armes`         SMALLINT(2) NOT NULL DEFAULT '0',
    `Bouclier`      SMALLINT(2) NOT NULL DEFAULT '0',
    `Protection`    SMALLINT(2) NOT NULL DEFAULT '0',
    `NRJ`           SMALLINT(2) NOT NULL DEFAULT '0',
    `Hyp`           SMALLINT(2) NOT NULL DEFAULT '0',
    `RC`            SMALLINT(2) NOT NULL DEFAULT '0',
    `RI`            SMALLINT(2) NOT NULL DEFAULT '0',
    `PH`            SMALLINT(2) NOT NULL DEFAULT '0',
    `Laser`         SMALLINT(2) NOT NULL DEFAULT '0',
    `Ions`          SMALLINT(2) NOT NULL DEFAULT '0',
    `Plasma`        SMALLINT(2) NOT NULL DEFAULT '0',
    `RRI`           SMALLINT(2) NOT NULL DEFAULT '0',
    `Graviton`      SMALLINT(2) NOT NULL DEFAULT '0',
    `Astrophysique` SMALLINT(2) NOT NULL DEFAULT '0',
    PRIMARY KEY (`player_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;



--
-- Structure de la table `ogspy_parsedspy`
--
CREATE TABLE `ogspy_game_parsedspy`
(
    `id_spy`        INT(11)     NOT NULL AUTO_INCREMENT,
    `planet_name`   VARCHAR(20) NOT NULL DEFAULT '',
    `coordinates`   VARCHAR(9)  NOT NULL DEFAULT '',
    `metal`         BIGINT      NOT NULL DEFAULT '-1',
    `cristal`       BIGINT      NOT NULL DEFAULT '-1',
    `deuterium`     BIGINT      NOT NULL DEFAULT '-1',
    `energie`       INT(7)      NOT NULL DEFAULT '-1',
    `activite`      INT(2)      NOT NULL DEFAULT '-1',
    `M`             SMALLINT(2) NOT NULL DEFAULT '-1',
    `C`             SMALLINT(2) NOT NULL DEFAULT '-1',
    `D`             SMALLINT(2) NOT NULL DEFAULT '-1',
    `CES`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `CEF`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `UdR`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `UdN`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `CSp`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `HM`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `HC`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `HD`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `Lab`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `Ter`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `DdR`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `Silo`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `Dock`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `BaLu`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `Pha`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `PoSa`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `LM`            INT(11)     NOT NULL DEFAULT '-1',
    `LLE`           INT(11)     NOT NULL DEFAULT '-1',
    `LLO`           INT(11)     NOT NULL DEFAULT '-1',
    `CG`            INT(11)     NOT NULL DEFAULT '-1',
    `AI`            INT(11)     NOT NULL DEFAULT '-1',
    `LP`            INT(11)     NOT NULL DEFAULT '-1',
    `PB`            SMALLINT(1) NOT NULL DEFAULT '-1',
    `GB`            SMALLINT(1) NOT NULL DEFAULT '-1',
    `MIC`           SMALLINT(3) NOT NULL DEFAULT '-1',
    `MIP`           SMALLINT(3) NOT NULL DEFAULT '-1',
    `PT`            INT(11)     NOT NULL DEFAULT '-1',
    `GT`            INT(11)     NOT NULL DEFAULT '-1',
    `CLE`           INT(11)     NOT NULL DEFAULT '-1',
    `CLO`           INT(11)     NOT NULL DEFAULT '-1',
    `CR`            INT(11)     NOT NULL DEFAULT '-1',
    `VB`            INT(11)     NOT NULL DEFAULT '-1',
    `VC`            INT(11)     NOT NULL DEFAULT '-1',
    `REC`           INT(11)     NOT NULL DEFAULT '-1',
    `SE`            INT(11)     NOT NULL DEFAULT '-1',
    `BMD`           INT(11)     NOT NULL DEFAULT '-1',
    `DST`           INT(11)     NOT NULL DEFAULT '-1',
    `EDLM`          INT(11)     NOT NULL DEFAULT '-1',
    `SAT`           INT(11)              DEFAULT '-1',
    `TRA`           INT(11)     NOT NULL DEFAULT '-1',
    `FOR`           INT(11)     NOT NULL DEFAULT '-1',
    `FAU`           INT(11)     NOT NULL DEFAULT '-1',
    `ECL`           INT(11)     NOT NULL DEFAULT '-1',
    `Esp`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `Ordi`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `Armes`         SMALLINT(2) NOT NULL DEFAULT '-1',
    `Bouclier`      SMALLINT(2) NOT NULL DEFAULT '-1',
    `Protection`    SMALLINT(2) NOT NULL DEFAULT '-1',
    `NRJ`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `Hyp`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `RC`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `RI`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `PH`            SMALLINT(2) NOT NULL DEFAULT '-1',
    `Laser`         SMALLINT(2) NOT NULL DEFAULT '-1',
    `Ions`          SMALLINT(2) NOT NULL DEFAULT '-1',
    `Plasma`        SMALLINT(2) NOT NULL DEFAULT '-1',
    `RRI`           SMALLINT(2) NOT NULL DEFAULT '-1',
    `Graviton`      SMALLINT(2) NOT NULL DEFAULT '-1',
    `Astrophysique` SMALLINT(2) NOT NULL DEFAULT '-1',
    `dateRE`        INT(11)     NOT NULL DEFAULT '0',
    `proba`         SMALLINT(2) NOT NULL DEFAULT '0',
    `active`        TINYINT(1)  NOT NULL DEFAULT '1',
    `sender_id`     INT(11)     NOT NULL,
    PRIMARY KEY (`id_spy`),
    KEY `coordinates` (`coordinates`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_parsedRC`
--
CREATE TABLE `ogspy_game_parsedRC`
(
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
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_parsedRCRound`
--
CREATE TABLE `ogspy_game_parsedRCRound`
(
    `id_rcround`        INT(11) NOT NULL AUTO_INCREMENT,
    `id_rc`             INT(11) NOT NULL,
    `numround`          INT(2)  NOT NULL,
    `attaque_tir`       BIGINT  NOT NULL DEFAULT '-1',
    `attaque_puissance` BIGINT  NOT NULL DEFAULT '-1',
    `defense_bouclier`  BIGINT  NOT NULL DEFAULT '-1',
    `attaque_bouclier`  BIGINT  NOT NULL DEFAULT '-1',
    `defense_tir`       BIGINT  NOT NULL DEFAULT '-1',
    `defense_puissance` BIGINT  NOT NULL DEFAULT '-1',
    PRIMARY KEY (`id_rcround`),
    KEY `rcround` (`id_rc`, `numround`),
    KEY `id_rc` (`id_rc`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_round_attack`
--
CREATE TABLE `ogspy_game_rc_round_attack`
(
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
    `ECL`            INT(11)     NOT NULL DEFAULT '-1',
    `FAU`            INT(11)     NOT NULL DEFAULT '-1',
    PRIMARY KEY (`id_roundattack`),
    KEY `id_rcround` (`id_rcround`),
    KEY `player` (`player`, `coordinates`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_round_defense`
--
CREATE TABLE `ogspy_game_rc_round_defense`
(
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
    `FOR`             INT(11)     NOT NULL DEFAULT '-1',
    `TRA`             INT(11)     NOT NULL DEFAULT '-1',
    `ECL`             INT(11)     NOT NULL DEFAULT '-1',
    `FAU`             INT(11)     NOT NULL DEFAULT '-1',
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
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;


--
-- Structure de la table `ogspy_rank_ally_economics`
--
CREATE TABLE ogspy_game_rank_ally_economics
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_technology`
--
CREATE TABLE ogspy_game_rank_ally_technology
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military`
--
CREATE TABLE ogspy_game_rank_ally_military
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_built`
--
CREATE TABLE ogspy_game_rank_ally_military_built
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_loose`
--
CREATE TABLE ogspy_game_rank_ally_military_loose
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_destruct`
--
CREATE TABLE ogspy_game_rank_ally_military_destruct
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_honor`
--
CREATE TABLE ogspy_game_rank_ally_honor
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_points`
--
CREATE TABLE ogspy_game_rank_ally_points
(
    `datadate`          INT(11)     NOT NULL DEFAULT '0',
    `rank`              INT(11)     NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT(6)      NOT NULL DEFAULT '-1',
    `number_member`     INT(11)     NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL,
    `sender_id`         INT(11)     NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`),
    KEY `ally` (`ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_economique`
--
CREATE TABLE ogspy_game_rank_player_economics
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_technology`
--
CREATE TABLE ogspy_game_rank_player_technology
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military`
--
CREATE TABLE ogspy_game_rank_player_military
(
    `datadate`      INT(11)      NOT NULL DEFAULT '0',
    `rank`          INT(11)      NOT NULL DEFAULT '0',
    `player`        VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id`     INT(6)       NOT NULL DEFAULT '-1',
    `ally`          VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`       INT(6)       NOT NULL DEFAULT '-1',
    `points`        BIGINT       NOT NULL DEFAULT '0',
    `sender_id`     INT(11)      NOT NULL DEFAULT '0',
    `nb_spacecraft` BIGINT       NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_built`
--
CREATE TABLE ogspy_game_rank_player_military_built
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_loose`
--
CREATE TABLE ogspy_game_rank_player_military_loose
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_destruct`
--
CREATE TABLE ogspy_game_rank_player_military_destruct
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_honor`
--
CREATE TABLE ogspy_game_rank_player_honor
(
    `datadate`  INT(11)      NOT NULL DEFAULT '0',
    `rank`      INT(11)      NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT(6)       NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT(6)       NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT(11)      NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_points`
--
CREATE TABLE ogspy_game_rank_player_points
(
    `datadate`  INT          NOT NULL DEFAULT 0,
    `rank`      INT          NOT NULL DEFAULT 0,
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT -1,
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT -1,
    `points`    BIGINT       NOT NULL DEFAULT 0,
    `sender_id` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`),
    KEY `player` (`player`)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;
--
-- Structure de la table `ogspy_game_fleet`
--
CREATE TABLE `ogspy_game_player_fleet`
(
    `id`              INT NOT NULL AUTO_INCREMENT,
    `astro_object_id` INT NOT NULL,
    `PT`              INT NOT NULL DEFAULT 0,
    `GT`              INT NOT NULL DEFAULT 0,
    `CLE`             INT NOT NULL DEFAULT 0,
    `CLO`             INT NOT NULL DEFAULT 0,
    `CR`              INT NOT NULL DEFAULT 0,
    `VB`              INT NOT NULL DEFAULT 0,
    `VC`              INT NOT NULL DEFAULT 0,
    `REC`             INT NOT NULL DEFAULT 0,
    `SE`              INT NOT NULL DEFAULT 0,
    `BMD`             INT NOT NULL DEFAULT 0,
    `DST`             INT NOT NULL DEFAULT 0,
    `EDLM`            INT NOT NULL DEFAULT 0,
    `TRA`             INT NOT NULL DEFAULT 0,
    `FAU`             INT NOT NULL DEFAULT 0,
    `ECL`             INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `astro_object_id` (`astro_object_id`)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;

-- -----------------------------------------------------------------------------
-- Partie CREATE
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------

--
-- Contenu de la table `ogspy_config`
--
-- Insertion des configurations
INSERT INTO `ogspy_config` (`name`, `value`)
VALUES ('allied', ''),
       ('ally_protection', ''),
       ('debug_log', '0'),
       ('default_skin', 'skin/OGSpy_skin/'),
       ('disable_ip_check', '1'),
       ('keeprank_criterion', 'day'),
       ('last_maintenance_action', '0'),
       ('max_battlereport', '10'),
       ('max_favorites', '20'),
       ('max_favorites_spy', '10'),
       ('max_keeplog', '7'),
       ('max_keeprank', '30'),
       ('max_keepspyreport', '30'),
       ('max_spyreport', '10'),
       ('reason', ''),
       ('servername', 'Cartographie'),
       ('server_active', '1'),
       ('session_time', '30'),
       ('url_forum', 'https://forum.ogsteam.eu/'),
       ('log_phperror', '0'),
       ('block_ratio', '0'),
       ('ratio_limit', '0'),
       ('config_cache', '3600'),
       ('mod_cache', '604800'),
       ('ddr', '1'),
       ('astro_strict', '1'),
       ('donutSystem', '1'),
       ('donutGalaxy', '1'),
       ('speed_fleet_peaceful', '1'),
       ('speed_fleet_war', '1'),
       ('speed_fleet_holding', '1'),
       ('speed_research_divisor', '1'),
       ('enable_stat_view', '1'),
       ('enable_members_view', '0'),
       ('portee_missil', '1'),
       ('enable_register_view', '0'),
       ('register_forum', ''),
       ('register_alliance', ''),
       ('galaxy_by_line_stat', '9'),
       ('system_by_line_stat', '10'),
       ('galaxy_by_line_ally', '9'),
       ('system_by_line_ally', '10'),
       ('color_ally', 'Magenta_Yellow_Red'),
       ('nb_colonnes_ally', '3'),
       ('open_user', ''),
       ('open_admin', ''),
       ('mail_active', '0'),
       ('mail_smtp_use', '0'),
       ('mail_smtp_server', ''),
       ('mail_smtp_secure', '0'),
       ('mail_smtp_host', ''),
       ('mail_smtp_port', ''),
       ('mail_smtp_username', ''),
       ('mail_smtp_password', '');

--
--  Contenu de la table `ogspy_group`
--
INSERT INTO `ogspy_group`
VALUES (1, 'Standard', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');
