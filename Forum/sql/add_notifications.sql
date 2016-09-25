
--
-- Table structure for table `confa_users`
--

ALTER TABLE confa_users  ADD COLUMN notify_reply BOOL NOT NULL DEFAULT TRUE;
UPDATE confa_users SET notify_reply=TRUE;

ALTER TABLE confa_users  ADD COLUMN notify_post BOOL NOT NULL DEFAULT TRUE;
UPDATE confa_users SET notify_post=TRUE;

ALTER TABLE confa_users  ADD COLUMN notify_react BOOL NOT NULL DEFAULT TRUE;
UPDATE confa_users SET notify_react=TRUE;

ALTER TABLE confa_users  ADD COLUMN notify_bookmark BOOL NOT NULL DEFAULT FALSE;
UPDATE confa_users SET notify_bookmark=TRUE;
