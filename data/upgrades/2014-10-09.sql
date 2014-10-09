DROP TABLE IF EXISTS `board_subscriptions`;

CREATE TABLE `board_subscriptions` (
  `subId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `boardId` int(11) unsigned NOT NULL,

  PRIMARY KEY (`subId`),
  KEY `userId` (`userId`),
  KEY `boardId` (`boardId`),
  UNIQUE KEY `userId_boardId` (`userId`,`boardId`),

  CONSTRAINT `board_subscriptions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `board_subscriptions_ibfk_2` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

