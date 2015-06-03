alter table confa_posts add column content_flags smallint default 0;
update confa_posts set content_flags = 2 where body like '%http://www.youtube.com/%';
update confa_posts set content_flags = 2 where body like '%https://www.youtube.com/%';
update confa_posts set content_flags = content_flags + 2 where body like '%http://www.youtube.com/%';

