alter table confa_likes MODIFY column `value` int(4) null;
alter table confa_likes add column `reaction` varchar(20) null;