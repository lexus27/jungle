-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.22-log - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица jungle.ex_comment
CREATE TABLE IF NOT EXISTS `ex_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_schema` varchar(255) NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ex_comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ex_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_contact_type
CREATE TABLE IF NOT EXISTS `ex_contact_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `messenger_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_contact_type_id_uindex` (`id`),
  UNIQUE KEY `ex_contact_type_name_uindex` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_session
CREATE TABLE IF NOT EXISTS `ex_session` (
  `id` varchar(255) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `modify_time` datetime NOT NULL,
  `data` text,
  `registered_ip` varchar(255) DEFAULT NULL,
  `registered_user_agent` varchar(255) DEFAULT NULL,
  `token` tinyint(4) NOT NULL DEFAULT '0',
  `permanent` tinyint(4) NOT NULL DEFAULT '0',
  `permissions` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_session_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user
CREATE TABLE IF NOT EXISTS `ex_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name_id_uindex` (`id`),
  UNIQUE KEY `table_name_username_uindex` (`username`),
  KEY `ex_user_password_hash_index` (`password_hash`),
  KEY `ex_user_password2222_hash_index` (`password_hash`),
  KEY `222222` (`password_hash`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user_group
CREATE TABLE IF NOT EXISTS `ex_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `rank` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_user_group_id_uindex` (`id`),
  UNIQUE KEY `ex_user_group_title_uindex` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user_group_member
CREATE TABLE IF NOT EXISTS `ex_user_group_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_user_group_member_id_uindex` (`id`),
  KEY `ex_user_group_member_ex_user_id_fk` (`user_id`),
  KEY `ex_user_group_member_ex_user_group_id_fk` (`group_id`),
  CONSTRAINT `ex_user_group_member_ex_user_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `ex_user_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ex_user_group_member_ex_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `ex_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user_note
CREATE TABLE IF NOT EXISTS `ex_user_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `header` varchar(255) NOT NULL,
  `body` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_user_notes_id_uindex` (`id`),
  KEY `ex_user_notes_ex_user_id_fk` (`user_id`),
  CONSTRAINT `ex_user_notes_ex_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `ex_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user_profile
CREATE TABLE IF NOT EXISTS `ex_user_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `mobilephone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ex_user_profile_id_uindex` (`id`),
  KEY `ex_user_profile_id_index` (`id`),
  KEY `ex_user_profile_first_name_last_name_index` (`first_name`,`last_name`),
  CONSTRAINT `ex_user_profile_ex_user_id_fk` FOREIGN KEY (`id`) REFERENCES `ex_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.


-- Дамп структуры для таблица jungle.ex_user_profile_contact
CREATE TABLE IF NOT EXISTS `ex_user_profile_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `definition` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `ex_user_profile_contact_id_uindex` (`id`),
  KEY `ex_user_profile_contact_ex_contact_type_id_fk` (`type_id`),
  KEY `ex_user_profile_contact_ex_user_profile_id_fk` (`user_id`),
  CONSTRAINT `ex_user_profile_contact_ex_contact_type_id_fk` FOREIGN KEY (`type_id`) REFERENCES `ex_contact_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ex_user_profile_contact_ex_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `ex_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ex_user_profile_contact_ex_user_profile_id_fk` FOREIGN KEY (`user_id`) REFERENCES `ex_user_profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
