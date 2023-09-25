DROP DATABASE IF EXISTS `ex-sql-injection-001`;
CREATE DATABASE `ex-sql-injection-001`;

DROP DATABASE IF EXISTS `ex-sql-injection-002`;
CREATE DATABASE `ex-sql-injection-002`;

USE `ex-sql-injection-001`;

CREATE TABLE `users`
(
	`id`   INT                 NOT NULL AUTO_INCREMENT,
	`user` VARCHAR(255) UNIQUE NOT NULL,
	`pass` VARCHAR(60)         NOT NULL,
	PRIMARY KEY (`id`)
);

INSERT INTO `users`(`user`, `pass`)
VALUES ('user',
        '$2y$10$r75bN32WXp56TEzTrY8jcOKfsi2i8GyH8ilSpLohn1WwRdSjC1ywK' -- user
       ),
       ('dummy1',
        '$2y$10$114BHYh1HwDL/rvTO/l08ujjJPHFcIoNQ2sNVF9kT2xbSyjpSQ8Dm' -- een willekeurig wachtwoord
       ),
       ('dummy2',
        '$2y$10$0nNqacuFZySfDqFp6WGi3OSj6FjjXWYuuGkysCnA6utSxj4plsnLi' -- een willekeurig wachtwoord
       );

USE `ex-sql-injection-002`;

CREATE TABLE `users`
(
	`id`   INT                 NOT NULL AUTO_INCREMENT,
	`user` VARCHAR(255) UNIQUE NOT NULL,
	`pass` VARCHAR(60)         NOT NULL,
	PRIMARY KEY (`id`)
);

INSERT INTO `users`(`user`, `pass`)
VALUES ('user',
        '$2y$10$r75bN32WXp56TEzTrY8jcOKfsi2i8GyH8ilSpLohn1WwRdSjC1ywK' -- user
       ),
       ('dummy1',
        '$2y$10$114BHYh1HwDL/rvTO/l08ujjJPHFcIoNQ2sNVF9kT2xbSyjpSQ8Dm' -- een willekeurig wachtwoord
       ),
       ('dummy2',
        '$2y$10$0nNqacuFZySfDqFp6WGi3OSj6FjjXWYuuGkysCnA6utSxj4plsnLi' -- een willekeurig wachtwoord
       );

-- P.S. Neem geen voorbeeld aan deze methode; het is snel, maar niet handig om wachtwoorden er zo in te hard-coden.
