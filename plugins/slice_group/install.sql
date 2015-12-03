CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%slice_groups` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `output` text NOT NULL,
    `createuser` varchar(255) NOT NULL,
    `updateuser` varchar(255) NOT NULL,
    `createdate` datetime NOT NULL,
    `updatedate` datetime NOT NULL,
    `attributes` text,
    `revision` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;