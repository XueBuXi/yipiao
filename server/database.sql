-- YiPiaoTicket 2018.05.01
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `yipiao_ticket`;
CREATE TABLE `yipiao_ticket` (
  `ticket` varchar(30) NOT NULL COMMENT '票号',
  `tpass` varchar(20) NOT NULL COMMENT '票密码',
  `user` varchar(100) DEFAULT NULL COMMENT '订票用户',
  `seat` varchar(10) DEFAULT NULL COMMENT '座位 01-01',
  `status` varchar(10) NOT NULL DEFAULT 'null' COMMENT 'null/sold/active/use',
  `type` varchar(10) NOT NULL DEFAULT 'online' COMMENT 'offline/online 线上/下',
  `level` varchar(10) NOT NULL DEFAULT 'normal' COMMENT '票等normal/vip',
  `phone` varchar(12) NOT NULL COMMENT '预留手机',
  `active_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '激活时间',
  `sold_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '出票时间',
  `seat_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '选座时间',
  `use_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '入场时间',
  PRIMARY KEY (`ticket`),
  UNIQUE KEY `ticket_tpass` (`ticket`,`tpass`),
  UNIQUE KEY `seat` (`seat`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2018-05-01 05:43:43