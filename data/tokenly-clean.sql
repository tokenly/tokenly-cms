-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ltb
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app_meta`
--

DROP TABLE IF EXISTS `app_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_meta` (
  `appMetaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) unsigned NOT NULL,
  `metaKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `metaValue` longtext COLLATE utf8_unicode_ci,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'textbox',
  `options` longtext COLLATE utf8_unicode_ci,
  `isSetting` int(2) DEFAULT '0',
  PRIMARY KEY (`appMetaId`),
  KEY `appId` (`appId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `app_meta_ibfk_3` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_meta`
--

LOCK TABLES `app_meta` WRITE;
/*!40000 ALTER TABLE `app_meta` DISABLE KEYS */;
INSERT INTO `app_meta` VALUES (1,7,'postsPerPage','30','Posts Per Page','textbox',NULL,1),(4,7,'maxExcerpt','250','Max Post Excerpt Characters','textbox',NULL,1),(5,7,'enableComments','1','Enable Comments','bool',NULL,1),(21,2,'avatarWidth','150','Avatar Width (px)','textbox',NULL,1),(22,2,'avatarHeight','150','Avatar Height (px)','textbox',NULL,1),(23,2,'disableRegister','0','Disable New User Registration','bool',NULL,1),(24,25,'topicsPerPage','60','Topics Per Page','textbox',NULL,1),(25,25,'postsPerPage','10','Topics Replies (posts) Per Page','textbox',NULL,1),(26,25,'forum-title','Tokenly Forums','Forum Title','textbox',NULL,1),(27,25,'forum-description','','Forum Description','textarea',NULL,1),(28,7,'featuredWidth','600','Featured Image Width (px)','textbox',NULL,1),(29,7,'featuredHeight','372','Featured Image Height (px)','textbox',NULL,1),(31,26,'blog-feed-title','Tokenly RSS Feed','Blog Feed Title','textbox',NULL,1),(32,26,'blog-feed-description','','Blog Feed Description','textarea',NULL,1),(33,27,'store-title','Lets Shop Bitcoin!','Store Title','textbox',NULL,1),(34,27,'productsPerPage','20','Products Per Page','textbox',NULL,1),(35,7,'coverWidth','400','Cover Image Width','textbox',NULL,1),(36,7,'coverHeight','400','Cover Image Height','textbox',NULL,1),(37,30,'distribute-fee','0.00001','Share Distributor - per address miner fee','textbox','',1),(38,30,'distribute-dust','0.000025','Share Distributor - dust output BTC value','textbox','',1),(39,30,'pop-comment-weight','8','PoP points per blog comment made','textbox','',1),(40,30,'pop-forum-post-weight','10','PoP points per forum post made','textbox','',1),(41,30,'pop-forum-topic-weight','10','PoP points per forum thread made','textbox','',1),(42,30,'pop-register-weight','0','PoP bonus points for new registrants','textbox','',1),(43,30,'pop-view-weight','3','PoP points per first page view','textbox','',1),(44,30,'distributor-decimals','2','Share Distributor - Round Values to x Decimals','textbox','',1),(45,30,'distribute-batch-size','25','Share Distributor - # Transactions per Batch','textbox','',1),(46,30,'pop-listen-weight','20','PoP - Proof of Listening Weight','textbox','',1),(47,30,'pol-word-expire','96','Proof of Listening - Magic Words expiration (in hours)','textbox','',1),(48,30,'pop-like-weight','1','PoP points per Like','textbox','',1),(49,30,'pop-referral-weight','10','PoP Points per Active Referra','textbox','',1),(50,30,'referral-min-active-pop','10','Min PoP per active referral','textbox','',1),(51,30,'pop-publish-weight','25','PoP points per published blog post','textbox','',1),(52,30,'pop-editor-cut','20','Editor PoP point distribution cut per article (%)','textbox','',1),(53,25,'mod-group','15','Forum Moderator Group ID','textbox','',1),(54,30,'tca-forum-btc-fee','0.1','TCA Forum Builder BTC Cost','textbox','',1),(55,30,'tca-forum-token-fee','50000','TCA Forum Builder Token Cost','textbox','',1),(56,30,'tca-forum-token','LTBCOIN','TCA Forum Builder Token Name','textbox','',1),(57,30,'token-logo-width','150','Token Logo Image Width','textbox','',1),(58,30,'token-logo-height','150','Token Logo Image Height','textbox','',1),(59,30,'tca-forum-category','14','TCA Private Forum Default Category ID','textbox','',1),(60,7,'category-image-width','400','Category Image Width','textbox','',1),(61,7,'category-image-height','400','Category Image Height','textbox','',1),(62,7,'submission-fee','1000','Article Submission Fee','textbox',NULL,1),(63,7,'submission-fee-token','LTBCOIN','Submission Fee Token','textbox',NULL,1),(64,25,'weighted-votes-token','LTBCOIN','Weighted Votes Token','textbox','',1),(65,25,'min-upvote-points','0.05','Minimum Upvote Points','textbox','',1),(66,25,'max-upvote-points','5','Maximum Upvote Points','textbox','',1),(67,25,'weighted-vote-token-cap','500000','Weighted Vote Token Cap','textbox','',1),(68,25,'min-required-upvote-points','5','Minimum Upvote Points Required to Upvote','textbox','',1);
/*!40000 ALTER TABLE `app_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_perms`
--

DROP TABLE IF EXISTS `app_perms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_perms` (
  `permId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) unsigned NOT NULL,
  `permKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`permId`),
  KEY `appId` (`appId`),
  KEY `permKey` (`permKey`),
  CONSTRAINT `app_perms_ibfk_1` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_perms`
--

