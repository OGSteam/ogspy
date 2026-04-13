--
-- OGSpy version 4.0.3
--
--

-- -----------------------------------------------------------------------------
-- Mise a jour
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
-- Changement de la taille des cles
ALTER TABLE `ogspy_config` CHANGE `name` `name` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_statistics` CHANGE `statistic_name` `statistic_name` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_mod_config` CHANGE `config` `config` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 
ALTER TABLE `ogspy_mod_user_config` CHANGE `config` `config` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL; 

-- Note : la suppression des colonnes legacy (ally, player, points_per_member) est différée
-- en attendant la mise à jour coordonnée de l'extension xtense.
-- Ces colonnes seront supprimées dans une version ultérieure (4.1.0 ou 4.0.4).

-- Mise à jour des index sur les tables de classement alliance (text ally → ally_id)
ALTER TABLE `ogspy_game_rank_ally_economics`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_technology`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_military`          DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_military_built`    DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_military_loose`    DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_honor`             DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);
ALTER TABLE `ogspy_game_rank_ally_points`            DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally_id`);

-- Mise à jour des index sur les tables de classement joueur (text player → player_id)
ALTER TABLE `ogspy_game_rank_player_economics`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_technology`        DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_military`          DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_military_built`    DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_military_loose`    DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_honor`             DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);
ALTER TABLE `ogspy_game_rank_player_points`            DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player_id`);




