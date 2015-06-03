CREATE TABLE IF NOT EXISTS confa_ignor(
`id` int(11) NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ignored_by` int(11) NOT NULL,
  `ignored` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ignored` (`ignored`),
  KEY `ignored_by` (`ignored_by`),
  UNIQUE KEY `ignored_by_ignored`(`ignored_by`, `ignored`),
  CONSTRAINT `confa_ignor_ibfk_1` FOREIGN KEY (`ignored_by`) REFERENCES `confa_users` (`ID`),
  CONSTRAINT `confa_ignor_ibfk_2` FOREIGN KEY (`ignored`) REFERENCES `confa_users` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;

ALTER TABLE confa_users ADD COLUMN show_hidden SMALLINT NULL DEFAULT 1;

