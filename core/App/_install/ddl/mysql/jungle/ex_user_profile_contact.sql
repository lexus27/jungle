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