CREATE TABLE IF NOT EXISTS `terminal`.`server_accounts` (
 `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `server_id` int(11) unsigned NOT NULL,
 `user_id` int(11) unsigned NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user notes';