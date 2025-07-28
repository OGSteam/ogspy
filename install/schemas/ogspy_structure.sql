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
    `id`                 INT          NOT NULL AUTO_INCREMENT,
    `name`               VARCHAR(20)  NOT NULL DEFAULT '',
    `password_s`         VARCHAR(255) NOT NULL DEFAULT '',
    `pwd_change`         TINYINT(1)   NOT NULL DEFAULT '1',
    `email`              VARCHAR(50)  NOT NULL DEFAULT '',
    `email_valid`        TINYINT(1)   NOT NULL DEFAULT '0',
    `admin`              TINYINT(1)   NOT NULL DEFAULT '0',
    `coadmin`            TINYINT(1)   NOT NULL DEFAULT '0',
    `active`             TINYINT(1)   NOT NULL DEFAULT '0',
    `regdate`            INT          NOT NULL DEFAULT '0',
    `lastvisit`          INT          NOT NULL DEFAULT '0',
    `default_galaxy`     SMALLINT     NOT NULL DEFAULT '1',
    `default_system`     SMALLINT(3)  NOT NULL DEFAULT '1',
    `planet_imports`     INT          NOT NULL DEFAULT '0',
    `spy_imports`        INT          NOT NULL DEFAULT '0',
    `rank_imports`       INT          NOT NULL DEFAULT '0',
    `search`             INT          NOT NULL DEFAULT '0',
    `xtense_type`        ENUM ('FF', 'GM-FF', 'GM-GC', 'ANDROID'),
    `xtense_version`     VARCHAR(10),
    `management_user`    TINYINT(1)   NOT NULL DEFAULT '0',
    `management_ranking` TINYINT(1)   NOT NULL DEFAULT '0',
    `disable_ip_check`   TINYINT(1)   NOT NULL DEFAULT '0',
    `player_id`          INT          NOT NULL DEFAULT '0',
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
    `user_id`           INT        NOT NULL DEFAULT '0',
    `session_start`     INT        NOT NULL DEFAULT '0',
    `session_expire`    INT        NOT NULL DEFAULT '0',
    `session_ip`        CHAR(32)   NOT NULL DEFAULT '',
    `session_type`      TINYINT(1) NOT NULL DEFAULT '0',
    `session_lastvisit` INT        NOT NULL DEFAULT '0',
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
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255) NOT NULL COMMENT 'Nom du mod',
    `menu`       VARCHAR(255) NOT NULL COMMENT 'Titre du lien dans le menu',
    `action`     VARCHAR(255) NOT NULL COMMENT 'Action transmise en get et traitée dans index.php',
    `root`       VARCHAR(255) NOT NULL COMMENT 'Répertoire où se situe le mod (relatif au répertoire mods)',
    `link`       VARCHAR(255) NOT NULL COMMENT 'fichier principale du mod',
    `version`    VARCHAR(100) NOT NULL COMMENT 'Version du mod',
    `position`   INT          NOT NULL DEFAULT '-1',
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
    `user_id` INT          NOT NULL,
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
    `user_id` INT         NOT NULL DEFAULT '0',
    `galaxy`  SMALLINT    NOT NULL DEFAULT '1',
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
    `id`              INT                                NOT NULL,
    `name`            VARCHAR(65)                        NOT NULL COMMENT 'Nom du joueur',
    `status`          VARCHAR(6)                         NOT NULL DEFAULT '',
    `class`           ENUM ('none', 'COL', 'GEN', 'EXP') NOT NULL DEFAULT 'none',
    `ally_id`         INT                                NOT NULL COMMENT 'Action transmise en get et traitée dans index.php',
    `off_commandant`  TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_amiral`      TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_ingenieur`   TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_geologue`    TINYINT(1)                         NOT NULL DEFAULT '0',
    `off_technocrate` TINYINT(1)                         NOT NULL DEFAULT '0',
    `datadate`        INT                                NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)

)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_ally`
--
CREATE TABLE `ogspy_game_ally`
(
    `id`       INT                                NOT NULL,
    `name`     VARCHAR(65) COMMENT 'Nom de l alliance',
    `tag`      VARCHAR(65)                        NOT NULL DEFAULT '',
    `class`    ENUM ('none', 'MAR', 'WAR', 'RES') NOT NULL DEFAULT 'none',
    `datadate` INT                                NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_game_astro_object`
--
CREATE TABLE `ogspy_game_astro_object`
(
    `id`                  INT         NOT NULL,
    `type`                VARCHAR(10) NOT NULL DEFAULT 'planet' COMMENT 'Type astre : planet, moon, debris,...',
    `galaxy`              SMALLINT    NOT NULL DEFAULT '1',
    `system`              SMALLINT(3) NOT NULL DEFAULT '1',
    `row`                 SMALLINT    NOT NULL DEFAULT '1',
    `name`                VARCHAR(50) NOT NULL DEFAULT '',
    `player_id`           INT         NOT NULL DEFAULT '0',
    `fields`              SMALLINT(3) NOT NULL DEFAULT '0',
    `boosters`            VARCHAR(64) NOT NULL DEFAULT 'm:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0',
    `temperature_min`     SMALLINT    NOT NULL DEFAULT '0',
    `temperature_max`     SMALLINT    NOT NULL DEFAULT '0',
    `Sat`                 SMALLINT(5) NOT NULL DEFAULT '0',
    `Sat_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `FOR`                 SMALLINT(5) NOT NULL DEFAULT '0',
    `FOR_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `M`                   SMALLINT    NOT NULL DEFAULT '0',
    `M_percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `C`                   SMALLINT    NOT NULL DEFAULT '0',
    `C_Percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `D`                   SMALLINT    NOT NULL DEFAULT '0',
    `D_percentage`        SMALLINT(3) NOT NULL DEFAULT '100',
    `CES`                 SMALLINT    NOT NULL DEFAULT '0',
    `CES_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `CEF`                 SMALLINT    NOT NULL DEFAULT '0',
    `CEF_percentage`      SMALLINT(3) NOT NULL DEFAULT '100',
    `UdR`                 SMALLINT    NOT NULL DEFAULT '0',
    `UdN`                 SMALLINT    NOT NULL DEFAULT '0',
    `CSp`                 SMALLINT    NOT NULL DEFAULT '0',
    `HM`                  SMALLINT    NOT NULL DEFAULT '0',
    `HC`                  SMALLINT    NOT NULL DEFAULT '0',
    `HD`                  SMALLINT    NOT NULL DEFAULT '0',
    `Lab`                 SMALLINT    NOT NULL DEFAULT '0',
    `Ter`                 SMALLINT    NOT NULL DEFAULT '0',
    `DdR`                 SMALLINT    NOT NULL DEFAULT '0',
    `Silo`                SMALLINT    NOT NULL DEFAULT '0',
    `Dock`                SMALLINT    NOT NULL DEFAULT '0',
    `BaLu`                SMALLINT    NOT NULL DEFAULT '0',
    `Pha`                 SMALLINT    NOT NULL DEFAULT '0',
    `PoSa`                SMALLINT    NOT NULL DEFAULT '0',
    `last_update`         INT         NOT NULL DEFAULT '0',
    `last_update_moon`    INT         NOT NULL DEFAULT '0',
    `last_update_user_id` INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `univers` (`galaxy`, `system`, `row`)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_bin;
--
-- Structure de la table `ogspy_game_player_defense`
--
CREATE TABLE `ogspy_game_player_defense`
(
    `id`              INT         NOT NULL AUTO_INCREMENT,
    `astro_object_id` INT         NOT NULL,
    `LM`              INT         NOT NULL DEFAULT '0',
    `LLE`             INT         NOT NULL DEFAULT '0',
    `LLO`             INT         NOT NULL DEFAULT '0',
    `CG`              INT         NOT NULL DEFAULT '0',
    `AI`              INT         NOT NULL DEFAULT '0',
    `LP`              INT         NOT NULL DEFAULT '0',
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
    `user_id` INT NOT NULL DEFAULT '0',
    `spy_id`  INT NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`, `spy_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_user_technology` // Linked to player
--
CREATE TABLE ogspy_game_player_technology
(
    `id`            INT      NOT NULL AUTO_INCREMENT,
    `player_id`     INT      NOT NULL DEFAULT '0',
    `Esp`           SMALLINT NOT NULL DEFAULT '0',
    `Ordi`          SMALLINT NOT NULL DEFAULT '0',
    `Armes`         SMALLINT NOT NULL DEFAULT '0',
    `Bouclier`      SMALLINT NOT NULL DEFAULT '0',
    `Protection`    SMALLINT NOT NULL DEFAULT '0',
    `NRJ`           SMALLINT NOT NULL DEFAULT '0',
    `Hyp`           SMALLINT NOT NULL DEFAULT '0',
    `RC`            SMALLINT NOT NULL DEFAULT '0',
    `RI`            SMALLINT NOT NULL DEFAULT '0',
    `PH`            SMALLINT NOT NULL DEFAULT '0',
    `Laser`         SMALLINT NOT NULL DEFAULT '0',
    `Ions`          SMALLINT NOT NULL DEFAULT '0',
    `Plasma`        SMALLINT NOT NULL DEFAULT '0',
    `RRI`           SMALLINT NOT NULL DEFAULT '0',
    `Graviton`      SMALLINT NOT NULL DEFAULT '0',
    `Astrophysique` SMALLINT NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `player_id` (`player_id`),
    UNIQUE KEY `unique_player_id` (`player_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;



--
-- Structure de la table `ogspy_parsedspy`
--
CREATE TABLE `ogspy_game_parsedspy`
(
    `id`              INT         NOT NULL AUTO_INCREMENT,
    `astro_object_id` INT         NOT NULL,
    `planet_name`     VARCHAR(20) NOT NULL DEFAULT '',
    `metal`           BIGINT      NOT NULL DEFAULT '-1',
    `cristal`         BIGINT      NOT NULL DEFAULT '-1',
    `deuterium`       BIGINT      NOT NULL DEFAULT '-1',
    `energie`         INT(7)      NOT NULL DEFAULT '-1',
    `activite`        INT         NOT NULL DEFAULT '-1',
    `M`               SMALLINT    NOT NULL DEFAULT '-1',
    `C`               SMALLINT    NOT NULL DEFAULT '-1',
    `D`               SMALLINT    NOT NULL DEFAULT '-1',
    `CES`             SMALLINT    NOT NULL DEFAULT '-1',
    `CEF`             SMALLINT    NOT NULL DEFAULT '-1',
    `UdR`             SMALLINT    NOT NULL DEFAULT '-1',
    `UdN`             SMALLINT    NOT NULL DEFAULT '-1',
    `CSp`             SMALLINT    NOT NULL DEFAULT '-1',
    `HM`              SMALLINT    NOT NULL DEFAULT '-1',
    `HC`              SMALLINT    NOT NULL DEFAULT '-1',
    `HD`              SMALLINT    NOT NULL DEFAULT '-1',
    `Lab`             SMALLINT    NOT NULL DEFAULT '-1',
    `Ter`             SMALLINT    NOT NULL DEFAULT '-1',
    `DdR`             SMALLINT    NOT NULL DEFAULT '-1',
    `Silo`            SMALLINT    NOT NULL DEFAULT '-1',
    `Dock`            SMALLINT    NOT NULL DEFAULT '-1',
    `BaLu`            SMALLINT    NOT NULL DEFAULT '-1',
    `Pha`             SMALLINT    NOT NULL DEFAULT '-1',
    `PoSa`            SMALLINT    NOT NULL DEFAULT '-1',
    `LM`              INT         NOT NULL DEFAULT '-1',
    `LLE`             INT         NOT NULL DEFAULT '-1',
    `LLO`             INT         NOT NULL DEFAULT '-1',
    `CG`              INT         NOT NULL DEFAULT '-1',
    `AI`              INT         NOT NULL DEFAULT '-1',
    `LP`              INT         NOT NULL DEFAULT '-1',
    `PB`              SMALLINT(1) NOT NULL DEFAULT '-1',
    `GB`              SMALLINT(1) NOT NULL DEFAULT '-1',
    `MIC`             SMALLINT(3) NOT NULL DEFAULT '-1',
    `MIP`             SMALLINT(3) NOT NULL DEFAULT '-1',
    `PT`              INT         NOT NULL DEFAULT '-1',
    `GT`              INT         NOT NULL DEFAULT '-1',
    `CLE`             INT         NOT NULL DEFAULT '-1',
    `CLO`             INT         NOT NULL DEFAULT '-1',
    `CR`              INT         NOT NULL DEFAULT '-1',
    `VB`              INT         NOT NULL DEFAULT '-1',
    `VC`              INT         NOT NULL DEFAULT '-1',
    `REC`             INT         NOT NULL DEFAULT '-1',
    `SE`              INT         NOT NULL DEFAULT '-1',
    `BMD`             INT         NOT NULL DEFAULT '-1',
    `DST`             INT         NOT NULL DEFAULT '-1',
    `EDLM`            INT         NOT NULL DEFAULT '-1',
    `SAT`             INT                  DEFAULT '-1',
    `TRA`             INT         NOT NULL DEFAULT '-1',
    `FOR`             INT         NOT NULL DEFAULT '-1',
    `FAU`             INT         NOT NULL DEFAULT '-1',
    `ECL`             INT         NOT NULL DEFAULT '-1',
    `Esp`             SMALLINT    NOT NULL DEFAULT '-1',
    `Ordi`            SMALLINT    NOT NULL DEFAULT '-1',
    `Armes`           SMALLINT    NOT NULL DEFAULT '-1',
    `Bouclier`        SMALLINT    NOT NULL DEFAULT '-1',
    `Protection`      SMALLINT    NOT NULL DEFAULT '-1',
    `NRJ`             SMALLINT    NOT NULL DEFAULT '-1',
    `Hyp`             SMALLINT    NOT NULL DEFAULT '-1',
    `RC`              SMALLINT    NOT NULL DEFAULT '-1',
    `RI`              SMALLINT    NOT NULL DEFAULT '-1',
    `PH`              SMALLINT    NOT NULL DEFAULT '-1',
    `Laser`           SMALLINT    NOT NULL DEFAULT '-1',
    `Ions`            SMALLINT    NOT NULL DEFAULT '-1',
    `Plasma`          SMALLINT    NOT NULL DEFAULT '-1',
    `RRI`             SMALLINT    NOT NULL DEFAULT '-1',
    `Graviton`        SMALLINT    NOT NULL DEFAULT '-1',
    `Astrophysique`   SMALLINT    NOT NULL DEFAULT '-1',
    `dateRE`          INT         NOT NULL DEFAULT '0',
    `proba`           SMALLINT    NOT NULL DEFAULT '0',
    `active`          TINYINT(1)  NOT NULL DEFAULT '1',
    `sender_id`       INT         NOT NULL,
    PRIMARY KEY (`id`),
    KEY `coordinates` (`astro_object_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_parsedRC`
--
CREATE TABLE `ogspy_game_parsedRC`
(
    `id_rc`           INT    NOT NULL AUTO_INCREMENT,
    `dateRC`          INT    NOT NULL DEFAULT '0',
    `astro_object_id` INT    NOT NULL,
    `nb_rounds`       INT    NOT NULL DEFAULT '0',
    `victoire`        CHAR   NOT NULL DEFAULT 'A',
    `pertes_A`        BIGINT NOT NULL DEFAULT '0',
    `pertes_D`        BIGINT NOT NULL DEFAULT '0',
    `gain_M`          BIGINT NOT NULL DEFAULT '-1',
    `gain_C`          BIGINT NOT NULL DEFAULT '-1',
    `gain_D`          BIGINT NOT NULL DEFAULT '-1',
    `debris_M`        BIGINT NOT NULL DEFAULT '-1',
    `debris_C`        BIGINT NOT NULL DEFAULT '-1',
    `lune`            INT    NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_rc`),
    KEY `astro_object_id` (`astro_object_id`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_parsedRCRound`
--
CREATE TABLE `ogspy_game_parsedRCRound`
(
    `id_rcround`        INT    NOT NULL AUTO_INCREMENT,
    `id_rc`             INT    NOT NULL,
    `numround`          INT    NOT NULL,
    `attaque_tir`       BIGINT NOT NULL DEFAULT '-1',
    `attaque_puissance` BIGINT NOT NULL DEFAULT '-1',
    `defense_bouclier`  BIGINT NOT NULL DEFAULT '-1',
    `attaque_bouclier`  BIGINT NOT NULL DEFAULT '-1',
    `defense_tir`       BIGINT NOT NULL DEFAULT '-1',
    `defense_puissance` BIGINT NOT NULL DEFAULT '-1',
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
    `id_roundattack`  INT         NOT NULL AUTO_INCREMENT,
    `id_rcround`      INT         NOT NULL,
    `player`          VARCHAR(30) NOT NULL DEFAULT '',
    `astro_object_id` INT         NOT NULL,
    `Armes`           SMALLINT    NOT NULL DEFAULT '-1',
    `Bouclier`        SMALLINT    NOT NULL DEFAULT '-1',
    `Protection`      SMALLINT    NOT NULL DEFAULT '-1',
    `PT`              INT         NOT NULL DEFAULT '-1',
    `GT`              INT         NOT NULL DEFAULT '-1',
    `CLE`             INT         NOT NULL DEFAULT '-1',
    `CLO`             INT         NOT NULL DEFAULT '-1',
    `CR`              INT         NOT NULL DEFAULT '-1',
    `VB`              INT         NOT NULL DEFAULT '-1',
    `VC`              INT         NOT NULL DEFAULT '-1',
    `REC`             INT         NOT NULL DEFAULT '-1',
    `SE`              INT         NOT NULL DEFAULT '-1',
    `BMD`             INT         NOT NULL DEFAULT '-1',
    `DST`             INT         NOT NULL DEFAULT '-1',
    `EDLM`            INT         NOT NULL DEFAULT '-1',
    `TRA`             INT         NOT NULL DEFAULT '-1',
    `ECL`             INT         NOT NULL DEFAULT '-1',
    `FAU`             INT         NOT NULL DEFAULT '-1',
    PRIMARY KEY (`id_roundattack`),
    KEY `id_rcround` (`id_rcround`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_round_defense`
--
CREATE TABLE `ogspy_game_rc_round_defense`
(
    `id_rounddefense` INT         NOT NULL AUTO_INCREMENT,
    `id_rcround`      INT         NOT NULL,
    `player_id`       INT         NOT NULL,
    `astro_object_id` INT         NOT NULL,
    `Armes`           SMALLINT    NOT NULL DEFAULT '-1',
    `Bouclier`        SMALLINT    NOT NULL DEFAULT '-1',
    `Protection`      SMALLINT    NOT NULL DEFAULT '-1',
    `PT`              INT         NOT NULL DEFAULT '-1',
    `GT`              INT         NOT NULL DEFAULT '-1',
    `CLE`             INT         NOT NULL DEFAULT '-1',
    `CLO`             INT         NOT NULL DEFAULT '-1',
    `CR`              INT         NOT NULL DEFAULT '-1',
    `VB`              INT         NOT NULL DEFAULT '-1',
    `VC`              INT         NOT NULL DEFAULT '-1',
    `REC`             INT         NOT NULL DEFAULT '-1',
    `SE`              INT         NOT NULL DEFAULT '-1',
    `BMD`             INT         NOT NULL DEFAULT '-1',
    `DST`             INT         NOT NULL DEFAULT '-1',
    `EDLM`            INT         NOT NULL DEFAULT '-1',
    `SAT`             INT         NOT NULL DEFAULT '-1',
    `FOR`             INT         NOT NULL DEFAULT '-1',
    `TRA`             INT         NOT NULL DEFAULT '-1',
    `ECL`             INT         NOT NULL DEFAULT '-1',
    `FAU`             INT         NOT NULL DEFAULT '-1',
    `LM`              INT         NOT NULL DEFAULT '-1',
    `LLE`             INT         NOT NULL DEFAULT '-1',
    `LLO`             INT         NOT NULL DEFAULT '-1',
    `CG`              INT         NOT NULL DEFAULT '-1',
    `AI`              INT         NOT NULL DEFAULT '-1',
    `LP`              INT         NOT NULL DEFAULT '-1',
    `PB`              SMALLINT(1) NOT NULL DEFAULT '-1',
    `GB`              SMALLINT(1) NOT NULL DEFAULT '-1',
    PRIMARY KEY (`id_rounddefense`),
    KEY `id_rcround` (`id_rcround`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;


--
-- Structure de la table `ogspy_rank_ally_economics`
--
CREATE TABLE ogspy_game_rank_ally_economics
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_technology`
--
CREATE TABLE ogspy_game_rank_ally_technology
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military`
--
CREATE TABLE ogspy_game_rank_ally_military
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_built`
--
CREATE TABLE ogspy_game_rank_ally_military_built
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_loose`
--
CREATE TABLE ogspy_game_rank_ally_military_loose
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_military_destruct`
--
CREATE TABLE ogspy_game_rank_ally_military_destruct
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_honor`
--
CREATE TABLE ogspy_game_rank_ally_honor
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL DEFAULT '0',
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_ally_points`
--
CREATE TABLE ogspy_game_rank_ally_points
(
    `datadate`          INT         NOT NULL DEFAULT '0',
    `rank`              INT         NOT NULL DEFAULT '0',
    `ally`              VARCHAR(30) NOT NULL,
    `ally_id`           INT         NOT NULL DEFAULT '-1',
    `number_member`     INT         NOT NULL,
    `points`            BIGINT      NOT NULL DEFAULT '0',
    `points_per_member` BIGINT      NOT NULL,
    `sender_id`         INT         NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `ally`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_economique`
--
CREATE TABLE ogspy_game_rank_player_economics
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_technology`
--
CREATE TABLE ogspy_game_rank_player_technology
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military`
--
CREATE TABLE ogspy_game_rank_player_military
(
    `datadate`      INT          NOT NULL DEFAULT '0',
    `rank`          INT          NOT NULL DEFAULT '0',
    `player`        VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id`     INT          NOT NULL DEFAULT '-1',
    `ally`          VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`       INT          NOT NULL DEFAULT '-1',
    `points`        BIGINT       NOT NULL DEFAULT '0',
    `sender_id`     INT          NOT NULL DEFAULT '0',
    `nb_spacecraft` BIGINT       NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_built`
--
CREATE TABLE ogspy_game_rank_player_military_built
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_loose`
--
CREATE TABLE ogspy_game_rank_player_military_loose
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_military_destruct`
--
CREATE TABLE ogspy_game_rank_player_military_destruct
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
)
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_bin;

--
-- Structure de la table `ogspy_rank_player_honor`
--
CREATE TABLE ogspy_game_rank_player_honor
(
    `datadate`  INT          NOT NULL DEFAULT '0',
    `rank`      INT          NOT NULL DEFAULT '0',
    `player`    VARCHAR(30)  NOT NULL DEFAULT '',
    `player_id` INT          NOT NULL DEFAULT '-1',
    `ally`      VARCHAR(100) NOT NULL DEFAULT '',
    `ally_id`   INT          NOT NULL DEFAULT '-1',
    `points`    BIGINT       NOT NULL DEFAULT '0',
    `sender_id` INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`rank`, `datadate`),
    KEY `datadate` (`datadate`, `player`)
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
    KEY `datadate` (`datadate`, `player`)
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
