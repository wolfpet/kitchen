use confa;
DROP TABLE IF EXISTS `confa_events`;
SET character_set_client = utf8;
CREATE TABLE `confa_events` (
  `event_id` int(11) NOT NULL auto_increment,
  `item_owner_id` int(11),
  `item_id` int(11) NOT NULL,
  `event_owner_id` int(11) NOT NULL,
  `event_type` int(11) NOT NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
