CREATE TABLE `tad_booking` (
  `id` mediumint unsigned NOT NULL auto_increment COMMENT '預約編號',
  `uid` mediumint unsigned NOT NULL default '0' COMMENT '預約者',
  `booking_time` datetime NOT NULL COMMENT '預約時間',
  `booking_content` varchar(255) default '' COMMENT '預約理由',
  `start_date` date NOT NULL COMMENT '開始日期',
  `end_date` date COMMENT '結束日期',
  `info` text COMMENT '相關資訊',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `tad_booking_cate` (
  `id` smallint unsigned NOT NULL auto_increment COMMENT '編號',
  `title` varchar(255) NOT NULL default '' COMMENT '類別名稱',
  `sort` smallint unsigned NOT NULL default '0' COMMENT '類別排序',
  `enable` enum('1','0') NOT NULL default '1' COMMENT '狀態',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `tad_booking_data` (
  `booking_id` mediumint unsigned NOT NULL COMMENT '預約編號',
  `booking_date` date NOT NULL COMMENT '日期',
  `section_id` mediumint unsigned NOT NULL COMMENT '時段編號',
  `waiting` tinyint default '0' COMMENT '候補',
  `status` enum('1','0') COMMENT '是否核准',
  `approver` mediumint unsigned default '0' COMMENT '審核者',
  `pass_date` date COMMENT '通過日期',
PRIMARY KEY  (`booking_id`,`booking_date`,`section_id`)
) ENGINE=MyISAM;

CREATE TABLE `tad_booking_item` (
  `id` smallint unsigned NOT NULL auto_increment COMMENT '編號',
  `cate_id` smallint(6) unsigned default '0' COMMENT '類別編號',
  `title` varchar(255) NOT NULL default '' COMMENT '名稱',
  `desc` text COMMENT '說明',
  `sort` smallint unsigned default '0' COMMENT '排序',
  `start` date NOT NULL COMMENT '啟用日期',
  `end` date COMMENT '停用日期',
  `enable` enum('1','0') NOT NULL COMMENT '是否可借',
  `approval` varchar(255) default '' COMMENT '審核人員',
  `info` text COMMENT '相關資訊',
PRIMARY KEY  (`id`),
KEY (`cate_id`)
) ENGINE=MyISAM;

CREATE TABLE `tad_booking_section` (
  `id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '時段編號',
  `item_id` smallint(6) unsigned NOT NULL COMMENT '場地編號',
  `title` varchar(255) NOT NULL default '' COMMENT '時段標題',
  `sort` smallint(6) unsigned NOT NULL default '0' COMMENT '時段排序',
  `week` set('0','1','2','3','4','5','6') NOT NULL COMMENT '開放星期' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `tad_booking_week` (
  `booking_id` mediumint(9) unsigned NOT NULL COMMENT '預約編號',
  `week` tinyint(1) NOT NULL COMMENT '星期',
  `section_id` mediumint(8) unsigned NOT NULL COMMENT '時段編號',
  PRIMARY KEY (`booking_id`,`week`,`section_id`)
) ENGINE=MyISAM;

