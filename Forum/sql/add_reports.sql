
--
-- Table structure for table `confa_reports`

DROP TABLE IF EXISTS `confa_reports`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_reports` (
  `id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `post` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `content_flags` smallint(6) default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  CONSTRAINT `confa_reports_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_reports_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=332283 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
