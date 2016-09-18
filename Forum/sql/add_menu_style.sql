ALTER TABLE confa_users ADD COLUMN menu_style SMALLINT NULL DEFAULT 0;
UPDATE confa_users SET menu_style=0;