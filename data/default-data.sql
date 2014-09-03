-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tokenly
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.14.04.1

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
) ENGINE=InnoDB AUTO_INCREMENT=1128 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1127,'slick','e706269bdcef3aa2d1565e29e9003bee3912d11bfdaeb0f66cfef888e9a74afe','4e6d70d4f48d3100ae84ce2c86b94fafb985adc753911ea7d7c4893791014e8589d92c573c9e9a160fb3','test@test.com','2014-09-02 23:16:09','1f81cdfe0d4aa1082f0cb9779f5a958d1041158aee3185a0e7c57c93f12a962e','2014-09-02 23:17:50','2014-09-02 23:58:36','slick',1,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5312 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profileVals`
--

LOCK TABLES `user_profileVals` WRITE;
/*!40000 ALTER TABLE `user_profileVals` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_profileVals` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6702 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_meta`
--

LOCK TABLES `user_meta` WRITE;
/*!40000 ALTER TABLE `user_meta` DISABLE KEYS */;
INSERT INTO `user_meta` VALUES (6697,1127,'last_attempt','2014-09-02 23:17:50'),(6698,1127,'login_attempts','0'),(6699,1127,'login_attempts','0'),(6700,1127,'num_logins','1'),(6701,1127,'ref-link','78eca97b');
/*!40000 ALTER TABLE `user_meta` ENABLE KEYS */;
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
INSERT INTO `settings` VALUES (1,'systemDisabled','0','System Maintenance Mode?',1,0),(2,'disabledMessage','Performing some quick website upgrades, check back in a few minutes!','System Maintenance Message',0,1);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_fields`
--

LOCK TABLES `profile_fields` WRITE;
/*!40000 ALTER TABLE `profile_fields` DISABLE KEYS */;
INSERT INTO `profile_fields` VALUES (17,'textbox','','Bitcoin Tipping Address',1,1,20,1,'bitcoin-address',NULL),(18,'textbox','','Real Name',1,1,0,1,'real-name',NULL),(19,'select','< 1000\r\n1000 - 5000\r\n5000 - 8000\r\nOver 9000','Experience Level',1,1,50,1,'xp-level',NULL);
/*!40000 ALTER TABLE `profile_fields` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_fieldGroups`
--

LOCK TABLES `profile_fieldGroups` WRITE;
/*!40000 ALTER TABLE `profile_fieldGroups` DISABLE KEYS */;
INSERT INTO `profile_fieldGroups` VALUES (45,18,26),(46,17,26),(47,19,26);
/*!40000 ALTER TABLE `profile_fieldGroups` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (25,'Root Admin','root-admin',0,1),(26,'Default','default',1,1);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1756 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_users`
--

LOCK TABLES `group_users` WRITE;
/*!40000 ALTER TABLE `group_users` DISABLE KEYS */;
INSERT INTO `group_users` VALUES (1754,25,1127),(1755,26,1127);
/*!40000 ALTER TABLE `group_users` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_sites`
--

LOCK TABLES `group_sites` WRITE;
/*!40000 ALTER TABLE `group_sites` DISABLE KEYS */;
INSERT INTO `group_sites` VALUES (58,25,1),(59,26,1);
/*!40000 ALTER TABLE `group_sites` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1170 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_perms`
--

LOCK TABLES `group_perms` WRITE;
/*!40000 ALTER TABLE `group_perms` DISABLE KEYS */;
INSERT INTO `group_perms` VALUES (1137,1,25),(1138,2,25),(1139,3,25),(1140,4,25),(1141,5,25),(1142,6,25),(1143,8,25),(1144,9,25),(1145,10,25),(1146,11,25),(1147,13,25),(1148,14,25),(1149,15,25),(1150,16,25),(1151,17,25),(1152,18,25),(1153,19,25),(1154,20,25),(1155,21,25),(1156,22,25),(1157,23,25),(1158,24,25),(1159,25,25),(1160,26,25),(1161,27,25),(1162,28,25),(1163,29,25),(1164,30,25),(1165,31,25),(1166,32,25),(1167,33,25),(1168,34,25),(1169,35,25);
/*!40000 ALTER TABLE `group_perms` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1017 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_access`
--

LOCK TABLES `group_access` WRITE;
/*!40000 ALTER TABLE `group_access` DISABLE KEYS */;
INSERT INTO `group_access` VALUES (963,25,1),(964,25,2),(965,25,3),(966,25,4),(967,25,7),(968,25,8),(969,25,9),(970,25,10),(971,25,11),(972,25,12),(973,25,13),(974,25,14),(975,25,15),(976,25,16),(977,25,17),(978,25,18),(979,25,19),(980,25,20),(981,25,21),(982,25,22),(983,25,23),(984,25,24),(985,25,25),(986,25,26),(987,25,27),(988,25,28),(989,25,29),(990,25,30),(991,25,31),(992,25,32),(993,25,37),(994,25,38),(995,25,39),(996,25,40),(997,25,41),(998,25,42),(999,25,43),(1000,25,46),(1001,25,47),(1002,25,48),(1003,25,49),(1004,25,50),(1005,25,51),(1006,25,52),(1007,25,53),(1008,25,54),(1009,25,59),(1010,25,60),(1011,25,62),(1012,25,63),(1013,25,64),(1014,25,65),(1015,25,66),(1016,25,67);
/*!40000 ALTER TABLE `group_access` ENABLE KEYS */;
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
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `blog_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (35,'Breaking News','breaking-news',0,0,1,'');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2065 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postCategories`
--

