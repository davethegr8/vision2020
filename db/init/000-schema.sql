-- Create syntax for TABLE 'admin'
CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'auction'
CREATE TABLE `auction` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) unsigned NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `size` varchar(128) DEFAULT NULL,
  `medium` varchar(128) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `notified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'invites'
CREATE TABLE `invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(16) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'jury'
CREATE TABLE `jury` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'jury_votes'
CREATE TABLE `jury_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jury_id` int(11) unsigned NOT NULL,
  `submission_id` int(11) unsigned NOT NULL,
  `type` varchar(16) DEFAULT NULL,
  `type_id` int(11) unsigned NOT NULL,
  `score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'panel'
CREATE TABLE `panel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) unsigned NOT NULL,
  `in_person` text,
  `status` varchar(16) DEFAULT NULL,
  `notified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'photos'
CREATE TABLE `photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `filename` varchar(256) DEFAULT NULL,
  `table_ref` varchar(256) DEFAULT NULL,
  `table_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'submissions'
CREATE TABLE `submissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `address` varchar(512) DEFAULT NULL,
  `contact` varchar(32) DEFAULT NULL,
  `bio` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
