<?php

/**
 * @Author: IT Work
 * @Date:   2020-01-24 18:10:09
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-01-26 23:57:39
 */
DEFAULT CURRENT_TIMESTAMP

CREATE TABLE `account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL DEFAULT '0',
  `password` varchar(40) NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `sign` varchar(320) NOT NULL COMMENT '签名',
  `referees` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `idcard` varchar(18) NOT NULL DEFAULT '0' COMMENT '身份证号码',
  `verify` varchar(16) NOT NULL,
  `head_img` varchar(300) NOT NULL DEFAULT '0',
  `last_login_ip` varchar(32) NOT NULL DEFAULT '0',
  `last_login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `scenario` tinyint(1) NOT NULL DEFAULT '0' COMMENT '场景值1管理系统2分销系统5协会审核6微信小程序',
  `role_id` int(11) NOT NULL COMMENT '所属角色',
  `type` tinyint(1) NOT NULL COMMENT '1系统2第三方5微信商城',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`username`,`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10087 DEFAULT CHARSET=utf8 COMMENT='管理用户表,商户员工表'