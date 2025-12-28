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
-- points_per_member
ALTER TABLE `ogspy_game_rank_ally_military_loose` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military_built` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_military` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_economics` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_technology` DROP `points_per_member`;
ALTER TABLE `ogspy_game_rank_ally_points` DROP `points_per_member`;
-- ally
ALTER TABLE `ogspy_game_rank_ally_military_loose` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_military_destruct` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_military_built` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_military` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_economics` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_technology` DROP `ally`;
ALTER TABLE `ogspy_game_rank_ally_points` DROP `ally`;

-- Suppression champs player/ally classement player
--ally
ALTER TABLE `ogspy_game_rank_player_honor` DROP `ally`; 
ALTER TABLE `ogspy_game_rank_player_military_loose` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_military_built` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_military` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_economics` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_technology` DROP `ally`;
ALTER TABLE `ogspy_game_rank_player_points` DROP `ally`;
--player
ALTER TABLE `ogspy_game_rank_player_honor` DROP `player`; 
ALTER TABLE `ogspy_game_rank_player_military_loose` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_military_destruct` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_military_built` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_military` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_economics` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_technology` DROP `player`;
ALTER TABLE `ogspy_game_rank_player_points` DROP `player`;




