-- this table will register events such as likes, replies, etc
-- event_type: 0 - reaction, 1-new thread, 2 - reply,  3-bookmark

use confa;
DROP TABLE IF EXISTS `confa_movies`;
SET character_set_client = utf8;
CREATE TABLE `confa_movies` (
  `movie_id` int(11) NOT NULL auto_increment,
  `added_by_id` int(11),
  `msg_id` int(11) NOT NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
