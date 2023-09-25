drop database if exists examenvoorbereiding_fons;
create database examenvoorbereiding_fons;
use examenvoorbereiding_fons;

create table user_types
(
	id         int unsigned auto_increment not null,

	`name`     varchar(255)                not null unique,

	created_at datetime                    not null default current_timestamp,
	updated_at datetime                             default null,

	primary key (id),
	index (`name`, created_at)
);

create table users
(
	id         int unsigned auto_increment not null,

	type_id    int unsigned                not null,
	username   varchar(255)                not null unique,
	password   varchar(97)                          default null,

	created_at datetime                    not null default current_timestamp,
	updated_at datetime                             default null,

	primary key (id),
	index (username, created_at),
	foreign key (type_id) references user_types (id)
);

insert into user_types (name)
values ('Administrator'),
       ('Gebruiker');