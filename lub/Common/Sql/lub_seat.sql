

CREATE TABLE IF NOT EXISTS `@lubtmp@@seat@` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `row` int(3) unsigned NOT NULL COMMENT '行',
  `list` int(3) unsigned NOT NULL COMMENT '列',
  `area` int(11) NOT NULL COMMENT '区域',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计划座椅表模板';

