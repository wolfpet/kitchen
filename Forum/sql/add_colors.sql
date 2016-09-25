
--
-- Table structure for table `confa_users`
--

ALTER TABLE confa_users  ADD COLUMN color_ribbon VARCHAR(10) DEFAULT '#ffffff';
UPDATE confa_users SET color_ribbon='#ffffff';

ALTER TABLE confa_users  ADD COLUMN color_ribbon_background VARCHAR(10) DEFAULT '#0080c0';
UPDATE confa_users SET color_ribbon_background='#0080c0';

ALTER TABLE confa_users  ADD COLUMN color_icon_hover VARCHAR(10) DEFAULT '#0090c0';
UPDATE confa_users SET color_icon_hover='#0090c0';

ALTER TABLE confa_users  ADD COLUMN color_group_border VARCHAR(10) DEFAULT '#0090c0';
UPDATE confa_users SET color_group_border='#0090c0';

ALTER TABLE confa_users  ADD COLUMN color_topics_unread VARCHAR(10) DEFAULT '#0000ff';
UPDATE confa_users SET color_topics_unread='#0000ff';

ALTER TABLE confa_users  ADD COLUMN color_topics_hover VARCHAR(10) DEFAULT '#ff0000';
UPDATE confa_users SET color_topics_hover='#ff0000';

ALTER TABLE confa_users  ADD COLUMN color_topics_visited VARCHAR(10) DEFAULT '#0080c0';
UPDATE confa_users SET color_topics_visited='#0080c0';

ALTER TABLE confa_users  ADD COLUMN color_titles VARCHAR(10) DEFAULT '#0080c0';
UPDATE confa_users SET color_titles='#0080c0';
