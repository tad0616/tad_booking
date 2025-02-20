CREATE TABLE `tad_booking` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT COMMENT '預約編號',
  `uid` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '預約者',
  `booking_time` datetime NOT NULL COMMENT '預約時間',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '預約理由',
  `start_date` date NOT NULL COMMENT '開始日期',
  `end_date` date DEFAULT NULL COMMENT '結束日期',
  `info` text COLLATE utf8mb4_general_ci COMMENT '相關資訊',
  `batch` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '批次資訊',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_cate` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT '編號',
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '類別名稱',
  `sort` smallint unsigned NOT NULL DEFAULT '0' COMMENT '類別排序',
  `enable` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '狀態',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_data` (
  `booking_id` mediumint unsigned NOT NULL COMMENT '預約編號',
  `booking_date` date NOT NULL COMMENT '日期',
  `item_id` mediumint unsigned NOT NULL COMMENT '項目編號',
  `section_id` mediumint unsigned NOT NULL COMMENT '時段編號',
  `waiting` tinyint DEFAULT '0' COMMENT '候補',
  `status` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '是否核准',
  `approver` mediumint unsigned DEFAULT '0' COMMENT '審核者',
  `pass_date` date DEFAULT NULL COMMENT '通過日期',
  PRIMARY KEY (`booking_id`,`booking_date`,`section_id`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_files_center` (
  `files_sn` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT '檔案流水號',
  `col_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '欄位名稱',
  `col_sn` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '欄位編號',
  `sort` smallint unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `kind` enum('img','file') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'img' COMMENT '檔案種類',
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '檔案名稱',
  `file_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '檔案類型',
  `file_size` int unsigned NOT NULL DEFAULT '0' COMMENT '檔案大小',
  `description` text COLLATE utf8mb4_general_ci NOT NULL COMMENT '檔案說明',
  `counter` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '下載人次',
  `original_filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '檔案名稱',
  `hash_filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加密檔案名稱',
  `sub_dir` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '檔案子路徑',
  `upload_date` datetime NOT NULL COMMENT '上傳時間',
  `uid` mediumint unsigned NOT NULL DEFAULT '0' COMMENT '上傳者',
  `tag` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '註記',
  PRIMARY KEY (`files_sn`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_item` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT COMMENT '編號',
  `cate_id` smallint unsigned DEFAULT '0' COMMENT '類別編號',
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名稱',
  `desc` text COLLATE utf8mb4_general_ci COMMENT '說明',
  `sort` smallint unsigned DEFAULT '0' COMMENT '排序',
  `start` date NOT NULL COMMENT '啟用日期',
  `end` date DEFAULT NULL COMMENT '停用日期',
  `enable` enum('1','0') COLLATE utf8mb4_general_ci NOT NULL COMMENT '是否可借',
  `approval` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '審核人員',
  `info` text COLLATE utf8mb4_general_ci COMMENT '相關資訊',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_section` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT COMMENT '時段編號',
  `item_id` smallint unsigned NOT NULL COMMENT '場地編號',
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '時段標題',
  `sort` smallint unsigned NOT NULL DEFAULT '0' COMMENT '時段排序',
  `week` set('0','1','2','3','4','5','6') COLLATE utf8mb4_general_ci NOT NULL COMMENT '開放星期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


CREATE TABLE `tad_booking_week` (
  `booking_id` mediumint unsigned NOT NULL COMMENT '預約編號',
  `week` tinyint(1) NOT NULL COMMENT '星期',
  `section_id` mediumint unsigned NOT NULL COMMENT '時段編號',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`booking_id`,`week`,`section_id`)
) ENGINE=MyISAM;