LOCK TABLES `app_perms` WRITE;
/*!40000 ALTER TABLE `app_perms` DISABLE KEYS */;
INSERT INTO `app_perms` VALUES (1,25,'canPostTopic'),(2,25,'canPostReply'),(3,25,'canEditSelf'),(4,25,'canBurySelf'),(5,25,'canDeleteSelfTopic'),(6,25,'canLockSelf'),(8,25,'canEditOther'),(9,25,'canBuryOther'),(10,25,'canDeleteOtherTopic'),(11,25,'canLockOther'),(13,25,'canStickySelf'),(14,25,'canStickyOther'),(15,25,'canMoveSelf'),(16,25,'canMoveOther'),(17,7,'canPostComment'),(18,7,'canEditSelfComment'),(19,7,'canDeleteSelfComment'),(20,7,'canEditOtherComment'),(21,7,'canDeleteOtherComment'),(22,7,'canWritePost'),(23,7,'canEditSelfPost'),(24,7,'canDeleteSelfPost'),(25,7,'canEditOtherPost'),(26,7,'canDeleteOtherPost'),(27,7,'canPublishPost'),(28,7,'canChangeAuthor'),(29,30,'canDistribute'),(30,30,'canDeleteDistribution'),(31,30,'canChangeDistributeStatus'),(32,30,'canChangeDistributeLabels'),(33,7,'canUseMagicWords'),(34,25,'canReportPost'),(35,25,'canReceiveReports'),(36,25,'isTroll'),(37,7,'canSetEditStatus'),(38,7,'canChangeEditor'),(39,25,'canRequestBan'),(40,25,'canReceiveBanRequest'),(41,25,'canPermaDeletePost'),(42,25,'canPermaDeleteTopic'),(43,25,'canChangeBoardOwner'),(44,25,'canChangeBoardCategory'),(45,25,'canManageAllBoards'),(46,30,'canViewAllAssets'),(47,30,'canChangeAssetOwner'),(48,25,'canChangeBoardRank'),(49,7,'canBypassSubmitFee'),(50,25,'canUpvoteDownvote'),(51,7,'canDeleteSelfPostVersion'),(52,7,'canDeleteOtherPostVersion'),(53,7,'canEditAfterPublished');
/*!40000 ALTER TABLE `app_perms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apps`
--

DROP TABLE IF EXISTS `apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apps` (
  `appId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(2) DEFAULT '0',
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `defaultModule` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`appId`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apps`
--

LOCK TABLES `apps` WRITE;
/*!40000 ALTER TABLE `apps` DISABLE KEYS */;
INSERT INTO `apps` VALUES (1,'Dashboard','dashboard',1,'Dashboard','dashboard',1),(2,'Accounts','account',1,'Account','account',1),(5,'Pages','pages',1,'Page','',0),(6,'Profile','profile',1,'Profile','profile',0),(7,'Blog','blog',1,'Blog','blog',0),(25,'Forum','forum',1,'Forum','forum',0),(26,'RSS','rss',1,'RSS','rss',0),(27,'Store','store',1,'Store','store',0),(30,'LTBcoin','ltbcoin',1,'LTBcoin','ltbcoin',0);
/*!40000 ALTER TABLE `apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `block_meta`
--

DROP TABLE IF EXISTS `block_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `block_meta` (
  `blockMetaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blockId` int(11) unsigned NOT NULL,
  `metaKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`blockMetaId`),
  KEY `blockId` (`blockId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `block_meta_ibfk_1` FOREIGN KEY (`blockId`) REFERENCES `content_blocks` (`blockId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `block_meta`
--

LOCK TABLES `block_meta` WRITE;
/*!40000 ALTER TABLE `block_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `block_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_categories` (
  `categoryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parentId` int(11) unsigned DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `blog_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (39,'Breaking News','breaking-news',0,0,1,'',NULL);
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_comments` (
  `commentId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `postId` int(11) unsigned NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `commentDate` datetime DEFAULT NULL,
  `buried` int(2) DEFAULT '0',
  `editTime` datetime DEFAULT NULL,
  `editorial` int(1) DEFAULT '0',
  PRIMARY KEY (`commentId`),
  KEY `userId` (`userId`),
  KEY `postId` (`postId`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `blog_comments_ibfk_2` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_postCategories`
--

DROP TABLE IF EXISTS `blog_postCategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_postCategories` (
  `postCatId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postId` int(11) unsigned NOT NULL,
  `categoryId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`postCatId`),
  KEY `postId` (`postId`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `blog_postCategories_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE,
  CONSTRAINT `blog_postCategories_ibfk_2` FOREIGN KEY (`categoryId`) REFERENCES `blog_categories` (`categoryId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8223 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postCategories`
--

LOCK TABLES `blog_postCategories` WRITE;
/*!40000 ALTER TABLE `blog_postCategories` DISABLE KEYS */;
INSERT INTO `blog_postCategories` VALUES (8222,974,39);
/*!40000 ALTER TABLE `blog_postCategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_postMeta`
--

DROP TABLE IF EXISTS `blog_postMeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_postMeta` (
  `metaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `metaTypeId` int(11) unsigned NOT NULL,
  `postId` int(11) unsigned NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`metaId`),
  KEY `metaTypeId` (`metaTypeId`),
  KEY `postId` (`postId`),
  CONSTRAINT `blog_postMeta_ibfk_1` FOREIGN KEY (`metaTypeId`) REFERENCES `blog_postMetaTypes` (`metaTypeId`) ON DELETE CASCADE,
  CONSTRAINT `blog_postMeta_ibfk_2` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3575 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMeta`
--

LOCK TABLES `blog_postMeta` WRITE;
/*!40000 ALTER TABLE `blog_postMeta` DISABLE KEYS */;
INSERT INTO `blog_postMeta` VALUES (3571,2,974,''),(3572,4,974,''),(3573,5,974,''),(3574,7,974,'');
/*!40000 ALTER TABLE `blog_postMeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_postMetaTypes`
--

DROP TABLE IF EXISTS `blog_postMetaTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_postMetaTypes` (
  `metaTypeId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `options` longtext COLLATE utf8_unicode_ci,
  `active` int(2) DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  `isPublic` int(2) DEFAULT '1',
  `hidden` int(2) DEFAULT '0',
  PRIMARY KEY (`metaTypeId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `blog_postMetaTypes_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMetaTypes`
--

LOCK TABLES `blog_postMetaTypes` WRITE;
/*!40000 ALTER TABLE `blog_postMetaTypes` DISABLE KEYS */;
INSERT INTO `blog_postMetaTypes` VALUES (2,'Bitcoin Tipping Address','tip-address','textbox','',1,0,1,1,0),(4,'Soundcloud Track ID','soundcloud-id','textbox','',1,0,1,1,0),(5,'Audio URL (overrides soundcloud url)','audio-url','textbox','',1,0,1,1,0),(6,'Audio Byte Length','audio-length','textbox','',1,100,1,1,1),(7,'Magic Word','magic-word','textbox','',1,150,1,0,0);
/*!40000 ALTER TABLE `blog_postMetaTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `postId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `userId` int(11) unsigned NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  `postDate` datetime DEFAULT NULL,
  `publishDate` datetime DEFAULT NULL,
  `published` int(2) DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `excerpt` longtext COLLATE utf8_unicode_ci,
  `views` int(11) DEFAULT '0',
  `featured` int(2) DEFAULT '0',
  `coverImage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ready` int(2) DEFAULT '0',
  `commentCount` int(11) DEFAULT '0',
  `commentCheck` datetime DEFAULT NULL,
  `formatType` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'wysiwyg',
  `editTime` datetime DEFAULT NULL,
  `editedBy` int(11) unsigned DEFAULT '0',
  `status` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'draft',
  `notes` longtext COLLATE utf8_unicode_ci,
  `trash` int(1) DEFAULT '0',
  `version` int(11) DEFAULT '0',
  PRIMARY KEY (`postId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  KEY `siteId` (`siteId`),
  KEY `editedBy` (`editedBy`),
  KEY `publishDate` (`publishDate`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=975 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (974,'Dolor sit amet!','dolor-sit-amet','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',79,1,'2014-12-11 17:06:38','2014-12-11 17:05:00',1,NULL,'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing ...',1,0,NULL,0,0,'2014-12-11 17:06:40','markdown','2014-12-11 17:06:54',79,'published','',0,724);
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_subscriptions`
--

DROP TABLE IF EXISTS `board_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_subscriptions` (
  `subId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `boardId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`subId`),
  UNIQUE KEY `userId_boardId` (`userId`,`boardId`),
  KEY `userId` (`userId`),
  KEY `boardId` (`boardId`),
  CONSTRAINT `board_subscriptions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `board_subscriptions_ibfk_2` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_subscriptions`
--

LOCK TABLES `board_subscriptions` WRITE;
/*!40000 ALTER TABLE `board_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `board_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coin_addresses`
--

DROP TABLE IF EXISTS `coin_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coin_addresses` (
  `addressId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'btc',
  `address` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `submitDate` datetime DEFAULT NULL,
  `verified` int(2) DEFAULT '0',
  `isXCP` int(2) DEFAULT '0',
  `isPrimary` int(2) DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public` int(2) DEFAULT '0',
  PRIMARY KEY (`addressId`),
  KEY `userId` (`userId`),
  KEY `type` (`type`),
  KEY `address` (`address`),
  CONSTRAINT `coin_addresses_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6190 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coin_addresses`
--

LOCK TABLES `coin_addresses` WRITE;
/*!40000 ALTER TABLE `coin_addresses` DISABLE KEYS */;
INSERT INTO `coin_addresses` VALUES (6189,79,'btc','15fx1Gqe4KodZvyzN6VUSkEmhCssrM1yD7','2014-12-11 17:04:16',0,1,1,'LTBcoin <a href=\'https://counterwallet.co\' target=\'_blank\'>Compatible Address</a>',0);
/*!40000 ALTER TABLE `coin_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_blocks`
--

DROP TABLE IF EXISTS `content_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_blocks` (
  `blockId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `active` int(2) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  `formatType` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'wysiwyg',
  PRIMARY KEY (`blockId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `content_blocks_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_blocks`
--

LOCK TABLES `content_blocks` WRITE;
/*!40000 ALTER TABLE `content_blocks` DISABLE KEYS */;
INSERT INTO `content_blocks` VALUES (22,'Footer Info','footer-info','Powered by [Tokenly](https://github.com/tokenly/tokenly-cms)',1,1,'markdown');
/*!40000 ALTER TABLE `content_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_versions`
--

DROP TABLE IF EXISTS `content_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_versions` (
  `versionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `itemId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned DEFAULT '0',
  `content` longtext COLLATE utf8_unicode_ci,
  `formatType` varchar(25) COLLATE utf8_unicode_ci DEFAULT 'markdown',
  `num` int(11) DEFAULT '1',
  `changes` int(11) DEFAULT '0',
  `versionDate` datetime DEFAULT NULL,
  PRIMARY KEY (`versionId`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=725 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_versions`
--

LOCK TABLES `content_versions` WRITE;
/*!40000 ALTER TABLE `content_versions` DISABLE KEYS */;
INSERT INTO `content_versions` VALUES (724,'blog-post',974,79,'{\"content\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\",\"excerpt\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\\r\\n\\r\\nLorem ipsum dolor sit amet, consectetur adipiscing ...\"}','markdown',1,0,'2014-12-11 17:06:38');
/*!40000 ALTER TABLE `content_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dash_menu`
--

DROP TABLE IF EXISTS `dash_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dash_menu` (
  `itemId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moduleId` int(11) unsigned NOT NULL,
  `dashGroup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(11) DEFAULT '0',
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `checkAccess` int(2) DEFAULT '0',
  `params` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`itemId`),
  KEY `moduleId` (`moduleId`),
  CONSTRAINT `dash_menu_ibfk_1` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dash_menu`
--

LOCK TABLES `dash_menu` WRITE;
/*!40000 ALTER TABLE `dash_menu` DISABLE KEYS */;
INSERT INTO `dash_menu` VALUES (1,1,' ',0,'Dashboard Home',0,NULL),(2,23,' ',1,'Account Settings',0,NULL),(3,21,' ',2,'My Profile',0,NULL),(4,3,' ',600,'Logout',0,''),(5,8,'System',0,'System Stats',1,NULL),(6,9,'System',1,'System Settings',1,NULL),(7,7,'System',2,'Sub-Sites',1,NULL),(8,4,'System',3,'Apps & Modules',1,NULL),(9,12,'System',4,'Themes',1,NULL),(10,25,'System',5,'Dashboard Menu',1,NULL),(11,10,'Users',1,'User Accounts',1,''),(12,11,'Users',1,'Groups',1,NULL),(13,20,'Users',2,'User Profile Fields',1,NULL),(14,16,'CMS',0,'Pages',1,NULL),(15,15,'CMS',1,'Content Blocks',1,NULL),(16,14,'CMS',2,'Page Tags',1,NULL),(17,13,'CMS',3,'Menus',1,NULL),(18,18,'CMS',4,'Menu Items',1,NULL),(19,19,'CMS',5,'File browser',1,NULL),(20,26,'Blog',1,'Categories',1,''),(21,27,'Blog',1,'Posts',1,NULL),(24,37,'Blog',0,'Blog Settings',1,'/blog'),(39,37,'Users',0,'Account System Settings',1,'/account'),(40,37,'Forum',0,'Forum Settings',1,'/forum'),(41,38,'Forum',1,'Categories',1,''),(42,39,'Forum',2,'Boards',1,''),(43,42,' ',3,'Notifications',0,''),(44,43,'Blog',50,'Post Metadata Types',1,''),(45,37,'RSS',0,'RSS Settings',1,'/rss'),(46,37,'Store',0,'Store Settings',1,'/store'),(47,47,'Store',0,'Categories',1,''),(48,48,'Store',20,'Products',1,''),(49,49,'Blog',100,'Disqus Comments',1,''),(50,50,'LTBcoin',10,'Share Distributor',1,''),(51,51,'LTBcoin',50,'Asset Dropper',1,''),(52,37,'LTBcoin',0,'LTBcoin Settings',1,'/ltbcoin'),(53,52,'System',100,'Notification Pusher',1,''),(54,53,'LTBcoin',160,'Proof of Participation',1,''),(55,54,'LTBcoin',200,'Magic Words',1,''),(56,59,'LTBcoin',220,'Magic Word Submissions',1,''),(57,60,' ',5,'Referrals',0,''),(58,61,'LTBcoin',230,'Address Manager',1,''),(59,62,' ',50,'Private Messages',0,''),(60,63,'LTBcoin',240,'Token Inventory',1,''),(61,64,'LTBcoin',250,'Asset Cache',1,''),(62,65,'RSS',100,'RSS Feed Proxies',1,''),(63,67,'Store',100,'Orders & Payments',1,''),(64,69,'Blog',0,'Submissions',1,'');
/*!40000 ALTER TABLE `dash_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_boardMeta`
--

DROP TABLE IF EXISTS `forum_boardMeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_boardMeta` (
  `metaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `boardId` int(11) unsigned NOT NULL,
  `metaKey` varchar(100) NOT NULL,
  `value` longtext,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`metaId`),
  KEY `boardId` (`boardId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `forum_boardMeta_ibfk_1` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_boardMeta`
--

LOCK TABLES `forum_boardMeta` WRITE;
/*!40000 ALTER TABLE `forum_boardMeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_boardMeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_boards`
--

DROP TABLE IF EXISTS `forum_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_boards` (
  `boardId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) unsigned DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rank` int(11) DEFAULT '0',
  `description` longtext COLLATE utf8_unicode_ci,
  `siteId` int(11) unsigned NOT NULL,
  `active` int(2) DEFAULT '0',
  `ownerId` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`boardId`),
  KEY `categoryId` (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  KEY `ownerId` (`ownerId`),
  CONSTRAINT `forum_boards_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_boards`
--

LOCK TABLES `forum_boards` WRITE;
/*!40000 ALTER TABLE `forum_boards` DISABLE KEYS */;
INSERT INTO `forum_boards` VALUES (92,15,'General Discussion','general-discussion',0,'',1,1,0),(93,0,'Introductions','introductions',10,'',1,1,0);
/*!40000 ALTER TABLE `forum_boards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_categories`
--

DROP TABLE IF EXISTS `forum_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_categories` (
  `categoryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `rank` int(11) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `forum_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_categories`
--

LOCK TABLES `forum_categories` WRITE;
/*!40000 ALTER TABLE `forum_categories` DISABLE KEYS */;
INSERT INTO `forum_categories` VALUES (15,'Tokenly Talk','tokenly-talk','',0,1);
/*!40000 ALTER TABLE `forum_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_mods`
--

DROP TABLE IF EXISTS `forum_mods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_mods` (
  `modId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `boardId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`modId`),
  KEY `userId` (`userId`),
  KEY `boardId` (`boardId`),
  CONSTRAINT `forum_mods_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `forum_mods_ibfk_2` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_mods`
--

LOCK TABLES `forum_mods` WRITE;
/*!40000 ALTER TABLE `forum_mods` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_mods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_posts` (
  `postId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `topicId` int(11) unsigned NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `buried` int(2) DEFAULT '0',
  `postTime` datetime DEFAULT NULL,
  `editTime` datetime DEFAULT NULL,
  `trollPost` int(2) DEFAULT '0',
  `buryTime` datetime DEFAULT NULL,
  `editedBy` int(11) unsigned DEFAULT '0',
  `buriedBy` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`postId`),
  KEY `userId` (`userId`),
  KEY `topicId` (`topicId`),
  CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `forum_posts_ibfk_3` FOREIGN KEY (`topicId`) REFERENCES `forum_topics` (`topicId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105689 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
INSERT INTO `forum_posts` VALUES (105688,79,6109,'Most excellent!\r\n',0,'2014-12-11 17:18:14',NULL,0,NULL,0,0);
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_subscriptions`
--

DROP TABLE IF EXISTS `forum_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_subscriptions` (
  `subId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `topicId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`subId`),
  KEY `userId` (`userId`),
  KEY `topicId` (`topicId`),
  CONSTRAINT `forum_subscriptions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `forum_subscriptions_ibfk_2` FOREIGN KEY (`topicId`) REFERENCES `forum_topics` (`topicId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8408 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_subscriptions`
--

LOCK TABLES `forum_subscriptions` WRITE;
/*!40000 ALTER TABLE `forum_subscriptions` DISABLE KEYS */;
INSERT INTO `forum_subscriptions` VALUES (8407,79,6109);
/*!40000 ALTER TABLE `forum_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topics`
--

DROP TABLE IF EXISTS `forum_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topics` (
  `topicId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `boardId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `locked` int(2) DEFAULT '0',
  `postTime` datetime DEFAULT NULL,
  `editTime` datetime DEFAULT NULL,
  `lastPost` datetime DEFAULT NULL,
  `sticky` int(2) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `lockTime` datetime DEFAULT NULL,
  `lockedBy` int(11) unsigned DEFAULT '0',
  `trollPost` int(2) DEFAULT '0',
  `buried` int(2) DEFAULT '0',
  `buryTime` datetime DEFAULT NULL,
  `editedBy` int(11) unsigned DEFAULT '0',
  `buriedBy` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`topicId`),
  KEY `boardId` (`boardId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE,
  CONSTRAINT `forum_topics_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6110 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topics`
--

LOCK TABLES `forum_topics` WRITE;
/*!40000 ALTER TABLE `forum_topics` DISABLE KEYS */;
INSERT INTO `forum_topics` VALUES (6109,92,79,'Test Discussion','test-discussion','Testing forum discussion system',0,'2014-12-11 17:18:04',NULL,'2014-12-11 17:18:14',0,0,NULL,0,0,0,NULL,0,0);
/*!40000 ALTER TABLE `forum_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_access`
--

DROP TABLE IF EXISTS `group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_access` (
  `groupAccessId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupId` int(11) unsigned NOT NULL,
  `moduleId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`groupAccessId`),
  KEY `groupId` (`groupId`),
  KEY `moduleId` (`moduleId`),
  CONSTRAINT `group_access_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`) ON DELETE CASCADE,
  CONSTRAINT `group_access_ibfk_2` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1641 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_access`
--

LOCK TABLES `group_access` WRITE;
/*!40000 ALTER TABLE `group_access` DISABLE KEYS */;
INSERT INTO `group_access` VALUES (893,27,1),(1004,29,1),(1005,29,54),(1006,29,61),(1007,29,63),(1159,30,39),(1160,30,64),(1161,31,64),(1305,14,38),(1306,14,39),(1454,23,27),(1455,23,50),(1456,23,51),(1556,1,1),(1557,1,4),(1558,1,7),(1559,1,8),(1560,1,9),(1561,1,10),(1562,1,11),(1563,1,12),(1564,1,13),(1565,1,14),(1566,1,15),(1567,1,16),(1568,1,18),(1569,1,19),(1570,1,20),(1571,1,25),(1572,1,26),(1573,1,27),(1574,1,31),(1575,1,37),(1576,1,38),(1577,1,39),(1578,1,43),(1579,1,47),(1580,1,48),(1581,1,49),(1582,1,50),(1583,1,51),(1584,1,52),(1585,1,53),(1586,1,54),(1587,1,59),(1588,1,61),(1589,1,63),(1590,1,64),(1591,1,65),(1592,1,67),(1593,1,69),(1594,1,60),(1595,2,1),(1596,2,54),(1597,2,61),(1598,2,63),(1599,2,69),(1600,12,26),(1601,12,27),(1602,11,27),(1603,16,1),(1604,16,8),(1605,16,9),(1606,16,10),(1607,16,11),(1608,16,12),(1609,16,13),(1610,16,14),(1611,16,15),(1612,16,16),(1613,16,18),(1614,16,19),(1615,16,20),(1616,16,25),(1617,16,26),(1618,16,27),(1619,16,31),(1620,16,37),(1621,16,38),(1622,16,39),(1623,16,43),(1624,16,49),(1625,16,50),(1626,16,51),(1627,16,52),(1628,16,53),(1629,16,54),(1630,16,59),(1631,16,61),(1632,16,63),(1633,16,64),(1634,16,65),(1635,16,67),(1636,16,69);
/*!40000 ALTER TABLE `group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_perms`
--

DROP TABLE IF EXISTS `group_perms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_perms` (
  `groupPermId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `permId` int(11) unsigned NOT NULL,
  `groupId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`groupPermId`),
  KEY `permId` (`permId`),
  KEY `groupId` (`groupId`),
  CONSTRAINT `group_perms_ibfk_1` FOREIGN KEY (`permId`) REFERENCES `app_perms` (`permId`) ON DELETE CASCADE,
  CONSTRAINT `group_perms_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2239 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_perms`
--

LOCK TABLES `group_perms` WRITE;
/*!40000 ALTER TABLE `group_perms` DISABLE KEYS */;
INSERT INTO `group_perms` VALUES (1227,1,29),(1228,2,29),(1229,3,29),(1230,4,29),(1231,5,29),(1232,36,29),(1652,1,14),(1653,2,14),(1654,3,14),(1655,4,14),(1656,5,14),(1657,6,14),(1658,8,14),(1659,9,14),(1660,10,14),(1661,11,14),(1662,13,14),(1663,14,14),(1664,15,14),(1665,16,14),(1666,34,14),(1667,35,14),(1668,39,14),(1669,41,14),(1670,42,14),(1671,43,14),(1672,44,14),(1673,45,14),(1674,48,14),(1791,1,15),(1792,2,15),(1793,3,15),(1794,4,15),(1795,5,15),(1796,6,15),(1797,9,15),(1798,10,15),(1799,11,15),(1800,13,15),(1801,14,15),(1802,15,15),(1803,16,15),(1804,34,15),(1805,35,15),(1806,39,15),(1916,17,23),(1917,18,23),(1918,19,23),(1919,22,23),(1920,23,23),(1921,24,23),(1922,33,23),(1923,49,23),(2087,17,1),(2088,18,1),(2089,19,1),(2090,20,1),(2091,21,1),(2092,22,1),(2093,23,1),(2094,24,1),(2095,25,1),(2096,26,1),(2097,27,1),(2098,28,1),(2099,33,1),(2100,37,1),(2101,38,1),(2102,51,1),(2103,52,1),(2104,53,1),(2105,1,1),(2106,2,1),(2107,3,1),(2108,4,1),(2109,5,1),(2110,6,1),(2111,8,1),(2112,9,1),(2113,10,1),(2114,11,1),(2115,13,1),(2116,14,1),(2117,15,1),(2118,16,1),(2119,34,1),(2120,35,1),(2121,39,1),(2122,40,1),(2123,41,1),(2124,42,1),(2125,43,1),(2126,44,1),(2127,45,1),(2128,48,1),(2129,29,1),(2130,30,1),(2131,31,1),(2132,32,1),(2133,46,1),(2134,47,1),(2135,17,2),(2136,18,2),(2137,19,2),(2138,22,2),(2139,23,2),(2140,24,2),(2141,51,2),(2142,53,2),(2143,1,2),(2144,2,2),(2145,3,2),(2146,6,2),(2147,34,2),(2148,50,2),(2149,17,12),(2150,18,12),(2151,19,12),(2152,22,12),(2153,23,12),(2154,24,12),(2155,25,12),(2156,26,12),(2157,27,12),(2158,37,12),(2159,51,12),(2160,52,12),(2161,53,12),(2162,17,11),(2163,18,11),(2164,19,11),(2165,22,11),(2166,23,11),(2167,24,11),(2168,49,11),(2169,51,11),(2170,53,11),(2171,17,16),(2172,18,16),(2173,19,16),(2174,20,16),(2175,21,16),(2176,22,16),(2177,23,16),(2178,24,16),(2179,25,16),(2180,26,16),(2181,27,16),(2182,28,16),(2183,33,16),(2184,37,16),(2185,38,16),(2186,49,16),(2187,51,16),(2188,52,16),(2189,53,16),(2190,1,16),(2191,2,16),(2192,3,16),(2193,4,16),(2194,5,16),(2195,6,16),(2196,8,16),(2197,9,16),(2198,10,16),(2199,11,16),(2200,13,16),(2201,14,16),(2202,15,16),(2203,16,16),(2204,34,16),(2205,35,16),(2206,39,16),(2207,40,16),(2208,41,16),(2209,42,16),(2210,43,16),(2211,44,16),(2212,45,16),(2213,48,16),(2214,29,16),(2215,30,16),(2216,31,16),(2217,32,16),(2218,46,16),(2219,47,16);
/*!40000 ALTER TABLE `group_perms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_sites`
--

DROP TABLE IF EXISTS `group_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_sites` (
  `groupSiteId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupId` int(11) unsigned NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`groupSiteId`),
  KEY `groupId` (`groupId`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `group_sites_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`) ON DELETE CASCADE,
  CONSTRAINT `group_sites_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_sites`
--

LOCK TABLES `group_sites` WRITE;
/*!40000 ALTER TABLE `group_sites` DISABLE KEYS */;
INSERT INTO `group_sites` VALUES (57,27,1),(68,29,1),(77,30,1),(78,31,1),(84,14,1),(90,15,1),(100,23,1),(112,1,1),(114,2,1),(115,12,1),(116,11,1),(117,16,1);
/*!40000 ALTER TABLE `group_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_users`
--

DROP TABLE IF EXISTS `group_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_users` (
  `groupUserId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`groupUserId`),
  KEY `groupId` (`groupId`),
  KEY `userId` (`userId`),
  CONSTRAINT `group_users_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`) ON DELETE CASCADE,
  CONSTRAINT `group_users_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11288 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_users`
--

LOCK TABLES `group_users` WRITE;
/*!40000 ALTER TABLE `group_users` DISABLE KEYS */;
INSERT INTO `group_users` VALUES (940,1,79),(941,2,79),(942,20,79),(6926,30,79);
/*!40000 ALTER TABLE `group_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `groupId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isDefault` int(2) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`groupId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Root Admins','root-admin',0,1),(2,'Default','default',1,1),(11,'Blog Writer','blog-writer',0,1),(12,'Blog Editor','blog-editor',0,1),(14,'Forum Admin','forum-admin',0,1),(15,'Forum Moderator','forum-moderator',0,1),(16,'Admin','admin',0,1),(20,'Drop List','drop-list',0,1),(23,'Podcaster','podcaster',0,1),(27,'Banned','banned',0,1),(29,'Forum Troll','forum-troll',0,1),(30,'Private Forum Owner','private-forum-owner',0,1),(31,'Asset Owner','asset-owner',0,1);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_links`
--

DROP TABLE IF EXISTS `menu_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_links` (
  `linkId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menuId` int(11) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(11) DEFAULT '0',
  `parentId` int(11) unsigned DEFAULT '0',
  `parentLink` int(2) DEFAULT '0',
  PRIMARY KEY (`linkId`),
  KEY `menuId` (`menuId`),
  KEY `url` (`url`),
  KEY `parentId` (`parentId`),
  CONSTRAINT `menu_links_ibfk_1` FOREIGN KEY (`menuId`) REFERENCES `menus` (`menuId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_links`
--

LOCK TABLES `menu_links` WRITE;
/*!40000 ALTER TABLE `menu_links` DISABLE KEYS */;
INSERT INTO `menu_links` VALUES (50,10,'/blog','Blog',0,0,0),(51,10,'/forum','Forums',0,0,0),(52,10,'/account','Account',0,0,0);
/*!40000 ALTER TABLE `menu_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_pages`
--

DROP TABLE IF EXISTS `menu_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_pages` (
  `menuPageId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageId` int(11) unsigned NOT NULL,
  `menuId` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(11) DEFAULT '0',
  `parentId` int(11) unsigned DEFAULT '0',
  `parentLink` int(2) DEFAULT '0',
  PRIMARY KEY (`menuPageId`),
  KEY `pageId` (`pageId`),
  KEY `menuId` (`menuId`),
  KEY `parentId` (`parentId`),
  CONSTRAINT `menu_pages_ibfk_1` FOREIGN KEY (`pageId`) REFERENCES `pages` (`pageId`) ON DELETE CASCADE,
  CONSTRAINT `menu_pages_ibfk_2` FOREIGN KEY (`menuId`) REFERENCES `menus` (`menuId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_pages`
--

LOCK TABLES `menu_pages` WRITE;
/*!40000 ALTER TABLE `menu_pages` DISABLE KEYS */;
INSERT INTO `menu_pages` VALUES (32,47,10,'About Us',0,0,0);
/*!40000 ALTER TABLE `menu_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `menuId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`menuId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (10,'Main Menu','main',1);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `moduleId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appId` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(2) DEFAULT '0',
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `checkAccess` int(2) DEFAULT '0',
  PRIMARY KEY (`moduleId`),
  KEY `appId` (`appId`),
  KEY `slug` (`slug`),
  CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,1,'Dashboard Home','dash-home',1,'Home','',0),(2,2,'Account Home','account-home',1,'Home','',0),(3,2,'Logout','logout',1,'Logout','logout',0),(4,1,'Apps & Modules','modules',1,'Modules','modules',1),(7,1,'Sites','sites',1,'Sites','sites',1),(8,1,'Stats','stats',1,'Stats','stats',1),(9,1,'Settings','settings',1,'Settings','settings',1),(10,1,'Accounts','accounts',1,'Accounts','accounts',1),(11,1,'Groups','groups',1,'Groups','groups',1),(12,1,'Themes','themes',1,'Themes','themes',1),(13,1,'Menus','menus',1,'Menus','menus',1),(14,1,'Page Tags','page-tags',1,'PageTags','page-tags',1),(15,1,'Content Blocks','content-blocks',1,'ContentBlocks','content-blocks',1),(16,1,'Pages','pages',1,'Pages','pages',1),(17,5,'Page View','page-view',1,'View','',0),(18,1,'Menu Items','menu-items',1,'MenuItems','menu-items',1),(19,1,'Files','files',1,'Files','files',1),(20,1,'Profile Fields','profile-fields',1,'ProfileFields','profile-fields',1),(21,2,'Profile','account-profile',1,'Profile','profile',0),(22,6,'User Profile','user-profile',1,'User','user',0),(23,2,'Account Settings','account-settings',1,'Settings','settings',0),(24,2,'Reset Password','account-reset',1,'Reset','reset',0),(25,1,'Dashboard Menu','dash-menu',1,'DashMenu','dash-menu',0),(26,1,'Blog Categories','blog-categories',1,'BlogCategory','blog-category',1),(27,1,'Blog Posts','blog-posts',1,'BlogPost','blog-post',1),(28,7,'Post','blog-post',1,'Post','post',0),(29,7,'Category','blog-category',1,'Category','category',0),(30,7,'Archive','blog-archive',1,'Archive','archive',0),(31,1,'Blog Comments','blog-comments',0,'BlogComments','blog-comments',1),(32,6,'Member List','member-list',1,'Members','members',0),(37,1,'App Settings','app-settings',1,'AppSettings','app-settings',1),(38,1,'Forum Categories','forum-categories',1,'ForumCategory','forum-cats',1),(39,1,'Forum Boards','forum-boards',1,'ForumBoard','forum-boards',1),(40,25,'Board','forum-board',1,'Board','board',0),(41,25,'Post','forum-post',1,'Post','post',0),(42,2,'Notification','notification',1,'Notification','notifications',0),(43,1,'Post Metadata Types','blog-post-meta',1,'BlogMeta','blog-meta',1),(46,26,'RSS Feed','rss-feed',1,'Feed','feed',0),(47,1,'Store Categories','store-categories',1,'StoreCategory','store-category',1),(48,1,'Store Products','store-products',1,'StoreProduct','store-product',1),(49,1,'Disqus Comments','disqus-comments',1,'Disqus','disqus',1),(50,1,'Share Distributor','share-distribute',1,'LTBcoin_Distribute','xcp-distribute',1),(51,1,'Asset Dropper','asset-drop',1,'LTBcoin_AssetDrop','asset-drop',1),(52,1,'Notification Pusher','notification-pusher',1,'Notifier','notifier',1),(53,1,'Proof of Participation','ltbcoin-pop',1,'LTBcoin_POP','ltbcoin-pop',1),(54,1,'Magic Words','magic-words',1,'LTBcoin_MagicWords','magic-words',1),(59,1,'Magic Word Submissions','magic-word-submits',1,'LTBcoin_MagicWordSubmits','all-magic-words',1),(60,2,'Referrals','account-referrals',1,'Referral','referrals',0),(61,1,'Address Manager','address-manager',1,'LTBcoin_Address','address-manager',1),(62,2,'Messages','private-message',1,'Message','messages',0),(63,1,'Token Inventory','token-inventory',1,'LTBcoin_Inventory','inventory',1),(64,1,'Asset Cache','asset-cache',1,'LTBcoin_AssetCache','asset-cache',1),(65,1,'RSS Feed Proxies','rss-feed-proxy',1,'RSSProxy','rss-feed-proxy',1),(66,26,'Proxy Feed','proxy-feed',1,'Proxy','proxy',0),(67,1,'Orders','store-orders',1,'Store_Order','store-orders',1),(68,26,'Podcast Proxy','pod-proxy',1,'PodProxy','pod-proxy',0),(69,1,'Blog Submissions','blog-submissions',1,'Blog_Submissions','submissions',1);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_index`
--

DROP TABLE IF EXISTS `page_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_index` (
  `pageIndexId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `moduleId` int(11) unsigned NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`pageIndexId`),
  KEY `moduleId` (`moduleId`),
  KEY `siteId` (`siteId`),
  KEY `itemId` (`itemId`),
  KEY `url` (`url`),
  CONSTRAINT `page_index_ibfk_1` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`),
  CONSTRAINT `page_index_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_index`
--

LOCK TABLES `page_index` WRITE;
/*!40000 ALTER TABLE `page_index` DISABLE KEYS */;
INSERT INTO `page_index` VALUES (379,'about-us-1',17,1,47);
/*!40000 ALTER TABLE `page_index` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_meta`
--

DROP TABLE IF EXISTS `page_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_meta` (
  `pageMetaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pageId` int(11) unsigned NOT NULL,
  `metaKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`pageMetaId`),
  KEY `pageId` (`pageId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `page_meta_ibfk_1` FOREIGN KEY (`pageId`) REFERENCES `pages` (`pageId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_meta`
--

LOCK TABLES `page_meta` WRITE;
/*!40000 ALTER TABLE `page_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `page_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_tags`
--

DROP TABLE IF EXISTS `page_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_tags` (
  `tagId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`tagId`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_tags`
--

LOCK TABLES `page_tags` WRITE;
/*!40000 ALTER TABLE `page_tags` DISABLE KEYS */;
INSERT INTO `page_tags` VALUES (1,'CONTACT_FORM','Slick_Tags_ContactForm'),(3,'REDIRECT','Slick_Tags_Redirect'),(8,'FORUM_BUILDER','Slick_Tags_ForumBuilder'),(17,'BLOG_QUICKUPDATE','Slick_Tags_BlogQuickUpdate'),(18,'HITCOUNTER','Slick_Tags_HitCounter');
/*!40000 ALTER TABLE `page_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `pageId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `siteId` int(11) unsigned NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `active` int(2) DEFAULT '0',
  `formatType` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'wysiwyg',
  PRIMARY KEY (`pageId`),
  KEY `url` (`url`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (47,'About Us','about-us-1',1,'default','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.','',1,'markdown');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_order`
--

DROP TABLE IF EXISTS `payment_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `address` (`address`),
  KEY `account` (`account`),
  KEY `asset` (`asset`),
  KEY `orderType` (`orderType`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_order`
--

LOCK TABLES `payment_order` WRITE;
/*!40000 ALTER TABLE `payment_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pop_firstView`
--

DROP TABLE IF EXISTS `pop_firstView`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pop_firstView` (
  `popId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `moduleId` int(11) unsigned NOT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `popDate` datetime NOT NULL,
  PRIMARY KEY (`popId`),
  KEY `userId` (`userId`),
  KEY `moduleId` (`moduleId`),
  CONSTRAINT `pop_firstView_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `pop_firstView_ibfk_2` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`)
) ENGINE=InnoDB AUTO_INCREMENT=439507 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pop_firstView`
--

LOCK TABLES `pop_firstView` WRITE;
/*!40000 ALTER TABLE `pop_firstView` DISABLE KEYS */;
INSERT INTO `pop_firstView` VALUES (439505,79,40,92,'2014-12-11 17:17:48'),(439506,79,41,6109,'2014-12-11 17:18:04');
/*!40000 ALTER TABLE `pop_firstView` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pop_reports`
--

DROP TABLE IF EXISTS `pop_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pop_reports` (
  `reportId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `totalPoints` double DEFAULT NULL,
  `info` longtext COLLATE utf8_unicode_ci,
  `reportDate` datetime DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extraInfo` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`reportId`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pop_reports`
--

LOCK TABLES `pop_reports` WRITE;
/*!40000 ALTER TABLE `pop_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `pop_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pop_words`
--

DROP TABLE IF EXISTS `pop_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pop_words` (
  `submitId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `moduleId` int(11) unsigned NOT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `submitDate` datetime DEFAULT NULL,
  PRIMARY KEY (`submitId`),
  KEY `userId` (`userId`),
  KEY `word` (`word`),
  KEY `moduleId` (`moduleId`),
  CONSTRAINT `pop_words_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `pop_words_ibfk_2` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60673 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pop_words`
--

LOCK TABLES `pop_words` WRITE;
/*!40000 ALTER TABLE `pop_words` DISABLE KEYS */;
/*!40000 ALTER TABLE `pop_words` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_messages`
--

DROP TABLE IF EXISTS `private_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_messages` (
  `messageId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `toUser` int(11) unsigned NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci,
  `isRead` int(2) DEFAULT '0',
  `sendDate` datetime DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `replyId` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`messageId`),
  KEY `userId` (`userId`),
  KEY `toUser` (`toUser`),
  KEY `replyId` (`replyId`),
  CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `private_messages_ibfk_2` FOREIGN KEY (`toUser`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6059 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_messages`
--

LOCK TABLES `private_messages` WRITE;
/*!40000 ALTER TABLE `private_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_fieldGroups`
--

DROP TABLE IF EXISTS `profile_fieldGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_fieldGroups` (
  `fieldGroupId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fieldId` int(11) unsigned NOT NULL,
  `groupId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`fieldGroupId`),
  KEY `fieldId` (`fieldId`),
  KEY `groupId` (`groupId`),
  CONSTRAINT `profile_fieldGroups_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `profile_fields` (`fieldId`) ON DELETE CASCADE,
  CONSTRAINT `profile_fieldGroups_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_fieldGroups`
--

LOCK TABLES `profile_fieldGroups` WRITE;
/*!40000 ALTER TABLE `profile_fieldGroups` DISABLE KEYS */;
INSERT INTO `profile_fieldGroups` VALUES (36,9,2),(37,7,2),(38,14,2),(39,6,2),(40,11,2),(42,13,2),(43,12,2),(45,3,2);
/*!40000 ALTER TABLE `profile_fieldGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_fields`
--

DROP TABLE IF EXISTS `profile_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_fields` (
  `fieldId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `options` longtext COLLATE utf8_unicode_ci,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public` int(2) DEFAULT '0',
  `active` int(2) DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fieldId`),
  KEY `type` (`type`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `profile_fields_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_fields`
--

LOCK TABLES `profile_fields` WRITE;
/*!40000 ALTER TABLE `profile_fields` DISABLE KEYS */;
INSERT INTO `profile_fields` VALUES (3,'textbox','','Bitcoin Tipping Address (This is not your <a href=\"http://letstalkbitcoin.com/account/settings\">LTBCOIN address</a>)',1,1,100,1,'bitcoin-address',NULL),(6,'textarea','','Bio',1,1,20,1,'bio',NULL),(7,'textbox','','Location',1,1,10,1,'location',NULL),(9,'textbox','','Real Name',1,1,0,1,'real-name',NULL),(11,'textarea','','Forum Signature',1,1,30,1,'forum-signature',NULL),(12,'textbox','','LTBcoin <a href=\'https://counterwallet.co\' target=\'_blank\'>Compatible Address</a>',1,1,1,1,'ltbcoin-address','btc'),(13,'textbox','','Litecoin Address',1,0,120,1,'litecoin-address',NULL),(14,'textbox','','Website',1,1,15,1,'website',NULL);
/*!40000 ALTER TABLE `profile_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proxy_url`
--

DROP TABLE IF EXISTS `proxy_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxy_url` (
  `proxyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`proxyId`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_url`
--

LOCK TABLES `proxy_url` WRITE;
/*!40000 ALTER TABLE `proxy_url` DISABLE KEYS */;
/*!40000 ALTER TABLE `proxy_url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset_links`
--

DROP TABLE IF EXISTS `reset_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reset_links` (
  `resetId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `requestTime` datetime NOT NULL,
  PRIMARY KEY (`resetId`),
  KEY `userId` (`userId`),
  KEY `url` (`url`),
  CONSTRAINT `reset_links_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=959 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_links`
--

LOCK TABLES `reset_links` WRITE;
/*!40000 ALTER TABLE `reset_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `reset_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `settingId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `settingKey` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settingValue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bool` int(11) DEFAULT '0',
  `textarea` int(2) DEFAULT '0',
  PRIMARY KEY (`settingId`),
  KEY `settingKey` (`settingKey`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'systemDisabled','0','System Maintenance Mode?',1,0),(2,'disabledMessage','Performing website maintenance, we will be back shortly!','System Maintenance Message',0,1);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_apps`
--

DROP TABLE IF EXISTS `site_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_apps` (
  `siteAppId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `siteId` int(11) unsigned NOT NULL,
  `appId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`siteAppId`),
  KEY `siteId` (`siteId`),
  KEY `appId` (`appId`),
  CONSTRAINT `site_apps_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE,
  CONSTRAINT `site_apps_ibfk_2` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_apps`
--

LOCK TABLES `site_apps` WRITE;
/*!40000 ALTER TABLE `site_apps` DISABLE KEYS */;
INSERT INTO `site_apps` VALUES (130,1,1),(131,1,2),(132,1,5),(133,1,6),(134,1,7),(135,1,25),(136,1,26),(137,1,27),(144,1,30);
/*!40000 ALTER TABLE `site_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sites` (
  `siteId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isDefault` int(2) DEFAULT '0',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `themeId` int(11) unsigned NOT NULL DEFAULT '1',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`siteId`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

LOCK TABLES `sites` WRITE;
/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
INSERT INTO `sites` VALUES (1,'Lets Talk Bitcoin','coinfire-dev.com',1,'http://coinfire-dev.com/labs/coinfire/www',1,'1-f06fa465a97db082c010e0dc2f3553f784dc9748ee1b69b8fac352a195ca1da7.jpg');
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats` (
  `statId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `statKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `statValue` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`statId`),
  KEY `statKey` (`statKey`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stats`
--

LOCK TABLES `stats` WRITE;
/*!40000 ALTER TABLE `stats` DISABLE KEYS */;
INSERT INTO `stats` VALUES (1,'mostOnline','448'),(2,'_hits','5983');
/*!40000 ALTER TABLE `stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_categories`
--

DROP TABLE IF EXISTS `store_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_categories` (
  `categoryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parentId` int(11) unsigned DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `active` int(2) DEFAULT '0',
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `store_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_categories`
--

LOCK TABLES `store_categories` WRITE;
/*!40000 ALTER TABLE `store_categories` DISABLE KEYS */;
INSERT INTO `store_categories` VALUES (1,'Shirts & Sweaters','shirts-sweaters',0,0,1,'',1),(2,'Misc Merchandise','misc-merchandise',0,10,1,'',1),(3,'Hats','hats',0,5,1,'',1),(4,'T-Shirts','t-shirts',1,0,1,'',1),(5,'Hoodies','hoodies',1,10,1,'',1);
/*!40000 ALTER TABLE `store_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_productCats`
--

DROP TABLE IF EXISTS `store_productCats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_productCats` (
  `productCatId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(11) unsigned NOT NULL,
  `categoryId` int(11) unsigned NOT NULL,
  `rank` int(11) DEFAULT '0',
  PRIMARY KEY (`productCatId`),
  KEY `productId` (`productId`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `store_productCats_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `store_products` (`productId`) ON DELETE CASCADE,
  CONSTRAINT `store_productCats_ibfk_2` FOREIGN KEY (`categoryId`) REFERENCES `store_categories` (`categoryId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_productCats`
--

LOCK TABLES `store_productCats` WRITE;
/*!40000 ALTER TABLE `store_productCats` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_productCats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_productImages`
--

DROP TABLE IF EXISTS `store_productImages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_productImages` (
  `imageId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `productId` int(11) unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cover` int(2) DEFAULT '0',
  PRIMARY KEY (`imageId`),
  KEY `productId` (`productId`),
  CONSTRAINT `store_productImages_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `store_products` (`productId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_productImages`
--

LOCK TABLES `store_productImages` WRITE;
/*!40000 ALTER TABLE `store_productImages` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_productImages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_products`
--

DROP TABLE IF EXISTS `store_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_products` (
  `productId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `basePrice` decimal(20,8) DEFAULT '0.00000000',
  `description` longtext COLLATE utf8_unicode_ci,
  `active` int(2) DEFAULT '0',
  `inStock` int(2) DEFAULT '0',
  `siteId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`productId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `store_products_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_products`
--

LOCK TABLES `store_products` WRITE;
/*!40000 ALTER TABLE `store_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themes` (
  `themeId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` int(2) DEFAULT '0',
  PRIMARY KEY (`themeId`),
  KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES (1,'Lets Talk Bitcoin 2.0','ltb',1);
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `token_access`
--

DROP TABLE IF EXISTS `token_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `token_access` (
  `accessId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `moduleId` int(11) unsigned NOT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `itemType` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permId` int(11) unsigned DEFAULT '0',
  `asset` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `op` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stackOp` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'AND',
  `stackOrder` int(11) DEFAULT '0',
  `overrideable` int(1) DEFAULT '0',
  PRIMARY KEY (`accessId`),
  KEY `userId` (`userId`),
  KEY `moduleId` (`moduleId`),
  KEY `itemId` (`itemId`),
  KEY `itemType` (`itemType`),
  KEY `permId` (`permId`),
  KEY `asset` (`asset`),
  CONSTRAINT `token_access_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `token_access_ibfk_2` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token_access`
--

LOCK TABLES `token_access` WRITE;
/*!40000 ALTER TABLE `token_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `token_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_likes`
--

DROP TABLE IF EXISTS `user_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_likes` (
  `likeId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `itemId` int(11) unsigned NOT NULL,
  `type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `likeTime` datetime DEFAULT NULL,
  `score` decimal(20,8) DEFAULT NULL,
  `opUser` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`likeId`),
  KEY `userId` (`userId`),
  KEY `itemId` (`itemId`),
  KEY `type` (`type`),
  KEY `opUser` (`opUser`),
  CONSTRAINT `user_likes_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=214632 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_likes`
--

LOCK TABLES `user_likes` WRITE;
/*!40000 ALTER TABLE `user_likes` DISABLE KEYS */;
INSERT INTO `user_likes` VALUES (43,79,2868,'post',NULL,4.54000000,672),(44,79,2859,'post',NULL,0.05000000,38),(97,79,285,'topic',NULL,0.05000000,38),(136,79,3051,'post',NULL,5.00000000,679),(258,79,345,'topic',NULL,0.05000000,38),(336,79,3792,'post',NULL,5.00000000,879),(370,79,3706,'post',NULL,5.00000000,394),(392,79,3927,'post',NULL,4.54000000,672),(484,79,4036,'post',NULL,0.05000000,38),(501,79,4180,'post',NULL,5.00000000,587),(505,79,4195,'post',NULL,0.05000000,38),(548,79,4243,'post',NULL,5.00000000,604),(584,79,4333,'post',NULL,5.00000000,438),(585,79,4325,'post',NULL,5.00000000,608),(726,79,4540,'post',NULL,5.00000000,826),(728,79,4554,'post',NULL,5.00000000,604),(880,79,4895,'post',NULL,4.16000000,586),(979,79,5097,'post',NULL,5.00000000,909),(999,79,5153,'post',NULL,5.00000000,929),(1287,79,5696,'post',NULL,1.00000000,683),(1339,79,5782,'post',NULL,5.00000000,512),(1343,79,5734,'post',NULL,4.46000000,611),(1470,79,5575,'post',NULL,0.05000000,533),(1541,79,6114,'post',NULL,0.05000000,533),(1563,79,6134,'post',NULL,3.63000000,454),(1758,79,6477,'post','2014-07-18 14:47:39',5.00000000,602),(1802,79,6434,'post','2014-07-18 18:35:01',0.00000000,79),(1803,79,6501,'post','2014-07-18 18:35:43',4.87000000,497),(1830,79,6544,'post','2014-07-18 20:19:02',0.05000000,1124),(1891,79,6617,'post','2014-07-19 09:41:52',5.00000000,671),(1913,79,572,'topic','2014-07-19 13:53:30',5.00000000,847),(2265,79,623,'topic','2014-07-22 12:21:07',5.00000000,1103),(2267,79,7419,'post','2014-07-22 12:39:34',5.00000000,1206),(2299,79,7529,'post','2014-07-22 18:05:40',5.00000000,243),(2300,79,7526,'post','2014-07-22 18:05:46',5.00000000,538),(2497,79,8070,'post','2014-07-24 12:06:17',5.00000000,204),(2535,79,8222,'post','2014-07-24 21:39:32',0.00000000,79),(2641,79,8001,'post','2014-07-25 13:09:38',5.00000000,730),(2736,79,8714,'post','2014-07-26 16:44:18',4.99000000,1297),(2758,79,8785,'post','2014-07-26 20:12:34',5.00000000,40),(2761,79,8794,'post','2014-07-26 20:32:32',5.00000000,1393),(2808,79,8955,'post','2014-07-27 09:02:45',5.00000000,40),(2903,79,9018,'post','2014-07-28 08:51:45',4.51000000,768),(2908,79,9402,'post','2014-07-28 09:32:04',5.00000000,952),(3002,79,9611,'post','2014-07-28 19:24:26',0.05000000,659),(3023,79,9673,'post','2014-07-28 22:56:10',5.00000000,442),(3071,79,799,'topic','2014-07-29 11:01:17',0.00000000,79),(3084,79,9889,'post','2014-07-29 13:46:46',5.00000000,1367),(3089,79,9895,'post','2014-07-29 14:22:41',5.00000000,1569),(3721,79,10605,'post','2014-07-31 15:06:48',4.14000000,1495),(3750,79,10814,'post','2014-07-31 19:12:09',4.14000000,1495),(4177,79,11308,'post','2014-08-01 22:39:32',5.00000000,1715),(4405,79,917,'topic','2014-08-02 09:29:20',5.00000000,1386),(4461,79,924,'topic','2014-08-02 14:56:16',1.96000000,894),(5249,79,12447,'post','2014-08-04 18:24:48',5.00000000,1639),(5275,79,12542,'post','2014-08-04 20:43:11',5.00000000,1568),(5987,79,1021,'topic','2014-08-05 17:58:22',5.00000000,40),(7327,79,13852,'post','2014-08-07 23:58:09',5.00000000,219),(7331,79,13848,'post','2014-08-07 23:59:56',5.00000000,605),(7769,79,13874,'post','2014-08-08 08:32:12',5.00000000,1906),(8041,79,575,'topic','2014-08-08 21:35:48',5.00000000,563),(8638,79,14914,'post','2014-08-09 18:12:09',0.05000000,1838),(8648,79,14973,'post','2014-08-09 21:14:10',5.00000000,1997),(9338,79,14734,'post','2014-08-10 09:26:15',4.55000000,282),(9485,79,15543,'post','2014-08-10 15:26:51',5.00000000,1206),(10454,79,16343,'post','2014-08-11 18:31:08',5.00000000,2102),(10459,79,16341,'post','2014-08-11 19:00:51',0.05000000,533),(10472,79,16369,'post','2014-08-11 19:59:26',5.00000000,220),(10863,79,16452,'post','2014-08-12 06:45:59',5.00000000,204),(10961,79,16767,'post','2014-08-12 08:39:50',5.00000000,2062),(11222,79,17029,'post','2014-08-12 22:34:27',5.00000000,735),(13583,79,1208,'topic','2014-08-15 09:19:04',0.00000000,79),(17858,79,1491,'topic','2014-08-20 18:18:28',5.00000000,40),(19206,79,22205,'post','2014-08-21 23:35:45',4.14000000,1495),(20076,79,22728,'post','2014-08-22 08:51:20',4.46000000,611),(21592,79,1591,'topic','2014-08-23 15:07:36',5.00000000,119),(23355,79,24211,'post','2014-08-24 19:20:54',4.14000000,1495),(26078,79,1883,'topic','2014-08-26 07:59:24',5.00000000,1804),(26506,79,26434,'post','2014-08-26 10:58:23',4.16000000,586),(28107,79,27483,'post','2014-08-27 07:59:48',5.00000000,1285),(28310,79,27644,'post','2014-08-27 09:40:44',4.81000000,877),(30371,79,2070,'topic','2014-08-28 08:59:57',0.05000000,82),(31294,79,29550,'post','2014-08-28 17:08:32',5.00000000,1346),(32766,79,29969,'post','2014-08-29 08:49:44',4.98000000,2000),(32811,79,30416,'post','2014-08-29 09:20:22',0.05000000,82),(32873,79,30479,'post','2014-08-29 10:53:32',4.46000000,990),(33332,79,2118,'topic','2014-08-29 22:37:45',2.61000000,480),(35563,79,32507,'post','2014-08-31 10:53:30',5.00000000,2390),(35637,79,32581,'post','2014-08-31 12:43:45',5.00000000,3434),(35644,79,32625,'post','2014-08-31 12:53:32',4.42000000,800),(35646,79,32641,'post','2014-08-31 13:00:41',5.00000000,2081),(37289,79,34004,'post','2014-09-01 07:39:25',0.05000000,659),(37301,79,34055,'post','2014-09-01 07:42:18',4.59000000,2373),(37334,79,33386,'post','2014-09-01 08:00:59',5.00000000,1152),(37362,79,34116,'post','2014-09-01 08:28:53',5.00000000,1973),(39806,79,20067,'post','2014-09-01 22:47:26',4.14000000,1495),(39807,79,20236,'post','2014-09-01 22:47:32',5.00000000,1554),(39808,79,20626,'post','2014-09-01 22:47:47',4.16000000,586),(39809,79,28,'topic','2014-09-01 22:48:31',0.05000000,38),(39810,79,74,'post','2014-09-01 22:48:39',0.05000000,38),(40728,79,35278,'post','2014-09-02 08:32:15',5.00000000,2596),(40752,79,2106,'topic','2014-09-02 08:48:15',4.45000000,1005),(40754,79,35048,'post','2014-09-02 08:52:33',5.00000000,501),(40757,79,31546,'post','2014-09-02 08:59:36',5.00000000,1673),(40779,79,35409,'post','2014-09-02 09:24:35',5.00000000,501),(41109,79,35527,'post','2014-09-02 10:19:53',5.00000000,3434),(41112,79,35531,'post','2014-09-02 10:23:18',5.00000000,631),(42040,79,35884,'post','2014-09-02 17:07:05',0.05000000,38),(42065,79,35897,'post','2014-09-02 17:25:02',5.00000000,1206),(45315,79,36351,'post','2014-09-03 09:54:16',5.00000000,2780),(45316,79,36735,'post','2014-09-03 09:54:44',5.00000000,1568),(45318,79,36844,'post','2014-09-03 09:56:52',0.05000000,38),(45319,79,36278,'post','2014-09-03 09:58:15',5.00000000,631),(51518,79,38676,'post','2014-09-05 08:36:12',4.99000000,1268),(52135,79,32025,'post','2014-09-05 18:37:56',5.00000000,1409),(52137,79,39331,'post','2014-09-05 18:47:59',1.00000000,683),(52189,79,39384,'post','2014-09-05 20:41:09',0.05000000,124),(55320,79,2535,'topic','2014-09-06 13:12:04',5.00000000,1146),(57081,79,41073,'post','2014-09-07 14:26:13',4.96000000,1188),(59966,79,43000,'post','2014-09-09 08:11:21',5.00000000,3972),(64538,79,44431,'post','2014-09-10 19:29:31',5.00000000,2171),(64542,79,44514,'post','2014-09-10 20:10:11',4.54000000,672),(66154,79,45367,'post','2014-09-11 09:48:36',4.54000000,672),(66232,79,45424,'post','2014-09-11 11:51:12',5.00000000,501),(66422,79,45532,'post','2014-09-11 19:11:45',5.00000000,2863),(73747,79,49575,'post','2014-09-15 08:25:11',5.00000000,1804),(76156,79,46454,'post','2014-09-16 08:29:01',1.00000000,683),(78596,79,52818,'post','2014-09-17 16:29:13',0.05000000,91),(78620,79,52900,'post','2014-09-17 18:33:09',5.00000000,1903),(78713,79,53040,'post','2014-09-17 21:53:14',5.00000000,204),(78714,79,53033,'post','2014-09-17 21:54:44',0.05000000,1838),(79070,79,53191,'post','2014-09-18 00:23:08',4.69000000,1116),(80259,79,53336,'post','2014-09-18 08:30:38',4.99000000,1653),(80846,79,54134,'post','2014-09-18 15:51:28',5.00000000,311),(82674,79,55049,'post','2014-09-19 10:01:08',5.00000000,3847),(83237,79,55296,'post','2014-09-19 22:57:26',0.05000000,1838),(84189,79,55674,'post','2014-09-20 09:34:41',0.05000000,1838),(85368,79,56802,'post','2014-09-21 10:45:57',4.16000000,586),(87998,79,57786,'post','2014-09-23 09:19:29',4.13000000,1156),(89825,79,59811,'post','2014-09-24 11:03:04',4.76000000,618),(90052,79,54378,'post','2014-09-24 13:16:24',5.00000000,2081),(90085,79,3387,'topic','2014-09-24 18:51:55',5.00000000,506),(91404,79,60581,'post','2014-09-25 08:59:19',4.54000000,672),(93877,79,62284,'post','2014-09-26 18:56:07',4.54000000,672),(93919,79,62318,'post','2014-09-26 22:05:30',5.00000000,1333),(94471,79,62993,'post','2014-09-27 08:18:20',4.59000000,5439),(94485,79,3455,'topic','2014-09-27 09:31:38',0.05000000,74),(94486,79,63037,'post','2014-09-27 09:37:52',5.00000000,360),(94489,79,63042,'post','2014-09-27 09:42:53',4.54000000,672),(94756,79,3549,'topic','2014-09-27 16:05:37',5.00000000,360),(97001,79,64881,'post','2014-09-29 09:27:47',1.00000000,683),(97005,79,64716,'post','2014-09-29 09:29:54',0.05000000,91),(97012,79,63355,'post','2014-09-29 09:39:23',4.45000000,1005),(103042,79,3769,'topic','2014-10-03 01:17:35',0.05000000,91),(105614,79,70462,'post','2014-10-05 15:56:11',5.00000000,2253),(105654,79,63170,'post','2014-10-05 18:56:58',5.00000000,1561),(106788,79,70925,'post','2014-10-06 09:58:57',5.00000000,5285),(107153,79,71440,'post','2014-10-06 22:48:50',4.46000000,990),(107948,79,4017,'topic','2014-10-07 21:23:44',4.46000000,990),(109222,79,41586,'post','2014-10-09 09:27:07',5.00000000,1680),(109223,79,72917,'post','2014-10-09 09:28:23',4.29000000,457),(109792,79,4068,'topic','2014-10-09 20:03:47',0.05000000,55),(109888,79,73464,'post','2014-10-09 23:27:07',5.00000000,5280),(111973,79,74788,'post','2014-10-11 08:01:43',0.05000000,91),(111988,79,74866,'post','2014-10-11 09:14:45',4.54000000,672),(114950,79,4182,'topic','2014-10-13 11:14:11',5.00000000,40),(117278,79,78817,'post','2014-10-15 09:17:41',5.00000000,6303),(117382,79,78978,'post','2014-10-15 20:01:24',0.05000000,533),(118268,79,79495,'post','2014-10-16 08:33:21',0.05000000,533),(118515,79,79685,'post','2014-10-16 12:43:25',0.05000000,55),(121628,79,81653,'post','2014-10-18 10:06:34',5.00000000,6558),(121774,79,81794,'post','2014-10-18 14:11:25',5.00000000,220),(122133,79,81976,'post','2014-10-19 08:29:04',4.51000000,768),(122200,79,82252,'post','2014-10-19 10:23:57',5.00000000,1386),(122227,79,4226,'topic','2014-10-19 11:44:47',4.99000000,424),(122228,79,82279,'post','2014-10-19 11:45:06',5.00000000,1011),(123400,79,82540,'post','2014-10-20 07:56:23',5.00000000,2562),(124582,79,83441,'post','2014-10-21 08:51:10',4.59000000,2373),(124693,79,4611,'topic','2014-10-21 09:25:58',5.00000000,7020),(125391,79,4717,'topic','2014-10-22 02:01:07',0.00000000,79),(127449,79,84710,'post','2014-10-23 10:35:52',5.00000000,1903),(127450,79,326,'topic','2014-10-23 10:41:58',0.05000000,82),(127451,79,4737,'topic','2014-10-23 10:50:37',5.00000000,1903),(127684,79,84844,'post','2014-10-23 20:28:05',5.00000000,1903),(128492,79,4817,'topic','2014-10-24 08:20:13',4.54000000,672),(129124,79,85857,'post','2014-10-25 14:56:07',4.54000000,672),(129168,79,85905,'post','2014-10-25 18:53:28',5.00000000,6478),(130085,79,85903,'post','2014-10-26 10:27:24',5.00000000,6478),(131643,79,86579,'post','2014-10-27 10:08:19',5.00000000,6478),(131695,79,87077,'post','2014-10-27 10:24:17',5.00000000,7216),(131817,79,87113,'post','2014-10-27 10:48:21',5.00000000,7216),(133474,79,87282,'post','2014-10-28 08:49:09',5.00000000,220),(133475,79,87765,'post','2014-10-28 08:49:39',5.00000000,1058),(136080,79,4985,'topic','2014-10-29 09:50:59',5.00000000,7172),(136135,79,88745,'post','2014-10-29 10:04:09',5.00000000,420),(136907,79,4954,'topic','2014-10-29 20:16:31',0.87000000,636),(141597,79,51921,'post','2014-11-02 00:55:53',4.98000000,1650),(142134,79,5131,'topic','2014-11-02 09:21:49',5.00000000,7264),(142136,79,91377,'post','2014-11-02 09:28:14',0.05000000,1124),(142455,79,91624,'post','2014-11-02 14:31:51',0.87000000,636),(142475,79,5142,'topic','2014-11-02 18:36:26',5.00000000,7264),(143019,79,5144,'topic','2014-11-03 08:40:27',5.00000000,5336),(145946,79,92990,'post','2014-11-05 09:30:11',5.00000000,420),(147514,79,93104,'post','2014-11-06 10:07:54',4.87000000,497),(147515,79,93401,'post','2014-11-06 10:16:50',4.46000000,990),(148330,79,93617,'post','2014-11-07 14:12:13',5.00000000,1836),(148331,79,93628,'post','2014-11-07 14:12:19',1.00000000,683),(148638,79,93820,'post','2014-11-08 01:43:04',5.00000000,501),(148901,79,93885,'post','2014-11-08 10:13:08',4.93000000,1403),(149176,79,93985,'post','2014-11-08 13:31:11',5.00000000,420),(149986,79,94031,'post','2014-11-09 03:10:31',5.00000000,1423),(154162,79,4999,'topic','2014-11-10 00:05:41',5.00000000,7322),(154300,79,94522,'post','2014-11-10 00:32:07',5.00000000,631),(156411,79,94430,'post','2014-11-10 09:42:39',5.00000000,5518),(166046,79,95739,'post','2014-11-11 15:31:51',5.00000000,420),(170282,79,5483,'topic','2014-11-12 18:13:19',5.00000000,501),(183153,79,98123,'post','2014-11-16 22:12:56',5.00000000,7269),(183154,79,95019,'post','2014-11-16 22:13:33',5.00000000,220),(183155,79,98119,'post','2014-11-16 22:13:39',5.00000000,3838),(183156,79,5604,'topic','2014-11-16 22:13:48',0.87000000,636),(183157,79,97877,'post','2014-11-16 22:16:51',4.93000000,1403),(187011,79,98557,'post','2014-11-17 11:49:46',5.00000000,311),(197790,79,99677,'post','2014-11-19 08:56:23',0.05000000,533),(197791,79,99626,'post','2014-11-19 08:56:41',4.87000000,497),(197798,79,99466,'post','2014-11-19 08:57:20',4.46000000,990),(204477,79,99881,'post','2014-11-20 19:34:47',0.05000000,55),(204478,79,100451,'post','2014-11-20 19:59:16',5.00000000,8388),(204674,79,100462,'post','2014-11-20 20:54:59',5.00000000,2253),(206240,79,100649,'post','2014-11-21 11:00:21',4.54000000,672),(207748,79,100684,'post','2014-11-22 11:25:49',0.05000000,533),(209333,79,5786,'topic','2014-11-23 10:59:48',5.00000000,1068),(212309,79,5676,'topic','2014-11-24 09:33:39',0.05000000,91),(212744,79,101857,'post','2014-11-25 08:06:11',4.97000000,3380),(212749,79,102003,'post','2014-11-25 11:02:34',0.05000000,533),(212783,79,102289,'post','2014-11-26 08:50:07',0.05000000,533),(212784,79,102078,'post','2014-11-26 08:50:09',5.00000000,3838),(212928,79,102716,'post','2014-11-27 12:48:11',5.00000000,8541),(212929,79,102436,'post','2014-11-27 12:49:43',5.00000000,7269),(212933,79,102816,'post','2014-11-30 08:05:51',5.00000000,1219),(212934,79,102731,'post','2014-11-30 16:22:51',4.50000000,713),(212937,79,102958,'post','2014-12-01 09:07:23',5.00000000,8418),(212938,79,103105,'post','2014-12-01 09:08:04',0.05000000,91),(212939,79,102894,'post','2014-12-01 09:09:43',5.00000000,6016),(212940,79,102895,'post','2014-12-01 09:10:33',5.00000000,1476),(212989,79,62001,'post','2014-12-02 13:49:35',5.00000000,1309),(212990,79,62015,'post','2014-12-02 13:49:37',5.00000000,1309),(213075,79,103905,'post','2014-12-03 21:58:32',0.05000000,1124),(213212,79,104201,'post','2014-12-04 08:08:34',5.00000000,6915),(213669,79,104468,'post','2014-12-05 07:36:41',5.00000000,506),(213872,79,104606,'post','2014-12-06 08:46:18',5.00000000,8657),(213873,79,104570,'post','2014-12-06 08:46:32',5.00000000,1815),(213964,79,1502,'topic','2014-12-06 22:19:09',5.00000000,1627),(213965,79,47279,'post','2014-12-06 22:19:25',5.00000000,1627),(213966,79,46878,'post','2014-12-06 22:19:28',5.00000000,1627),(213967,79,104655,'post','2014-12-07 00:43:40',4.85000000,890),(214160,79,104749,'post','2014-12-07 19:53:19',5.00000000,1903),(214239,79,104893,'post','2014-12-08 08:50:40',5.00000000,1403),(214525,79,6045,'topic','2014-12-09 08:57:46',5.00000000,67),(214551,79,105250,'post','2014-12-10 02:09:20',5.00000000,3640),(214553,79,105295,'post','2014-12-10 02:22:29',5.00000000,1290),(214554,79,105270,'post','2014-12-10 02:23:11',5.00000000,1290),(214591,79,105379,'post','2014-12-10 16:04:26',5.00000000,8657),(214631,79,105684,'post','2014-12-11 12:43:37',4.24000000,794);
/*!40000 ALTER TABLE `user_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_meta`
--

DROP TABLE IF EXISTS `user_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_meta` (
  `metaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `metaKey` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `metaValue` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`metaId`),
  KEY `userId` (`userId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `user_meta_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68208 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_meta`
--

LOCK TABLES `user_meta` WRITE;
/*!40000 ALTER TABLE `user_meta` DISABLE KEYS */;
INSERT INTO `user_meta` VALUES (165,79,'IP_ADDRESS','127.0.0.1'),(166,79,'site_registered','ltb2.com'),(167,79,'pubProf','1'),(168,79,'last_attempt','2014-12-11 16:37:01'),(169,79,'login_attempts','0'),(170,79,'num_logins','247'),(180,79,'showEmail','1'),(181,79,'avatar',''),(233,79,'emailNotify','1'),(7650,79,'ref-link','41f33484'),(13708,79,'boardFilters','60,61,62,64,68,70,71,73,75,77,80,53,54,55,28,25,26,29,40,41,35,56,36,37,38,43,44,79,39,46,27,33,30,34,42,58'),(15697,79,'lastBalanceCheck','2014-12-11 16:34:45'),(39252,79,'site_referral','whit3r4bbi7'),(39609,79,'boardAntiFilters','31,32,45,47,48,49,50,51,52,57,59,63,69,72,74,76,78,81'),(65165,79,'article-credit-deposit-address','1P8oTKsv4qLA2eBeKRGzjipJc81dsWJQRp'),(65166,79,'article-credits','0'),(65167,79,'article-credit-deposit-change','500');
/*!40000 ALTER TABLE `user_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `noteId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `message` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `noteDate` datetime NOT NULL,
  `isRead` int(2) unsigned DEFAULT '0',
  `itemId` int(11) unsigned DEFAULT '0',
  `type` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`noteId`),
  KEY `userId` (`userId`),
  KEY `itemId` (`itemId`),
  KEY `type` (`type`),
  CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=657984 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profileVals`
--

DROP TABLE IF EXISTS `user_profileVals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profileVals` (
  `profileValId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `fieldId` int(11) unsigned NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`profileValId`),
  KEY `userId` (`userId`),
  KEY `fieldId` (`fieldId`),
  CONSTRAINT `user_profileVals_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `user_profileVals_ibfk_2` FOREIGN KEY (`fieldId`) REFERENCES `profile_fields` (`fieldId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20022 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profileVals`
--

LOCK TABLES `user_profileVals` WRITE;
/*!40000 ALTER TABLE `user_profileVals` DISABLE KEYS */;
INSERT INTO `user_profileVals` VALUES (56,79,3,'1Nsv3sX8ELmwtfEQxcEQtA3TbwyXzAaMfN','2014-12-11 17:03:32'),(57,79,7,'Canada','2014-12-11 17:03:31'),(58,79,6,'Leader of the Auto Bots','2014-12-11 17:03:32'),(59,79,9,'Optimus Prime','2014-12-11 17:03:31'),(60,79,11,'~ Prime','2014-12-11 17:03:32'),(262,79,14,'','2014-12-11 17:03:32'),(263,79,12,'15fx1Gqe4KodZvyzN6VUSkEmhCssrM1yD7','2014-12-11 17:04:17'),(264,79,13,'','2014-06-15 15:58:30');
/*!40000 ALTER TABLE `user_profileVals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_referrals`
--

DROP TABLE IF EXISTS `user_referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_referrals` (
  `referralId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `affiliateId` int(11) unsigned NOT NULL,
  `refTime` datetime DEFAULT NULL,
  PRIMARY KEY (`referralId`),
  UNIQUE KEY `userId_2` (`userId`),
  KEY `userId` (`userId`),
  KEY `affiliateId` (`affiliateId`),
  CONSTRAINT `user_referrals_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `user_referrals_ibfk_2` FOREIGN KEY (`affiliateId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4367 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_referrals`
--

LOCK TABLES `user_referrals` WRITE;
/*!40000 ALTER TABLE `user_referrals` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_referrals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7430 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES (516,79,'70263300c7da6f9e8781ddbb0911330ed64e18f81bce00283e844c0f9a277733','127.0.0.1','2014-12-06 16:33:15','2014-12-08 09:25:16'),(4756,79,'1341d81a9d5f50e2bdf46edb6f454b6b8425affa2815a2c11cfc9ab2b99e8ac8','173.180.232.183','2014-12-07 16:26:45','2014-12-08 15:33:27'),(5169,79,'9f3e41b311cae15662e5935aa24358a5885878fb00f5b632603535061ca73d6b','173.180.232.183','2014-12-08 09:30:28','2014-12-09 12:16:10'),(6346,79,'f37d86f9c99c5cf815ac86c797dfdb586971b32ebc9b5f32b65ad7d50d5f5378','173.180.232.183','2014-12-09 13:39:49','2014-12-09 13:50:07'),(6409,79,'113403a8902eb4fa5375473db8d8b0cac68aed19fd0ea95f9f013e3c40348361','173.180.232.183','2014-12-09 17:05:08','2014-12-09 19:22:44'),(6558,79,'7e3ad5d7114c68588895dabd0d03d9511af70ddcc933daa36e82fb0755c39e18','173.180.232.183','2014-12-10 01:44:08','2014-12-11 14:48:39'),(7429,79,'f6e61ab364279d20d9f8059c0643a7b1b849a42bff0322f38579ff768cdeee28','127.0.0.1','2014-12-11 16:37:02','2014-12-11 17:22:52');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `spice` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regDate` datetime DEFAULT NULL,
  `auth` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastAuth` datetime DEFAULT NULL,
  `lastActive` datetime DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activated` int(2) DEFAULT '0',
  `activate_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`userId`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `auth` (`auth`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8759 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (79,'admin','155d1f3bebdb3be51ca0cff7f50a923f0fd3e047046f9f85c3085e822130a3d2','fd94aab9e2ab8efa4e5aaefbc0dec2ba23b87f6df9abd2730b700ad744136c8f76f9eef77b','admin@example.com','2014-04-23 23:04:23','70263300c7da6f9e8781ddbb0911330ed64e18f81bce00283e844c0f9a277733','2014-12-11 16:37:02','2014-12-11 17:22:52','admin',1,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcp_assetCache`
--

DROP TABLE IF EXISTS `xcp_assetCache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcp_assetCache` (
  `assetId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `divisible` int(2) DEFAULT '0',
  `lastChecked` datetime DEFAULT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerId` int(11) unsigned DEFAULT '0',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`assetId`),
  KEY `asset` (`asset`),
  KEY `ownerId` (`ownerId`)
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcp_assetCache`
--

LOCK TABLES `xcp_assetCache` WRITE;
/*!40000 ALTER TABLE `xcp_assetCache` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcp_assetCache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcp_balances`
--

DROP TABLE IF EXISTS `xcp_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcp_balances` (
  `balanceId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `addressId` int(11) unsigned NOT NULL,
  `asset` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `balance` decimal(20,8) DEFAULT '0.00000000',
  `lastChecked` datetime DEFAULT NULL,
  PRIMARY KEY (`balanceId`),
  KEY `addressId` (`addressId`),
  KEY `asset` (`asset`),
  CONSTRAINT `xcp_balances_ibfk_1` FOREIGN KEY (`addressId`) REFERENCES `coin_addresses` (`addressId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5607 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcp_balances`
--

LOCK TABLES `xcp_balances` WRITE;
/*!40000 ALTER TABLE `xcp_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcp_balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xcp_distribute`
--

DROP TABLE IF EXISTS `xcp_distribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xcp_distribute` (
  `distributeId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `addressList` longtext COLLATE utf8_unicode_ci,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `complete` int(2) DEFAULT '0',
  `txInfo` longtext COLLATE utf8_unicode_ci,
  `initDate` datetime DEFAULT NULL,
  `completeDate` datetime DEFAULT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asset` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fee` decimal(20,8) DEFAULT NULL,
  `userId` int(11) unsigned DEFAULT '0',
  `tokenReceived` decimal(20,8) DEFAULT '0.00000000',
  `feeReceived` decimal(20,8) DEFAULT '0.00000000',
  `divisible` int(2) DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valueType` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'fixed',
  `currentBatch` int(11) DEFAULT '1',
  PRIMARY KEY (`distributeId`),
  UNIQUE KEY `address_2` (`address`),
  KEY `address` (`address`),
  KEY `account` (`account`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xcp_distribute`
--

LOCK TABLES `xcp_distribute` WRITE;
/*!40000 ALTER TABLE `xcp_distribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `xcp_distribute` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-11 20:09:38
