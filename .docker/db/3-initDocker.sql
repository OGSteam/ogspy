
USE `ogspy`;

-- -----------------------------------------------------------------------------
-- Partie spécifique pour le docker
-- -----------------------------------------------------------------------------
-- -----------------------------------------------------------------------------
INSERT INTO `ogspy_config` VALUES ('num_of_galaxies','9');
INSERT INTO `ogspy_config` VALUES ('num_of_systems','499');
INSERT INTO `ogspy_config` VALUES ('speed_uni','1');
INSERT INTO `ogspy_config` VALUES ('speed_fleet_peaceful','1');
INSERT INTO `ogspy_config` VALUES ('speed_fleet_war','1');
INSERT INTO `ogspy_config` VALUES ('speed_fleet_holding','1');
INSERT INTO `ogspy_config` VALUES ('speed_research_divisor','1');
INSERT INTO `ogspy_config` VALUES ('version','3.3.9-dev');
INSERT INTO `ogspy_config` VALUES ('log_phperror', '1');
INSERT INTO `ogspy_user` (`user_id`, `user_name`, `user_password_s`, `user_regdate`, `user_active`, `user_admin`, `user_class`, `user_pwd_change`) VALUES (1, 'ogsteam', '$2y$10$z.6/280zsg65IoOJ/wmOC.cHIWFnKFE8TaY7JSr0DH3fnQsxg7rRW', '1567070548', '1', '1', 'COL', 0);
INSERT INTO `ogspy_user_group` (`group_id`, `user_id`) VALUES (1, 1);
