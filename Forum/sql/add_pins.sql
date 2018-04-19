use confa;
DROP TABLE IF EXISTS `confa_pins`;
SET character_set_client = utf8;
CREATE TABLE `confa_pins` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
