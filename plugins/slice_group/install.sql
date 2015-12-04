CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%slice_groups` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `start_wrapper` text NULL,
    `output` text NULL,
    `end_wrapper` text NULL,
    `option_min` int(10) unsigned NOT NULL,
    `option_max` int(10) unsigned NOT NULL,
    `createuser` varchar(255) NOT NULL,
    `updateuser` varchar(255) NOT NULL,
    `createdate` datetime NOT NULL,
    `updatedate` datetime NOT NULL,
    `attributes` text,
    `revision` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;