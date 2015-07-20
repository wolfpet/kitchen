
--
-- Table structure for table `confa_versions`
-- subject=\'' . mysql_escape_string($subj) . '\',body=' . $ibody . ',created=now(),ip=' .$ip. ',user_agent=' .$agent. ',content_flags='.$content_flags . ', chars='. $chars .

DROP TABLE IF EXISTS `confa_versions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `confa_versions` (
  `id` int(11) NOT NULL auto_increment,
  `parent` int(11) NOT NULL,
  `subject` varchar(255) character set latin1 NOT NULL,
  `body` text character set latin1,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` int(11) default '1',
  `chars` int(11) default NULL,
  `IP` varchar(16) default NULL,
  `user_agent` varchar(128) default NULL,
  `views` int(11) default '0',
  `content_flags` smallint(6) default '0',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=332283 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

alter table confa_posts add column modified timestamp null default null;
