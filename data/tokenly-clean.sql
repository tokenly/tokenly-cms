-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tokenly_test
-- ------------------------------------------------------
-- Server version	5.5.44-0ubuntu0.14.04.1-log

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
  `valueBlob` longblob,
  PRIMARY KEY (`appMetaId`),
  KEY `appId` (`appId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `app_meta_ibfk_3` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_meta`
--

LOCK TABLES `app_meta` WRITE;
/*!40000 ALTER TABLE `app_meta` DISABLE KEYS */;
INSERT INTO `app_meta` VALUES (1,7,'postsPerPage','20','Posts Per Page','textbox',NULL,1,NULL),(4,7,'maxExcerpt','250','Max Post Excerpt Characters','textbox',NULL,1,NULL),(5,7,'enableComments','1','Enable Comments','bool',NULL,1,NULL),(21,2,'avatarWidth','150','Avatar Width (px)','textbox',NULL,1,NULL),(22,2,'avatarHeight','150','Avatar Height (px)','textbox',NULL,1,NULL),(23,2,'disableRegister','0','Disable New User Registration','bool',NULL,1,NULL),(24,25,'topicsPerPage','60','Topics Per Page','textbox',NULL,1,NULL),(25,25,'postsPerPage','10','Topics Replies (posts) Per Page','textbox',NULL,1,NULL),(26,25,'forum-title','The LTB Network Forum','Forum Title','textbox',NULL,1,NULL),(27,25,'forum-description','This is the home of the LTB content network, where audience and content creators gather to discuss and create great content.','Forum Description','textarea',NULL,1,NULL),(28,7,'featuredWidth','600','Featured Image Width (px)','textbox',NULL,1,NULL),(29,7,'featuredHeight','372','Featured Image Height (px)','textbox',NULL,1,NULL),(31,26,'blog-feed-title','Lets Talk Bitcoin!','Blog Feed Title','textbox',NULL,1,NULL),(32,26,'blog-feed-description','','Blog Feed Description','textarea',NULL,1,NULL),(33,27,'store-title','Lets Shop Bitcoin!','Store Title','textbox',NULL,1,NULL),(34,27,'productsPerPage','20','Products Per Page','textbox',NULL,1,NULL),(35,7,'coverWidth','400','Cover Image Width','textbox',NULL,1,NULL),(36,7,'coverHeight','400','Cover Image Height','textbox',NULL,1,NULL),(37,30,'distribute-fee','0.00001','Share Distributor - per address miner fee','textbox','',1,NULL),(38,30,'distribute-dust','0.000025','Share Distributor - dust output BTC value','textbox','',1,NULL),(39,30,'pop-comment-weight','8','PoP points per blog comment made','textbox','',1,NULL),(40,30,'pop-forum-post-weight','10','PoP points per forum post made','textbox','',1,NULL),(41,30,'pop-forum-topic-weight','10','PoP points per forum thread made','textbox','',1,NULL),(42,30,'pop-register-weight','0','PoP bonus points for new registrants','textbox','',1,NULL),(43,30,'pop-view-weight','3','PoP points per first page view','textbox','',1,NULL),(44,30,'distributor-decimals','2','Share Distributor - Round Values to x Decimals','textbox','',1,NULL),(45,30,'distribute-batch-size','25','Share Distributor - # Transactions per Batch','textbox','',1,NULL),(46,30,'pop-listen-weight','20','PoP - Proof of Listening Weight','textbox','',1,NULL),(47,30,'pol-word-expire','168','Proof of Listening - Magic Words expiration (in hours)','textbox','',1,NULL),(48,30,'pop-like-weight','1','PoP points per Like','textbox','',1,NULL),(49,30,'pop-referral-weight','10','PoP Points per Active Referra','textbox','',1,NULL),(50,30,'referral-min-active-pop','10','Min PoP per active referral','textbox','',1,NULL),(51,30,'pop-publish-weight','25','PoP points per published blog post','textbox','',1,NULL),(52,30,'pop-editor-cut','20','Editor PoP point distribution cut per article (%)','textbox','',1,NULL),(53,25,'mod-group','15','Forum Moderator Group ID','textbox','',1,NULL),(54,30,'tca-forum-btc-fee','0.1','TCA Forum Builder BTC Cost','textbox','',1,NULL),(55,30,'tca-forum-token-fee','50000','TCA Forum Builder Token Cost','textbox','',1,NULL),(56,30,'tca-forum-token','LTBCOIN','TCA Forum Builder Token Name','textbox','',1,NULL),(57,30,'token-logo-width','150','Token Logo Image Width','textbox','',1,NULL),(58,30,'token-logo-height','150','Token Logo Image Height','textbox','',1,NULL),(59,30,'tca-forum-category','14','TCA Private Forum Default Category ID','textbox','',1,NULL),(60,7,'category-image-width','400','Category Image Width','textbox','',1,NULL),(61,7,'category-image-height','400','Category Image Height','textbox','',1,NULL),(62,7,'submission-fee','1000','Article Submission Fee','textbox',NULL,1,NULL),(63,7,'submission-fee-token','LTBCOIN','Submission Fee Token','textbox',NULL,1,NULL),(64,25,'weighted-votes-token','LTBCOIN','Weighted Votes Token','textbox','',1,NULL),(65,25,'min-upvote-points','0.05','Minimum Upvote Points','textbox','',1,NULL),(66,25,'max-upvote-points','5','Maximum Upvote Points','textbox','',1,NULL),(67,25,'weighted-vote-token-cap','500000','Weighted Vote Token Cap','textbox','',1,NULL),(68,25,'min-required-upvote-points','5','Minimum Upvote Points Required to Upvote','textbox','',1,NULL),(71,25,'min-posts-captcha','10','Minimum post count before CAPTCHA removal','textbox','',1,NULL),(140,7,'header_html','','Header Custom HTML','textarea','',1,NULL),(141,7,'footer_html','','Footer Custom HTML','textarea','',1,NULL),(142,7,'blog_tagline','','Blog Tagline/Slogan','textbox','',1,NULL),(143,7,'meta_description','','Meta Tag Description','textarea','',1,NULL),(146,32,'dashboard-modules-1','{\"2\":{\"0\":{\"moduleId\":\"1\",\"appId\":\"2\",\"name\":\"Dashboard Home\",\"slug\":\"dash-home\",\"active\":\"1\",\"location\":\"DashHome\",\"url\":\"home\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Account Dashboard Home\",\"label\":\"Account Dashboard Home\"},\"2\":{\"moduleId\":\"23\",\"appId\":\"2\",\"name\":\"Account Settings\",\"slug\":\"account-settings\",\"active\":\"1\",\"location\":\"Settings\",\"url\":\"settings\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"label\":\"Account Settings\"},\"1\":{\"moduleId\":\"21\",\"appId\":\"2\",\"name\":\"Profile\",\"slug\":\"account-profile\",\"active\":\"1\",\"location\":\"Profile\",\"url\":\"profile\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"My Profile\",\"label\":\"My Profile\"},\"3\":{\"moduleId\":\"42\",\"appId\":\"2\",\"name\":\"Notification\",\"slug\":\"notification\",\"active\":\"1\",\"location\":\"Notification\",\"url\":\"notifications\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Notifications\",\"label\":\"Notifications\"},\"5\":{\"moduleId\":\"62\",\"appId\":\"2\",\"name\":\"Messages\",\"slug\":\"private-message\",\"active\":\"1\",\"location\":\"Message\",\"url\":\"messages\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Private Messages\",\"label\":\"Private Messages\"},\"4\":{\"moduleId\":\"60\",\"appId\":\"2\",\"name\":\"Referrals\",\"slug\":\"account-referrals\",\"active\":\"1\",\"location\":\"Referral\",\"url\":\"referrals\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Referrals\",\"label\":\"Referrals\"}},\"33\":[{\"moduleId\":\"79\",\"appId\":\"33\",\"name\":\"Address Transaction Reports\",\"slug\":\"accountant-report\",\"active\":\"1\",\"location\":\"Report\",\"url\":\"tx-report\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"TX Reports\",\"label\":\"TX Reports\"}],\"31\":[{\"moduleId\":\"77\",\"appId\":\"31\",\"name\":\"Ad URL Tracker\",\"slug\":\"ad-url-tracker\",\"active\":\"1\",\"location\":\"Tracker\",\"url\":\"ad-tracker\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"URL Tracker\",\"label\":\"URL Tracker\"}],\"7\":{\"0\":{\"moduleId\":\"26\",\"appId\":\"7\",\"name\":\"Blog Categories\",\"slug\":\"blog-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"blog-category\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Categories\",\"label\":\"Categories\"},\"1\":{\"moduleId\":\"43\",\"appId\":\"7\",\"name\":\"Post Metadata Types\",\"slug\":\"blog-post-meta\",\"active\":\"1\",\"location\":\"Meta\",\"url\":\"meta\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Custom Post Fields\",\"label\":\"Custom Post Fields\"},\"2\":{\"moduleId\":\"49\",\"appId\":\"7\",\"name\":\"Disqus Comments\",\"slug\":\"disqus-comments\",\"active\":\"1\",\"location\":\"Disqus\",\"url\":\"disqus\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Disqus Comments\",\"label\":\"Disqus Comments\"},\"4\":{\"moduleId\":\"59\",\"appId\":\"7\",\"name\":\"Magic Word Submissions\",\"slug\":\"magic-word-submits\",\"active\":\"1\",\"location\":\"MagicWordSubmits\",\"url\":\"all-magic-words\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Magic Word Submissions\",\"label\":\"Magic Word Submissions\"},\"3\":{\"moduleId\":\"54\",\"appId\":\"7\",\"name\":\"Magic Words\",\"slug\":\"magic-words\",\"active\":\"1\",\"location\":\"MagicWords\",\"url\":\"magic-words\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Magic Words\",\"label\":\"Magic Words\"},\"6\":{\"moduleId\":\"70\",\"appId\":\"7\",\"name\":\"My Blogs\",\"slug\":\"multi-blogs\",\"active\":\"1\",\"location\":\"Multiblog\",\"url\":\"multi-blogs\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"My Blogs\",\"label\":\"My Blogs\"},\"7\":{\"moduleId\":\"74\",\"appId\":\"7\",\"name\":\"Newsroom\",\"slug\":\"newsroom\",\"active\":\"1\",\"location\":\"Newsroom\",\"url\":\"newsroom\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Newsroom\",\"label\":\"Newsroom\"},\"5\":{\"moduleId\":\"69\",\"appId\":\"7\",\"name\":\"Blog Submissions\",\"slug\":\"blog-submissions\",\"active\":\"1\",\"location\":\"Submissions\",\"url\":\"submissions\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Submissions\",\"label\":\"Submissions\"}},\"32\":{\"14\":{\"moduleId\":\"37\",\"appId\":\"32\",\"name\":\"App Settings\",\"slug\":\"app-settings\",\"active\":\"1\",\"location\":\"AppSettings\",\"url\":\"app-settings\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"App Settings\",\"label\":\"App Settings\"},\"0\":{\"moduleId\":\"4\",\"appId\":\"32\",\"name\":\"Apps & Modules\",\"slug\":\"modules\",\"active\":\"1\",\"location\":\"Modules\",\"url\":\"modules\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Apps & Modules\",\"label\":\"Apps & Modules\"},\"9\":{\"moduleId\":\"15\",\"appId\":\"32\",\"name\":\"Content Blocks\",\"slug\":\"content-blocks\",\"active\":\"1\",\"location\":\"ContentBlocks\",\"url\":\"content-blocks\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Content Blocks\",\"label\":\"Content Blocks\"},\"13\":{\"moduleId\":\"20\",\"appId\":\"32\",\"name\":\"Profile Fields\",\"slug\":\"profile-fields\",\"active\":\"1\",\"location\":\"ProfileFields\",\"url\":\"profile-fields\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Custom Profile Fields\",\"label\":\"Custom Profile Fields\"},\"12\":{\"moduleId\":\"19\",\"appId\":\"32\",\"name\":\"Files\",\"slug\":\"files\",\"active\":\"1\",\"location\":\"Files\",\"url\":\"files\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"File Browser\",\"label\":\"File Browser\"},\"4\":{\"moduleId\":\"10\",\"appId\":\"32\",\"name\":\"Accounts\",\"slug\":\"accounts\",\"active\":\"1\",\"location\":\"Accounts\",\"url\":\"accounts\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Accounts\",\"label\":\"Manage Accounts\"},\"5\":{\"moduleId\":\"11\",\"appId\":\"32\",\"name\":\"Groups\",\"slug\":\"groups\",\"active\":\"1\",\"location\":\"Groups\",\"url\":\"groups\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Groups\",\"label\":\"Manage Groups\"},\"11\":{\"moduleId\":\"18\",\"appId\":\"32\",\"name\":\"Menu Items\",\"slug\":\"menu-items\",\"active\":\"1\",\"location\":\"MenuItems\",\"url\":\"menu-items\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Menu Items\",\"label\":\"Menu Items\"},\"7\":{\"moduleId\":\"13\",\"appId\":\"32\",\"name\":\"Menus\",\"slug\":\"menus\",\"active\":\"1\",\"location\":\"Menus\",\"url\":\"menus\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Menus\",\"label\":\"Menus\"},\"15\":{\"moduleId\":\"52\",\"appId\":\"32\",\"name\":\"Notification Pusher\",\"slug\":\"notification-pusher\",\"active\":\"1\",\"location\":\"Notifier\",\"url\":\"notifier\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Notification Pusher\",\"label\":\"Notification Pusher\"},\"8\":{\"moduleId\":\"14\",\"appId\":\"32\",\"name\":\"Page Tags\",\"slug\":\"page-tags\",\"active\":\"1\",\"location\":\"PageTags\",\"url\":\"page-tags\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Page Tags\",\"label\":\"Page Tags\"},\"10\":{\"moduleId\":\"16\",\"appId\":\"32\",\"name\":\"Pages\",\"slug\":\"pages\",\"active\":\"1\",\"location\":\"Pages\",\"url\":\"pages\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Pages\",\"label\":\"Pages\"},\"2\":{\"moduleId\":\"8\",\"appId\":\"32\",\"name\":\"Stats\",\"slug\":\"stats\",\"active\":\"1\",\"location\":\"Stats\",\"url\":\"stats\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Stats\",\"label\":\"Stats\"},\"1\":{\"moduleId\":\"7\",\"appId\":\"32\",\"name\":\"Sites\",\"slug\":\"sites\",\"active\":\"1\",\"location\":\"Sites\",\"url\":\"sites\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Sub-sites\",\"label\":\"Sub-sites\"},\"3\":{\"moduleId\":\"9\",\"appId\":\"32\",\"name\":\"Settings\",\"slug\":\"settings\",\"active\":\"1\",\"location\":\"Settings\",\"url\":\"settings\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"System Settings\",\"label\":\"System Settings\"},\"6\":{\"moduleId\":\"12\",\"appId\":\"32\",\"name\":\"Themes\",\"slug\":\"themes\",\"active\":\"1\",\"location\":\"Themes\",\"url\":\"themes\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Themes\",\"label\":\"Themes\"}},\"25\":{\"1\":{\"moduleId\":\"39\",\"appId\":\"25\",\"name\":\"Forum Boards\",\"slug\":\"forum-boards\",\"active\":\"1\",\"location\":\"Boards\",\"url\":\"boards\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Boards\",\"label\":\"Manage Boards\"},\"0\":{\"moduleId\":\"38\",\"appId\":\"25\",\"name\":\"Forum Categories\",\"slug\":\"forum-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"categories\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Categories\",\"label\":\"Manage Categories\"}},\"26\":[{\"moduleId\":\"65\",\"appId\":\"26\",\"name\":\"RSS Feed Proxies\",\"slug\":\"rss-feed-proxy\",\"active\":\"1\",\"location\":\"ProxyURLs\",\"url\":\"proxy-feed-urls\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Proxy URLs\",\"label\":\"Proxy URLs\"}],\"27\":[{\"moduleId\":\"47\",\"appId\":\"27\",\"name\":\"Store Categories\",\"slug\":\"store-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"categories\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Categories\",\"label\":\"Manage Categories\"},{\"moduleId\":\"48\",\"appId\":\"27\",\"name\":\"Store Products\",\"slug\":\"store-products\",\"active\":\"1\",\"location\":\"Products\",\"url\":\"products\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Products\",\"label\":\"Manage Products\"},{\"moduleId\":\"67\",\"appId\":\"27\",\"name\":\"Orders\",\"slug\":\"store-orders\",\"active\":\"1\",\"location\":\"Order\",\"url\":\"orders\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Orders\",\"label\":\"Orders\"},{\"moduleId\":\"75\",\"appId\":\"27\",\"name\":\"Payment Collector\",\"slug\":\"payment-collector\",\"active\":\"1\",\"location\":\"Collector\",\"url\":\"payment-collector\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Payment Collector\",\"label\":\"Payment Collector\"}],\"30\":{\"3\":{\"moduleId\":\"61\",\"appId\":\"30\",\"name\":\"Address Manager\",\"slug\":\"address-manager\",\"active\":\"1\",\"location\":\"Address\",\"url\":\"address-manager\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Address Manager\",\"label\":\"Address Manager\"},\"5\":{\"moduleId\":\"64\",\"appId\":\"30\",\"name\":\"Asset Cache\",\"slug\":\"asset-cache\",\"active\":\"1\",\"location\":\"AssetCache\",\"url\":\"asset-cache\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Cache\",\"label\":\"Asset Cache\"},\"1\":{\"moduleId\":\"51\",\"appId\":\"30\",\"name\":\"Asset Dropper\",\"slug\":\"asset-drop\",\"active\":\"1\",\"location\":\"AssetDrop\",\"url\":\"asset-drop\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Dropper\",\"label\":\"Asset Dropper\"},\"6\":{\"moduleId\":\"71\",\"appId\":\"30\",\"name\":\"Asset Scouter\",\"slug\":\"xcp-asset-scout\",\"active\":\"1\",\"location\":\"AssetScout\",\"url\":\"xcp-asset-scout\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Scouter\",\"label\":\"Asset Scouter\"},\"4\":{\"moduleId\":\"63\",\"appId\":\"30\",\"name\":\"Token Inventory\",\"slug\":\"token-inventory\",\"active\":\"1\",\"location\":\"Inventory\",\"url\":\"inventory\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Inventory\",\"label\":\"Inventory\"},\"2\":{\"moduleId\":\"53\",\"appId\":\"30\",\"name\":\"Proof of Participation\",\"slug\":\"ltbcoin-pop\",\"active\":\"1\",\"location\":\"Participation\",\"url\":\"ltbcoin-pop\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Participation Reports\",\"label\":\"Participation Reports\"},\"0\":{\"moduleId\":\"50\",\"appId\":\"30\",\"name\":\"Share Distributor\",\"slug\":\"share-distribute\",\"active\":\"1\",\"location\":\"Distribute\",\"url\":\"xcp-distribute\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Share Distributor\",\"label\":\"Share Distributor\"}}}','','textbox',NULL,0,NULL),(147,32,'dashboard-hash','98a3ab7c340e8a033e7b37b6ef9428751581760af67bbab2b9e05d4964a8874a','','textbox',NULL,0,NULL),(148,32,'dashboard-modules-1','{\"2\":{\"0\":{\"moduleId\":\"1\",\"appId\":\"2\",\"name\":\"Dashboard Home\",\"slug\":\"dash-home\",\"active\":\"1\",\"location\":\"DashHome\",\"url\":\"home\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Account Dashboard Home\",\"label\":\"Account Dashboard Home\"},\"2\":{\"moduleId\":\"23\",\"appId\":\"2\",\"name\":\"Account Settings\",\"slug\":\"account-settings\",\"active\":\"1\",\"location\":\"Settings\",\"url\":\"settings\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"label\":\"Account Settings\"},\"1\":{\"moduleId\":\"21\",\"appId\":\"2\",\"name\":\"Profile\",\"slug\":\"account-profile\",\"active\":\"1\",\"location\":\"Profile\",\"url\":\"profile\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"My Profile\",\"label\":\"My Profile\"},\"3\":{\"moduleId\":\"42\",\"appId\":\"2\",\"name\":\"Notification\",\"slug\":\"notification\",\"active\":\"1\",\"location\":\"Notification\",\"url\":\"notifications\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Notifications\",\"label\":\"Notifications\"},\"5\":{\"moduleId\":\"62\",\"appId\":\"2\",\"name\":\"Messages\",\"slug\":\"private-message\",\"active\":\"1\",\"location\":\"Message\",\"url\":\"messages\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Private Messages\",\"label\":\"Private Messages\"},\"4\":{\"moduleId\":\"60\",\"appId\":\"2\",\"name\":\"Referrals\",\"slug\":\"account-referrals\",\"active\":\"1\",\"location\":\"Referral\",\"url\":\"referrals\",\"checkAccess\":\"0\",\"module-type\":\"dashboard\",\"menu-label\":\"Referrals\",\"label\":\"Referrals\"}},\"33\":[{\"moduleId\":\"79\",\"appId\":\"33\",\"name\":\"Address Transaction Reports\",\"slug\":\"accountant-report\",\"active\":\"1\",\"location\":\"Report\",\"url\":\"tx-report\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"TX Reports\",\"label\":\"TX Reports\"}],\"31\":[{\"moduleId\":\"77\",\"appId\":\"31\",\"name\":\"Ad URL Tracker\",\"slug\":\"ad-url-tracker\",\"active\":\"1\",\"location\":\"Tracker\",\"url\":\"ad-tracker\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"URL Tracker\",\"label\":\"URL Tracker\"}],\"7\":{\"0\":{\"moduleId\":\"26\",\"appId\":\"7\",\"name\":\"Blog Categories\",\"slug\":\"blog-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"blog-category\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Categories\",\"label\":\"Categories\"},\"1\":{\"moduleId\":\"43\",\"appId\":\"7\",\"name\":\"Post Metadata Types\",\"slug\":\"blog-post-meta\",\"active\":\"1\",\"location\":\"Meta\",\"url\":\"meta\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Custom Post Fields\",\"label\":\"Custom Post Fields\"},\"2\":{\"moduleId\":\"49\",\"appId\":\"7\",\"name\":\"Disqus Comments\",\"slug\":\"disqus-comments\",\"active\":\"1\",\"location\":\"Disqus\",\"url\":\"disqus\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Disqus Comments\",\"label\":\"Disqus Comments\"},\"4\":{\"moduleId\":\"59\",\"appId\":\"7\",\"name\":\"Magic Word Submissions\",\"slug\":\"magic-word-submits\",\"active\":\"1\",\"location\":\"MagicWordSubmits\",\"url\":\"all-magic-words\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Magic Word Submissions\",\"label\":\"Magic Word Submissions\"},\"3\":{\"moduleId\":\"54\",\"appId\":\"7\",\"name\":\"Magic Words\",\"slug\":\"magic-words\",\"active\":\"1\",\"location\":\"MagicWords\",\"url\":\"magic-words\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Magic Words\",\"label\":\"Magic Words\"},\"6\":{\"moduleId\":\"70\",\"appId\":\"7\",\"name\":\"My Blogs\",\"slug\":\"multi-blogs\",\"active\":\"1\",\"location\":\"Multiblog\",\"url\":\"multi-blogs\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"My Blogs\",\"label\":\"My Blogs\"},\"7\":{\"moduleId\":\"74\",\"appId\":\"7\",\"name\":\"Newsroom\",\"slug\":\"newsroom\",\"active\":\"1\",\"location\":\"Newsroom\",\"url\":\"newsroom\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Newsroom\",\"label\":\"Newsroom\"},\"5\":{\"moduleId\":\"69\",\"appId\":\"7\",\"name\":\"Blog Submissions\",\"slug\":\"blog-submissions\",\"active\":\"1\",\"location\":\"Submissions\",\"url\":\"submissions\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Submissions\",\"label\":\"Submissions\"}},\"32\":{\"14\":{\"moduleId\":\"37\",\"appId\":\"32\",\"name\":\"App Settings\",\"slug\":\"app-settings\",\"active\":\"1\",\"location\":\"AppSettings\",\"url\":\"app-settings\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"App Settings\",\"label\":\"App Settings\"},\"0\":{\"moduleId\":\"4\",\"appId\":\"32\",\"name\":\"Apps & Modules\",\"slug\":\"modules\",\"active\":\"1\",\"location\":\"Modules\",\"url\":\"modules\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Apps & Modules\",\"label\":\"Apps & Modules\"},\"9\":{\"moduleId\":\"15\",\"appId\":\"32\",\"name\":\"Content Blocks\",\"slug\":\"content-blocks\",\"active\":\"1\",\"location\":\"ContentBlocks\",\"url\":\"content-blocks\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Content Blocks\",\"label\":\"Content Blocks\"},\"13\":{\"moduleId\":\"20\",\"appId\":\"32\",\"name\":\"Profile Fields\",\"slug\":\"profile-fields\",\"active\":\"1\",\"location\":\"ProfileFields\",\"url\":\"profile-fields\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Custom Profile Fields\",\"label\":\"Custom Profile Fields\"},\"12\":{\"moduleId\":\"19\",\"appId\":\"32\",\"name\":\"Files\",\"slug\":\"files\",\"active\":\"1\",\"location\":\"Files\",\"url\":\"files\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"File Browser\",\"label\":\"File Browser\"},\"4\":{\"moduleId\":\"10\",\"appId\":\"32\",\"name\":\"Accounts\",\"slug\":\"accounts\",\"active\":\"1\",\"location\":\"Accounts\",\"url\":\"accounts\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Accounts\",\"label\":\"Manage Accounts\"},\"5\":{\"moduleId\":\"11\",\"appId\":\"32\",\"name\":\"Groups\",\"slug\":\"groups\",\"active\":\"1\",\"location\":\"Groups\",\"url\":\"groups\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Groups\",\"label\":\"Manage Groups\"},\"11\":{\"moduleId\":\"18\",\"appId\":\"32\",\"name\":\"Menu Items\",\"slug\":\"menu-items\",\"active\":\"1\",\"location\":\"MenuItems\",\"url\":\"menu-items\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Menu Items\",\"label\":\"Menu Items\"},\"7\":{\"moduleId\":\"13\",\"appId\":\"32\",\"name\":\"Menus\",\"slug\":\"menus\",\"active\":\"1\",\"location\":\"Menus\",\"url\":\"menus\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Menus\",\"label\":\"Menus\"},\"15\":{\"moduleId\":\"52\",\"appId\":\"32\",\"name\":\"Notification Pusher\",\"slug\":\"notification-pusher\",\"active\":\"1\",\"location\":\"Notifier\",\"url\":\"notifier\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Notification Pusher\",\"label\":\"Notification Pusher\"},\"8\":{\"moduleId\":\"14\",\"appId\":\"32\",\"name\":\"Page Tags\",\"slug\":\"page-tags\",\"active\":\"1\",\"location\":\"PageTags\",\"url\":\"page-tags\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Page Tags\",\"label\":\"Page Tags\"},\"10\":{\"moduleId\":\"16\",\"appId\":\"32\",\"name\":\"Pages\",\"slug\":\"pages\",\"active\":\"1\",\"location\":\"Pages\",\"url\":\"pages\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Pages\",\"label\":\"Pages\"},\"2\":{\"moduleId\":\"8\",\"appId\":\"32\",\"name\":\"Stats\",\"slug\":\"stats\",\"active\":\"1\",\"location\":\"Stats\",\"url\":\"stats\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Stats\",\"label\":\"Stats\"},\"1\":{\"moduleId\":\"7\",\"appId\":\"32\",\"name\":\"Sites\",\"slug\":\"sites\",\"active\":\"1\",\"location\":\"Sites\",\"url\":\"sites\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Sub-sites\",\"label\":\"Sub-sites\"},\"3\":{\"moduleId\":\"9\",\"appId\":\"32\",\"name\":\"Settings\",\"slug\":\"settings\",\"active\":\"1\",\"location\":\"Settings\",\"url\":\"settings\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"System Settings\",\"label\":\"System Settings\"},\"6\":{\"moduleId\":\"12\",\"appId\":\"32\",\"name\":\"Themes\",\"slug\":\"themes\",\"active\":\"1\",\"location\":\"Themes\",\"url\":\"themes\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Themes\",\"label\":\"Themes\"}},\"25\":{\"1\":{\"moduleId\":\"39\",\"appId\":\"25\",\"name\":\"Forum Boards\",\"slug\":\"forum-boards\",\"active\":\"1\",\"location\":\"Boards\",\"url\":\"boards\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Boards\",\"label\":\"Manage Boards\"},\"0\":{\"moduleId\":\"38\",\"appId\":\"25\",\"name\":\"Forum Categories\",\"slug\":\"forum-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"categories\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Categories\",\"label\":\"Manage Categories\"}},\"26\":[{\"moduleId\":\"65\",\"appId\":\"26\",\"name\":\"RSS Feed Proxies\",\"slug\":\"rss-feed-proxy\",\"active\":\"1\",\"location\":\"ProxyURLs\",\"url\":\"proxy-feed-urls\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Proxy URLs\",\"label\":\"Proxy URLs\"}],\"27\":[{\"moduleId\":\"47\",\"appId\":\"27\",\"name\":\"Store Categories\",\"slug\":\"store-categories\",\"active\":\"1\",\"location\":\"Categories\",\"url\":\"categories\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Categories\",\"label\":\"Manage Categories\"},{\"moduleId\":\"48\",\"appId\":\"27\",\"name\":\"Store Products\",\"slug\":\"store-products\",\"active\":\"1\",\"location\":\"Products\",\"url\":\"products\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Manage Products\",\"label\":\"Manage Products\"},{\"moduleId\":\"67\",\"appId\":\"27\",\"name\":\"Orders\",\"slug\":\"store-orders\",\"active\":\"1\",\"location\":\"Order\",\"url\":\"orders\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Orders\",\"label\":\"Orders\"},{\"moduleId\":\"75\",\"appId\":\"27\",\"name\":\"Payment Collector\",\"slug\":\"payment-collector\",\"active\":\"1\",\"location\":\"Collector\",\"url\":\"payment-collector\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Payment Collector\",\"label\":\"Payment Collector\"}],\"30\":{\"3\":{\"moduleId\":\"61\",\"appId\":\"30\",\"name\":\"Address Manager\",\"slug\":\"address-manager\",\"active\":\"1\",\"location\":\"Address\",\"url\":\"address-manager\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Address Manager\",\"label\":\"Address Manager\"},\"5\":{\"moduleId\":\"64\",\"appId\":\"30\",\"name\":\"Asset Cache\",\"slug\":\"asset-cache\",\"active\":\"1\",\"location\":\"AssetCache\",\"url\":\"asset-cache\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Cache\",\"label\":\"Asset Cache\"},\"1\":{\"moduleId\":\"51\",\"appId\":\"30\",\"name\":\"Asset Dropper\",\"slug\":\"asset-drop\",\"active\":\"1\",\"location\":\"AssetDrop\",\"url\":\"asset-drop\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Dropper\",\"label\":\"Asset Dropper\"},\"6\":{\"moduleId\":\"71\",\"appId\":\"30\",\"name\":\"Asset Scouter\",\"slug\":\"xcp-asset-scout\",\"active\":\"1\",\"location\":\"AssetScout\",\"url\":\"xcp-asset-scout\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Asset Scouter\",\"label\":\"Asset Scouter\"},\"4\":{\"moduleId\":\"63\",\"appId\":\"30\",\"name\":\"Token Inventory\",\"slug\":\"token-inventory\",\"active\":\"1\",\"location\":\"Inventory\",\"url\":\"inventory\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Inventory\",\"label\":\"Inventory\"},\"2\":{\"moduleId\":\"53\",\"appId\":\"30\",\"name\":\"Proof of Participation\",\"slug\":\"ltbcoin-pop\",\"active\":\"1\",\"location\":\"Participation\",\"url\":\"ltbcoin-pop\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Participation Reports\",\"label\":\"Participation Reports\"},\"0\":{\"moduleId\":\"50\",\"appId\":\"30\",\"name\":\"Share Distributor\",\"slug\":\"share-distribute\",\"active\":\"1\",\"location\":\"Distribute\",\"url\":\"xcp-distribute\",\"checkAccess\":\"1\",\"module-type\":\"dashboard\",\"menu-label\":\"Share Distributor\",\"label\":\"Share Distributor\"}}}','','textbox',NULL,0,NULL),(149,32,'dashboard-hash','98a3ab7c340e8a033e7b37b6ef9428751581760af67bbab2b9e05d4964a8874a','','textbox',NULL,0,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_perms`
--

LOCK TABLES `app_perms` WRITE;
/*!40000 ALTER TABLE `app_perms` DISABLE KEYS */;
INSERT INTO `app_perms` VALUES (1,25,'canPostTopic'),(2,25,'canPostReply'),(3,25,'canEditSelf'),(4,25,'canBurySelf'),(5,25,'canDeleteSelfTopic'),(6,25,'canLockSelf'),(8,25,'canEditOther'),(9,25,'canBuryOther'),(10,25,'canDeleteOtherTopic'),(11,25,'canLockOther'),(13,25,'canStickySelf'),(14,25,'canStickyOther'),(15,25,'canMoveSelf'),(16,25,'canMoveOther'),(17,7,'canPostComment'),(18,7,'canEditSelfComment'),(19,7,'canDeleteSelfComment'),(20,7,'canEditOtherComment'),(21,7,'canDeleteOtherComment'),(22,7,'canWritePost'),(23,7,'canEditSelfPost'),(24,7,'canDeleteSelfPost'),(25,7,'canEditOtherPost'),(26,7,'canDeleteOtherPost'),(27,7,'canPublishPost'),(28,7,'canChangeAuthor'),(29,30,'canDistribute'),(30,30,'canDeleteDistribution'),(31,30,'canChangeDistributeStatus'),(32,30,'canChangeDistributeLabels'),(33,7,'canUseMagicWords'),(34,25,'canReportPost'),(35,25,'canReceiveReports'),(36,25,'isTroll'),(37,7,'canSetEditStatus'),(38,7,'canChangeEditor'),(39,25,'canRequestBan'),(40,25,'canReceiveBanRequest'),(41,25,'canPermaDeletePost'),(42,25,'canPermaDeleteTopic'),(43,25,'canChangeBoardOwner'),(44,25,'canChangeBoardCategory'),(45,25,'canManageAllBoards'),(46,30,'canViewAllAssets'),(47,30,'canChangeAssetOwner'),(48,25,'canChangeBoardRank'),(49,7,'canBypassSubmitFee'),(50,25,'canUpvoteDownvote'),(51,7,'canDeleteSelfPostVersion'),(52,7,'canDeleteOtherPostVersion'),(53,7,'canEditAfterPublished'),(54,7,'canManageAllBlogs'),(55,7,'canChangeBlogOwner'),(56,7,'canCreateBlogs');
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apps`
--

LOCK TABLES `apps` WRITE;
/*!40000 ALTER TABLE `apps` DISABLE KEYS */;
INSERT INTO `apps` VALUES (1,'Dashboard','dashboard',1,'Dashboard','dashboard',1),(2,'Account','account',1,'Account','account',2),(5,'Pages','pages',1,'Page','',0),(6,'Profile','profile',1,'Profile','profile',0),(7,'Blog','blog',1,'Blog','blog',0),(25,'Forum','forum',1,'Forum','forum',0),(26,'RSS','rss',1,'RSS','rss',0),(27,'Store','store',1,'Store','store',0),(30,'Tokenly','tokenly',1,'Tokenly','tokenly',0),(31,'Ad Manager','ad',1,'Ad','ad',0),(32,'CMS','cms',1,'CMS','cms',0),(33,'Accountant','accountant',1,'Accountant','accounting',0);
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
  `blogId` int(11) unsigned DEFAULT '0',
  `public` int(1) DEFAULT '0',
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  KEY `blogId` (`blogId`),
  CONSTRAINT `blog_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_contributors`
--

DROP TABLE IF EXISTS `blog_contributors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_contributors` (
  `contributorId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `postId` int(11) unsigned NOT NULL,
  `inviteId` int(11) unsigned NOT NULL,
  `role` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `share` decimal(20,8) DEFAULT '0.00000000',
  PRIMARY KEY (`contributorId`),
  KEY `postId` (`postId`),
  KEY `inviteId` (`inviteId`),
  CONSTRAINT `blog_contributors_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE,
  CONSTRAINT `blog_contributors_ibfk_2` FOREIGN KEY (`inviteId`) REFERENCES `user_invites` (`inviteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_contributors`
--

LOCK TABLES `blog_contributors` WRITE;
/*!40000 ALTER TABLE `blog_contributors` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_contributors` ENABLE KEYS */;
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
  `approved` int(1) DEFAULT '0',
  PRIMARY KEY (`postCatId`),
  KEY `postId` (`postId`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `blog_postCategories_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE,
  CONSTRAINT `blog_postCategories_ibfk_2` FOREIGN KEY (`categoryId`) REFERENCES `blog_categories` (`categoryId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9114 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postCategories`
--

LOCK TABLES `blog_postCategories` WRITE;
/*!40000 ALTER TABLE `blog_postCategories` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3989 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMeta`
--

LOCK TABLES `blog_postMeta` WRITE;
/*!40000 ALTER TABLE `blog_postMeta` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postMetaTypes`
--

LOCK TABLES `blog_postMetaTypes` WRITE;
/*!40000 ALTER TABLE `blog_postMetaTypes` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1067 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_roles`
--

DROP TABLE IF EXISTS `blog_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_roles` (
  `userRoleId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blogId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned DEFAULT '0',
  `type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`userRoleId`),
  KEY `blogId` (`blogId`),
  KEY `userId` (`userId`),
  CONSTRAINT `blog_roles_ibfk_1` FOREIGN KEY (`blogId`) REFERENCES `blogs` (`blogId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_roles`
--

LOCK TABLES `blog_roles` WRITE;
/*!40000 ALTER TABLE `blog_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `blogId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `siteId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` int(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `themeId` int(11) DEFAULT '0',
  `settings` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`blogId`),
  KEY `siteId` (`siteId`),
  KEY `userId` (`userId`),
  KEY `slug` (`slug`),
  CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=7051 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coin_addresses`
--

LOCK TABLES `coin_addresses` WRITE;
/*!40000 ALTER TABLE `coin_addresses` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_blocks`
--

LOCK TABLES `content_blocks` WRITE;
/*!40000 ALTER TABLE `content_blocks` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1165 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_versions`
--

LOCK TABLES `content_versions` WRITE;
/*!40000 ALTER TABLE `content_versions` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dash_menu`
--

LOCK TABLES `dash_menu` WRITE;
/*!40000 ALTER TABLE `dash_menu` DISABLE KEYS */;
INSERT INTO `dash_menu` VALUES (1,1,' ',0,'Dashboard Home',0,NULL),(2,23,' ',1,'Account Settings',0,NULL),(3,21,' ',2,'My Profile',0,NULL),(4,3,' ',600,'Logout',0,''),(5,8,'System',0,'System Stats',1,NULL),(6,9,'System',1,'System Settings',1,NULL),(7,7,'System',2,'Sub-Sites',1,NULL),(8,4,'System',3,'Apps & Modules',1,NULL),(9,12,'System',4,'Themes',1,NULL),(11,10,'Users',1,'User Accounts',1,''),(12,11,'Users',1,'Groups',1,NULL),(13,20,'Users',2,'User Profile Fields',1,NULL),(14,16,'CMS',0,'Pages',1,NULL),(15,15,'CMS',1,'Content Blocks',1,NULL),(16,14,'CMS',2,'Page Tags',1,NULL),(17,13,'CMS',3,'Menus',1,NULL),(18,18,'CMS',4,'Menu Items',1,NULL),(19,19,'CMS',5,'File browser',1,NULL),(20,26,'Blog',1,'Categories',1,''),(24,37,'Blog',0,'Blog Settings',1,'/blog'),(39,37,'Users',0,'Account System Settings',1,'/account'),(40,37,'Forum',0,'Forum Settings',1,'/forum'),(41,38,'Forum',1,'Categories',1,''),(42,39,'Forum',2,'Boards',1,''),(43,42,' ',3,'Notifications',0,''),(44,43,'Blog',50,'Post Metadata Types',1,''),(45,37,'RSS',0,'RSS Settings',1,'/rss'),(46,37,'Store',0,'Store Settings',1,'/store'),(47,47,'Store',0,'Categories',1,''),(48,48,'Store',20,'Products',1,''),(49,49,'Blog',100,'Disqus Comments',1,''),(50,50,'LTBcoin',10,'Share Distributor',1,''),(51,51,'LTBcoin',50,'Asset Dropper',1,''),(52,37,'LTBcoin',0,'LTBcoin Settings',1,'/ltbcoin'),(53,52,'System',100,'Notification Pusher',1,''),(54,53,'LTBcoin',160,'Proof of Participation',1,''),(55,54,'LTBcoin',200,'Magic Words',1,''),(56,59,'LTBcoin',220,'Magic Word Submissions',1,''),(57,60,' ',5,'Referrals',0,''),(58,61,'LTBcoin',230,'Address Manager',1,''),(59,62,' ',50,'Private Messages',0,''),(60,63,'LTBcoin',240,'Token Inventory',1,''),(61,64,'LTBcoin',250,'Asset Cache',1,''),(62,65,'RSS',100,'RSS Feed Proxies',1,''),(63,67,'Store',100,'Orders & Payments',1,''),(64,69,'Blog',0,'Submissions',1,''),(65,70,'Blog',0,'My Blogs',1,''),(66,71,'LTBcoin',0,'Asset Scouter',1,''),(67,74,'Blog',0,'Newsroom',1,''),(68,75,'Store',0,'Payment Collector',1,''),(69,77,'Advertisements',0,'URL Tracker',1,''),(70,37,'Ad',0,'Ad Settings',1,'/ad'),(71,79,'Accounting',0,'TX Reports',1,'');
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
  `metaKey` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`metaId`),
  KEY `boardId` (`boardId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `forum_boardMeta_ibfk_1` FOREIGN KEY (`boardId`) REFERENCES `forum_boards` (`boardId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_boards`
--

LOCK TABLES `forum_boards` WRITE;
/*!40000 ALTER TABLE `forum_boards` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_categories`
--

LOCK TABLES `forum_categories` WRITE;
/*!40000 ALTER TABLE `forum_categories` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=116965 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10184 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_subscriptions`
--

LOCK TABLES `forum_subscriptions` WRITE;
/*!40000 ALTER TABLE `forum_subscriptions` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7690 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topics`
--

LOCK TABLES `forum_topics` WRITE;
/*!40000 ALTER TABLE `forum_topics` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2196 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_access`
--

LOCK TABLES `group_access` WRITE;
/*!40000 ALTER TABLE `group_access` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3043 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_perms`
--

LOCK TABLES `group_perms` WRITE;
/*!40000 ALTER TABLE `group_perms` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_sites`
--

LOCK TABLES `group_sites` WRITE;
/*!40000 ALTER TABLE `group_sites` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13239 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_users`
--

LOCK TABLES `group_users` WRITE;
/*!40000 ALTER TABLE `group_users` DISABLE KEYS */;
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
  `isSilent` INT(2) unsigned DEFAULT '0',
  `displayRank` INT(11) DEFAULT '0',
  `displayView` VARCHAR(255),
  `displayName` VARCHAR(255),
  PRIMARY KEY (`groupId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_links`
--

LOCK TABLES `menu_links` WRITE;
/*!40000 ALTER TABLE `menu_links` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_pages`
--

LOCK TABLES `menu_pages` WRITE;
/*!40000 ALTER TABLE `menu_pages` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,2,'Dashboard Home','dash-home',1,'DashHome','home',0),(2,2,'Account Home','account-home',1,'Home','',0),(3,2,'Logout','logout',1,'Logout','logout',0),(4,32,'Apps & Modules','modules',1,'Modules','modules',1),(7,32,'Sites','sites',1,'Sites','sites',1),(8,32,'Stats','stats',1,'Stats','stats',1),(9,32,'Settings','settings',1,'Settings','settings',1),(10,32,'Accounts','accounts',1,'Accounts','accounts',1),(11,32,'Groups','groups',1,'Groups','groups',1),(12,32,'Themes','themes',1,'Themes','themes',1),(13,32,'Menus','menus',1,'Menus','menus',1),(14,32,'Page Tags','page-tags',1,'PageTags','page-tags',1),(15,32,'Content Blocks','content-blocks',1,'ContentBlocks','content-blocks',1),(16,32,'Pages','pages',1,'Pages','pages',1),(17,5,'Page View','page-view',1,'View','',0),(18,32,'Menu Items','menu-items',1,'MenuItems','menu-items',1),(19,32,'Files','files',1,'Files','files',1),(20,32,'Profile Fields','profile-fields',1,'ProfileFields','profile-fields',1),(21,2,'Profile','account-profile',1,'Profile','profile',0),(22,6,'User Profile','user-profile',1,'User','user',0),(23,2,'Account Settings','account-settings',1,'Settings','settings',0),(24,2,'Reset Password','account-reset',1,'Reset','reset',0),(26,7,'Blog Categories','blog-categories',1,'Categories','blog-category',1),(28,7,'Post','blog-post',1,'Post','post',0),(29,7,'Category','blog-category',1,'Category','category',0),(30,7,'Archive','blog-archive',1,'Archive','archive',0),(31,7,'Blog Comments','blog-comments',0,'Comments','blog-comments',1),(32,6,'Member List','member-list',1,'Members','members',0),(37,32,'App Settings','app-settings',1,'AppSettings','app-settings',1),(38,25,'Forum Categories','forum-categories',1,'Categories','categories',1),(39,25,'Forum Boards','forum-boards',1,'Boards','boards',1),(40,25,'Board','forum-board',1,'Board','board',0),(41,25,'Post','forum-post',1,'Post','post',0),(42,2,'Notification','notification',1,'Notification','notifications',0),(43,7,'Post Metadata Types','blog-post-meta',1,'Meta','meta',1),(46,26,'RSS Feed','rss-feed',1,'Feed','feed',0),(47,27,'Store Categories','store-categories',1,'Categories','categories',1),(48,27,'Store Products','store-products',1,'Products','products',1),(49,7,'Disqus Comments','disqus-comments',1,'Disqus','disqus',1),(50,30,'Share Distributor','share-distribute',1,'Distribute','xcp-distribute',1),(51,30,'Asset Dropper','asset-drop',1,'AssetDrop','asset-drop',1),(52,32,'Notification Pusher','notification-pusher',1,'Notifier','notifier',1),(53,30,'Proof of Participation','ltbcoin-pop',1,'Participation','ltbcoin-pop',1),(54,7,'Magic Words','magic-words',1,'MagicWords','magic-words',1),(59,7,'Magic Word Submissions','magic-word-submits',1,'MagicWordSubmits','all-magic-words',1),(60,2,'Referrals','account-referrals',1,'Referral','referrals',0),(61,30,'Address Manager','address-manager',1,'Address','address-manager',1),(62,2,'Messages','private-message',1,'Message','messages',0),(63,30,'Token Inventory','token-inventory',1,'Inventory','inventory',1),(64,30,'Asset Cache','asset-cache',1,'AssetCache','asset-cache',1),(65,26,'RSS Feed Proxies','rss-feed-proxy',1,'ProxyURLs','proxy-feed-urls',1),(66,26,'Proxy Feed','proxy-feed',1,'Proxy','proxy',0),(67,27,'Orders','store-orders',1,'Order','orders',1),(68,26,'Podcast Proxy','pod-proxy',1,'PodProxy','pod-proxy',0),(69,7,'Blog Submissions','blog-submissions',1,'Submissions','submissions',1),(70,7,'My Blogs','multi-blogs',1,'Multiblog','multi-blogs',1),(71,30,'Asset Scouter','xcp-asset-scout',1,'AssetScout','xcp-asset-scout',1),(73,2,'User Invitiations','user-invite',1,'Invite','invite',0),(74,7,'Newsroom','newsroom',1,'Newsroom','newsroom',1),(75,27,'Payment Collector','payment-collector',1,'Collector','payment-collector',1),(77,31,'Ad URL Tracker','ad-url-tracker',1,'Tracker','ad-tracker',1),(78,31,'Ad Tracking Links','tracking-links',1,'Link','link',0),(79,33,'Address Transaction Reports','accountant-report',1,'Report','tx-report',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=390 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_index`
--

LOCK TABLES `page_index` WRITE;
/*!40000 ALTER TABLE `page_index` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_tags`
--

LOCK TABLES `page_tags` WRITE;
/*!40000 ALTER TABLE `page_tags` DISABLE KEYS */;
INSERT INTO `page_tags` VALUES (1,'CONTACT_FORM','ContactForm'),(3,'REDIRECT','Redirect'),(5,'GOOGLE_FORUMSEARCH','GoogleSearch'),(6,'LTB_STATS','LTBStats'),(8,'FORUM_BUILDER','ForumBuilder'),(18,'HITCOUNTER','HitCounter'),(24,'TOKENSLOT_DEMO','TokenSlotDemo'),(25,'TEST_TAG','Test');
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_collections`
--

DROP TABLE IF EXISTS `payment_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_collections` (
  `collectionId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned DEFAULT '0',
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `source` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `destination` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `asset` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'BTC',
  `txId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `info` longtext COLLATE utf8_unicode_ci,
  `collectionDate` datetime DEFAULT NULL,
  PRIMARY KEY (`collectionId`),
  KEY `userId` (`userId`),
  KEY `type` (`type`),
  KEY `source` (`source`),
  KEY `destination` (`destination`),
  KEY `asset` (`asset`),
  KEY `txId` (`txId`),
  KEY `itemId` (`itemId`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_collections`
--

LOCK TABLES `payment_collections` WRITE;
/*!40000 ALTER TABLE `payment_collections` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_order`
--

DROP TABLE IF EXISTS `payment_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_order` (
  `orderId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderData` longtext COLLATE utf8_unicode_ci,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `account` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `asset` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BTC',
  `received` decimal(20,8) DEFAULT '0.00000000',
  `complete` int(1) DEFAULT '0',
  `orderTime` datetime DEFAULT NULL,
  `orderType` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `completeTime` datetime DEFAULT NULL,
  `collected` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`orderId`),
  KEY `address` (`address`),
  KEY `account` (`account`),
  KEY `asset` (`asset`),
  KEY `orderType` (`orderType`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=496172 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pop_firstView`
--

LOCK TABLES `pop_firstView` WRITE;
/*!40000 ALTER TABLE `pop_firstView` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=93549 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6662 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_fieldGroups`
--

LOCK TABLES `profile_fieldGroups` WRITE;
/*!40000 ALTER TABLE `profile_fieldGroups` DISABLE KEYS */;
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
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`proxyId`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=1118 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
INSERT INTO `settings` VALUES (1,'systemDisabled','0','System Maintenance Mode?',1,0),(2,'disabledMessage','Performing a few upgrades, please check back shortly!','System Maintenance Message',0,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_apps`
--

LOCK TABLES `site_apps` WRITE;
/*!40000 ALTER TABLE `site_apps` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

LOCK TABLES `sites` WRITE;
/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES (1,'Lets Talk Bitcoin 2.0','ltb',1),(4,'Tokenly Blog','tokenly-blog',0);
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
  `reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`accessId`),
  KEY `userId` (`userId`),
  KEY `moduleId` (`moduleId`),
  KEY `itemId` (`itemId`),
  KEY `itemType` (`itemType`),
  KEY `permId` (`permId`),
  KEY `asset` (`asset`),
  CONSTRAINT `token_access_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  CONSTRAINT `token_access_ibfk_2` FOREIGN KEY (`moduleId`) REFERENCES `modules` (`moduleId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token_access`
--

LOCK TABLES `token_access` WRITE;
/*!40000 ALTER TABLE `token_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `token_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracking_clicks`
--

DROP TABLE IF EXISTS `tracking_clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracking_clicks` (
  `clickId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `urlId` int(11) unsigned NOT NULL,
  `userId` int(11) DEFAULT '0',
  `IP` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `click_time` datetime DEFAULT NULL,
  `adspaceId` int(11) unsigned DEFAULT 0,
  PRIMARY KEY (`clickId`),
  KEY `urlId` (`urlId`),
  KEY `userId` (`userId`),
  KEY `IP` (`IP`),
  KEY `request_url` (`request_url`),
  CONSTRAINT `tracking_clicks_ibfk_1` FOREIGN KEY (`urlId`) REFERENCES `tracking_urls` (`urlId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracking_clicks`
--

LOCK TABLES `tracking_clicks` WRITE;
/*!40000 ALTER TABLE `tracking_clicks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracking_clicks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tracking_urls`
--

DROP TABLE IF EXISTS `tracking_urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `last_update` datetime DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,  
  PRIMARY KEY (`urlId`),
  KEY `siteId` (`siteId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  CONSTRAINT `tracking_urls_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE,
  CONSTRAINT `tracking_urls_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tracking_urls`
--

LOCK TABLES `tracking_urls` WRITE;
/*!40000 ALTER TABLE `tracking_urls` DISABLE KEYS */;
/*!40000 ALTER TABLE `tracking_urls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_invites`
--

DROP TABLE IF EXISTS `user_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_invites` (
  `inviteId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned DEFAULT '0',
  `sendUser` int(11) unsigned DEFAULT '0',
  `acceptUser` int(11) unsigned DEFAULT '0',
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `accepted` int(1) DEFAULT '0',
  `acceptCode` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `inviteDate` datetime DEFAULT NULL,
  `acceptDate` datetime DEFAULT NULL,
  `class` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `info` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`inviteId`),
  KEY `userId` (`userId`),
  KEY `sendUser` (`sendUser`),
  KEY `acceptUser` (`acceptUser`),
  KEY `type` (`type`),
  KEY `itemId` (`itemId`),
  KEY `acceptCode` (`acceptCode`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_invites`
--

LOCK TABLES `user_invites` WRITE;
/*!40000 ALTER TABLE `user_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_invites` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=218277 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_likes`
--

LOCK TABLES `user_likes` WRITE;
/*!40000 ALTER TABLE `user_likes` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=79831 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_meta`
--

LOCK TABLES `user_meta` WRITE;
/*!40000 ALTER TABLE `user_meta` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=752406 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=21780 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profileVals`
--

LOCK TABLES `user_profileVals` WRITE;
/*!40000 ALTER TABLE `user_profileVals` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4543 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=37443 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10156 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=442 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6983 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coin_addressTx`
--

CREATE TABLE `coin_addressTx` (
  `addressTxId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `addressId` int(11) unsigned NOT NULL,
  `txId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'btc',
  `txInfo` longtext COLLATE utf8_unicode_ci,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `asset` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'btc',
  PRIMARY KEY (`addressTxId`),
  KEY `addressId` (`addressId`),
  KEY `txId` (`txId`),
  KEY `type` (`type`),
  CONSTRAINT `coin_addressTx_ibfk_1` FOREIGN KEY (`addressId`) REFERENCES `coin_addresses` (`addressId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4330 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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

-- Dump completed on 2015-07-30 21:53:44

--
-- Table structure for table `adspaces`
--


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
