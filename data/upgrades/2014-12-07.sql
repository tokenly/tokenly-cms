CREATE TABLE `user_sessions` (
  `sessionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `auth` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `IP` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authTime` datetime DEFAULT NULL,
  `lastActive` datetime DEFAULT NULL,
  PRIMARY KEY (`sessionId`),
  KEY `userId` (`userId`),
  KEY `auth` (`auth`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
alter table users drop column auth;
