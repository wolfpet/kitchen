alter table confa_sessions add column last_bydate_time timestamp null default '0000-00-00 00:00:00';
alter table confa_sessions add column last_answered_time timestamp null default '0000-00-00 00:00:00';
