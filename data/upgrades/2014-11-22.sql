alter table user_likes add score DECIMAL(20,8);
alter table user_likes add opUser INT(11) unsigned DEFAULT 0;
alter table user_likes add index(opUser);
