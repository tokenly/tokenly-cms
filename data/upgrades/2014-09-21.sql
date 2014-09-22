CREATE TABLE `payment_order` (
  `orderId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderData` longtext,
  `address` varchar(50) NOT NULL,
  `account` varchar(255) NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `asset` varchar(100) NOT NULL DEFAULT 'BTC',
  `received` decimal(20,8) DEFAULT '0.00000000',
  `complete` int(1) DEFAULT '0',
  `orderTime` datetime DEFAULT NULL,
  `orderType` varchar(100) DEFAULT NULL,
  `completeTime` datetime DEFAULT NULL,
  PRIMARY KEY (`orderId`),
  UNIQUE KEY `address_2` (`address`),
  UNIQUE KEY `account_2` (`account`),
  KEY `address` (`address`),
  KEY `account` (`account`),
  KEY `asset` (`asset`),
  KEY `orderType` (`orderType`)
);

alter table forum_boards add ownerId INT(11) unsigned DEFAULT 0;
alter table forum_boards add INDEX(ownerId);
alter table xcp_assetCache add ownerId INT(11) unsigned DEFAULT 0;
alter table xcp_assetCache add INDEX(ownerId);
alter table xcp_assetCache add image VARCHAR(255);

create table forum_boardMeta(metaId INT(11) unsigned AUTO_INCREMENT, PRIMARY KEY(metaId), boardId INT(11) unsigned NOT NULL, INDEX(boardId), FOREIGN KEY(boardId) REFERENCES forum_boards(boardId) ON DELETE CASCADE, metaKey VARCHAR(100) NOT NULL, INDEX(metaKey), value LONGTEXT, lastUpdate datetime);

