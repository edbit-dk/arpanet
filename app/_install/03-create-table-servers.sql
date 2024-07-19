CREATE TABLE IF NOT EXISTS `terminal`.`servers` (
 `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `admin_id` int(11) unsigned NOT NULL,
 `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'server name, unique',
 `location` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'server location',
 `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'server activation status',
 PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user notes';