DROP TABLE IF EXISTS `%TABLE_PREFIX%blogger_entries`;

CREATE TABLE `%TABLE_PREFIX%blogger_entries` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL DEFAULT 1,
  `tags` text NOT NULL DEFAULT '',
  `status` smallint(1) NOT NULL DEFAULT 0,
  `postedBy` VARCHAR(255) NOT NULL DEFAULT '',
  `postedAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `%TABLE_PREFIX%blogger_content`;

CREATE TABLE `%TABLE_PREFIX%blogger_content` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `clang` smallint(5) NOT NULL DEFAULT 1,
  `title` varchar(1024) NOT NULL DEFAULT '',
  `text` text NOT NULL DEFAULT '',
  `preview` varchar(1024) NOT NULL DEFAULT '',
  `gallery` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `%TABLE_PREFIX%blogger_categories`;

CREATE TABLE `%TABLE_PREFIX%blogger_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `createdBy` smallint(5) NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `%TABLE_PREFIX%blogger_categories` (name) VALUES ("Default");


DROP TABLE IF EXISTS `%TABLE_PREFIX%blogger_tags`;

CREATE TABLE `%TABLE_PREFIX%blogger_tags` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag` varchar(256) NOT NULL DEFAULT '',
  `createdBy` smallint(5) NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `%TABLE_PREFIX%blogger_tags` (tag) VALUES ("Default");