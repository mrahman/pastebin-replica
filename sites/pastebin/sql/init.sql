CREATE DATABASE IF NOT EXISTS pastebin DEFAULT CHARSET utf8mb4;
GRANT ALL PRIVILEGES ON pastebin.* to 'pasteuser'@'localhost' IDENTIFIED BY 'pastepass' WITH GRANT OPTION;

use pastebin;

CREATE TABLE IF NOT EXISTS `pastebin` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `poster` varchar(16) DEFAULT NULL,
  `posted` datetime DEFAULT NULL,
  `code` text,
  `parent_pid` int(11) DEFAULT '0',
  `format` varchar(16) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `expiry_flag` enum('d','m','f') NOT NULL DEFAULT 'm',
  PRIMARY KEY (`pid`),
  KEY `parent_pid` (`parent_pid`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4;

CREATE TABLE IF NOT EXISTS `recent` (
  `pid` int(11) NOT NULL,
  `seq_no` int(11) NOT NULL,
  PRIMARY KEY (`seq_no`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4;

