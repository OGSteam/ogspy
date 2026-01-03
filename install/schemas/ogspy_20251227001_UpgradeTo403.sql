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

-- Suppression champs points_per_member/ally classement alliance
-- points_per_member+
ALTER TABLE `ogspy_game_rank_ally_honor` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military_loose` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military_built` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_economics` DROP IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_technology` DROP  IF EXISTS `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_points` DROP  IF EXISTS `points_per_member`;
-- ally
ALTER TABLE `ogspy_game_rank_ally_honor` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_military_loose` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_military_built` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_military` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_economics` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_technology` DROP  IF EXISTS `ally`;
ALTER TABLE `ogspy_game_rank_ally_points` DROP  IF EXISTS `ally`;

-- Suppression champs player/ally classement player
--ally
ALTER TABLE `ogspy_game_rank_player_honor` DROP  IF EXISTS `ally`; 
ALTER TABLE `ogspy_game_rank_player_military_loose` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_military_built` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_military` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_economics` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_technology` DROP IF EXISTS  `ally`;
ALTER TABLE `ogspy_game_rank_player_points` DROP IF EXISTS  `ally`;
--player
ALTER TABLE `ogspy_game_rank_player_honor` DROP IF EXISTS  `player`; 
ALTER TABLE `ogspy_game_rank_player_military_loose` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_military_built` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_military` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_economics` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_technology` DROP IF EXISTS  `player`;
ALTER TABLE `ogspy_game_rank_player_points` DROP IF EXISTS  `player`;




