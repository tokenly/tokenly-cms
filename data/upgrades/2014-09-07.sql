create table forum_mods(modId INT(11) unsigned AUTO_INCREMENT, PRIMARY KEY(modId), userId INT(11) unsigned NOT NULL, INDEX(userId), FOREIGN KEY(userId) REFERENCES users(userId) ON DELETE CASCADE, boardId INT(11) unsigned NOT NULL, INDEX(boardId), FOREIGN KEY(boardId) REFERENCES forum_boards(boardId) ON DELETE CASCADE);
alter table forum_posts add buryTime datetime;
alter table forum_posts add editedBy INT(11) unsigned DEFAULT 0;
alter table forum_posts add buriedBy INT(11) unsigned DEFAULT 0;
alter table forum_topics add buried INT(2) DEFAULT 0;
alter table forum_topics add buryTime datetime;
alter table forum_topics add editedBy INT(11) unsigned DEFAULT 0;
alter table forum_topics add buriedBy INT(11) unsigned DEFAULT 0;
alter table blog_posts add notes LONGTEXT;
