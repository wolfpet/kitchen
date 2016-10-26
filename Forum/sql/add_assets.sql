DROP TABLE IF EXISTS `confa_assets`;
SET character_set_client = utf8;
CREATE TABLE `confa_assets` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `msg_id` varchar(255) character set latin1 NOT NULL,
  `URL` text character set latin1,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
