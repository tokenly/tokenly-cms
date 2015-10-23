CREATE TABLE `adspaces` (
  `adspaceId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `width` int(11) unsigned DEFAULT '0',
  `height` int(11) unsigned DEFAULT '0',
  `maxItems` int(11) unsigned DEFAULT '1',
  `active` tinyint(2) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `items` longtext,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`adspaceId`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


alter table tracking_urls add last_update datetime;
alter table tracking_urls add label VARCHAR(255);
alter table tracking_urls add image VARCHAR(255);

alter table tracking_clicks add adspaceId INT(11) unsigned DEFAULT 0;
