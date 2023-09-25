drop database if exists examenoefening_fons;
create database examenoefening_fons;
use examenoefening_fons;

create table users
(
	id         int unsigned not null auto_increment,
	uid        varchar(20)  not null unique,
	first_name varchar(256) not null,
	last_name  varchar(256) not null,
	phone      varchar(20) unique,
	email      varchar(256) unique,
	username   varchar(256) not null unique,
	password   varchar(60),
	is_admin   bool         not null default false,
	created_at datetime     not null default current_timestamp,
	updated_at datetime,

	primary key (id),
	index (first_name, last_name, email)
);

create table `groups`
(
	id          int unsigned not null auto_increment,
	uid         varchar(20)  not null unique,
	name        varchar(256) not null,
	description longtext,
	created_at  datetime     not null default current_timestamp,
	updated_at  datetime,

	primary key (id),
	index (name),
	index (created_at)
);

create table group_members
(
	group_id     int unsigned not null,
	member_id    int unsigned not null,
	is_moderator bool         not null default false,

	unique (group_id, member_id),
	foreign key (group_id) references `groups` (id) ON DELETE CASCADE,
	foreign key (member_id) references users (id) ON DELETE CASCADE
);

create table posts
(
	id         int unsigned not null auto_increment,
	uid        varchar(20)  not null unique,
	content    longtext,
	poster_id  int unsigned not null,
	group_id   int unsigned,
	created_at datetime     not null default current_timestamp,
	updated_at datetime,

	primary key (id),
	foreign key (poster_id) references users (id) ON DELETE CASCADE,
	foreign key (group_id) references `groups` (id) ON DELETE CASCADE,
	index (created_at)
);

create table friends
(
	friend_a    int unsigned not null,
	friend_b    int unsigned not null,
	is_accepted bool         not null default false,

	unique (friend_a, friend_b)
);

create table comments
(
	id           int unsigned not null auto_increment,
	uid          varchar(20)  not null unique,
	content      longtext,
	commenter_id int unsigned not null,
	post_id      int unsigned not null,
	created_at   datetime     not null default current_timestamp,
	updated_at   datetime,

	primary key (id),
	foreign key (commenter_id) references users (id) ON DELETE CASCADE,
	foreign key (post_id) references posts (id) ON DELETE CASCADE,
	index (created_at)
);

create table likes
(
	liked_id   int unsigned not null,
	liked_type varchar(255) not null,
	liker_id   int unsigned not null,

	unique (liked_id, liked_type, liker_id),
	foreign key (liker_id) references users (id) ON DELETE CASCADE
);