LOCK TABLES `blog_postCategories` WRITE;
/*!40000 ALTER TABLE `blog_postCategories` DISABLE KEYS */;
INSERT INTO `blog_postCategories` VALUES (2064,460,35);
/*!40000 ALTER TABLE `blog_postCategories` ENABLE KEYS */;
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
  PRIMARY KEY (`postId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  KEY `siteId` (`siteId`),
  KEY `editedBy` (`editedBy`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=461 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (460,'An example blog post','an-example-blog-post','##Some Markdown\n\n**Bacon** ipsum dolor sit amet pork turducken chicken, shank salami chuck t-bone beef short ribs pastrami swine tri-tip. Shoulder beef ribs kielbasa beef andouille pork belly. Kevin drumstick doner ball tip. Beef hamburger sausage pork loin biltong. Short loin swine flank, pancetta capicola ham filet mignon ribeye meatball. Turkey boudin salami pancetta, pastrami short ribs biltong beef ribs turducken chicken kevin pork loin. Bacon tongue meatball shoulder beef ribs pig beef hamburger chicken kielbasa.\n\nHamburger sausage shank, jowl kielbasa salami drumstick porchetta fatback meatloaf tail corned beef. Venison drumstick brisket kielbasa ham hock turducken shank pig bresaola tongue sausage bacon meatball. Ground round swine brisket, drumstick pig kevin t-bone porchetta. Ground round fatback strip steak salami tail. Jerky tenderloin pork belly beef tongue.\n\nKielbasa ball tip hamburger, pork belly pancetta sirloin ribeye porchetta pork loin bresaola turducken shoulder. Pork belly ground round drumstick biltong landjaeger. Bresaola tri-tip cow pastrami meatloaf. Chuck doner pastrami bresaola boudin. Turkey shoulder tri-tip pork belly, pastrami venison filet mignon fatback beef ribs ribeye pancetta spare ribs biltong brisket salami. Doner sausage sirloin, turkey tail kielbasa swine strip steak cow turducken biltong beef meatball pastrami beef ribs.\n\nBoudin capicola turkey rump pancetta. Meatball filet mignon t-bone, cow tri-tip venison fatback hamburger frankfurter short loin sirloin turducken. Andouille pancetta pork loin capicola. Pork belly pig jerky drumstick pancetta.',1127,1,'2014-09-02 23:23:34','2014-09-02 23:21:54',1,NULL,'Hamburger sausage shank, jowl kielbasa salami drumstick porchetta fatback meatloaf tail corned beef. Venison drumstick brisket kielbasa ham hock turducken shank pig bresaola tongue sausage bacon meatball. Ground round swine brisket, drumstick pig kevin t-bone porchetta. Ground round fatback strip steak salami tail. Jerky tenderloin pork belly beef tongue.',1,0,NULL,0,0,NULL,'markdown','2014-09-02 23:58:23',0,'published');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_meta`
--

LOCK TABLES `app_meta` WRITE;
/*!40000 ALTER TABLE `app_meta` DISABLE KEYS */;
INSERT INTO `app_meta` VALUES (1,7,'postsPerPage','48','Posts Per Page','textbox',NULL,1),(4,7,'maxExcerpt','250','Max Post Excerpt Characters','textbox',NULL,1),(5,7,'enableComments','1','Enable Comments','bool',NULL,1),(21,2,'avatarWidth','150','Avatar Width (px)','textbox',NULL,1),(22,2,'avatarHeight','150','Avatar Height (px)','textbox',NULL,1),(23,2,'disableRegister','0','Disable New User Registration','bool',NULL,1),(24,25,'topicsPerPage','100','Topics Per Page','textbox',NULL,1),(25,25,'postsPerPage','10','Topics Replies (posts) Per Page','textbox',NULL,1),(26,25,'forum-title','The LTB Network Forum','Forum Title','textbox',NULL,1),(27,25,'forum-description','This is the home of the LTB content network, where audience and content creators gather to discuss and create great content.','Forum Description','textarea',NULL,1),(28,7,'featuredWidth','600','Featured Image Width (px)','textbox',NULL,1),(29,7,'featuredHeight','372','Featured Image Height (px)','textbox',NULL,1),(31,26,'blog-feed-title','Lets Talk Bitcoin!','Blog Feed Title','textbox',NULL,1),(32,26,'blog-feed-description','','Blog Feed Description','textarea',NULL,1),(33,27,'store-title','Lets Shop Bitcoin!','Store Title','textbox',NULL,1),(34,27,'productsPerPage','20','Products Per Page','textbox',NULL,1),(35,7,'coverWidth','400','Cover Image Width','textbox',NULL,1),(36,7,'coverHeight','400','Cover Image Height','textbox',NULL,1),(37,30,'distribute-fee','0.00001','Share Distributor - per address miner fee','textbox','',1),(38,30,'distribute-dust','0.000025','Share Distributor - dust output BTC value','textbox','',1),(39,30,'pop-comment-weight','4','PoP points per blog comment made','textbox','',1),(40,30,'pop-forum-post-weight','4','PoP points per forum post made','textbox','',1),(41,30,'pop-forum-topic-weight','4','PoP points per forum thread made','textbox','',1),(42,30,'pop-register-weight','2','PoP bonus points for new registrants','textbox','',1),(43,30,'pop-view-weight','1','PoP points per first page view','textbox','',1),(44,30,'distributor-decimals','2','Share Distributor - Round Values to x Decimals','textbox','',1),(45,30,'distribute-batch-size','25','Share Distributor - # Transactions per Batch','textbox','',1),(46,30,'pop-listen-weight','5','PoP - Proof of Listening Weight','textbox','',1),(47,30,'pol-word-expire','96','Proof of Listening - Magic Words expiration (in hours)','textbox','',1),(48,30,'pop-like-weight','0.5','PoP points per Like','textbox','',1),(49,30,'pop-referral-weight','10','PoP Points per Active Referral','textbox','',1),(52,30,'referral-min-active-pop','10','Min PoP per active referral','textbox','',1),(53,30,'pop-publish-weight','25','PoP points per published blog post','textbox','',1),(54,30,'pop-editor-cut','20','Editor PoP point distribution cut per article (%)','textbox','',1);
/*!40000 ALTER TABLE `app_meta` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `block_meta`
--

LOCK TABLES `block_meta` WRITE;
/*!40000 ALTER TABLE `block_meta` DISABLE KEYS */;
INSERT INTO `block_meta` VALUES (3,16,'inkpad-url','afjU3ZbD7i');
/*!40000 ALTER TABLE `block_meta` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1515 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMeta`
--

LOCK TABLES `blog_postMeta` WRITE;
/*!40000 ALTER TABLE `blog_postMeta` DISABLE KEYS */;
INSERT INTO `blog_postMeta` VALUES (1509,10,460,''),(1510,12,460,''),(1511,11,460,''),(1512,14,460,''),(1513,16,460,'9FOHe8GXUl'),(1514,15,460,'lRDuA1rSLz');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMetaTypes`
--

LOCK TABLES `blog_postMetaTypes` WRITE;
/*!40000 ALTER TABLE `blog_postMetaTypes` DISABLE KEYS */;
INSERT INTO `blog_postMetaTypes` VALUES (10,'Bitcoin Tipping Address','tip-address','textbox','',1,0,1,1,0),(11,'Soundcloud Track ID','soundcloud-id','textbox','',1,10,1,1,0),(12,'Audio URL (overrides soundcloud url)','audio-url','textbox','',1,0,1,1,0),(13,'Audio Byte Length','audio-length','textbox','',1,0,1,1,1),(14,'Magic Word','magic-word','textbox','',1,40,1,0,0),(15,'Inkpad URL','inkpad-url','textbox','',1,50,1,0,1),(16,'Inkpad Excerpt URL','inkpad-excerpt-url','textbox','',1,0,1,0,1);
/*!40000 ALTER TABLE `blog_postMetaTypes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_blocks`
--

LOCK TABLES `content_blocks` WRITE;
/*!40000 ALTER TABLE `content_blocks` DISABLE KEYS */;
INSERT INTO `content_blocks` VALUES (16,'Footer Info','footer-info','Powered by *Tokenly*',1,1,'markdown');
/*!40000 ALTER TABLE `content_blocks` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (39,'About Us','about-us',1,'default','Kielbasa ball tip hamburger, pork belly pancetta sirloin ribeye porchetta pork loin bresaola turducken shoulder. Pork belly ground round drumstick biltong landjaeger. Bresaola tri-tip cow pastrami meatloaf. Chuck doner pastrami bresaola boudin. Turkey shoulder tri-tip pork belly, pastrami venison filet mignon fatback beef ribs ribeye pancetta spare ribs biltong brisket salami. Doner sausage sirloin, turkey tail kielbasa swine strip steak cow turducken biltong beef meatball pastrami beef ribs.\n\nBoudin capicola turkey rump pancetta. Meatball filet mignon t-bone, cow tri-tip venison fatback hamburger frankfurter short loin sirloin turducken. Andouille pancetta pork loin capicola. Pork belly pig jerky drumstick pancetta.','',1,'markdown');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
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
INSERT INTO `menus` VALUES (9,'Main Menu','main',1),(10,'Header Sub Menu','header-sub',1);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_pages`
--

LOCK TABLES `menu_pages` WRITE;
/*!40000 ALTER TABLE `menu_pages` DISABLE KEYS */;
INSERT INTO `menu_pages` VALUES (28,39,9,'About Us',10,0,0);
/*!40000 ALTER TABLE `menu_pages` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_links`
--

LOCK TABLES `menu_links` WRITE;
/*!40000 ALTER TABLE `menu_links` DISABLE KEYS */;
INSERT INTO `menu_links` VALUES (39,9,'/','Home',0,0,0),(40,9,'http://ltbcoin.com','LTBCOIN',20,0,0),(41,10,'http://reddit.com/r/bitcoin','REDDIT',0,0,0),(42,9,'/forum/board/all','FORUMS',30,0,0),(43,9,'/blog','BLOG',40,0,0);
/*!40000 ALTER TABLE `menu_links` ENABLE KEYS */;
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
  PRIMARY KEY (`boardId`),
  KEY `categoryId` (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `forum_boards_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_boards`
--

LOCK TABLES `forum_boards` WRITE;
/*!40000 ALTER TABLE `forum_boards` DISABLE KEYS */;
INSERT INTO `forum_boards` VALUES (48,11,'Introductions','introductions',0,'',1,1),(49,11,'Bitcoin','bitcoin',20,'',1,1),(50,11,'Off Topic','off-topic',30,'',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_categories`
--

LOCK TABLES `forum_categories` WRITE;
/*!40000 ALTER TABLE `forum_categories` DISABLE KEYS */;
INSERT INTO `forum_categories` VALUES (11,'LTB Forums','ltb-forums','',0,1);
/*!40000 ALTER TABLE `forum_categories` ENABLE KEYS */;
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
  PRIMARY KEY (`topicId`),
  KEY `boardId` (`boardId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE,
  CONSTRAINT `forum_topics_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=575 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topics`
--

LOCK TABLES `forum_topics` WRITE;
/*!40000 ALTER TABLE `forum_topics` DISABLE KEYS */;
INSERT INTO `forum_topics` VALUES (574,48,1127,'Hello','hello','hey everybody',0,'2014-09-02 23:41:30',NULL,'2014-09-02 23:41:54',0,0,NULL,0,0);
/*!40000 ALTER TABLE `forum_topics` ENABLE KEYS */;
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
  PRIMARY KEY (`postId`),
  KEY `userId` (`userId`),
  KEY `topicId` (`topicId`),
  CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `forum_posts_ibfk_3` FOREIGN KEY (`topicId`) REFERENCES `forum_topics` (`topicId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6521 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
INSERT INTO `forum_posts` VALUES (6520,1127,574,'test post',0,'2014-09-02 23:41:54',NULL,0);
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
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
  UNIQUE KEY `url` (`url`),
  KEY `moduleId` (`moduleId`),
  KEY `siteId` (`siteId`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `page_index_ibfk_1` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`),
  CONSTRAINT `page_index_ibfk_2` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_index`
--

LOCK TABLES `page_index` WRITE;
/*!40000 ALTER TABLE `page_index` DISABLE KEYS */;
INSERT INTO `page_index` VALUES (370,'about-us',17,1,39);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_meta`
--

LOCK TABLES `page_meta` WRITE;
/*!40000 ALTER TABLE `page_meta` DISABLE KEYS */;
INSERT INTO `page_meta` VALUES (5,39,'inkpad-url','HwS7k5RkjE');
/*!40000 ALTER TABLE `page_meta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-02 23:58:49
