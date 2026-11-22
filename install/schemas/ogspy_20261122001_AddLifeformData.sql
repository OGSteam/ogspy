CREATE TABLE IF NOT EXISTS `ogspy_game_astro_object_lifeform_building`
(
    `astro_object_id` INT          NOT NULL,
    `technology_id`  INT UNSIGNED NOT NULL,
    `level`          SMALLINT UNSIGNED NOT NULL DEFAULT '0',
    `last_update`    INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`astro_object_id`, `technology_id`),
    KEY `technology_id` (`technology_id`)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `ogspy_game_astro_object_lifeform_research`
(
    `astro_object_id` INT          NOT NULL,
    `technology_id`  INT UNSIGNED NOT NULL,
    `level`          SMALLINT UNSIGNED NOT NULL DEFAULT '0',
    `last_update`    INT          NOT NULL DEFAULT '0',
    PRIMARY KEY (`astro_object_id`, `technology_id`),
    KEY `technology_id` (`technology_id`)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;
