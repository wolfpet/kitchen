CREATE TABLE IF NOT EXISTS confa_likes(
`id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `value` int(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  UNIQUE KEY `user_post`(`user`, `post`),
  CONSTRAINT `confa_likes_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_likes_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

ALTER TABLE confa_posts ADD COLUMN likes SMALLINT NULL DEFAULT 0;
ALTER TABLE confa_posts ADD COLUMN dislikes SMALLINT NULL DEFAULT 0;
CREATE TABLE IF NOT EXISTS confa_bookmarks(
`id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `post` (`post`),
  UNIQUE KEY `user_post`(`user`, `post`),
  CONSTRAINT `confa_bookmarks_ibfk_1` FOREIGN KEY (`user`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_bookmarks_ibfk_2` FOREIGN KEY (`post`) REFERENCES `confa_posts` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

