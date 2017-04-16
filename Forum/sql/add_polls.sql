use confa;
DROP TABLE IF EXISTS `confa_polls`;
SET character_set_client = utf8;
CREATE TABLE `confa_polls` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `content` text,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `question_id` int(11),
  `answer_id` int(11),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
