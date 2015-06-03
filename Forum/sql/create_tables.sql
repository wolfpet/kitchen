-- MySQL dump 10.11
--
-- Host: localhost    Database: confa2
-- ------------------------------------------------------
-- Server version	5.0.90

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
-- Table structure for table `confa_ban_history`
--

DROP TABLE IF EXISTS `confa_ban_history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_ban_history` (
  `id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `moder` int(11) NOT NULL,
  `expires` timestamp NOT NULL default '0000-00-00 00:00:00',
  `victim` int(11) NOT NULL,
  `ban_reason` varchar(256) default NULL,
  PRIMARY KEY  (`id`),
  KEY `moder` (`moder`),
  KEY `victim` (`victim`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_bookmarks`
--

DROP TABLE IF EXISTS `confa_bookmarks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_bookmarks` (
  `id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_bookmarks_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_bookmarks_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_likes`
--

DROP TABLE IF EXISTS `confa_likes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_likes` (
  `id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `value` int(4) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_post` (`user`,`post`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_likes_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_likes_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4682 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_pm`
--

DROP TABLE IF EXISTS `confa_pm`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_pm` (
  `id` int(11) NOT NULL auto_increment,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `flags` int(11) default NULL,
  `chars` int(11) default NULL,
  `status` int(11) default '1',
  PRIMARY KEY  (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8506 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_posts`
--

DROP TABLE IF EXISTS `confa_posts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_posts` (
  `id` int(11) NOT NULL auto_increment,
  `parent` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `subject` varchar(255) character set latin1 NOT NULL,
  `body` text character set latin1,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` int(11) default '1',
  `thread_id` int(11) NOT NULL,
  `level` int(11) NOT NULL default '0',
  `chars` int(11) default NULL,
  `auth` tinyint(4) default NULL,
  `IP` varchar(16) default NULL,
  `user_agent` varchar(128) default NULL,
  `page` int(11) default '1',
  `views` int(11) default '0',
  `reputation` int(11) default '0',
  `test` int(11) default '0',
  `closed` tinyint(4) default '0',
  `likes` smallint(6) default '0',
  `dislikes` smallint(6) default '0',
  `content_flags` smallint(6) default '0',
  PRIMARY KEY  (`id`),
  KEY `thread_id` (`thread_id`),
  KEY `IP` (`IP`),
  KEY `author` (`author`),
  KEY `status` (`status`),
  KEY `parent` (`parent`),
  KEY `created` (`created`),
  KEY `test` (`test`)
) ENGINE=InnoDB AUTO_INCREMENT=332283 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_regs`
--

DROP TABLE IF EXISTS `confa_regs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_regs` (
  `ID` int(11) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(41) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `email` varchar(80) NOT NULL,
  `actkey` varchar(64) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `actkey` (`actkey`)
) ENGINE=InnoDB AUTO_INCREMENT=613 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_reputation`
--

DROP TABLE IF EXISTS `confa_reputation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_reputation` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `post` (`post`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_sessions`
--

DROP TABLE IF EXISTS `confa_sessions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_sessions` (
  `id` int(11) NOT NULL auto_increment,
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `hash` varchar(32) default NULL,
  `last_bydate_time` timestamp NULL default '0000-00-00 00:00:00',
  `last_answered_time` timestamp NULL default '0000-00-00 00:00:00',
  `last_bydate_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=21070 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_threads`
--

DROP TABLE IF EXISTS `confa_threads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_threads` (
  `id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `author` int(11) default NULL,
  `counter` int(11) NOT NULL default '0',
  `page` int(11) default '1',
  `status` int(11) default '1',
  `npage` int(11) default NULL,
  `closed` tinyint(4) default '0',
  `properties` varchar(16) default NULL,
  PRIMARY KEY  (`id`),
  KEY `page` (`page`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=23596 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_tickets`
--

DROP TABLE IF EXISTS `confa_tickets`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_tickets` (
  `ticket` varchar(128) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `used` tinyint(4) default '0',
  PRIMARY KEY  (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `confa_users`
--

DROP TABLE IF EXISTS `confa_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_users` (
  `ID` int(11) NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(41) character set latin1 default NULL,
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `status` int(11) default '1',
  `moder` tinyint(4) default NULL,
  `ban` int(11) default NULL,
  `email` varchar(80) default NULL,
  `ban_ends` timestamp NOT NULL default '0000-00-00 00:00:00',
  `page` int(11) NOT NULL default '1',
  `new_pm` int(11) NOT NULL default '0',
  `ban_private` int(11) default NULL,
  `rep_in` int(11) default '0',
  `rep_in_count` int(11) default '0',
  `rep_out_count` int(11) default '0',
  `rep_out` int(11) default '0',
  `prop_bold` tinyint(1) default '0',
  `prop_tz` tinyint(4) default '-5',
  `fullname` varchar(255) default NULL,
  `attributes` text,
  `uid` varchar(64) default NULL,
  `pban` int(11) default '0',
  `lock_expiry` date default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `username_2` (`username`),
  UNIQUE KEY `username_3` (`username`),
  KEY `ban_ends` (`ban_ends`),
  KEY `ban` (`ban`),
  KEY `status` (`status`),
  KEY `password` (`password`)
) ENGINE=InnoDB AUTO_INCREMENT=1019 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'confa2'
--
DELIMITER ;;
/*!50003 DROP PROCEDURE IF EXISTS `get_last_ids` */;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`client`@`localhost`*/ /*!50003 PROCEDURE `get_last_ids`(in u_id int, out max_id int, out last_id int)
begin case u_id when u_id is null then select max(id) from confa_posts into max_id; else begin select max(id) from confa_posts into max_id; select max(last_bydate_id) from confa_sessions where user_id=u_id into last_id; update confa_sessions set last_bydate_id=max_id where user_id = u_id; end; end case; end */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-08 21:29:51
