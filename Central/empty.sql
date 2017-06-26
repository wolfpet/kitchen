-- MySQL dump 10.13  Distrib 5.6.27, for Linux (x86_64)
--
-- Host: localhost    Database: dev
-- ------------------------------------------------------
-- Server version	5.6.27

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
-- Table structure for table `confa_assets`
--

DROP TABLE IF EXISTS `confa_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `msg_id` varchar(255) CHARACTER SET latin1 NOT NULL,
  `URL` text CHARACTER SET latin1,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1452 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_assets`
--

LOCK TABLES `confa_assets` WRITE;
/*!40000 ALTER TABLE `confa_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_ban_history`
--

DROP TABLE IF EXISTS `confa_ban_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_ban_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `moder` int(11) NOT NULL,
  `expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `victim` int(11) NOT NULL,
  `ban_reason` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moder` (`moder`),
  KEY `victim` (`victim`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_ban_history`
--

LOCK TABLES `confa_ban_history` WRITE;
/*!40000 ALTER TABLE `confa_ban_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_ban_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_bookmarks`
--

DROP TABLE IF EXISTS `confa_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_bookmarks_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_bookmarks_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=619 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_bookmarks`
--

LOCK TABLES `confa_bookmarks` WRITE;
/*!40000 ALTER TABLE `confa_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_events`
--

DROP TABLE IF EXISTS `confa_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_owner_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `event_owner_id` int(11) NOT NULL,
  `event_type` int(11) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=950 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_events`
--

LOCK TABLES `confa_events` WRITE;
/*!40000 ALTER TABLE `confa_events` DISABLE KEYS */;
INSERT INTO `confa_events` VALUES (948,0,347977,1086,1,'2017-06-07 18:42:11'),(949,0,347978,1087,1,'2017-06-07 18:48:21');
/*!40000 ALTER TABLE `confa_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_ignor`
--

DROP TABLE IF EXISTS `confa_ignor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_ignor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignored_by` int(11) NOT NULL,
  `ignored` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ignored_by_ignored` (`ignored_by`,`ignored`),
  KEY `ignored` (`ignored`),
  KEY `ignored_by` (`ignored_by`),
  CONSTRAINT `confa_ignor_ibfk_1` FOREIGN KEY (`ignored_by`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_ignor_ibfk_2` FOREIGN KEY (`ignored`) REFERENCES `confa_users` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_ignor`
--

LOCK TABLES `confa_ignor` WRITE;
/*!40000 ALTER TABLE `confa_ignor` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_ignor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_likes`
--

DROP TABLE IF EXISTS `confa_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `value` int(4) DEFAULT NULL,
  `reaction` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_post` (`user`,`post`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_likes_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_likes_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7807 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_likes`
--

LOCK TABLES `confa_likes` WRITE;
/*!40000 ALTER TABLE `confa_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_pm`
--

DROP TABLE IF EXISTS `confa_pm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `flags` int(11) DEFAULT NULL,
  `chars` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9351 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_pm`
--

LOCK TABLES `confa_pm` WRITE;
/*!40000 ALTER TABLE `confa_pm` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_pm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_polls`
--

DROP TABLE IF EXISTS `confa_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `content` text,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `question_id` int(11) DEFAULT NULL,
  `answer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_polls`
--

LOCK TABLES `confa_polls` WRITE;
/*!40000 ALTER TABLE `confa_polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_posts`
--

DROP TABLE IF EXISTS `confa_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `subject` varchar(255) CHARACTER SET latin1 NOT NULL,
  `body` text CHARACTER SET latin1,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  `thread_id` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `chars` int(11) DEFAULT NULL,
  `auth` tinyint(4) DEFAULT NULL,
  `IP` varchar(16) DEFAULT NULL,
  `user_agent` varchar(128) DEFAULT NULL,
  `page` int(11) DEFAULT '1',
  `views` int(11) DEFAULT '0',
  `reputation` int(11) DEFAULT '0',
  `test` int(11) DEFAULT '0',
  `closed` tinyint(4) DEFAULT '0',
  `likes` smallint(6) DEFAULT '0',
  `dislikes` smallint(6) DEFAULT '0',
  `content_flags` smallint(6) DEFAULT '0',
  `modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  KEY `IP` (`IP`),
  KEY `author` (`author`),
  KEY `status` (`status`),
  KEY `parent` (`parent`),
  KEY `created` (`created`),
  KEY `test` (`test`)
) ENGINE=InnoDB AUTO_INCREMENT=347979 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_posts`
--

LOCK TABLES `confa_posts` WRITE;
/*!40000 ALTER TABLE `confa_posts` DISABLE KEYS */;
INSERT INTO `confa_posts` VALUES (347978,0,1087,'First post: welcome!','Since you see this message you already know how to read the forum. To react or respond to this message please click React or Reply down below. While everyone can read this forum, only registered users can participate in the discussion. Use Register icon on the menu bar to proceed. Thank you and good luck!\r\n\r\nAdministrator.','2017-06-07 18:48:21',1,25418,0,324,1,'172.110.64.210','Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',1,9,0,0,0,0,0,0,NULL);
/*!40000 ALTER TABLE `confa_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_regs`
--

DROP TABLE IF EXISTS `confa_regs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_regs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(41) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(80) NOT NULL,
  `actkey` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `actkey` (`actkey`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_regs`
--

LOCK TABLES `confa_regs` WRITE;
/*!40000 ALTER TABLE `confa_regs` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_regs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_reports`
--

DROP TABLE IF EXISTS `confa_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `content_flags` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_reports_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_reports_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_reports`
--

LOCK TABLES `confa_reports` WRITE;
/*!40000 ALTER TABLE `confa_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_reputation`
--

DROP TABLE IF EXISTS `confa_reputation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_reputation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post` (`post`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_reputation`
--

LOCK TABLES `confa_reputation` WRITE;
/*!40000 ALTER TABLE `confa_reputation` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_reputation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_sessions`
--

DROP TABLE IF EXISTS `confa_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `last_bydate_time` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `last_answered_time` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `last_bydate_id` int(11) DEFAULT '0',
  `safe_mode` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=21711 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_sessions`
--

LOCK TABLES `confa_sessions` WRITE;
/*!40000 ALTER TABLE `confa_sessions` DISABLE KEYS */;
INSERT INTO `confa_sessions` VALUES (21710,'2017-06-07 18:48:26','2017-06-07 14:47:42',1087,'d0ee71ae00989597e6cf4be3a5efdcda','0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
/*!40000 ALTER TABLE `confa_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_threads`
--

DROP TABLE IF EXISTS `confa_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` int(11) DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  `page` int(11) DEFAULT '1',
  `status` int(11) DEFAULT '1',
  `npage` int(11) DEFAULT NULL,
  `closed` tinyint(4) DEFAULT '0',
  `properties` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=25419 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_threads`
--

LOCK TABLES `confa_threads` WRITE;
/*!40000 ALTER TABLE `confa_threads` DISABLE KEYS */;
INSERT INTO `confa_threads` VALUES (25417,'2017-06-07 18:42:11',1086,0,1,1,NULL,0,NULL),(25418,'2017-06-07 18:48:21',1087,0,1,1,NULL,0,NULL);
/*!40000 ALTER TABLE `confa_threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_tickets`
--

DROP TABLE IF EXISTS `confa_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_tickets` (
  `ticket` varchar(128) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_tickets`
--

LOCK TABLES `confa_tickets` WRITE;
/*!40000 ALTER TABLE `confa_tickets` DISABLE KEYS */;
INSERT INTO `confa_tickets` VALUES ('2892906706-1496861264','2017-06-07 18:48:21',0);
/*!40000 ALTER TABLE `confa_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_users`
--

DROP TABLE IF EXISTS `confa_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(41) CHARACTER SET latin1 DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) DEFAULT '1',
  `moder` tinyint(4) DEFAULT NULL,
  `ban` int(11) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `ban_ends` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page` int(11) NOT NULL DEFAULT '1',
  `new_pm` int(11) NOT NULL DEFAULT '0',
  `ban_private` int(11) DEFAULT NULL,
  `rep_in` int(11) DEFAULT '0',
  `rep_in_count` int(11) DEFAULT '0',
  `rep_out_count` int(11) DEFAULT '0',
  `rep_out` int(11) DEFAULT '0',
  `prop_bold` tinyint(1) DEFAULT '0',
  `prop_tz` varchar(150) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `attributes` text,
  `uid` varchar(64) DEFAULT NULL,
  `pban` int(11) DEFAULT '0',
  `lock_expiry` date DEFAULT NULL,
  `last_pm_check_time` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `show_smileys` smallint(6) DEFAULT '1',
  `show_hidden` smallint(6) DEFAULT '1',
  `safe_mode` smallint(6) DEFAULT '0',
  `reply_to_email` tinyint(1) DEFAULT '0',
  `menu_style` smallint(6) DEFAULT '0',
  `color_ribbon` varchar(10) DEFAULT '#ffffff',
  `color_ribbon_background` varchar(10) DEFAULT '#0080c0',
  `color_icon_hover` varchar(10) DEFAULT '#0090c0',
  `color_group_border` varchar(10) DEFAULT '#0090c0',
  `color_topics_unread` varchar(10) DEFAULT '#0000ff',
  `color_topics_hover` varchar(10) DEFAULT '#ff0000',
  `color_topics_visited` varchar(10) DEFAULT '#0080c0',
  `color_titles` varchar(10) DEFAULT '#0080c0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `username_2` (`username`),
  UNIQUE KEY `username_3` (`username`),
  KEY `ban_ends` (`ban_ends`),
  KEY `ban` (`ban`),
  KEY `status` (`status`),
  KEY `password` (`password`)
) ENGINE=InnoDB AUTO_INCREMENT=1088 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_users`
--

LOCK TABLES `confa_users` WRITE;
/*!40000 ALTER TABLE `confa_users` DISABLE KEYS */;
INSERT INTO `confa_users` VALUES (1087,'administrator','*00A51F3F48415C7D4E8908980D443C29C69B60C9','2017-06-07 18:47:30','2017-06-07 18:47:30',1,1,NULL,'p_wol@hotmail.com','0000-00-00 00:00:00',1,0,NULL,0,0,0,0,0,'America/Toronto',NULL,NULL,NULL,0,NULL,'0000-00-00 00:00:00',1,1,0,0,0,'#ffffff','#0080c0','#0090c0','#0090c0','#0000ff','#ff0000','#0080c0','#0080c0');
/*!40000 ALTER TABLE `confa_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confa_versions`
--

DROP TABLE IF EXISTS `confa_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confa_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `subject` varchar(255) CHARACTER SET latin1 NOT NULL,
  `body` text CHARACTER SET latin1,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) DEFAULT '1',
  `chars` int(11) DEFAULT NULL,
  `IP` varchar(16) DEFAULT NULL,
  `user_agent` varchar(128) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `content_flags` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=333677 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confa_versions`
--

LOCK TABLES `confa_versions` WRITE;
/*!40000 ALTER TABLE `confa_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `confa_versions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-08 17:54:34
