CREATE TABLE `content_versions` (
  `versionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `itemId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned DEFAULT '0',
  `content` longtext COLLATE utf8_unicode_ci,
  `formatType` varchar(25) COLLATE utf8_unicode_ci DEFAULT 'markdown',
  `num` INT(11) DEFAULT 1,
  `changes` INT(11) DEFAULT 0,
  `versionDate` datetime DEFAULT NULL,
  PRIMARY KEY (`versionId`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
alter table blog_posts add version INT(11) DEFAULT 0;
