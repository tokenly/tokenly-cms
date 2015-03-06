CREATE TABLE `tracking_urls` (
  `urlId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `siteId` int(11) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `clicks` int(11) DEFAULT '0',
  `unique_clicks` int(11) DEFAULT '0',
  `impressions` int(11) DEFAULT '0',
  `active` int(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `last_click` datetime DEFAULT NULL,
  PRIMARY KEY (`urlId`),
  KEY `siteId` (`siteId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  CONSTRAINT `tracking_urls_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE,
  CONSTRAINT `tracking_urls_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tracking_clicks` (
  `clickId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `urlId` int(11) unsigned NOT NULL,
  `userId` int(11) DEFAULT '0',
  `IP` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `click_time` datetime DEFAULT NULL,
  PRIMARY KEY (`clickId`),
  KEY `urlId` (`urlId`),
  KEY `userId` (`userId`),
  KEY `IP` (`IP`),
  KEY `request_url` (`request_url`),
  CONSTRAINT `tracking_clicks_ibfk_1` FOREIGN KEY (`urlId`) REFERENCES `tracking_urls` (`urlId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
