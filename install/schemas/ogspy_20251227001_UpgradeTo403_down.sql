--
-- OGSpy version 4.0.3 - Rollback
--

-- -----------------------------------------------------------------------------
-- Annulation du changement de taille des cles
-- -----------------------------------------------------------------------------
ALTER TABLE `ogspy_config` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_statistics` CHANGE `statistic_name` `statistic_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_mod_config` CHANGE `config` `config` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_mod_user_config` CHANGE `config` `config` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 

-- -----------------------------------------------------------------------------
-- Restauration des index sur les tables de classement alliance (ally_id -> text ally)
-- -----------------------------------------------------------------------------
ALTER TABLE `ogspy_game_rank_ally_economics`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_technology`       DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_military`         DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_military_built`   DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_military_loose`   DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_honor`            DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);
ALTER TABLE `ogspy_game_rank_ally_points`           DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`);

-- -----------------------------------------------------------------------------
-- Restauration des index sur les tables de classement joueur (player_id -> text player)
-- -----------------------------------------------------------------------------
ALTER TABLE `ogspy_game_rank_player_economics`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_technology`       DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_military`         DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_military_built`   DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_military_loose`   DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_honor`            DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
ALTER TABLE `ogspy_game_rank_player_points`           DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`);
