DROP TABLE IF EXISTS `%TABLE_PREFIX%blogger_entries`;

CREATE TABLE `%TABLE_PREFIX%blogger_entries` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `art_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `translation` smallint(1) UNSIGNED NOT NULL DEFAULT 0,
  `clang` smallint(5) NOT NULL DEFAULT 1,
  `category` smallint(5) NOT NULL DEFAULT 1,
  `preview` varchar(1024) NOT NULL DEFAULT '',
  `headline` varchar(1024) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `gallery` text NOT NULL DEFAULT '',
  `tags` text NOT NULL DEFAULT '',
  `offline` smallint(1) NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL,
  `createdBy` smallint(5) NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedBy` smallint(5) NOT NULL,
  `updatedAt` datetime NOT NULL,
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