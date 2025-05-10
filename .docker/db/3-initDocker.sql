
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

INSERT INTO ogspy_player_building (`user_id`, `planet_id`, `planet_name`, `coordinates`, `fields`, `boosters`, `temperature_min`, `temperature_max`, `Sat`, `Sat_percentage`, `FOR`, `FOR_percentage`, `M`, `M_percentage`, `C`, `C_Percentage`, `D`, `D_percentage`, `CES`, `CES_percentage`, `CEF`, `CEF_percentage`, `UdR`, `UdN`, `CSp`, `HM`, `HC`, `HD`, `Lab`, `Ter`, `DdR`, `Silo`, `Dock`, `BaLu`, `Pha`, `PoSa`)
VALUES (1, 101, 'area'    , '2:330:12', 282, 'm:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0', -31,  9, 2010, 100, 848, 150, 38, 100, 34, 100, 34, 100, 12, 100, 23, 60,  7, 7, 12, 13,  9,  8, 18, 8, 1, 8, 7, 0, 0, 0);

INSERT INTO `ogspy_user_technology` (`user_id`, `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`) VALUES
    (1, 20, 20, 20, 20, 20, 20, 17, 20, 17, 16, 20, 20, 19, 12, 2, 23);
