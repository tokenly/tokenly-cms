CREATE TABLE `system_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `source` varchar(255) NOT NULL,
  `amount` decimal(20,10) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `source` (`source`),
  KEY `type` (`type`),
  KEY `ref` (`ref`),
  CONSTRAINT `system_credits_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
