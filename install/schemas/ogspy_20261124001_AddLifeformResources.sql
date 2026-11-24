CREATE TABLE IF NOT EXISTS `ogspy_game_lifeform_resource`
(
    `astro_object_id` INT           NOT NULL,
    `species_code`    VARCHAR(16)   NULL,
    `population`      BIGINT UNSIGNED NULL,
    `food`            BIGINT UNSIGNED NULL,
    `last_update`     INT           NOT NULL DEFAULT '0',
    PRIMARY KEY (`astro_object_id`)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;

CREATE TABLE IF NOT EXISTS `ogspy_game_lifeform_resource_history`
(
    `astro_object_id` INT           NOT NULL,
    `observation_day` DATE          NOT NULL,
    `species_code`    VARCHAR(16)   NULL,
    `population`      BIGINT UNSIGNED NULL,
    `food`            BIGINT UNSIGNED NULL,
    `observed_at`     INT           NOT NULL DEFAULT '0',
    PRIMARY KEY (`astro_object_id`, `observation_day`),
    KEY `observed_at` (`observed_at`)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin;
