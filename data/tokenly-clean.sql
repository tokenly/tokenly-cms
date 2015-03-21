-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ltb2
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1-log

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
  `valueBlob` LONGBLOB,
  PRIMARY KEY (`appMetaId`),
  KEY `appId` (`appId`),
  KEY `metaKey` (`metaKey`),
  CONSTRAINT `app_meta_ibfk_3` FOREIGN KEY (`appId`) REFERENCES `apps` (`appId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_meta`
--

LOCK TABLES `app_meta` WRITE;
/*!40000 ALTER TABLE `app_meta` DISABLE KEYS */;
INSERT INTO `app_meta` VALUES (1,7,'postsPerPage','30','Posts Per Page','textbox',NULL,1),(4,7,'maxExcerpt','250','Max Post Excerpt Characters','textbox',NULL,1),(5,7,'enableComments','1','Enable Comments','bool',NULL,1),(21,2,'avatarWidth','150','Avatar Width (px)','textbox',NULL,1),(22,2,'avatarHeight','150','Avatar Height (px)','textbox',NULL,1),(23,2,'disableRegister','0','Disable New User Registration','bool',NULL,1),(24,25,'topicsPerPage','60','Topics Per Page','textbox',NULL,1),(25,25,'postsPerPage','10','Topics Replies (posts) Per Page','textbox',NULL,1),(26,25,'forum-title','The LTB Network Forum','Forum Title','textbox',NULL,1),(27,25,'forum-description','This is the home of the LTB content network, where audience and content creators gather to discuss and create great content.','Forum Description','textarea',NULL,1),(28,7,'featuredWidth','600','Featured Image Width (px)','textbox',NULL,1),(29,7,'featuredHeight','372','Featured Image Height (px)','textbox',NULL,1),(31,26,'blog-feed-title','Lets Talk Bitcoin!','Blog Feed Title','textbox',NULL,1),(32,26,'blog-feed-description','','Blog Feed Description','textarea',NULL,1),(33,27,'store-title','Lets Shop Bitcoin!','Store Title','textbox',NULL,1),(34,27,'productsPerPage','20','Products Per Page','textbox',NULL,1),(35,7,'coverWidth','400','Cover Image Width','textbox',NULL,1),(36,7,'coverHeight','400','Cover Image Height','textbox',NULL,1),(37,30,'distribute-fee','0.00001','Share Distributor - per address miner fee','textbox','',1),(38,30,'distribute-dust','0.000025','Share Distributor - dust output BTC value','textbox','',1),(39,30,'pop-comment-weight','8','PoP points per blog comment made','textbox','',1),(40,30,'pop-forum-post-weight','10','PoP points per forum post made','textbox','',1),(41,30,'pop-forum-topic-weight','10','PoP points per forum thread made','textbox','',1),(42,30,'pop-register-weight','0','PoP bonus points for new registrants','textbox','',1),(43,30,'pop-view-weight','3','PoP points per first page view','textbox','',1),(44,30,'distributor-decimals','2','Share Distributor - Round Values to x Decimals','textbox','',1),(45,30,'distribute-batch-size','25','Share Distributor - # Transactions per Batch','textbox','',1),(46,30,'pop-listen-weight','20','PoP - Proof of Listening Weight','textbox','',1),(47,30,'pol-word-expire','96','Proof of Listening - Magic Words expiration (in hours)','textbox','',1),(48,30,'pop-like-weight','1','PoP points per Like','textbox','',1),(49,30,'pop-referral-weight','10','PoP Points per Active Referra','textbox','',1),(50,30,'referral-min-active-pop','10','Min PoP per active referral','textbox','',1),(51,30,'pop-publish-weight','25','PoP points per published blog post','textbox','',1),(52,30,'pop-editor-cut','20','Editor PoP point distribution cut per article (%)','textbox','',1),(53,25,'mod-group','15','Forum Moderator Group ID','textbox','',1),(54,30,'tca-forum-btc-fee','0.1','TCA Forum Builder BTC Cost','textbox','',1),(55,30,'tca-forum-token-fee','50000','TCA Forum Builder Token Cost','textbox','',1),(56,30,'tca-forum-token','LTBCOIN','TCA Forum Builder Token Name','textbox','',1),(57,30,'token-logo-width','150','Token Logo Image Width','textbox','',1),(58,30,'token-logo-height','150','Token Logo Image Height','textbox','',1),(59,30,'tca-forum-category','14','TCA Private Forum Default Category ID','textbox','',1),(60,7,'category-image-width','400','Category Image Width','textbox','',1),(61,7,'category-image-height','400','Category Image Height','textbox','',1),(62,7,'submission-fee','1000','Article Submission Fee','textbox',NULL,1),(63,7,'submission-fee-token','LTBCOIN','Submission Fee Token','textbox',NULL,1),(64,25,'weighted-votes-token','LTBCOIN','Weighted Votes Token','textbox','',1),(65,25,'min-upvote-points','0.05','Minimum Upvote Points','textbox','',1),(66,25,'max-upvote-points','5','Maximum Upvote Points','textbox','',1),(67,25,'weighted-vote-token-cap','500000','Weighted Vote Token Cap','textbox','',1),(68,25,'min-required-upvote-points','5','Minimum Upvote Points Required to Upvote','textbox','',1),(69,30,'poloniex_ticker','{\"BTC_1CR\":{\"last\":\"0.00027100\",\"lowestAsk\":\"0.00034989\",\"highestBid\":\"0.00027120\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_ABY\":{\"last\":\"0.00000036\",\"lowestAsk\":\"0.00000036\",\"highestBid\":\"0.00000032\",\"percentChange\":\"0.12500000\",\"baseVolume\":\"0.18451930\",\"quoteVolume\":\"576415.35886527\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000037\",\"low24hr\":\"0.00000031\"},\"BTC_ACH\":{\"last\":\"0.00000039\",\"lowestAsk\":\"0.00000065\",\"highestBid\":\"0.00000039\",\"percentChange\":\"-0.49350649\",\"baseVolume\":\"0.08413365\",\"quoteVolume\":\"166826.58473717\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000078\",\"low24hr\":\"0.00000039\"},\"BTC_ADN\":{\"last\":\"0.00000100\",\"lowestAsk\":\"0.00000107\",\"highestBid\":\"0.00000100\",\"percentChange\":\"-0.00990099\",\"baseVolume\":\"0.02707321\",\"quoteVolume\":\"26821.99346075\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000101\",\"low24hr\":\"0.00000100\"},\"BTC_BALLS\":{\"last\":\"0.00000036\",\"lowestAsk\":\"0.00000042\",\"highestBid\":\"0.00000036\",\"percentChange\":\"-0.26530612\",\"baseVolume\":\"0.16207468\",\"quoteVolume\":\"403502.19232564\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000052\",\"low24hr\":\"0.00000033\"},\"BTC_BBR\":{\"last\":\"0.00009753\",\"lowestAsk\":\"0.00009895\",\"highestBid\":\"0.00009757\",\"percentChange\":\"-0.08961075\",\"baseVolume\":\"15.01750950\",\"quoteVolume\":\"165599.32026720\",\"isFrozen\":\"0\",\"high24hr\":\"0.00010713\",\"low24hr\":\"0.00008902\"},\"BTC_BCN\":{\"last\":\"0.00000004\",\"lowestAsk\":\"0.00000005\",\"highestBid\":\"0.00000004\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.40903520\",\"quoteVolume\":\"9125061.50996817\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000005\",\"low24hr\":\"0.00000004\"},\"BTC_BELA\":{\"last\":\"0.00000010\",\"lowestAsk\":\"0.00000011\",\"highestBid\":\"0.00000010\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00106251\",\"quoteVolume\":\"10625.14279321\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000010\",\"low24hr\":\"0.00000010\"},\"BTC_BITS\":{\"last\":\"0.00001118\",\"lowestAsk\":\"0.00001118\",\"highestBid\":\"0.00000896\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00153059\",\"quoteVolume\":\"136.90429338\",\"isFrozen\":\"0\",\"high24hr\":\"0.00001118\",\"low24hr\":\"0.00001118\"},\"BTC_BLK\":{\"last\":\"0.00007949\",\"lowestAsk\":\"0.00007917\",\"highestBid\":\"0.00007603\",\"percentChange\":\"-0.03671837\",\"baseVolume\":\"0.35953157\",\"quoteVolume\":\"4698.41995073\",\"isFrozen\":\"0\",\"high24hr\":\"0.00008267\",\"low24hr\":\"0.00007468\"},\"BTC_BLOCK\":{\"last\":\"0.00010997\",\"lowestAsk\":\"0.00011391\",\"highestBid\":\"0.00009460\",\"percentChange\":\"-0.09070614\",\"baseVolume\":\"0.59535921\",\"quoteVolume\":\"6453.97034780\",\"isFrozen\":\"0\",\"high24hr\":\"0.00012100\",\"low24hr\":\"0.00009000\"},\"BTC_BTCD\":{\"last\":\"0.00434470\",\"lowestAsk\":\"0.00464355\",\"highestBid\":\"0.00434471\",\"percentChange\":\"0.00875085\",\"baseVolume\":\"0.22465503\",\"quoteVolume\":\"48.09206553\",\"isFrozen\":\"0\",\"high24hr\":\"0.00477983\",\"low24hr\":\"0.00430700\"},\"BTC_BTM\":{\"last\":\"0.00023421\",\"lowestAsk\":\"0.00023421\",\"highestBid\":\"0.00019326\",\"percentChange\":\"0.23138801\",\"baseVolume\":\"0.48429454\",\"quoteVolume\":\"2121.81074505\",\"isFrozen\":\"0\",\"high24hr\":\"0.00023421\",\"low24hr\":\"0.00019020\"},\"BTC_BTS\":{\"last\":\"0.00003547\",\"lowestAsk\":\"0.00003674\",\"highestBid\":\"0.00003547\",\"percentChange\":\"-0.10632401\",\"baseVolume\":\"10.91181943\",\"quoteVolume\":\"294161.00541231\",\"isFrozen\":\"0\",\"high24hr\":\"0.00003969\",\"low24hr\":\"0.00003400\"},\"BTC_BURST\":{\"last\":\"0.00000177\",\"lowestAsk\":\"0.00000177\",\"highestBid\":\"0.00000175\",\"percentChange\":\"-0.01117318\",\"baseVolume\":\"11.41294971\",\"quoteVolume\":\"6352155.25020166\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000191\",\"low24hr\":\"0.00000172\"},\"BTC_C2\":{\"last\":\"0.00000110\",\"lowestAsk\":\"0.00000114\",\"highestBid\":\"0.00000110\",\"percentChange\":\"-0.00900901\",\"baseVolume\":\"0.08066735\",\"quoteVolume\":\"73247.01527320\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000135\",\"low24hr\":\"0.00000110\"},\"BTC_CCN\":{\"last\":\"0.00004970\",\"lowestAsk\":\"0.00005655\",\"highestBid\":\"0.00004970\",\"percentChange\":\"-0.04423077\",\"baseVolume\":\"0.01732359\",\"quoteVolume\":\"344.38044095\",\"isFrozen\":\"0\",\"high24hr\":\"0.00005200\",\"low24hr\":\"0.00004970\"},\"BTC_CGA\":{\"last\":\"0.00002228\",\"lowestAsk\":\"0.00003899\",\"highestBid\":\"0.00002228\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00011018\",\"quoteVolume\":\"4.94545221\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002228\",\"low24hr\":\"0.00002228\"},\"BTC_CHA\":{\"last\":\"0.00015288\",\"lowestAsk\":\"0.00029562\",\"highestBid\":\"0.00014101\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_CINNI\":{\"last\":\"0.00000381\",\"lowestAsk\":\"0.00000499\",\"highestBid\":\"0.00000380\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_CLAM\":{\"last\":\"0.00694996\",\"lowestAsk\":\"0.00690000\",\"highestBid\":\"0.00678580\",\"percentChange\":\"0.01904526\",\"baseVolume\":\"13.08652952\",\"quoteVolume\":\"1914.53288487\",\"isFrozen\":\"0\",\"high24hr\":\"0.00704996\",\"low24hr\":\"0.00640000\"},\"BTC_CNMT\":{\"last\":\"0.00021658\",\"lowestAsk\":\"0.00029999\",\"highestBid\":\"0.00018440\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_CNOTE\":{\"last\":\"0.00000004\",\"lowestAsk\":\"0.00000005\",\"highestBid\":\"0.00000004\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00481600\",\"quoteVolume\":\"120399.99680000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000004\",\"low24hr\":\"0.00000004\"},\"BTC_CURE\":{\"last\":\"0.00003002\",\"lowestAsk\":\"0.00003192\",\"highestBid\":\"0.00003037\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.01631716\",\"quoteVolume\":\"525.06410347\",\"isFrozen\":\"0\",\"high24hr\":\"0.00003364\",\"low24hr\":\"0.00003002\"},\"BTC_CYC\":{\"last\":\"0.00000038\",\"lowestAsk\":\"0.00000039\",\"highestBid\":\"0.00000037\",\"percentChange\":\"-0.05000000\",\"baseVolume\":\"0.00353759\",\"quoteVolume\":\"8965.87936287\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000040\",\"low24hr\":\"0.00000038\"},\"BTC_DGB\":{\"last\":\"0.00000073\",\"lowestAsk\":\"0.00000075\",\"highestBid\":\"0.00000074\",\"percentChange\":\"0.01388889\",\"baseVolume\":\"0.22139196\",\"quoteVolume\":\"297015.75045576\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000079\",\"low24hr\":\"0.00000070\"},\"BTC_DIEM\":{\"last\":\"0.00000001\",\"lowestAsk\":\"0.00000001\",\"highestBid\":\"0.00000000\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.01019901\",\"quoteVolume\":\"1019901.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000001\",\"low24hr\":\"0.00000001\"},\"BTC_DOGE\":{\"last\":\"0.00000060\",\"lowestAsk\":\"0.00000061\",\"highestBid\":\"0.00000060\",\"percentChange\":\"-0.03225806\",\"baseVolume\":\"9.57156666\",\"quoteVolume\":\"15912913.70865411\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000062\",\"low24hr\":\"0.00000059\"},\"BTC_DRK\":{\"last\":\"0.01200657\",\"lowestAsk\":\"0.01215729\",\"highestBid\":\"0.01201414\",\"percentChange\":\"-0.07186124\",\"baseVolume\":\"41.70323072\",\"quoteVolume\":\"3326.39640062\",\"isFrozen\":\"0\",\"high24hr\":\"0.01320800\",\"low24hr\":\"0.01200039\"},\"BTC_EMC2\":{\"last\":\"0.00000094\",\"lowestAsk\":\"0.00000099\",\"highestBid\":\"0.00000094\",\"percentChange\":\"-0.09615385\",\"baseVolume\":\"0.30092448\",\"quoteVolume\":\"308929.50418807\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000112\",\"low24hr\":\"0.00000088\"},\"BTC_EXE\":{\"last\":\"0.00000142\",\"lowestAsk\":\"0.00000166\",\"highestBid\":\"0.00000136\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00310412\",\"quoteVolume\":\"2135.85879517\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000149\",\"low24hr\":\"0.00000142\"},\"BTC_FIBRE\":{\"last\":\"0.00068999\",\"lowestAsk\":\"0.00072487\",\"highestBid\":\"0.00051370\",\"percentChange\":\"0.72506125\",\"baseVolume\":\"0.26879270\",\"quoteVolume\":\"589.22003160\",\"isFrozen\":\"0\",\"high24hr\":\"0.00068999\",\"low24hr\":\"0.00039998\"},\"BTC_FLDC\":{\"last\":\"0.00000122\",\"lowestAsk\":\"0.00000122\",\"highestBid\":\"0.00000100\",\"percentChange\":\"0.05172414\",\"baseVolume\":\"3.13911340\",\"quoteVolume\":\"2814035.53397065\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000133\",\"low24hr\":\"0.00000097\"},\"BTC_FLT\":{\"last\":\"0.00000113\",\"lowestAsk\":\"0.00000116\",\"highestBid\":\"0.00000111\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_FRAC\":{\"last\":\"0.00002640\",\"lowestAsk\":\"0.00003198\",\"highestBid\":\"0.00002260\",\"percentChange\":\"-0.18769231\",\"baseVolume\":\"0.06855641\",\"quoteVolume\":\"2158.69118880\",\"isFrozen\":\"0\",\"high24hr\":\"0.00003250\",\"low24hr\":\"0.00002640\"},\"BTC_GAP\":{\"last\":\"0.00001295\",\"lowestAsk\":\"0.00001437\",\"highestBid\":\"0.00001292\",\"percentChange\":\"-0.15359477\",\"baseVolume\":\"0.13400952\",\"quoteVolume\":\"10110.64864555\",\"isFrozen\":\"0\",\"high24hr\":\"0.00001530\",\"low24hr\":\"0.00001291\"},\"BTC_GDN\":{\"last\":\"0.00000018\",\"lowestAsk\":\"0.00000022\",\"highestBid\":\"0.00000018\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00268542\",\"quoteVolume\":\"14919.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000018\",\"low24hr\":\"0.00000018\"},\"BTC_GEMZ\":{\"last\":\"0.00007001\",\"lowestAsk\":\"0.00006924\",\"highestBid\":\"0.00006754\",\"percentChange\":\"0.02323882\",\"baseVolume\":\"5.66365966\",\"quoteVolume\":\"80424.71634879\",\"isFrozen\":\"0\",\"high24hr\":\"0.00007299\",\"low24hr\":\"0.00006750\"},\"BTC_GMC\":{\"last\":\"0.00000259\",\"lowestAsk\":\"0.00000259\",\"highestBid\":\"0.00000251\",\"percentChange\":\"-0.15081967\",\"baseVolume\":\"2.44334431\",\"quoteVolume\":\"986399.44606289\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000330\",\"low24hr\":\"0.00000208\"},\"BTC_GML\":{\"last\":\"0.00000005\",\"lowestAsk\":\"0.00000005\",\"highestBid\":\"0.00000004\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_GOLD\":{\"last\":\"0.39999999\",\"lowestAsk\":\"0.64999999\",\"highestBid\":\"0.39999999\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.01229658\",\"quoteVolume\":\"0.03074144\",\"isFrozen\":\"0\",\"high24hr\":\"0.39999999\",\"low24hr\":\"0.39999999\"},\"BTC_GRC\":{\"last\":\"0.00003300\",\"lowestAsk\":\"0.00003780\",\"highestBid\":\"0.00003306\",\"percentChange\":\"-0.12767645\",\"baseVolume\":\"0.26561882\",\"quoteVolume\":\"7038.15951829\",\"isFrozen\":\"0\",\"high24hr\":\"0.00003785\",\"low24hr\":\"0.00003300\"},\"BTC_GRS\":{\"last\":\"0.00000160\",\"lowestAsk\":\"0.00000169\",\"highestBid\":\"0.00000160\",\"percentChange\":\"-0.03614458\",\"baseVolume\":\"0.03509309\",\"quoteVolume\":\"20841.70808634\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000169\",\"low24hr\":\"0.00000160\"},\"BTC_HIRO\":{\"last\":\"0.00000005\",\"lowestAsk\":\"0.00000006\",\"highestBid\":\"0.00000005\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.01526900\",\"quoteVolume\":\"305380.04835342\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000005\",\"low24hr\":\"0.00000005\"},\"BTC_HUC\":{\"last\":\"0.00000560\",\"lowestAsk\":\"0.00000618\",\"highestBid\":\"0.00000389\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"1\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_HYP\":{\"last\":\"0.00000213\",\"lowestAsk\":\"0.00000220\",\"highestBid\":\"0.00000214\",\"percentChange\":\"-0.02739726\",\"baseVolume\":\"1.70524129\",\"quoteVolume\":\"811508.75131679\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000229\",\"low24hr\":\"0.00000201\"},\"BTC_HZ\":{\"last\":\"0.00000042\",\"lowestAsk\":\"0.00000046\",\"highestBid\":\"0.00000042\",\"percentChange\":\"-0.16000000\",\"baseVolume\":\"1.77015354\",\"quoteVolume\":\"4042516.82852127\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000052\",\"low24hr\":\"0.00000040\"},\"BTC_JLH\":{\"last\":\"0.00012641\",\"lowestAsk\":\"0.00012883\",\"highestBid\":\"0.00011718\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_LQD\":{\"last\":\"0.00500170\",\"lowestAsk\":\"0.00589998\",\"highestBid\":\"0.00500170\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_LTBC\":{\"last\":\"0.00000093\",\"lowestAsk\":\"0.00000095\",\"highestBid\":\"0.00000088\",\"percentChange\":\"-0.09708738\",\"baseVolume\":\"0.43004360\",\"quoteVolume\":\"461754.13048105\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000103\",\"low24hr\":\"0.00000088\"},\"BTC_LTC\":{\"last\":\"0.00763727\",\"lowestAsk\":\"0.00763583\",\"highestBid\":\"0.00752183\",\"percentChange\":\"-0.03193598\",\"baseVolume\":\"5.28270029\",\"quoteVolume\":\"691.89220738\",\"isFrozen\":\"0\",\"high24hr\":\"0.00788922\",\"low24hr\":\"0.00750001\"},\"BTC_MAID\":{\"last\":\"0.00012950\",\"lowestAsk\":\"0.00013205\",\"highestBid\":\"0.00012956\",\"percentChange\":\"0.00177922\",\"baseVolume\":\"8.06388717\",\"quoteVolume\":\"61147.45489053\",\"isFrozen\":\"0\",\"high24hr\":\"0.00013550\",\"low24hr\":\"0.00012927\"},\"BTC_MCN\":{\"last\":\"0.00000110\",\"lowestAsk\":\"0.00000158\",\"highestBid\":\"0.00000112\",\"percentChange\":\"-0.05982906\",\"baseVolume\":\"0.20649499\",\"quoteVolume\":\"182276.62736270\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000160\",\"low24hr\":\"0.00000110\"},\"BTC_MIL\":{\"last\":\"0.00000600\",\"lowestAsk\":\"0.00001196\",\"highestBid\":\"0.00000600\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_MINT\":{\"last\":\"0.00000011\",\"lowestAsk\":\"0.00000012\",\"highestBid\":\"0.00000011\",\"percentChange\":\"1.20000000\",\"baseVolume\":\"6.00454189\",\"quoteVolume\":\"55777261.81666081\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000018\",\"low24hr\":\"0.00000005\"},\"BTC_MMC\":{\"last\":\"0.00000303\",\"lowestAsk\":\"0.00001000\",\"highestBid\":\"0.00000303\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_MMNXT\":{\"last\":\"0.00004801\",\"lowestAsk\":\"0.00005248\",\"highestBid\":\"0.00004805\",\"percentChange\":\"-0.05156065\",\"baseVolume\":\"0.03539102\",\"quoteVolume\":\"718.62947258\",\"isFrozen\":\"0\",\"high24hr\":\"0.00005062\",\"low24hr\":\"0.00004801\"},\"BTC_MMXIV\":{\"last\":\"0.00431230\",\"lowestAsk\":\"0.00549994\",\"highestBid\":\"0.00431230\",\"percentChange\":\"-0.40275997\",\"baseVolume\":\"0.71871421\",\"quoteVolume\":\"131.98135323\",\"isFrozen\":\"0\",\"high24hr\":\"0.00722038\",\"low24hr\":\"0.00431222\"},\"BTC_MNS1\":{\"last\":\"0.00212969\",\"lowestAsk\":\"0.00212969\",\"highestBid\":\"0.00168100\",\"percentChange\":\"0.00000470\",\"baseVolume\":\"0.00180024\",\"quoteVolume\":\"0.84530912\",\"isFrozen\":\"0\",\"high24hr\":\"0.00212969\",\"low24hr\":\"0.00212968\"},\"BTC_MRS\":{\"last\":\"0.00000037\",\"lowestAsk\":\"0.00000049\",\"highestBid\":\"0.00000037\",\"percentChange\":\"-0.44776119\",\"baseVolume\":\"0.00587530\",\"quoteVolume\":\"15075.80060000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000067\",\"low24hr\":\"0.00000037\"},\"BTC_MSC\":{\"last\":\"0.00950000\",\"lowestAsk\":\"0.01099979\",\"highestBid\":\"0.00951100\",\"percentChange\":\"-0.13636678\",\"baseVolume\":\"1.20367010\",\"quoteVolume\":\"110.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.01100004\",\"low24hr\":\"0.00950000\"},\"BTC_MYR\":{\"last\":\"0.00000033\",\"lowestAsk\":\"0.00000036\",\"highestBid\":\"0.00000034\",\"percentChange\":\"-0.05714286\",\"baseVolume\":\"0.36858484\",\"quoteVolume\":\"1087564.80889489\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000035\",\"low24hr\":\"0.00000032\"},\"BTC_NAUT\":{\"last\":\"0.00005278\",\"lowestAsk\":\"0.00006923\",\"highestBid\":\"0.00005257\",\"percentChange\":\"-0.24535316\",\"baseVolume\":\"0.00950373\",\"quoteVolume\":\"151.38543179\",\"isFrozen\":\"0\",\"high24hr\":\"0.00006994\",\"low24hr\":\"0.00005278\"},\"BTC_NAV\":{\"last\":\"0.00002999\",\"lowestAsk\":\"0.00002999\",\"highestBid\":\"0.00002740\",\"percentChange\":\"0.05043783\",\"baseVolume\":\"0.26470643\",\"quoteVolume\":\"9773.61471689\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002999\",\"low24hr\":\"0.00002544\"},\"BTC_NBT\":{\"last\":\"0.00260000\",\"lowestAsk\":\"0.00405711\",\"highestBid\":\"0.00270020\",\"percentChange\":\"-0.35804530\",\"baseVolume\":\"0.05368785\",\"quoteVolume\":\"14.38666518\",\"isFrozen\":\"0\",\"high24hr\":\"0.00406972\",\"low24hr\":\"0.00260000\"},\"BTC_NEOS\":{\"last\":\"0.00002070\",\"lowestAsk\":\"0.00002361\",\"highestBid\":\"0.00002126\",\"percentChange\":\"-0.15268113\",\"baseVolume\":\"0.01421390\",\"quoteVolume\":\"668.05787707\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002443\",\"low24hr\":\"0.00002054\"},\"BTC_NMC\":{\"last\":\"0.00205000\",\"lowestAsk\":\"0.00211000\",\"highestBid\":\"0.00205000\",\"percentChange\":\"0.00001463\",\"baseVolume\":\"0.04854643\",\"quoteVolume\":\"23.69631911\",\"isFrozen\":\"0\",\"high24hr\":\"0.00211999\",\"low24hr\":\"0.00203000\"},\"BTC_NOBL\":{\"last\":\"0.00000008\",\"lowestAsk\":\"0.00000008\",\"highestBid\":\"0.00000007\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00101344\",\"quoteVolume\":\"12668.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000008\",\"low24hr\":\"0.00000008\"},\"BTC_NOTE\":{\"last\":\"0.00003421\",\"lowestAsk\":\"0.00003855\",\"highestBid\":\"0.00003437\",\"percentChange\":\"-0.11784425\",\"baseVolume\":\"1.11351360\",\"quoteVolume\":\"28730.62326819\",\"isFrozen\":\"0\",\"high24hr\":\"0.00004005\",\"low24hr\":\"0.00003420\"},\"BTC_NOXT\":{\"last\":\"0.00012655\",\"lowestAsk\":\"0.00020997\",\"highestBid\":\"0.00012655\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00012655\",\"quoteVolume\":\"1.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00012655\",\"low24hr\":\"0.00012655\"},\"BTC_NRS\":{\"last\":\"0.00001266\",\"lowestAsk\":\"0.00006500\",\"highestBid\":\"0.00000800\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"1\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_NXT\":{\"last\":\"0.00004859\",\"lowestAsk\":\"0.00004860\",\"highestBid\":\"0.00004804\",\"percentChange\":\"-0.01239837\",\"baseVolume\":\"2.80445956\",\"quoteVolume\":\"57317.20757179\",\"isFrozen\":\"0\",\"high24hr\":\"0.00005000\",\"low24hr\":\"0.00004799\"},\"BTC_NXTI\":{\"last\":\"0.00020556\",\"lowestAsk\":\"0.00025845\",\"highestBid\":\"0.00020520\",\"percentChange\":\"-0.11901599\",\"baseVolume\":\"0.14434221\",\"quoteVolume\":\"701.50894271\",\"isFrozen\":\"0\",\"high24hr\":\"0.00023333\",\"low24hr\":\"0.00020556\"},\"BTC_OPAL\":{\"last\":\"0.00003048\",\"lowestAsk\":\"0.00003051\",\"highestBid\":\"0.00002846\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_PIGGY\":{\"last\":\"0.00000015\",\"lowestAsk\":\"0.00000015\",\"highestBid\":\"0.00000010\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.80378488\",\"quoteVolume\":\"7400735.86695182\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000017\",\"low24hr\":\"0.00000009\"},\"BTC_PINK\":{\"last\":\"0.00000044\",\"lowestAsk\":\"0.00000060\",\"highestBid\":\"0.00000041\",\"percentChange\":\"-0.21428571\",\"baseVolume\":\"0.00289642\",\"quoteVolume\":\"5546.90895701\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000056\",\"low24hr\":\"0.00000044\"},\"BTC_POT\":{\"last\":\"0.00000533\",\"lowestAsk\":\"0.00000560\",\"highestBid\":\"0.00000531\",\"percentChange\":\"-0.04651163\",\"baseVolume\":\"0.05785051\",\"quoteVolume\":\"10307.09087722\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000584\",\"low24hr\":\"0.00000533\"},\"BTC_PPC\":{\"last\":\"0.00167000\",\"lowestAsk\":\"0.00171489\",\"highestBid\":\"0.00165001\",\"percentChange\":\"-0.10496077\",\"baseVolume\":\"0.42268636\",\"quoteVolume\":\"243.02733888\",\"isFrozen\":\"0\",\"high24hr\":\"0.00188999\",\"low24hr\":\"0.00167000\"},\"BTC_PRC\":{\"last\":\"0.00000320\",\"lowestAsk\":\"0.00000275\",\"highestBid\":\"0.00000075\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_PTS\":{\"last\":\"0.00000107\",\"lowestAsk\":\"0.00000103\",\"highestBid\":\"0.00000102\",\"percentChange\":\"0.01904762\",\"baseVolume\":\"0.03725453\",\"quoteVolume\":\"35820.84902718\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000109\",\"low24hr\":\"0.00000100\"},\"BTC_QBK\":{\"last\":\"0.00011000\",\"lowestAsk\":\"0.00011000\",\"highestBid\":\"0.00010400\",\"percentChange\":\"0.04761905\",\"baseVolume\":\"0.02168342\",\"quoteVolume\":\"203.76667921\",\"isFrozen\":\"0\",\"high24hr\":\"0.00011000\",\"low24hr\":\"0.00010500\"},\"BTC_QORA\":{\"last\":\"0.00000009\",\"lowestAsk\":\"0.00000010\",\"highestBid\":\"0.00000006\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"1\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_QTL\":{\"last\":\"0.00002435\",\"lowestAsk\":\"0.00002579\",\"highestBid\":\"0.00002449\",\"percentChange\":\"-0.01257097\",\"baseVolume\":\"0.14512940\",\"quoteVolume\":\"5689.88288218\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002697\",\"low24hr\":\"0.00002379\"},\"BTC_RBY\":{\"last\":\"0.00000513\",\"lowestAsk\":\"0.00000749\",\"highestBid\":\"0.00000451\",\"percentChange\":\"0.14000000\",\"baseVolume\":\"0.06783437\",\"quoteVolume\":\"14047.83088440\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000520\",\"low24hr\":\"0.00000450\"},\"BTC_RDD\":{\"last\":\"0.00000010\",\"lowestAsk\":\"0.00000011\",\"highestBid\":\"0.00000010\",\"percentChange\":\"-0.09090909\",\"baseVolume\":\"1.00559130\",\"quoteVolume\":\"10677280.44064082\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000011\",\"low24hr\":\"0.00000009\"},\"BTC_RIC\":{\"last\":\"0.00002200\",\"lowestAsk\":\"0.00002296\",\"highestBid\":\"0.00002200\",\"percentChange\":\"-0.02222222\",\"baseVolume\":\"0.32848391\",\"quoteVolume\":\"14880.25971330\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002338\",\"low24hr\":\"0.00002200\"},\"BTC_SDC\":{\"last\":\"0.00023745\",\"lowestAsk\":\"0.00023746\",\"highestBid\":\"0.00020683\",\"percentChange\":\"0.08148114\",\"baseVolume\":\"0.00188092\",\"quoteVolume\":\"7.95563855\",\"isFrozen\":\"0\",\"high24hr\":\"0.00023745\",\"low24hr\":\"0.00021956\"},\"BTC_SILK\":{\"last\":\"0.00000096\",\"lowestAsk\":\"0.00000093\",\"highestBid\":\"0.00000080\",\"percentChange\":\"0.26315789\",\"baseVolume\":\"0.03909513\",\"quoteVolume\":\"48054.16266391\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000105\",\"low24hr\":\"0.00000075\"},\"BTC_SJCX\":{\"last\":\"0.00010029\",\"lowestAsk\":\"0.00010229\",\"highestBid\":\"0.00010150\",\"percentChange\":\"0.00259922\",\"baseVolume\":\"4.01197651\",\"quoteVolume\":\"39842.51980226\",\"isFrozen\":\"0\",\"high24hr\":\"0.00010609\",\"low24hr\":\"0.00009690\"},\"BTC_SQL\":{\"last\":\"0.00000414\",\"lowestAsk\":\"0.00001100\",\"highestBid\":\"0.00000414\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_SRCC\":{\"last\":\"0.00000500\",\"lowestAsk\":\"0.00000550\",\"highestBid\":\"0.00000014\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_SSD\":{\"last\":\"0.00000246\",\"lowestAsk\":\"0.00000260\",\"highestBid\":\"0.00000246\",\"percentChange\":\"-0.14285714\",\"baseVolume\":\"0.00401498\",\"quoteVolume\":\"1509.22747102\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000302\",\"low24hr\":\"0.00000246\"},\"BTC_STR\":{\"last\":\"0.00001468\",\"lowestAsk\":\"0.00001466\",\"highestBid\":\"0.00001452\",\"percentChange\":\"-0.03230059\",\"baseVolume\":\"57.62645070\",\"quoteVolume\":\"3868963.71617087\",\"isFrozen\":\"0\",\"high24hr\":\"0.00001598\",\"low24hr\":\"0.00001450\"},\"BTC_SWARM\":{\"last\":\"0.00005594\",\"lowestAsk\":\"0.00005448\",\"highestBid\":\"0.00005000\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_SYNC\":{\"last\":\"0.36100000\",\"lowestAsk\":\"0.40099997\",\"highestBid\":\"0.30000023\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_SYS\":{\"last\":\"0.00000137\",\"lowestAsk\":\"0.00000146\",\"highestBid\":\"0.00000137\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.01300145\",\"quoteVolume\":\"9522.81839382\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000137\",\"low24hr\":\"0.00000136\"},\"BTC_TAC\":{\"last\":\"0.00000010\",\"lowestAsk\":\"0.00000017\",\"highestBid\":\"0.00000010\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00010815\",\"quoteVolume\":\"1081.47396758\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000010\",\"low24hr\":\"0.00000010\"},\"BTC_UIS\":{\"last\":\"0.00000138\",\"lowestAsk\":\"0.00000162\",\"highestBid\":\"0.00000131\",\"percentChange\":\"-0.01428571\",\"baseVolume\":\"0.00347506\",\"quoteVolume\":\"2329.14882353\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000152\",\"low24hr\":\"0.00000138\"},\"BTC_ULTC\":{\"last\":\"0.00002482\",\"lowestAsk\":\"0.00002477\",\"highestBid\":\"0.00002295\",\"percentChange\":\"0.08195292\",\"baseVolume\":\"0.00686500\",\"quoteVolume\":\"296.98242098\",\"isFrozen\":\"0\",\"high24hr\":\"0.00002482\",\"low24hr\":\"0.00002294\"},\"BTC_UNITY\":{\"last\":\"0.00910005\",\"lowestAsk\":\"0.01176959\",\"highestBid\":\"0.00935002\",\"percentChange\":\"-0.18421784\",\"baseVolume\":\"0.08911203\",\"quoteVolume\":\"8.34552190\",\"isFrozen\":\"0\",\"high24hr\":\"0.01115500\",\"low24hr\":\"0.00910005\"},\"BTC_URO\":{\"last\":\"0.00113468\",\"lowestAsk\":\"0.00112690\",\"highestBid\":\"0.00109128\",\"percentChange\":\"-0.11844865\",\"baseVolume\":\"0.03927390\",\"quoteVolume\":\"32.56853065\",\"isFrozen\":\"0\",\"high24hr\":\"0.00137023\",\"low24hr\":\"0.00111798\"},\"BTC_VIA\":{\"last\":\"0.00009367\",\"lowestAsk\":\"0.00010236\",\"highestBid\":\"0.00009246\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_VRC\":{\"last\":\"0.00005704\",\"lowestAsk\":\"0.00005704\",\"highestBid\":\"0.00005651\",\"percentChange\":\"-0.03387534\",\"baseVolume\":\"7.21485932\",\"quoteVolume\":\"129431.44327720\",\"isFrozen\":\"0\",\"high24hr\":\"0.00006158\",\"low24hr\":\"0.00005155\"},\"BTC_VTC\":{\"last\":\"0.00006001\",\"lowestAsk\":\"0.00006439\",\"highestBid\":\"0.00006001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.27679631\",\"quoteVolume\":\"4608.85569233\",\"isFrozen\":\"0\",\"high24hr\":\"0.00006448\",\"low24hr\":\"0.00006000\"},\"BTC_WDC\":{\"last\":\"0.00001765\",\"lowestAsk\":\"0.00001879\",\"highestBid\":\"0.00001821\",\"percentChange\":\"-0.04697624\",\"baseVolume\":\"0.00626836\",\"quoteVolume\":\"344.42296533\",\"isFrozen\":\"0\",\"high24hr\":\"0.00001852\",\"low24hr\":\"0.00001763\"},\"BTC_WOLF\":{\"last\":\"0.00002033\",\"lowestAsk\":\"0.00009000\",\"highestBid\":\"0.00002033\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_XAP\":{\"last\":\"0.00007400\",\"lowestAsk\":\"0.00008190\",\"highestBid\":\"0.00007400\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00330963\",\"quoteVolume\":\"44.72471587\",\"isFrozen\":\"0\",\"high24hr\":\"0.00007400\",\"low24hr\":\"0.00007400\"},\"BTC_XBC\":{\"last\":\"0.00128998\",\"lowestAsk\":\"0.00128999\",\"highestBid\":\"0.00085002\",\"percentChange\":\"0.63292742\",\"baseVolume\":\"0.45433353\",\"quoteVolume\":\"425.69852758\",\"isFrozen\":\"0\",\"high24hr\":\"0.00150000\",\"low24hr\":\"0.00078998\"},\"BTC_XC\":{\"last\":\"0.00017988\",\"lowestAsk\":\"0.00017000\",\"highestBid\":\"0.00012534\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_XCH\":{\"last\":\"0.00000306\",\"lowestAsk\":\"0.00000306\",\"highestBid\":\"0.00000231\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_XCN\":{\"last\":\"0.00000090\",\"lowestAsk\":\"0.00000091\",\"highestBid\":\"0.00000090\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00044311\",\"quoteVolume\":\"492.34834784\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000090\",\"low24hr\":\"0.00000090\"},\"BTC_XCP\":{\"last\":\"0.00592397\",\"lowestAsk\":\"0.00608532\",\"highestBid\":\"0.00598331\",\"percentChange\":\"-0.02447712\",\"baseVolume\":\"11.47450569\",\"quoteVolume\":\"1820.40204658\",\"isFrozen\":\"0\",\"high24hr\":\"0.00649900\",\"low24hr\":\"0.00592393\"},\"BTC_XCR\":{\"last\":\"0.00000829\",\"lowestAsk\":\"0.00000829\",\"highestBid\":\"0.00000500\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_XDN\":{\"last\":\"0.00000040\",\"lowestAsk\":\"0.00000041\",\"highestBid\":\"0.00000040\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.69693876\",\"quoteVolume\":\"1738054.42197977\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000041\",\"low24hr\":\"0.00000039\"},\"BTC_XDP\":{\"last\":\"0.00013484\",\"lowestAsk\":\"0.00013493\",\"highestBid\":\"0.00012005\",\"percentChange\":\"-0.00199837\",\"baseVolume\":\"2.27905434\",\"quoteVolume\":\"18157.76705283\",\"isFrozen\":\"0\",\"high24hr\":\"0.00014777\",\"low24hr\":\"0.00011001\"},\"BTC_XMG\":{\"last\":\"0.00005603\",\"lowestAsk\":\"0.00005604\",\"highestBid\":\"0.00005234\",\"percentChange\":\"0.07172915\",\"baseVolume\":\"0.03054740\",\"quoteVolume\":\"568.89339328\",\"isFrozen\":\"0\",\"high24hr\":\"0.00005699\",\"low24hr\":\"0.00005228\"},\"BTC_XMR\":{\"last\":\"0.00108233\",\"lowestAsk\":\"0.00108208\",\"highestBid\":\"0.00107456\",\"percentChange\":\"-0.00902773\",\"baseVolume\":\"41.13459642\",\"quoteVolume\":\"38033.58865694\",\"isFrozen\":\"0\",\"high24hr\":\"0.00110000\",\"low24hr\":\"0.00106303\"},\"BTC_XPB\":{\"last\":\"0.00000725\",\"lowestAsk\":\"0.00000995\",\"highestBid\":\"0.00000801\",\"percentChange\":\"-0.19354839\",\"baseVolume\":\"0.21111325\",\"quoteVolume\":\"28645.80550945\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000899\",\"low24hr\":\"0.00000707\"},\"BTC_XPM\":{\"last\":\"0.00014222\",\"lowestAsk\":\"0.00015218\",\"highestBid\":\"0.00014671\",\"percentChange\":\"-0.08233320\",\"baseVolume\":\"0.16743629\",\"quoteVolume\":\"1094.67955360\",\"isFrozen\":\"0\",\"high24hr\":\"0.00015499\",\"low24hr\":\"0.00014101\"},\"BTC_XRP\":{\"last\":\"0.00005870\",\"lowestAsk\":\"0.00005879\",\"highestBid\":\"0.00005773\",\"percentChange\":\"-0.06155076\",\"baseVolume\":\"19.79014968\",\"quoteVolume\":\"330830.82015824\",\"isFrozen\":\"0\",\"high24hr\":\"0.00006288\",\"low24hr\":\"0.00005713\"},\"BTC_XST\":{\"last\":\"0.00002879\",\"lowestAsk\":\"0.00002878\",\"highestBid\":\"0.00002431\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"BTC_YACC\":{\"last\":\"0.00000002\",\"lowestAsk\":\"0.00000003\",\"highestBid\":\"0.00000002\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00548335\",\"quoteVolume\":\"269167.53856148\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000003\",\"low24hr\":\"0.00000002\"},\"XMR_BBR\":{\"last\":\"0.04600101\",\"lowestAsk\":\"0.13369999\",\"highestBid\":\"0.05000003\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_BCN\":{\"last\":\"0.00003410\",\"lowestAsk\":\"0.00004887\",\"highestBid\":\"0.00003659\",\"percentChange\":\"0.00058685\",\"baseVolume\":\"0.06513705\",\"quoteVolume\":\"1910.59433216\",\"isFrozen\":\"0\",\"high24hr\":\"0.00003410\",\"low24hr\":\"0.00003408\"},\"XMR_BLK\":{\"last\":\"0.04100069\",\"lowestAsk\":\"0.10000338\",\"highestBid\":\"0.04700006\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_BTCD\":{\"last\":\"3.75000000\",\"lowestAsk\":\"7.31000001\",\"highestBid\":\"3.75000001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.14437028\",\"quoteVolume\":\"0.03849874\",\"isFrozen\":\"0\",\"high24hr\":\"3.75000000\",\"low24hr\":\"3.75000000\"},\"XMR_DIEM\":{\"last\":\"0.00000120\",\"lowestAsk\":\"0.00000118\",\"highestBid\":\"0.00000103\",\"percentChange\":\"0.22448980\",\"baseVolume\":\"1.23500850\",\"quoteVolume\":\"1216148.30633333\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000120\",\"low24hr\":\"0.00000098\"},\"XMR_DRK\":{\"last\":\"17.21500000\",\"lowestAsk\":\"17.21499999\",\"highestBid\":\"9.10000001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_DSH\":{\"last\":\"0.00000022\",\"lowestAsk\":\"0.00000024\",\"highestBid\":\"0.00000022\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"6.20752759\",\"quoteVolume\":\"28147680.26674256\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000024\",\"low24hr\":\"0.00000022\"},\"XMR_HYP\":{\"last\":\"0.00219999\",\"lowestAsk\":\"0.00216000\",\"highestBid\":\"0.00160001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_IFC\":{\"last\":\"0.00001012\",\"lowestAsk\":\"0.00001012\",\"highestBid\":\"0.00000609\",\"percentChange\":\"0.68666667\",\"baseVolume\":\"0.91483171\",\"quoteVolume\":\"98052.11772306\",\"isFrozen\":\"0\",\"high24hr\":\"0.00001012\",\"low24hr\":\"0.00000600\"},\"XMR_LTC\":{\"last\":\"6.03000000\",\"lowestAsk\":\"10.00000000\",\"highestBid\":\"6.03000001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.07700551\",\"quoteVolume\":\"0.01277040\",\"isFrozen\":\"0\",\"high24hr\":\"6.03000001\",\"low24hr\":\"6.03000000\"},\"XMR_MAID\":{\"last\":\"0.15823645\",\"lowestAsk\":\"0.15823642\",\"highestBid\":\"0.07994201\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_MNTA\":{\"last\":\"0.00008500\",\"lowestAsk\":\"0.00013000\",\"highestBid\":\"0.00002033\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_NXT\":{\"last\":\"0.05650000\",\"lowestAsk\":\"0.05649999\",\"highestBid\":\"0.04600001\",\"percentChange\":\"0.22826087\",\"baseVolume\":\"0.14408154\",\"quoteVolume\":\"3.13042208\",\"isFrozen\":\"0\",\"high24hr\":\"0.05650000\",\"low24hr\":\"0.04600000\"},\"XMR_QORA\":{\"last\":\"0.00005012\",\"lowestAsk\":\"0.00012495\",\"highestBid\":\"0.00005016\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"1\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XMR_XDN\":{\"last\":\"0.00031803\",\"lowestAsk\":\"0.00042879\",\"highestBid\":\"0.00031803\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XUSD_BTC\":{\"last\":\"250.00000000\",\"lowestAsk\":\"250.00000000\",\"highestBid\":\"246.56000744\",\"percentChange\":\"0.05842504\",\"baseVolume\":\"650.66833212\",\"quoteVolume\":\"2.71076613\",\"isFrozen\":\"0\",\"high24hr\":\"250.00000000\",\"low24hr\":\"233.00000000\"},\"XUSD_HYP\":{\"last\":\"0.00020669\",\"lowestAsk\":\"0.00099980\",\"highestBid\":\"0.00021001\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XUSD_LTC\":{\"last\":\"1.90000000\",\"lowestAsk\":\"2.14340000\",\"highestBid\":\"1.88240000\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"13.58201449\",\"quoteVolume\":\"7.14842868\",\"isFrozen\":\"0\",\"high24hr\":\"1.90000000\",\"low24hr\":\"1.90000000\"},\"XUSD_MMXIV\":{\"last\":\"0.30000888\",\"lowestAsk\":\"9.99998799\",\"highestBid\":\"0.50000050\",\"percentChange\":\"0.00000000\",\"baseVolume\":\"0.00000000\",\"quoteVolume\":\"0.00000000\",\"isFrozen\":\"0\",\"high24hr\":\"0.00000000\",\"low24hr\":\"0.00000000\"},\"XUSD_NXT\":{\"last\":\"0.01201400\",\"lowestAsk\":\"0.01328986\",\"highestBid\":\"0.01191200\",\"percentChange\":\"0.01701515\",\"baseVolume\":\"0.38131109\",\"quoteVolume\":\"31.79125889\",\"isFrozen\":\"0\",\"high24hr\":\"0.01225600\",\"low24hr\":\"0.01181300\"},\"XUSD_STR\":{\"last\":\"0.00369560\",\"lowestAsk\":\"0.00369560\",\"highestBid\":\"0.00360731\",\"percentChange\":\"0.01572120\",\"baseVolume\":\"4.91138095\",\"quoteVolume\":\"1338.40299171\",\"isFrozen\":\"0\",\"high24hr\":\"0.00371212\",\"low24hr\":\"0.00360512\"},\"XUSD_XMR\":{\"last\":\"0.26910000\",\"lowestAsk\":\"0.27270000\",\"highestBid\":\"0.26260000\",\"percentChange\":\"0.03779406\",\"baseVolume\":\"112.70563441\",\"quoteVolume\":\"423.33702814\",\"isFrozen\":\"0\",\"high24hr\":\"0.36099993\",\"low24hr\":\"0.25650000\"},\"XUSD_XRP\":{\"last\":\"0.01473055\",\"lowestAsk\":\"0.01473055\",\"highestBid\":\"0.01472245\",\"percentChange\":\"-0.01711215\",\"baseVolume\":\"34.24260393\",\"quoteVolume\":\"5635.55232820\",\"isFrozen\":\"0\",\"high24hr\":\"0.01502937\",\"low24hr\":\"0.00421334\"}}','','textbox',NULL,0),(70,30,'btc_rate','{\"24h_avg\":242.01,\"ask\":246.32,\"bid\":245.86,\"last\":246.04,\"timestamp\":\"Sat, 14 Feb 2015 17:47:05 -0000\",\"volume_btc\":132988.15,\"volume_percent\":93.81}','','textbox',NULL,0),(71,25,'min-posts-captcha','10','Minimum post count before CAPTCHA removal','textbox','',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_perms`
--

LOCK TABLES `app_perms` WRITE;
/*!40000 ALTER TABLE `app_perms` DISABLE KEYS */;
INSERT INTO `app_perms` VALUES (1,25,'canPostTopic'),(2,25,'canPostReply'),(3,25,'canEditSelf'),(4,25,'canBurySelf'),(5,25,'canDeleteSelfTopic'),(6,25,'canLockSelf'),(8,25,'canEditOther'),(9,25,'canBuryOther'),(10,25,'canDeleteOtherTopic'),(11,25,'canLockOther'),(13,25,'canStickySelf'),(14,25,'canStickyOther'),(15,25,'canMoveSelf'),(16,25,'canMoveOther'),(17,7,'canPostComment'),(18,7,'canEditSelfComment'),(19,7,'canDeleteSelfComment'),(20,7,'canEditOtherComment'),(21,7,'canDeleteOtherComment'),(22,7,'canWritePost'),(23,7,'canEditSelfPost'),(24,7,'canDeleteSelfPost'),(25,7,'canEditOtherPost'),(26,7,'canDeleteOtherPost'),(27,7,'canPublishPost'),(28,7,'canChangeAuthor'),(29,30,'canDistribute'),(30,30,'canDeleteDistribution'),(31,30,'canChangeDistributeStatus'),(32,30,'canChangeDistributeLabels'),(33,7,'canUseMagicWords'),(34,25,'canReportPost'),(35,25,'canReceiveReports'),(36,25,'isTroll'),(37,7,'canSetEditStatus'),(38,7,'canChangeEditor'),(39,25,'canRequestBan'),(40,25,'canReceiveBanRequest'),(41,25,'canPermaDeletePost'),(42,25,'canPermaDeleteTopic'),(43,25,'canChangeBoardOwner'),(44,25,'canChangeBoardCategory'),(45,25,'canManageAllBoards'),(46,30,'canViewAllAssets'),(47,30,'canChangeAssetOwner'),(48,25,'canChangeBoardRank'),(49,7,'canBypassSubmitFee'),(50,25,'canUpvoteDownvote'),(51,7,'canDeleteSelfPostVersion'),(52,7,'canDeleteOtherPostVersion'),(53,7,'canEditAfterPublished'),(54,7,'canManageAllBlogs'),(56,7,'canChangeBlogOwner'),(57,7,'canCreateBlogs');
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
INSERT INTO `block_meta` VALUES (1,20,'inkpad-url','mSkEFhSegx'),(2,21,'inkpad-url','Afp5g1z9gP');
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
  `blogId` int(11) unsigned NOT NULL,
  `public` int(1) DEFAULT '0',
  PRIMARY KEY (`categoryId`),
  KEY `slug` (`slug`),
  KEY `siteId` (`siteId`),
  KEY `blogId` (`blogId`),
  CONSTRAINT `blog_categories_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE,
  CONSTRAINT `blog_categories_ibfk_2` FOREIGN KEY (`blogId`) REFERENCES `blogs` (`blogId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (45,'News','news',0,0,1,'',NULL,10,1),(46,'Network Shows','network-shows',0,0,1,'',NULL,10,1),(47,'General','general',0,0,1,'',NULL,10,1),(48,'Announcements','announcements',0,0,1,'This is a private category',NULL,10,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `share` decimal(20,8) DEFAULT NULL,
  PRIMARY KEY (`contributorId`),
  KEY `postId` (`postId`),
  KEY `inviteId` (`inviteId`),
  KEY `role` (`role`),
  CONSTRAINT `blog_contributors_ibfk_1` FOREIGN KEY (`postId`) REFERENCES `blog_posts` (`postId`) ON DELETE CASCADE,
  CONSTRAINT `blog_contributors_ibfk_2` FOREIGN KEY (`inviteId`) REFERENCES `user_invites` (`inviteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8315 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_postCategories`
--

LOCK TABLES `blog_postCategories` WRITE;
/*!40000 ALTER TABLE `blog_postCategories` DISABLE KEYS */;
INSERT INTO `blog_postCategories` VALUES (8314,980,48,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3595 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=981 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (980,'Test Post','test-post','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',79,1,'2015-02-14 19:50:09','2015-02-14 19:49:00',0,NULL,'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing ...',0,0,NULL,1,0,'2015-02-14 19:50:34','markdown','2015-02-14 19:50:09',0,'ready','',0,736);
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
  `userId` int(11) unsigned DEFAULT 0,
  `type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `token` VARCHAR(100),
  PRIMARY KEY (`userRoleId`),
  KEY `blogId` (`blogId`),
  KEY `userId` (`userId`),
  KEY `token` (`token`),
  CONSTRAINT `blog_roles_ibfk_1` FOREIGN KEY (`blogId`) REFERENCES `blogs` (`blogId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`blogId`),
  KEY `siteId` (`siteId`),
  KEY `userId` (`userId`),
  KEY `slug` (`slug`),
  CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES (10,1,79,'Let\'s Talk Bitcoin!','ltb','',NULL,1,'2015-02-14 19:46:41','2015-02-14 19:46:41');
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
) ENGINE=InnoDB AUTO_INCREMENT=403 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6189 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_blocks`
--

LOCK TABLES `content_blocks` WRITE;
/*!40000 ALTER TABLE `content_blocks` DISABLE KEYS */;
INSERT INTO `content_blocks` VALUES (1,'Footer info','footer-info','<p>Website development by <a href=\"mailto:sales@ironcladtech.ca\"><span style=\"color: #fff;\">IronClad Web Technologies</span></a></p>',1,1,'wysiwyg'),(5,'Homepage Sidebar','home-sidebar','',1,1,'wysiwyg'),(6,'Header Social Media','header-social','',1,1,'wysiwyg'),(7,'Markdown Guide','markdown-guide','<p><strong>Alternate Guides:</strong><br /> <a href=\"https://www.loomio.org/d/Wh7vHaTk/markdown-text-formatting-made-easy\" target=\"_blank\">Loomio</a><br /> <a href=\"https://guides.github.com/features/mastering-markdown/\" target=\"_blank\">Github</a></p>\r\n<h4>Emphasis:</h4>\r\n<blockquote><strong><em>Italic:</em></strong> *emphasize single asterisks* _emphasize single underscores_<br /> <strong>Bold:</strong> **Strong double asterisks** __Strong double underscores__</blockquote>\r\n<p>will produce:</p>\r\n<p><em>emphasize single asterisks</em><br /> <em>emphasize single underscores</em><br /> <strong>Strong double asterisks</strong><br /> <strong>Strong double underscores</strong></p>\r\n<h4>Inline Links:</h4>\r\n<blockquote>A [link](http://www.letstalkbitcoin.com).</blockquote>\r\n<p>Will produce:</p>\r\n<p>A <a href=\"http://www.letstalkbitcoin.com\">link</a></p>\r\n<h4>Referenced Links:</h4>\r\n<blockquote>Some text with [a link][1] and another [link][2].<br /> [1]: http://www.letstalkbitcoin.com/ \"Title1\"<br /> [2]: http://www.google.com/ \"Title2\"</blockquote>\r\n<p>The reference section can be anywhere in the document</p>\r\n<p>Will produce:</p>\r\n<p>Some test with <a href=\"http://www.letstalkbitcoin.com\" title=\"title1\">a link</a> and another <a href=\"http://www.google.com\" title=\"title2\">link</a></p>\r\n<h4>Inline Images:</h4>\r\n<blockquote>Logo: ![Alt](/ltb.png \"Title\")</blockquote>\r\n<p>The \"Alt\" text (alternative text) makes images accessible to visually impaired</p>\r\n<h4>Referenced Images:</h4>\r\n<blockquote>Smaller logo: ![Alt][1]<br /> [1]: /ltb-smaller.png \"Title\"</blockquote>\r\n<p>Inline and referenced images will produce:</p>\r\n<p>Logo: <img src=\"../../../themes/ltb/images/logo.png\" border=\"0\" alt=\"Title\" /></p>\r\n<h4>Linked Images:</h4>\r\n<blockquote>Linked logo: [![alt text](/ltb-smaller.png)](http://letstalkbitcoin.com/)</blockquote>\r\n<p>Linked images will produce:</p>\r\n<p>Linked logo: <a href=\"../../../\" title=\"Title\"><img src=\"../../../themes/ltb/images/logo.png\" border=\"0\" /></a></p>\r\n<h4>Footnotes:</h4>\r\n<blockquote>[^1]: To say down here.</blockquote>\r\n<p>Footnotes will be added to the bottom of the document, with a link back to the original reference</p>\r\n<p>Will produce:</p>\r\n<p><sup><a href=\"#fn1\">1</a></sup>To say down here.<br /> <sup id=\"fn1\">1. [Text of footnote 1]<a href=\"#ref1\" title=\"Jump back to footnote 1 in the text.\">Back</a></sup></p>\r\n<h4>Line Breaks</h4>\r\n<blockquote>\r\n<pre>To make text go down just one line instead of paragraph, add 2 spaces at the end of the line before your  \r\nline break\r\n</pre>\r\n</blockquote>\r\n<h4>Horitzontal Rules:</h4>\r\n<blockquote>* * * *<br /> ****<br /> --------------------------</blockquote>\r\n<p>All of the above will produce a horizontal rule:</p>\r\n<hr />\r\n<h4>Bullet Lists:</h4>\r\n<blockquote>* Item<br /> * Item<br /> - Item<br /> - Item</blockquote>\r\n<p>Will produce:</p>\r\n<ul>\r\n<li>Item</li>\r\n<li>Item</li>\r\n<li>Item</li>\r\n<li>Item</li>\r\n</ul>\r\n<h4>Numbered Lists:</h4>\r\n<blockquote>1. Item<br /> 2. Item</blockquote>\r\n<p>Will produce:</p>\r\n<ol>\r\n<li>Item</li>\r\n<li>Item</li>\r\n</ol>Mixed Lists:\r\n<blockquote>\r\n<pre>1. Item\r\n2. Item\r\n   * Mixed\r\n   * Mixed  \r\n3. Item\r\n	</pre>\r\n</blockquote>\r\n<p>Will produce:</p>\r\n<ol>\r\n<li>Item</li>\r\n<li>Item\r\n<ul>\r\n<li>Mixed</li>\r\n<li>Mixed</li>\r\n</ul>\r\n</li>\r\n<li>Item</li>\r\n</ol>\r\n<h4>Blockquotes:</h4>\r\n<blockquote>&gt; Quoted text.<br /> &gt; &gt; Quoted quote.<br /> &gt; <br /> &gt; * Quoted <br /> &gt; * List</blockquote>\r\n<p>The above example will produce:</p>\r\n<blockquote>Quoted text.\r\n<blockquote>Quoted quote.</blockquote>\r\n<ol>\r\n<li>Quoted</li>\r\n<li>List</li>\r\n</ol></blockquote>\r\n<h4>Preformatted:</h4>\r\n<blockquote>\r\n<pre> Begin each line with \r\n  two spaces or more to \r\n  make text look\r\n  e x a c t l y \r\n  like  you  type i\r\n  t.\r\n  </pre>\r\n</blockquote>\r\n<h4>Code:</h4>\r\n<blockquote>`This is example code`</blockquote>\r\n<h4>Code Block:</h4>\r\n<blockquote>\r\n<pre>~~~~\r\nThis is a \r\npiece of example\r\nin a block\r\n\r\nif(foo == bar){\r\n	echo \'hello\'\r\n}\r\n~~~~\r\n\r\n```\r\nThis too\r\n```\r\n	</pre>\r\n</blockquote>\r\n<p>All of the above example will produce:</p>\r\n<code> This is a piece of example code in a block</code>\r\n<p>P.S. if you need your code to be indented, the HTML result will be:</p>\r\n<pre><code>This is a\r\n	piece of example code\r\n				in a block</code></pre>\r\n<h4>Headers:</h4>\r\n<blockquote># Header 1<br /> ## Header 2<br /> ### Header 3 <br /> #### Header 4 ####<br /> ##### Header 5 #####<br /> ###### Header 6 ######</blockquote>\r\n<p>Closing hash marks are optional on all levels. All of the above can be translated into:</p>\r\n<h1>Header 1</h1>\r\n<h2>Header 2</h2>\r\n<h3>Header 3</h3>\r\n<h4>Header 4</h4>\r\n<h5>Header 5</h5>\r\n<h6>Header 6</h6>\r\n<br />\r\n<h4>Abbreviations:</h4>\r\n<blockquote>*[HTML]: HyperText Markup Language</blockquote>\r\n<p>Will produce:</p>\r\n<p><abbr title=\"HyperText Markup Language\">HTML</abbr></p>\r\n<p>Definitions can be anywhere in the document</p>\r\n<p>For more information about markdown: <a href=\"http://en.wikipedia.org/wiki/Markdown\" target=\"_blank\">http://en.wikipedia.org/wiki/Markdown</a></p>',1,1,'wysiwyg'),(8,'RSS Page Content','rss-content','<h1>RSS Feeds</h1>\r\n<p><strong>Available Feeds</strong></p>\r\n<ul class=\"disc\">\r\n<li><a href=\"rss/feed/blog\" target=\"_blank\">Blog Post Feed</a></li>\r\n</ul>\r\n<h3>Create Custom Feed</h3>\r\n<p>Use the form below to generate a customized RSS feed link.</p>',1,1,'wysiwyg'),(20,'Dashboard Blog Submission Form','dashboard-blog-submission-form','',1,1,'markdown'),(21,'Dashboard Blog Submissions','dashboard-blog-submissions','',1,1,'markdown');
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
) ENGINE=InnoDB AUTO_INCREMENT=737 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dash_menu`
--

LOCK TABLES `dash_menu` WRITE;
/*!40000 ALTER TABLE `dash_menu` DISABLE KEYS */;
INSERT INTO `dash_menu` VALUES (1,1,' ',0,'Dashboard Home',0,NULL),(2,23,' ',1,'Account Settings',0,NULL),(3,21,' ',2,'My Profile',0,NULL),(4,3,' ',600,'Logout',0,''),(5,8,'System',0,'System Stats',1,NULL),(6,9,'System',1,'System Settings',1,NULL),(7,7,'System',2,'Sub-Sites',1,NULL),(8,4,'System',3,'Apps & Modules',1,NULL),(9,12,'System',4,'Themes',1,NULL),(10,25,'System',5,'Dashboard Menu',1,NULL),(11,10,'Users',1,'User Accounts',1,''),(12,11,'Users',1,'Groups',1,NULL),(13,20,'Users',2,'User Profile Fields',1,NULL),(14,16,'CMS',0,'Pages',1,NULL),(15,15,'CMS',1,'Content Blocks',1,NULL),(16,14,'CMS',2,'Page Tags',1,NULL),(17,13,'CMS',3,'Menus',1,NULL),(18,18,'CMS',4,'Menu Items',1,NULL),(19,19,'CMS',5,'File browser',1,NULL),(20,26,'Blog',1,'Categories',1,''),(24,37,'Blog',0,'Blog Settings',1,'/blog'),(39,37,'Users',0,'Account System Settings',1,'/account'),(40,37,'Forum',0,'Forum Settings',1,'/forum'),(41,38,'Forum',1,'Categories',1,''),(42,39,'Forum',2,'Boards',1,''),(43,42,' ',3,'Notifications',0,''),(44,43,'Blog',50,'Post Metadata Types',1,''),(45,37,'RSS',0,'RSS Settings',1,'/rss'),(46,37,'Store',0,'Store Settings',1,'/store'),(47,47,'Store',0,'Categories',1,''),(48,48,'Store',20,'Products',1,''),(49,49,'Blog',100,'Disqus Comments',1,''),(50,50,'LTBcoin',10,'Share Distributor',1,''),(51,51,'LTBcoin',50,'Asset Dropper',1,''),(52,37,'LTBcoin',0,'LTBcoin Settings',1,'/ltbcoin'),(53,52,'System',100,'Notification Pusher',1,''),(54,53,'LTBcoin',160,'Proof of Participation',1,''),(55,54,'LTBcoin',200,'Magic Words',1,''),(56,59,'LTBcoin',220,'Magic Word Submissions',1,''),(57,60,' ',5,'Referrals',0,''),(58,61,'LTBcoin',230,'Address Manager',1,''),(59,62,' ',50,'Private Messages',0,''),(60,63,'LTBcoin',240,'Token Inventory',1,''),(61,64,'LTBcoin',250,'Asset Cache',1,''),(62,65,'RSS',100,'RSS Feed Proxies',1,''),(63,67,'Store',100,'Orders & Payments',1,''),(64,69,'Blog',0,'Submissions',1,''),(65,72,'Blog',0,'My Blogs',1,''),(66,74,'Blog',0,'Newsroom',1,''),(67,75,'LTBcoin',0,'Asset Scouter',1,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
INSERT INTO `forum_categories` VALUES (9,'Audience Hall','audience','<h4>An open forum for listeners and readers to gather, discuss and debate.</h4>',10,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=105694 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8408 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6110 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=1833 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_access`
--

LOCK TABLES `group_access` WRITE;
/*!40000 ALTER TABLE `group_access` DISABLE KEYS */;
INSERT INTO `group_access` VALUES (893,27,1),(1004,29,1),(1005,29,54),(1006,29,61),(1007,29,63),(1159,30,39),(1160,30,64),(1161,31,64),(1455,23,50),(1456,23,51),(1722,1,1),(1723,1,4),(1724,1,7),(1725,1,8),(1726,1,9),(1727,1,10),(1728,1,11),(1729,1,12),(1730,1,13),(1731,1,14),(1732,1,15),(1733,1,16),(1734,1,18),(1735,1,19),(1736,1,20),(1737,1,25),(1738,1,26),(1740,1,31),(1741,1,37),(1742,1,38),(1743,1,39),(1744,1,43),(1745,1,47),(1746,1,48),(1747,1,49),(1748,1,50),(1749,1,51),(1750,1,52),(1751,1,53),(1752,1,54),(1753,1,59),(1754,1,61),(1755,1,63),(1756,1,64),(1757,1,65),(1758,1,67),(1759,1,69),(1760,1,72),(1761,1,60),(1772,1,74),(1773,1,75),(1779,16,1),(1780,16,8),(1781,16,9),(1782,16,10),(1783,16,11),(1784,16,12),(1785,16,13),(1786,16,14),(1787,16,15),(1788,16,16),(1789,16,18),(1790,16,19),(1791,16,20),(1792,16,25),(1793,16,26),(1795,16,31),(1796,16,37),(1797,16,38),(1798,16,39),(1799,16,43),(1800,16,49),(1801,16,50),(1802,16,51),(1803,16,52),(1804,16,53),(1805,16,54),(1806,16,59),(1807,16,61),(1808,16,63),(1809,16,64),(1810,16,65),(1811,16,67),(1812,16,69),(1813,16,74),(1820,1,76),(1821,2,1),(1822,2,54),(1823,2,61),(1824,2,63),(1825,2,69),(1826,2,72),(1827,12,74),(1828,36,1),(1829,36,26),(1830,36,69),(1831,36,72),(1832,36,74);
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
) ENGINE=InnoDB AUTO_INCREMENT=2515 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_perms`
--

LOCK TABLES `group_perms` WRITE;
/*!40000 ALTER TABLE `group_perms` DISABLE KEYS */;
INSERT INTO `group_perms` VALUES (1227,1,29),(1228,2,29),(1229,3,29),(1230,4,29),(1231,5,29),(1232,36,29),(1916,17,23),(1917,18,23),(1918,19,23),(1919,22,23),(1920,23,23),(1921,24,23),(1922,33,23),(1923,49,23),(2162,17,11),(2163,18,11),(2164,19,11),(2165,22,11),(2166,23,11),(2167,24,11),(2168,49,11),(2169,51,11),(2170,53,11),(2338,17,1),(2339,18,1),(2340,19,1),(2341,20,1),(2342,21,1),(2343,22,1),(2344,23,1),(2345,24,1),(2346,25,1),(2347,26,1),(2348,27,1),(2349,28,1),(2350,33,1),(2351,37,1),(2352,38,1),(2353,51,1),(2354,52,1),(2355,53,1),(2356,54,1),(2357,56,1),(2358,57,1),(2359,1,1),(2360,2,1),(2361,3,1),(2362,4,1),(2363,5,1),(2364,6,1),(2365,8,1),(2366,9,1),(2367,10,1),(2368,11,1),(2369,13,1),(2370,14,1),(2371,15,1),(2372,16,1),(2373,34,1),(2374,35,1),(2375,39,1),(2376,40,1),(2377,41,1),(2378,42,1),(2379,43,1),(2380,44,1),(2381,45,1),(2382,48,1),(2383,29,1),(2384,30,1),(2385,31,1),(2386,32,1),(2387,46,1),(2388,47,1),(2413,17,16),(2414,18,16),(2415,19,16),(2416,20,16),(2417,21,16),(2418,22,16),(2419,23,16),(2420,24,16),(2421,25,16),(2422,26,16),(2423,27,16),(2424,28,16),(2425,33,16),(2426,37,16),(2427,38,16),(2428,49,16),(2429,51,16),(2430,52,16),(2431,53,16),(2432,1,16),(2433,2,16),(2434,3,16),(2435,4,16),(2436,5,16),(2437,6,16),(2438,8,16),(2439,9,16),(2440,10,16),(2441,11,16),(2442,13,16),(2443,14,16),(2444,15,16),(2445,16,16),(2446,34,16),(2447,35,16),(2448,39,16),(2449,40,16),(2450,41,16),(2451,42,16),(2452,43,16),(2453,44,16),(2454,45,16),(2455,48,16),(2456,29,16),(2457,30,16),(2458,31,16),(2459,32,16),(2460,46,16),(2461,47,16),(2477,17,2),(2478,18,2),(2479,19,2),(2480,22,2),(2481,23,2),(2482,24,2),(2483,27,2),(2484,49,2),(2485,51,2),(2486,53,2),(2487,1,2),(2488,2,2),(2489,3,2),(2490,6,2),(2491,34,2),(2492,50,2),(2493,17,12),(2494,18,12),(2495,19,12),(2496,22,12),(2497,23,12),(2498,24,12),(2499,25,12),(2500,26,12),(2501,27,12),(2502,37,12),(2503,51,12),(2504,52,12),(2505,53,12),(2506,22,36),(2507,23,36),(2508,24,36),(2509,25,36),(2510,26,36),(2511,27,36),(2512,51,36),(2513,52,36),(2514,53,36);
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
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_sites`
--

LOCK TABLES `group_sites` WRITE;
/*!40000 ALTER TABLE `group_sites` DISABLE KEYS */;
INSERT INTO `group_sites` VALUES (57,27,1),(68,29,1),(77,30,1),(78,31,1),(100,23,1),(116,11,1),(123,1,1),(128,16,1),(130,2,1),(131,12,1),(132,36,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=11313 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_users`
--

LOCK TABLES `group_users` WRITE;
/*!40000 ALTER TABLE `group_users` DISABLE KEYS */;
INSERT INTO `group_users` VALUES (11309,1,79),(11310,2,79),(11311,20,79),(11312,30,79);
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Root Admins','root-admin',0,1),(2,'Default','default',1,1),(11,'Blog Writer','blog-writer',0,1),(12,'Blog Editor','blog-editor',0,1),(16,'Admin','admin',0,1),(20,'Drop List','drop-list',0,1),(23,'Podcaster','podcaster',0,1),(27,'Banned','banned',0,1),(29,'Forum Troll','forum-troll',0,1),(30,'Private Forum Owner','private-forum-owner',0,1),(31,'Asset Owner','asset-owner',0,1),(36,'Blog Owner','blog-owner',0,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_links`
--

LOCK TABLES `menu_links` WRITE;
/*!40000 ALTER TABLE `menu_links` DISABLE KEYS */;
INSERT INTO `menu_links` VALUES (3,2,'/','Home',0,0,0);
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
INSERT INTO `menu_pages` VALUES (32,4,2,'About Us',0,0,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (2,'Main Menu','main',1),(6,'Header Sub Menu','header-sub',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,1,'Dashboard Home','dash-home',1,'Home','',0),(2,2,'Account Home','account-home',1,'Home','',0),(3,2,'Logout','logout',1,'Logout','logout',0),(4,1,'Apps & Modules','modules',1,'Modules','modules',1),(7,1,'Sites','sites',1,'Sites','sites',1),(8,1,'Stats','stats',1,'Stats','stats',1),(9,1,'Settings','settings',1,'Settings','settings',1),(10,1,'Accounts','accounts',1,'Accounts','accounts',1),(11,1,'Groups','groups',1,'Groups','groups',1),(12,1,'Themes','themes',1,'Themes','themes',1),(13,1,'Menus','menus',1,'Menus','menus',1),(14,1,'Page Tags','page-tags',1,'PageTags','page-tags',1),(15,1,'Content Blocks','content-blocks',1,'ContentBlocks','content-blocks',1),(16,1,'Pages','pages',1,'Pages','pages',1),(17,5,'Page View','page-view',1,'View','',0),(18,1,'Menu Items','menu-items',1,'MenuItems','menu-items',1),(19,1,'Files','files',1,'Files','files',1),(20,1,'Profile Fields','profile-fields',1,'ProfileFields','profile-fields',1),(21,2,'Profile','account-profile',1,'Profile','profile',0),(22,6,'User Profile','user-profile',1,'User','user',0),(23,2,'Account Settings','account-settings',1,'Settings','settings',0),(24,2,'Reset Password','account-reset',1,'Reset','reset',0),(25,1,'Dashboard Menu','dash-menu',1,'DashMenu','dash-menu',0),(26,1,'Blog Categories','blog-categories',1,'Blog_Categories','blog-category',1),(28,7,'Post','blog-post',1,'Post','post',0),(29,7,'Category','blog-category',1,'Category','category',0),(30,7,'Archive','blog-archive',1,'Archive','archive',0),(31,1,'Blog Comments','blog-comments',0,'BlogComments','blog-comments',1),(32,6,'Member List','member-list',1,'Members','members',0),(37,1,'App Settings','app-settings',1,'AppSettings','app-settings',1),(38,1,'Forum Categories','forum-categories',1,'ForumCategory','forum-cats',1),(39,1,'Forum Boards','forum-boards',1,'ForumBoard','forum-boards',1),(40,25,'Board','forum-board',1,'Board','board',0),(41,25,'Post','forum-post',1,'Post','post',0),(42,2,'Notification','notification',1,'Notification','notifications',0),(43,1,'Post Metadata Types','blog-post-meta',1,'BlogMeta','blog-meta',1),(46,26,'RSS Feed','rss-feed',1,'Feed','feed',0),(47,1,'Store Categories','store-categories',1,'StoreCategory','store-category',1),(48,1,'Store Products','store-products',1,'StoreProduct','store-product',1),(49,1,'Disqus Comments','disqus-comments',1,'Disqus','disqus',1),(50,1,'Share Distributor','share-distribute',1,'LTBcoin_Distribute','xcp-distribute',1),(51,1,'Asset Dropper','asset-drop',1,'LTBcoin_AssetDrop','asset-drop',1),(52,1,'Notification Pusher','notification-pusher',1,'Notifier','notifier',1),(53,1,'Proof of Participation','ltbcoin-pop',1,'LTBcoin_POP','ltbcoin-pop',1),(54,1,'Magic Words','magic-words',1,'LTBcoin_MagicWords','magic-words',1),(59,1,'Magic Word Submissions','magic-word-submits',1,'LTBcoin_MagicWordSubmits','all-magic-words',1),(60,2,'Referrals','account-referrals',1,'Referral','referrals',0),(61,1,'Address Manager','address-manager',1,'LTBcoin_Address','address-manager',1),(62,2,'Messages','private-message',1,'Message','messages',0),(63,1,'Token Inventory','token-inventory',1,'LTBcoin_Inventory','inventory',1),(64,1,'Asset Cache','asset-cache',1,'LTBcoin_AssetCache','asset-cache',1),(65,1,'RSS Feed Proxies','rss-feed-proxy',1,'RSSProxy','rss-feed-proxy',1),(66,26,'Proxy Feed','proxy-feed',1,'Proxy','proxy',0),(67,1,'Orders','store-orders',1,'Store_Order','store-orders',1),(68,26,'Podcast Proxy','pod-proxy',1,'PodProxy','pod-proxy',0),(69,1,'Blog Submissions','blog-submissions',1,'Blog_Submissions','submissions',1),(72,1,'Multi-Blogs','multi-blogs',1,'Blog_Multiblog','multi-blogs',1),(74,1,'Newsroom','blog-newsroom',1,'Blog_Newsroom','newsroom',1),(75,1,'Asset Scouter','xcp-asset-scout',1,'LTBcoin_AssetScout','xcp-asset-scout',1),(76,2,'User Invitiations','user-invite',1,'Invite','invite',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=383 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_index`
--

LOCK TABLES `page_index` WRITE;
/*!40000 ALTER TABLE `page_index` DISABLE KEYS */;
INSERT INTO `page_index` VALUES (10,'about-us',17,1,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_tags`
--

LOCK TABLES `page_tags` WRITE;
/*!40000 ALTER TABLE `page_tags` DISABLE KEYS */;
INSERT INTO `page_tags` VALUES (1,'CONTACT_FORM','Slick_Tags_ContactForm'),(3,'REDIRECT','Slick_Tags_Redirect'),(6,'LTB_STATS','Slick_Tags_LTBStats'),(8,'FORUM_BUILDER','Slick_Tags_ForumBuilder'),(18,'HITCOUNTER','Slick_Tags_HitCounter');
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (4,'About Us','about-us',1,'default','Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?','',1,'markdown');
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
  `collected` INT(11) unsigned DEFAULT 0,
  PRIMARY KEY (`orderId`),
  KEY `address` (`address`),
  KEY `account` (`account`),
  KEY `asset` (`asset`),
  KEY `orderType` (`orderType`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_order`
--

LOCK TABLES `payment_order` WRITE;
/*!40000 ALTER TABLE `payment_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_order` ENABLE KEYS */;
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
  `type` varchar(100) NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `asset` varchar(100) DEFAULT 'BTC',
  `txId` varchar(255) DEFAULT NULL,
  `itemId` int(11) unsigned DEFAULT '0',
  `info` longtext,
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

LOCK TABLES `payment_order` WRITE;
/*!40000 ALTER TABLE `payment_collections` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_collections` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=439522 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=950 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
INSERT INTO `sites` VALUES (1,'Lets Talk Bitcoin','localhost',1,'http://localhost',1,'1-f06fa465a97db082c010e0dc2f3553f784dc9748ee1b69b8fac352a195ca1da7.jpg');
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
INSERT INTO `stats` VALUES (1,'mostOnline','0'),(2,'_hits','0');
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
  `reference` VARCHAR(100),
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
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=214632 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=68244 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_meta`
--

LOCK TABLES `user_meta` WRITE;
/*!40000 ALTER TABLE `user_meta` DISABLE KEYS */;
INSERT INTO `user_meta` VALUES (165,79,'IP_ADDRESS','127.0.0.1'),(166,79,'site_registered','ltb2.com'),(167,79,'pubProf','1'),(168,79,'last_attempt','2015-02-14 18:58:14'),(169,79,'login_attempts','0'),(170,79,'num_logins','363'),(180,79,'showEmail','1'),(181,79,'avatar',''),(233,79,'emailNotify','1'),(68243,79,'ref-link','41f33484');
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
) ENGINE=InnoDB AUTO_INCREMENT=658175 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
INSERT INTO `user_profileVals` VALUES (56,79,3,'','2015-02-14 20:02:22'),(57,79,7,'','2015-02-14 20:02:22'),(58,79,6,'','2015-02-14 20:02:22'),(59,79,9,'Satoshi','2015-02-14 20:02:22'),(60,79,11,'','2015-02-14 20:02:22'),(262,79,14,'','2015-02-14 20:02:22'),(263,79,12,'','2014-07-28 08:58:17'),(264,79,13,'','2014-06-15 15:58:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=7677 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8762 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (79,'admin','64696f0f7c2733521227930d0325bd4df7fed03084bffcb701d11bb58a28fa43','5da9fc84e4697ef5272ddb276dfeac9020b770cd80d9cb200a76961980067f872c87b565a6adac679aa7a7','admin@example.com','2014-04-23 23:04:23','70263300c7da6f9e8781ddbb0911330ed64e18f81bce00283e844c0f9a277733','2015-02-14 18:58:14','2015-02-14 20:06:25','admin',1,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=5654 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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

-- Dump completed on 2015-02-14 20:20:35

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
  PRIMARY KEY (`urlId`),
  KEY `siteId` (`siteId`),
  KEY `url` (`url`),
  KEY `userId` (`userId`),
  CONSTRAINT `tracking_urls_ibfk_1` FOREIGN KEY (`siteId`) REFERENCES `sites` (`siteId`) ON DELETE CASCADE,
  CONSTRAINT `tracking_urls_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  PRIMARY KEY (`clickId`),
  KEY `urlId` (`urlId`),
  KEY `userId` (`userId`),
  KEY `IP` (`IP`),
  KEY `request_url` (`request_url`),
  CONSTRAINT `tracking_clicks_ibfk_1` FOREIGN KEY (`urlId`) REFERENCES `tracking_urls` (`urlId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

